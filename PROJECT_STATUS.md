# حالة مشروع LarERP

تاريخ البداية: 2026-05-07  
الحالة الحالية: مرحلة Laravel Foundation بدأت فعليا

## القرار المعتمد

- بناء مشروع Laravel جديد نظيف.
- PostgreSQL قاعدة البيانات المستهدفة.
- Filament للوحة الإدارة.
- Spatie Permission للصلاحيات.
- Productized Single-Tenant لكل جمعية.
- مستودع GitHub واحد للكود الأساسي.
- قائد تطوير مركزي مع وكلاء متخصصين لكل موديول.

## ما تم إنجازه

- تجهيز المستودع `abdu808/larerp`.
- إضافة الوثائق المرجعية وخطط العمل.
- إنشاء تطبيق Laravel 12 داخل المستودع.
- تثبيت Filament و Spatie Permission.
- إنشاء لوحة إدارة أولية على المسار `/admin`.
- إنشاء نموذج `OrganizationProfile` لإعدادات الجمعية.
- إنشاء نموذج `AuditLog` لسجل التدقيق.
- إنشاء موارد Filament لإعدادات الجمعية وسجل التدقيق.
- إنشاء أدوار وصلاحيات أولية.
- إنشاء مستخدم إداري تجريبي.
- تشغيل `migrate:fresh --seed` بنجاح.
- تشغيل `npm run build` بنجاح.
- تشغيل `php artisan test` بنجاح.
- إضافة اختبار يثبت أن Super Admin يستطيع دخول لوحة الإدارة وأن المستخدم العادي يمنع.

## البيئة المحلية

تم استخدام Laragon الموجود على الجهاز:

- PHP: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`
- Composer PHAR: `C:\laragon\bin\composer\composer.phar`
- PHP extensions المفعلة: `zip`, `pdo_pgsql`, `pgsql`

Docker ليس ضروريا لهذه المرحلة، وسيتم الرجوع له لاحقا عند تجهيز نموذج الحاويات لكل جمعية.

## نتيجة التحقق

- `php artisan migrate:fresh --seed`: ناجح.
- `php artisan test`: ناجح، 4 اختبارات.
- `npm run build`: ناجح.
- `php artisan route:list --path=admin`: ناجح.

## المرحلة التالية

1. إغلاق فرع `feature/foundation` ورفعه.
2. بناء موديول المستخدمين والصلاحيات بشكل أوسع داخل Filament.
3. البدء في موديول المستفيدين والملفات الاجتماعية.
4. تثبيت نمط audit logging على العمليات الحساسة.
5. تجهيز إعداد PostgreSQL محلي أو Docker لاحقا عند الحاجة للنشر التجريبي.
