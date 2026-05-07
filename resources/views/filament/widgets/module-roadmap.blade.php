<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            خريطة الموديولات
        </x-slot>

        <x-slot name="description">
            هذه ليست شاشة نهائية للنظام، بل بوابة تنفيذ توضح ما هو جاهز وما الذي سنبنيه بعده.
        </x-slot>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" dir="rtl">
            @foreach ($modules as $module)
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                            {{ $module['name'] }}
                        </h3>

                        <span class="shrink-0 rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-300 dark:ring-primary-400/30">
                            {{ $module['status'] }}
                        </span>
                    </div>

                    <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        {{ $module['description'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
