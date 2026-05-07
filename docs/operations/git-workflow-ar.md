# استراتيجية Git والفروع

## الفروع الأساسية

### `main`

فرع مستقر فقط.  
لا يتم الدفع إليه مباشرة أثناء التطوير اليومي.

### `develop`

فرع التطوير الرئيسي.  
كل الميزات تدمج إليه بعد المراجعة.

## فروع الميزات

كل موديول أو مرحلة تعمل على فرع مستقل:

- `feature/foundation`
- `feature/social-care`
- `feature/donations-store`
- `feature/finance-inventory`
- `feature/governance-archive`
- `feature/reports-devops`

## قواعد الدمج

- لا يتم الدمج بدون مراجعة القائد المركزي.
- لا تدمج ميزة لا تحتوي على توثيق أو اختبارات مناسبة عند وجود كود.
- لا تدمج migration تكسر موديولا آخر بدون مراجعة تصميم قاعدة البيانات.
- لا يتم تعديل ملفات مشتركة مثل إعدادات Foundation إلا بتنسيق مسبق.

## الرسائل المقترحة للـ commits

استخدم رسائل واضحة:

- `Initialize LarERP product blueprint`
- `Add foundation workstream plan`
- `Add social care module design`
- `Implement beneficiaries migrations`
- `Add donation checkout flow`

## الإصدارات

عند الوصول إلى نسخة قابلة للتجربة:

- ينشأ tag مثل `v0.1.0-alpha`.
- يكتب تقرير إنجاز.
- توثق ملاحظات الترقية.

