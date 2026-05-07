<?php

namespace App\Filament\Widgets;

use App\Models\Beneficiary;
use App\Models\SocialCase;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BeneficiariesOperationsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected ?string $heading = 'مؤشرات التشغيل';

    protected ?string $description = 'ملخص MVP لقسم المستفيدين مع مؤشرات اختيارية تظهر عند توفر جداولها.';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        return [
            Stat::make('ملفات نشطة', $this->activeBeneficiaryFilesCount())
                ->description('ملفات المستفيدين القابلة للخدمة')
                ->descriptionIcon(Heroicon::HomeModern)
                ->color('success'),
            Stat::make('مستفيدون', $this->tableCount('beneficiaries', fn (): int => Beneficiary::query()->count()))
                ->description('إجمالي الأفراد المسجلين')
                ->descriptionIcon(Heroicon::UserGroup)
                ->color('info'),
            Stat::make('حالات مفتوحة', $this->openSocialCasesCount())
                ->description('كل حالة غير مغلقة')
                ->descriptionIcon(Heroicon::FolderOpen)
                ->color('warning'),
            Stat::make('حالات عاجلة/عالية', $this->highPrioritySocialCasesCount())
                ->description('أولوية high أو urgent')
                ->descriptionIcon(Heroicon::BellAlert)
                ->color('danger'),
            Stat::make('طلبات بانتظار فرز', $this->pendingScreeningRequestsCount())
                ->description($this->optionalMetricDescription('assistance_requests'))
                ->descriptionIcon(Heroicon::InboxStack)
                ->color('primary'),
            Stat::make('زيارات اليوم', $this->todayVisitsCount())
                ->description($this->optionalMetricDescription('field_visits'))
                ->descriptionIcon(Heroicon::CalendarDays)
                ->color('info'),
            Stat::make('قرارات بانتظار تنفيذ', $this->pendingExecutionDecisionsCount())
                ->description($this->optionalMetricDescription('committee_decisions'))
                ->descriptionIcon(Heroicon::ClipboardDocumentCheck)
                ->color('warning'),
            Stat::make('متابعات متأخرة', $this->overdueFollowUpsCount())
                ->description($this->optionalMetricDescription('follow_ups'))
                ->descriptionIcon(Heroicon::Clock)
                ->color('danger'),
        ];
    }

    private function activeBeneficiaryFilesCount(): int
    {
        return $this->tableCount('beneficiaries', function (): int {
            return Beneficiary::query()
                ->whereNull('file_owner_id')
                ->where('status', Beneficiary::STATUS_ACTIVE)
                ->count();
        });
    }

    private function openSocialCasesCount(): int
    {
        return $this->tableCount('social_cases', function (): int {
            return SocialCase::query()
                ->whereNotIn('status', ['closed', 'cancelled'])
                ->count();
        });
    }

    private function highPrioritySocialCasesCount(): int
    {
        return $this->tableCount('social_cases', function (): int {
            return SocialCase::query()
                ->whereNotIn('status', ['closed', 'cancelled'])
                ->whereIn('priority', ['high', 'urgent'])
                ->count();
        });
    }

    private function pendingScreeningRequestsCount(): int
    {
        return $this->tableCount('assistance_requests', function (): int {
            return DB::table('assistance_requests')
                ->when(
                    Schema::hasColumn('assistance_requests', 'status'),
                    fn (Builder $query): Builder => $query->whereIn('status', ['submitted', 'screening'])
                )
                ->count();
        });
    }

    private function todayVisitsCount(): int
    {
        return $this->tableCount('field_visits', function (): int {
            $dateColumn = $this->firstExistingColumn('field_visits', [
                'visit_date',
                'scheduled_date',
                'scheduled_at',
                'visited_at',
            ]);

            if ($dateColumn === null) {
                return 0;
            }

            return DB::table('field_visits')
                ->whereDate($dateColumn, today())
                ->count();
        });
    }

    private function pendingExecutionDecisionsCount(): int
    {
        return $this->tableCount('committee_decisions', function (): int {
            $query = DB::table('committee_decisions');

            if (Schema::hasColumn('committee_decisions', 'status')) {
                $query->whereIn('status', ['approved', 'approved_with_changes']);
            }

            $hasServiceDeliveries = Schema::hasTable('service_deliveries');
            $hasDeliveryStatus = $hasServiceDeliveries && Schema::hasColumn('service_deliveries', 'status');
            $hasDeliveredAt = $hasServiceDeliveries && Schema::hasColumn('service_deliveries', 'delivered_at');

            if (
                $hasServiceDeliveries
                && Schema::hasColumn('service_deliveries', 'committee_decision_id')
                && ($hasDeliveryStatus || $hasDeliveredAt)
            ) {
                $query->whereNotExists(function (Builder $query) use ($hasDeliveryStatus, $hasDeliveredAt): void {
                    $query->selectRaw('1')
                        ->from('service_deliveries')
                        ->whereColumn('service_deliveries.committee_decision_id', 'committee_decisions.id')
                        ->where(function (Builder $query) use ($hasDeliveryStatus, $hasDeliveredAt): void {
                            if ($hasDeliveryStatus) {
                                $query->whereIn('status', ['delivered', 'completed']);
                            }

                            if ($hasDeliveredAt) {
                                $query->orWhereNotNull('delivered_at');
                            }
                        });
                });
            }

            return $query->count();
        });
    }

    private function overdueFollowUpsCount(): int
    {
        return $this->tableCount('follow_ups', function (): int {
            $dueColumn = $this->firstExistingColumn('follow_ups', [
                'next_follow_up_at',
                'due_date',
                'scheduled_date',
                'scheduled_at',
                'follow_up_at',
            ]);

            if ($dueColumn === null) {
                return 0;
            }

            return DB::table('follow_ups')
                ->whereDate($dueColumn, '<', today())
                ->when(
                    Schema::hasColumn('follow_ups', 'status'),
                    fn (Builder $query): Builder => $query->whereNotIn('status', ['done', 'completed', 'closed', 'cancelled'])
                )
                ->count();
        });
    }

    /**
     * @param  callable(): int  $callback
     */
    private function tableCount(string $table, callable $callback): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return $callback();
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function firstExistingColumn(string $table, array $columns): ?string
    {
        foreach ($columns as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }

    private function optionalMetricDescription(string $table): string
    {
        return Schema::hasTable($table)
            ? 'من الجداول التشغيلية'
            : 'يظهر عند إضافة الجدول';
    }
}
