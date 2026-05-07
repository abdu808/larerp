<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ModuleRoadmapWidget extends Widget
{
    protected string $view = 'filament.widgets.module-roadmap';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'modules' => [
                [
                    'name' => 'Foundation',
                    'status' => 'بدأ',
                    'description' => 'لوحة الإدارة، المستخدمون، الأدوار، إعدادات الجمعية، وسجل التدقيق.',
                ],
                [
                    'name' => 'المستفيدون والملفات الاجتماعية',
                    'status' => 'MVP بدأ',
                    'description' => 'العائلات، المستفيدون، الحالات الاجتماعية، الملاحظات، والمرفقات الأولية.',
                ],
                [
                    'name' => 'التبرعات والمتجر',
                    'status' => 'MVP بدأ',
                    'description' => 'المشاريع، الحملات، والتبرعات اليدوية دون بوابة دفع فعلية حاليا.',
                ],
                [
                    'name' => 'المالية والمخزون',
                    'status' => 'MVP بدأ',
                    'description' => 'الحسابات المالية، المصروفات، أصناف المخزون، وحركات المخزون.',
                ],
                [
                    'name' => 'الحوكمة والأرشفة',
                    'status' => 'مخطط',
                    'description' => 'المجالس، الاجتماعات، المحاضر، الخطابات، وسير الاعتمادات.',
                ],
                [
                    'name' => 'التقارير والذكاء',
                    'status' => 'مخطط',
                    'description' => 'لوحات مؤشرات، تقارير تنفيذية، وتحليلات لاحقة قابلة للتطوير.',
                ],
            ],
        ];
    }
}
