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
                    'status' => 'التالي',
                    'description' => 'ملفات الأسر، الحالات، البحث الاجتماعي، المرفقات، وقرارات الاعتماد.',
                ],
                [
                    'name' => 'التبرعات والمتجر',
                    'status' => 'مخطط',
                    'description' => 'المشاريع، الحملات، السلة، المدفوعات، وإيصالات المتبرعين.',
                ],
                [
                    'name' => 'المالية والمخزون',
                    'status' => 'مخطط',
                    'description' => 'المصروفات، القيود، العهد، المستودعات، الصرف، والتسويات.',
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
