# خطة توزيع وكلاء تنفيذ قسم المستفيدين

هذه الخطة تلزم كل وكيل بالعمل داخل نطاق واضح. المرجع الأساسي لكل وكيل هو:

`docs/beneficiaries-domain-model-ar.md`

## قواعد عامة

- لا يعمل أي وكيل خارج نطاق الملفات المسندة له.
- لا يلمس وكيل تعديلات وكيل آخر.
- لا يتم قبول أي عمل بلا اختبار نطاقي، ثم اختبار كامل من القائد.
- لا تضاف حزم جديدة.
- لا يتم تعديل `DatabaseSeeder` إلا بإذن من القائد.
- أي قرار تصميمي خارج الوثيقة يجب ذكره في التقرير النهائي.

## التوزيع

### الوكيل 1: طلبات الخدمة والوثائق

النطاق:

- `app/Models/AssistanceRequest.php`
- `app/Models/BeneficiaryDocument.php`
- `app/Filament/Resources/AssistanceRequests/**`
- `app/Filament/Resources/BeneficiaryDocuments/**`
- `database/migrations/*assistance_requests*`
- `database/migrations/*beneficiary_documents*`
- `database/factories/AssistanceRequestFactory.php`
- `database/factories/BeneficiaryDocumentFactory.php`
- `tests/Feature/Beneficiaries/AssistanceRequestDocumentTest.php`

المطلوب:

- إنشاء طلب الخدمة كمدخل رسمي.
- إنشاء وثائق المستفيد المصنفة.
- حالات واضحة للطلب والوثيقة.
- فلاتر وbadges في Filament.
- منع حذف وثيقة مستخدمة في قرار لاحقا بحقل `used_in_decision_at` أو ما يعادله.

### الوكيل 2: الدراسة والتقييم والزيارات

النطاق:

- `app/Models/CaseStudy.php`
- `app/Models/NeedsAssessment.php`
- `app/Models/FieldVisit.php`
- `app/Filament/Resources/CaseStudies/**`
- `app/Filament/Resources/NeedsAssessments/**`
- `app/Filament/Resources/FieldVisits/**`
- `database/migrations/*case_studies*`
- `database/migrations/*needs_assessments*`
- `database/migrations/*field_visits*`
- `database/factories/CaseStudyFactory.php`
- `database/factories/NeedsAssessmentFactory.php`
- `database/factories/FieldVisitFactory.php`
- `tests/Feature/Beneficiaries/CaseStudyAssessmentVisitTest.php`

المطلوب:

- دراسة الباحث وتوصيته.
- تقييم احتياج 100 نقطة بالأوزان المعتمدة.
- زيارات ميدانية أو هاتفية بحالات واضحة.
- منع إرسال دراسة للجنة دون تقييم أو توصية.

### الوكيل 3: القرار والخطة والتنفيذ والمتابعة

النطاق:

- `app/Models/CommitteeDecision.php`
- `app/Models/SupportPlan.php`
- `app/Models/ServiceDelivery.php`
- `app/Models/FollowUp.php`
- `app/Filament/Resources/CommitteeDecisions/**`
- `app/Filament/Resources/SupportPlans/**`
- `app/Filament/Resources/ServiceDeliveries/**`
- `app/Filament/Resources/FollowUps/**`
- `database/migrations/*committee_decisions*`
- `database/migrations/*support_plans*`
- `database/migrations/*service_deliveries*`
- `database/migrations/*follow_ups*`
- `tests/Feature/Beneficiaries/DecisionSupportDeliveryFollowUpTest.php`

المطلوب:

- قرار لجنة مستقل لا يكون مجرد حقل في الحالة.
- خطة دعم إغاثية/تنموية.
- تنفيذ خدمة أو صرف بحالات واضحة.
- متابعة بنتيجة وقرار متابعة.
- لا ربط مالي أو مخزني عميق الآن؛ فقط مراجع اختيارية قابلة للربط لاحقا.

### الوكيل 4: لوحة التشغيل وملف 360

النطاق:

- `app/Filament/Pages/**Beneficiaries**`
- `app/Filament/Widgets/**Beneficiaries**`
- تحسين محدود في موارد `Families`, `Beneficiaries`, `SocialCases` بشرط عدم كسر عمل الوكلاء الآخرين.
- `tests/Feature/Beneficiaries/BeneficiaryOperationsDashboardTest.php`

المطلوب:

- لوحة تشغيل لقسم المستفيدين.
- مؤشرات الطلبات والحالات والزيارات والقرارات والمتابعات.
- روابط سريعة إلى الموارد الجديدة.
- عدم بناء واجهة ضخمة؛ المطلوب MVP عملي.

## بوابة القبول النهائية

بعد عودة الوكلاء، القائد فقط يشغل:

```powershell
$env:Path='C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64;' + $env:Path
& 'C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe' vendor\bin\pint --test --ansi
& 'C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe' artisan migrate:fresh --seed --ansi
& 'C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe' artisan test --ansi
npm run build
```
