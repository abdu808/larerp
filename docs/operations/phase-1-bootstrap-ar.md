# تجهيز Phase 1: إنشاء Laravel Foundation

## الهدف

الانتقال من مرحلة الوثائق والتصميم إلى إنشاء مشروع Laravel فعلي داخل المستودع.

## المتطلب السابق

يجب توفر أحد الخيارين:

### خيار محلي

- PHP 8.3+
- Composer
- Node.js
- PostgreSQL

### خيار Docker

- Docker
- Docker Compose

## أمر إنشاء Laravel عند توفر Composer

يجب تنفيذ الأمر من داخل جذر المستودع:

```bash
composer create-project laravel/laravel:^12.0 .
```

إذا لم يكن Laravel 12 متاحا أو لم يكن مستقرا عند التنفيذ، يستخدم أحدث إصدار Laravel مستقر.

## حزم Foundation المقترحة

بعد إنشاء Laravel:

```bash
composer require filament/filament
composer require spatie/laravel-permission
```

ثم:

```bash
php artisan filament:install --panels
php artisan migrate
```

## إعداد PostgreSQL

في ملف `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=larerp
DB_USERNAME=postgres
DB_PASSWORD=
```

## Checklist قبل بدء الكود

- التأكد من أن الفرع الحالي `develop`.
- إنشاء فرع `feature/foundation`.
- إنشاء Laravel داخل المستودع.
- ضبط `.env.example` بدون أسرار.
- تشغيل صفحة Laravel الافتراضية.
- تثبيت Filament.
- تثبيت Spatie Permission.
- إنشاء أول migration خاص بإعدادات الجمعية.
- إنشاء أول Admin user seeder.
- توثيق أي قرار جديد في docs.

## معيار قبول Phase 1 الأولي

- `php artisan test` يعمل.
- `php artisan migrate:fresh --seed` يعمل.
- لوحة Filament تعمل.
- يوجد مستخدم إداري تجريبي.
- PostgreSQL متصل.

