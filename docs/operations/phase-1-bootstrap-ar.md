# تجهيز Phase 1: Laravel Foundation

## الهدف

الانتقال من مرحلة الوثائق والتصميم إلى تطبيق Laravel فعلي داخل المستودع، ثم بناء أساس إداري ثابت يمكن أن تبنى عليه بقية الموديولات.

## الحالة الحالية

تم تنفيذ bootstrap الأولي بنجاح:

- Laravel Framework 12.58.0.
- Filament v5.6.2.
- Spatie Laravel Permission v7.4.1.
- لوحة الإدارة على `/admin`.
- إعدادات الجمعية.
- سجل التدقيق.
- Seeder للمستخدم الإداري.
- اختبارات أساسية.

## أوامر التشغيل والتحقق

```bash
composer install
npm install
php artisan migrate:fresh --seed
npm run build
php artisan test
```

## بيانات الدخول التجريبية

```text
Email: admin@larerp.local
Password: password
```

## Checklist القبول

- Laravel يعمل داخل المستودع.
- Filament مثبت ويعرض لوحة الإدارة.
- Spatie Permission مثبت ومربوط بالمستخدمين.
- يوجد دور `Super Admin` ودور `Admin`.
- لا يدخل لوحة الإدارة إلا مستخدم مصرح له.
- يوجد سجل إعدادات جمعية أولي.
- يوجد أساس لسجل التدقيق.
- `php artisan migrate:fresh --seed` يعمل.
- `php artisan test` يعمل.
- `npm run build` يعمل.

## المتبقي في Phase 1

- توسيع إدارة المستخدمين والأدوار والصلاحيات داخل Filament.
- تحسين سجل التدقيق وربطه بالأحداث الحساسة.
- اعتماد PostgreSQL محليا أو عبر Docker عند الانتقال لتجربة نشر أقرب للإنتاج.
- إضافة أول موديول عملي بعد Foundation: المستفيدون والملفات الاجتماعية.
