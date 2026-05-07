# متطلبات بيئة التطوير والتشغيل

## البيئة المطلوبة

### تشغيل محلي

- PHP 8.3 أو أحدث.
- Composer.
- Node.js 22 أو أحدث.
- npm.
- PostgreSQL 16 أو أحدث للبيئة المستهدفة.
- Redis اختياري للـ queues/cache.

امتدادات PHP المطلوبة:

- `zip`
- `pdo_pgsql`
- `pgsql`
- `mbstring`
- `openssl`
- `fileinfo`
- `pdo`
- `tokenizer`
- `xml`
- `ctype`
- `json`
- `bcmath`
- `curl`

### تشغيل بالحاويات

- Docker.
- Docker Compose.

هذا الخيار هو الأنسب لاحقا عند تجهيز نسخة مستقلة لكل جمعية، لأنه يجعل نشر قواعد البيانات والتخزين والخدمات المساندة أكثر انتظاما.

## البيئة المستخدمة حاليا

تم الاعتماد على Laragon في جهاز التطوير الحالي:

- PHP: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`
- Composer PHAR: `C:\laragon\bin\composer\composer.phar`
- Apache و Mailpit متوفران من Laragon.
- MySQL متوفر لكنه ليس قاعدة البيانات المستهدفة.

تم تفعيل الامتدادات التالية في `php.ini`:

- `zip`
- `pdo_pgsql`
- `pgsql`

Docker ليس متطلبا لإكمال مرحلة Foundation الحالية، لكنه سيعود كجزء مهم في مرحلة Productization and Deployment.

## قاعدة البيانات

الهدف النهائي هو PostgreSQL. أثناء الاختبارات الآلية نستخدم SQLite داخل الذاكرة لسرعة الاختبارات وعدم ربطها ببيئة خارجية.

إعداد PostgreSQL المقترح في `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=larerp
DB_USERNAME=postgres
DB_PASSWORD=
```
