@php
    use App\Models\AssistanceRequest;
    use App\Models\Beneficiary;
    use App\Models\BeneficiaryDocument;
    use App\Models\FieldVisit;
    use App\Models\SocialCase;

    $money = fn ($value): string => number_format((float) $value, 2) . ' ر.س';
    $value = fn ($value, string $fallback = '-'): string => filled($value) ? (string) $value : $fallback;
    $date = fn ($value): string => $value ? $value->format('Y-m-d') : '-';

    $tabs = [
        'overview' => 'نظرة عامة',
        'identity' => 'البيانات والسكن',
        'members' => 'التابعون',
        'financial' => 'الوضع المالي',
        'visits' => 'البحث الميداني',
        'requests' => 'طلبات الخدمة',
        'documents' => 'الوثائق',
    ];

    $openCases = $record->socialCases->where('status', '!=', SocialCase::STATUS_CLOSED)->count();
    $activeRequests = $record->assistanceRequests
        ->reject(fn (AssistanceRequest $request): bool => $request->isClosed())
        ->count();
    $missingDocuments = $record->documents
        ->whereIn('verification_status', [
            BeneficiaryDocument::STATUS_UPLOADED,
            BeneficiaryDocument::STATUS_UNDER_REVIEW,
            BeneficiaryDocument::STATUS_REJECTED,
            BeneficiaryDocument::STATUS_EXPIRED,
        ])
        ->count();
    $lastVisit = $record->fieldVisits->sortByDesc('scheduled_at')->first();
    $netIncome = $record->total_income - $record->total_expenses;
    $score = (int) ($record->score ?? 0);
@endphp

