<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    private const MASKED_VALUE = '[masked]';

    /**
     * @var list<string>
     */
    private array $sensitiveKeys = [
        'password',
        'password_confirmation',
        'current_password',
        'remember_token',
        'token',
        'api_token',
    ];

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public function logModelEvent(Model $model, string $event, ?array $oldValues = null, ?array $newValues = null): AuditLog
    {
        return $this->log(
            action: $this->actionFor($model, $event),
            auditable: $model,
            summary: $this->summaryFor($model, $event),
            oldValues: $oldValues,
            newValues: $newValues,
        );
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public function log(
        string $action,
        ?Model $auditable = null,
        ?string $summary = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        return AuditLog::query()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'summary' => $summary,
            'old_values' => $oldValues === null ? null : $this->mask($oldValues),
            'new_values' => $newValues === null ? null : $this->mask($newValues),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    public function mask(array $values): array
    {
        $masked = [];

        foreach ($values as $key => $value) {
            if ($this->isSensitiveKey((string) $key)) {
                $masked[$key] = self::MASKED_VALUE;

                continue;
            }

            $masked[$key] = is_array($value) ? $this->mask($value) : $value;
        }

        return $masked;
    }

    private function actionFor(Model $model, string $event): string
    {
        return str($model::class)->classBasename()->snake()->append('.', $event)->toString();
    }

    private function summaryFor(Model $model, string $event): string
    {
        return str($model::class)->classBasename()->headline()->append(' ', $event)->toString();
    }

    private function isSensitiveKey(string $key): bool
    {
        return in_array(str($key)->snake()->toString(), $this->sensitiveKeys, true);
    }
}
