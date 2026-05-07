# LarERP Charity Platform

منصة ERP للجمعيات الخيرية والتخصصية مبنية على Laravel و PostgreSQL و Filament.

## القرار المعماري

- المنتج يبنى على Laravel 12 كأساس نظيف قابل للتوسع.
- قاعدة البيانات المستهدفة PostgreSQL.
- لوحة الإدارة مبنية على Filament.
- الصلاحيات مبنية على Spatie Laravel Permission.
- النموذج التشغيلي Productized Single-Tenant: لكل جمعية نسخة مستقلة بقاعدة بيانات وتخزين وإعدادات مستقلة.
- الكود الأساسي موحد في هذا المستودع، ثم يتم نشره لكل جمعية كحاوية/بيئة مستقلة.
- المشروع السابق يستخدم كمرجع وظيفي وتحليلي، وليس كمصدر ترحيل مباشر.

## حالة المستودع

تم إنشاء تطبيق Laravel داخل المستودع وبدأت مرحلة Foundation.

المتوفر حاليا:

- Laravel 12.
- Filament admin panel.
- Spatie permissions and roles.
- نموذج إعدادات الجمعية.
- سجل تدقيق أولي.
- Seeder لمستخدم إداري تجريبي.
- اختبارات دخول لوحة الإدارة.

بيانات الدخول المحلية بعد تشغيل seed:

- البريد: `admin@larerp.local`
- كلمة المرور: `password`

## التشغيل المحلي

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

لوحة الإدارة:

```text
/admin
```

## الوثائق الأساسية

- [فهرس الوثائق](docs/INDEX-ar.md)
- [الوثيقة المرجعية](docs/charity-erp-platform-blueprint-ar.md)
- [مصفوفة تنفيذ الموديولات](docs/module-implementation-matrix-ar.md)
- [خطة الطريق](docs/ROADMAP-ar.md)
- [تنظيم عمل الوكلاء](docs/operations/agent-orchestration-ar.md)

## نموذج العمل

النموذج المعتمد:

**قائد تطوير مركزي + وكلاء متخصصون لكل موديول داخل نفس المشروع**

لا يعمل الوكلاء كمشاريع منفصلة. كل وكيل يستلم نطاقا واضحا، والدمج النهائي يتم مركزيا بعد المراجعة والاختبار.

## مراحل العمل

1. Product Blueprint and Architecture.
2. Laravel Foundation.
3. Internal ERP MVP.
4. Donations and Public Portal.
5. Finance, Inventory and Governance.
6. Productization and Deployment.
7. AI and Advanced Analytics.