<div dir="rtl" class="space-y-6">
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-5 dark:border-gray-800 dark:bg-gray-950/40">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-lg bg-primary-600 px-3 py-1 text-sm font-semibold text-white">
                            {{ $value($record->file_number, 'ملف جديد') }}
                        </span>
                        <span class="rounded-lg bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                            {{ Beneficiary::statusLabelFor($record->status) }}
                        </span>
                        <span class="rounded-lg bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-800 dark:bg-amber-500/15 dark:text-amber-200">
                            {{ Beneficiary::classificationLabelFor($record->classification) }}
                        </span>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold tracking-normal text-gray-950 dark:text-white">
                            {{ $value($record->full_name, 'مستفيد بدون اسم مكتمل') }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            ملف موحد يجمع بيانات المستفيد، التابعين، الدراسة، الزيارات، الطلبات، والوثائق في مسار واحد.
                        </p>
                        <a
                            href="{{ $editUrl }}"
                            class="mt-4 inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-500"
                        >
                            إدارة بيانات الملف
                        </a>
                    </div>
                </div>

                <div class="grid min-w-full grid-cols-2 gap-3 sm:min-w-96">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">درجة الاحتياج</p>
                        <div class="mt-2 flex items-end gap-2">
                            <span class="text-3xl font-bold text-gray-950 dark:text-white">{{ $score }}</span>
                            <span class="pb-1 text-sm text-gray-500">/ 100</span>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-full rounded-full bg-primary-600" style="width: {{ min(100, max(0, $score)) }}%"></div>
                        </div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">صافي الوضع الشهري</p>
                        <p class="mt-2 text-2xl font-bold {{ $netIncome < 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-950 dark:text-white' }}">
                            {{ $money($netIncome) }}
                        </p>
                        <p class="mt-2 text-xs text-gray-500">دخل ناقص مصروفات</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-0 divide-y divide-gray-200 md:grid-cols-4 md:divide-x md:divide-x-reverse md:divide-y-0 dark:divide-gray-800">
            <div class="p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">أفراد الملف</p>
                <p class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">{{ $record->file_members_count }}</p>
            </div>
            <div class="p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">حالات مفتوحة</p>
                <p class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">{{ $openCases }}</p>
            </div>
            <div class="p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">طلبات قائمة</p>
                <p class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">{{ $activeRequests }}</p>
            </div>
            <div class="p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">وثائق تحتاج متابعة</p>
                <p class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">{{ $missingDocuments }}</p>
            </div>
        </div>
    </section>

    <section
        x-data="{ activeTab: 'overview' }"
        class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
    >
        <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-800">
            <div class="flex flex-wrap gap-2">
                @foreach ($tabs as $key => $label)
                    <button
                        type="button"
                        x-on:click="activeTab = '{{ $key }}'"
                        x-bind:class="activeTab === '{{ $key }}'
                            ? 'bg-primary-600 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'"
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="p-5">
            <div x-show="activeTab === 'overview'" class="space-y-5">
                <h3 class="text-lg font-bold text-gray-950 dark:text-white">نظرة عامة على ملف المستفيد</h3>
                <div class="grid gap-4 lg:grid-cols-3">
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                        <p class="text-sm font-semibold text-gray-950 dark:text-white">هوية وتواصل</p>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex justify-between gap-3"><dt class="text-gray-500">الهوية</dt><dd>{{ $value($record->national_id) }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-gray-500">الجوال</dt><dd>{{ $value($record->phone) }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-gray-500">الحالة الاجتماعية</dt><dd>{{ Beneficiary::maritalStatusLabelFor($record->marital_status) ?: '-' }}</dd></div>
                        </dl>
                    </div>
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                        <p class="text-sm font-semibold text-gray-950 dark:text-white">السكن والوضع المالي</p>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex justify-between gap-3"><dt class="text-gray-500">نوع السكن</dt><dd>{{ Beneficiary::housingTypeLabelFor($record->housing_type) ?: '-' }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-gray-500">إجمالي الدخل</dt><dd>{{ $money($record->total_income) }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-gray-500">إجمالي المصروفات</dt><dd>{{ $money($record->total_expenses) }}</dd></div>
                        </dl>
                    </div>
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                        <p class="text-sm font-semibold text-gray-950 dark:text-white">آخر متابعة</p>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex justify-between gap-3"><dt class="text-gray-500">آخر زيارة</dt><dd>{{ $date($lastVisit?->scheduled_at) }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-gray-500">الوثائق</dt><dd>{{ $record->documents->count() }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-gray-500">تاريخ التسجيل</dt><dd>{{ $date($record->registered_at) }}</dd></div>
                        </dl>
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                    <p class="text-sm font-semibold text-gray-950 dark:text-white">ملخص الباحث الاجتماعي</p>
                    <p class="mt-2 whitespace-pre-line text-sm leading-7 text-gray-700 dark:text-gray-300">{{ $value($record->notes, 'لا توجد ملاحظات مسجلة على الملف.') }}</p>
                </div>
            </div>

            <div x-show="activeTab === 'identity'" class="space-y-5">
                <h3 class="text-lg font-bold text-gray-950 dark:text-white">البيانات الأساسية والسكن</h3>
                <div class="grid gap-4 lg:grid-cols-2">
                    @foreach ([
                        'رقم الملف' => $record->file_number,
                        'الاسم' => $record->full_name,
                        'رقم الهوية' => $record->national_id,
                        'الجنسية' => $record->nationality,
                        'الجنس' => Beneficiary::genderLabelFor($record->gender),
                        'تاريخ الميلاد' => $date($record->birth_date),
                        'الجوال' => $record->phone,
                        'المدينة' => $record->city,
                        'الحي' => $record->district,
                        'العنوان' => $record->address,
                    ] as $label => $item)
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
                            <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $value($item) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div x-show="activeTab === 'members'" class="space-y-5">
                <h3 class="text-lg font-bold text-gray-950 dark:text-white">المستفيد والتابعون داخل الملف</h3>
                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-800">
                    <table class="w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-950/40">
                            <tr class="text-right">
                                <th class="px-4 py-3 font-semibold">الاسم</th>
                                <th class="px-4 py-3 font-semibold">الهوية</th>
                                <th class="px-4 py-3 font-semibold">الصلة</th>
                                <th class="px-4 py-3 font-semibold">تواصل أساسي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <tr>
                                <td class="px-4 py-3">{{ $value($record->full_name) }}</td>
                                <td class="px-4 py-3">{{ $value($record->national_id) }}</td>
                                <td class="px-4 py-3">صاحب الملف</td>
                                <td class="px-4 py-3">{{ $record->is_primary_contact ? 'نعم' : '-' }}</td>
                            </tr>
                            @forelse ($record->dependents as $dependent)
                                <tr>
                                    <td class="px-4 py-3">{{ $value($dependent->full_name) }}</td>
                                    <td class="px-4 py-3">{{ $value($dependent->national_id) }}</td>
                                    <td class="px-4 py-3">{{ $value($dependent->relationship_to_guardian) }}</td>
                                    <td class="px-4 py-3">{{ $dependent->is_primary_contact ? 'نعم' : '-' }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="activeTab === 'financial'" class="space-y-5">
                <h3 class="text-lg font-bold text-gray-950 dark:text-white">الوضع المالي والتصنيف</h3>
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800"><p class="text-xs text-gray-500">إجمالي الدخل</p><p class="mt-2 text-xl font-bold">{{ $money($record->total_income) }}</p></div>
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800"><p class="text-xs text-gray-500">إجمالي المصروفات</p><p class="mt-2 text-xl font-bold">{{ $money($record->total_expenses) }}</p></div>
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800"><p class="text-xs text-gray-500">التصنيف</p><p class="mt-2 text-xl font-bold">{{ Beneficiary::classificationLabelFor($record->classification) }}</p></div>
                </div>
                <div class="grid gap-4 lg:grid-cols-2">
                    @foreach ([
                        'راتب/عمل' => $record->salary_income,
                        'الضمان' => $record->social_security_income,
                        'حساب المواطن' => $record->citizen_account_income,
                        'التقاعد' => $record->retirement_income,
                        'دخل آخر' => $record->other_income,
                        'الإيجار' => $record->rent_expense,
                        'الكهرباء' => $record->electricity_expense,
                        'المياه' => $record->water_expense,
                        'القروض/الديون' => $record->loans_expense,
                        'العلاج' => $record->treatment_expense,
                    ] as $label => $item)
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 text-sm dark:border-gray-800">
                            <span class="font-medium text-gray-600 dark:text-gray-300">{{ $label }}</span>
                            <span class="font-semibold text-gray-950 dark:text-white">{{ $money($item) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div x-show="activeTab === 'visits'" class="space-y-5">
                <h3 class="text-lg font-bold text-gray-950 dark:text-white">البحث الميداني والزيارات</h3>
                @forelse ($record->fieldVisits->sortByDesc('scheduled_at') as $visit)
                    <article class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <p class="font-semibold text-gray-950 dark:text-white">{{ FieldVisit::typeLabelFor($visit->type) }} - {{ FieldVisit::statusLabelFor($visit->status) }}</p>
                            <p class="text-sm text-gray-500">{{ $date($visit->scheduled_at) }}</p>
                        </div>
                        <p class="mt-3 text-sm leading-7 text-gray-700 dark:text-gray-300">{{ $value($visit->findings, 'لا توجد نتائج موثقة.') }}</p>
                    </article>
                @empty
                    <p class="rounded-lg border border-dashed border-gray-300 p-5 text-sm text-gray-500 dark:border-gray-700">لا توجد زيارات ميدانية مرتبطة بهذا الملف.</p>
                @endforelse
            </div>

            <div x-show="activeTab === 'requests'" class="space-y-5">
                <h3 class="text-lg font-bold text-gray-950 dark:text-white">طلبات الخدمة</h3>
                @forelse ($record->assistanceRequests->sortByDesc('submitted_at') as $request)
                    <article class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <p class="font-semibold text-gray-950 dark:text-white">{{ $value($request->request_number, 'طلب بدون رقم') }} - {{ AssistanceRequest::TYPE_OPTIONS[$request->request_type] ?? $request->request_type }}</p>
                            <p class="text-sm text-gray-500">{{ AssistanceRequest::statusLabelFor($request->status) }}</p>
                        </div>
                        <p class="mt-3 text-sm leading-7 text-gray-700 dark:text-gray-300">{{ $value($request->description, 'لا يوجد وصف للطلب.') }}</p>
                    </article>
                @empty
                    <p class="rounded-lg border border-dashed border-gray-300 p-5 text-sm text-gray-500 dark:border-gray-700">لا توجد طلبات خدمة مرتبطة بهذا الملف.</p>
                @endforelse
            </div>

            <div x-show="activeTab === 'documents'" class="space-y-5">
                <h3 class="text-lg font-bold text-gray-950 dark:text-white">وثائق المستفيد</h3>
                <div class="grid gap-4 lg:grid-cols-2">
                    @forelse ($record->documents->sortByDesc('created_at') as $document)
                        <article class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-950 dark:text-white">{{ $value($document->title) }}</p>
                                    <p class="mt-1 text-sm text-gray-500">{{ BeneficiaryDocument::DOCUMENT_TYPE_OPTIONS[$document->document_type] ?? $document->document_type }}</p>
                                </div>
                                <span class="rounded-md bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                    {{ BeneficiaryDocument::verificationStatusLabelFor($document->verification_status) }}
                                </span>
                            </div>
                            <p class="mt-3 text-sm text-gray-500">تنتهي في: {{ $date($document->expires_on) }}</p>
                        </article>
                    @empty
                        <p class="rounded-lg border border-dashed border-gray-300 p-5 text-sm text-gray-500 dark:border-gray-700">لا توجد وثائق مرتبطة بهذا الملف.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
