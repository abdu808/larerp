# LarERP Charity Platform

منصة ERP للجمعيات الخيرية والتخصصية مبنية على Laravel و PostgreSQL و Filament.

## القرار المعماري

- المنتج يبنى من الصفر بأساس Laravel نظيف.
- قاعدة البيانات PostgreSQL.
- لوحة الإدارة Filament.
- النموذج التشغيلي Productized Single-Tenant: لكل جمعية نسخة وقاعدة بيانات وتخزين وإعدادات مستقلة.
- الكود الأساسي موحد في هذا المستودع.
- المشروع الحالي React/Supabase يستخدم كمرجع وظيفي فقط، وليس كمصدر ترحيل مباشر.

## حالة المستودع

هذا المستودع بدأ كحاوية المشروع الجديد. البيئة الحالية لا تحتوي PHP أو Composer أو Docker، لذلك تم تجهيز الوثائق والهيكل أولا. عند توفر بيئة PHP/Composer، يتم إنشاء مشروع Laravel داخل هذا المستودع وفق الوثائق.

## الوثائق الأساسية

- [الوثيقة المرجعية](docs/charity-erp-platform-blueprint-ar.md)
- [مصفوفة تنفيذ الموديولات](docs/module-implementation-matrix-ar.md)

## نموذج العمل

النموذج المعتمد:

**قائد تطوير مركزي + وكلاء متخصصون لكل موديول داخل نفس المشروع**

لا يعمل الوكلاء كمشاريع منفصلة. كل وكيل يستلم نطاقا واضحا، والدمج النهائي يتم مركزيا بعد المراجعة.

## مراحل العمل

1. Product Blueprint and Architecture.
2. Laravel Foundation.
3. Internal ERP MVP.
4. Donations and Public Portal.
5. Finance, Inventory and Governance.
6. Productization and Deployment.
7. AI and Advanced Analytics.

