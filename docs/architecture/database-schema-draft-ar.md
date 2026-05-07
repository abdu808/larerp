# مسودة تصميم قاعدة البيانات

## السياق

هذه مسودة أولية لتصميم قاعدة بيانات PostgreSQL لمنصة ERP جمعيات مبنية على Laravel/Filament وفق نموذج Productized Single-Tenant. المقصود بالنموذج هنا أن كل جمعية تعمل على نسخة مستقلة من التطبيق وقاعدة البيانات، بينما يبقى المنتج موحدًا في الكود والبنية والموديولات.

المسودة لا تتضمن كود Laravel أو migrations فعلية، بل تحدد قواعد التصميم، الجداول المرشحة، العلاقات، وأنماط التسمية والفهارس والتدقيق.

## قواعد عامة

- قاعدة البيانات تستهدف PostgreSQL فقط، مع الاستفادة من `uuid`, `jsonb`, `timestamptz`, partial indexes, وقيود `check` عند الحاجة.
- كل جمعية تمتلك قاعدة بيانات مستقلة، لذلك لا يوجد `tenant_id` إلزامي في كل جدول.
- يمكن الاحتفاظ بجدول `organization_profile` لبيانات الجمعية نفسها، لكنه ليس مفتاح عزل متعدد المستأجرين.
- التصميم يجب أن يدعم تفعيل/تعطيل الموديولات تدريجيًا دون كسر البيانات الأساسية.
- الاعتماد على العلاقات الصريحة والقيود المرجعية بقدر الإمكان، مع استخدام `jsonb` فقط للبيانات المتغيرة أو إعدادات النماذج.
- الحذف الافتراضي للسجلات التشغيلية يكون soft delete عندما تكون السجلات جزءًا من مسار مالي أو إداري أو تدقيقي.
- لا تُخزن المبالغ المالية كـ float. تستخدم `numeric(14,2)` أو دقة أعلى عند الحاجة.
- كل التواريخ المهمة تخزن كـ `timestamptz`، ويمكن إضافة حقول تاريخ محلية للتقارير عند الحاجة.
- المستندات والملفات تحفظ كمراجع metadata في قاعدة البيانات، بينما التخزين الفعلي يكون عبر storage driver.
- لا تُحذف السجلات المالية أو سندات التبرع أو القيود المحاسبية حذفًا نهائيًا إلا بسياسة أرشفة واضحة.

## Naming Conventions

- أسماء الجداول بصيغة snake_case وجمعية: `donors`, `donations`, `accounting_entries`.
- أسماء المفاتيح الأساسية: `id` من نوع UUID، إلا إذا استدعى جدول lookup بسيط استخدام integer.
- أسماء المفاتيح الأجنبية: اسم المفرد + `_id` مثل `donor_id`, `campaign_id`, `created_by_id`.
- أسماء جداول الربط: الاسمان بصيغة مفردة مرتبين حسب المجال أو أبجديًا عند عدم وجود مالك واضح، مثل `beneficiary_program`.
- أسماء الأعمدة المنطقية تبدأ بـ `is_` أو `has_`: `is_active`, `has_restrictions`.
- أسماء التواريخ تنتهي بـ `_at` للطابع الزمني و`_date` للتاريخ المجرد.
- أسماء الحالات تستخدم `status` عند وجود حالة رئيسية واحدة، أو اسمًا أكثر تحديدًا مثل `payment_status`, `approval_status`.
- أسماء الفهارس المقترحة: `idx_{table}_{columns}`، والفهارس الفريدة: `uq_{table}_{columns}`.
- أسماء قيود التحقق: `chk_{table}_{rule}`.

## حقول مشتركة

تضاف الحقول التالية إلى معظم الجداول التشغيلية:

- `id`: UUID primary key.
- `created_at`: timestamptz.
- `updated_at`: timestamptz.
- `deleted_at`: timestamptz nullable عند استخدام soft delete.
- `created_by_id`: UUID nullable references `users(id)`.
- `updated_by_id`: UUID nullable references `users(id)`.
- `deleted_by_id`: UUID nullable references `users(id)`.
- `status`: قيمة حالة عندما يكون للسجل lifecycle واضح.
- `metadata`: jsonb nullable للامتدادات المحدودة غير الحرجة.

حقول audit المتقدمة للجداول الحساسة:

- `approved_by_id`, `approved_at`.
- `posted_by_id`, `posted_at` للقيود المالية.
- `cancelled_by_id`, `cancelled_at`, `cancellation_reason`.
- `locked_at`, `locked_by_id` لمنع التعديل بعد الاعتماد أو الترحيل.

## الجداول الأساسية

### الهوية والصلاحيات

#### `users`

يمثل مستخدمي النظام الداخليين.

- `id`
- `name`
- `email`
- `phone`
- `password`
- `locale`
- `timezone`
- `is_active`
- `last_login_at`

علاقات:

- يرتبط بالمستخدمين كمنشئ/محدّث/معتمد في معظم الجداول.
- يرتبط بالأدوار والصلاحيات عبر نظام صلاحيات Laravel/Filament المعتمد.

#### `roles`, `permissions`, `role_user`, `permission_role`

يمكن استخدام بنية متوافقة مع حزمة صلاحيات معروفة، مع مراعاة عدم ربطها بأي tenant.

### إعدادات الجمعية

#### `organization_profile`

يحفظ بيانات الجمعية المالكة للنسخة.

- `id`
- `legal_name`
- `display_name`
- `registration_number`
- `tax_number`
- `license_number`
- `phone`
- `email`
- `website`
- `address`
- `city`
- `country_code`
- `logo_file_id`
- `settings` jsonb

#### `branches`

الفروع أو المكاتب التابعة للجمعية.

- `id`
- `name`
- `code`
- `address`
- `city`
- `phone`
- `is_active`

### الملفات والمرفقات

#### `files`

سجل metadata للملفات.

- `id`
- `disk`
- `path`
- `original_name`
- `mime_type`
- `size_bytes`
- `checksum`
- `uploaded_by_id`
- `uploaded_at`

#### `attachments`

ربط polymorphic بين الملفات والسجلات.

- `id`
- `file_id`
- `attachable_type`
- `attachable_id`
- `collection`
- `caption`
- `sort_order`

ملاحظة: عند استخدام polymorphic relations يجب إضافة فهارس مركبة، لأن القيود المرجعية المباشرة غير متاحة على `attachable_type/attachable_id`.

## موديول المتبرعين والتبرعات

#### `donors`

- `id`
- `donor_type`: فرد، جهة، شركة.
- `name`
- `national_id`
- `commercial_registration`
- `email`
- `phone`
- `gender`
- `birth_date`
- `address`
- `city`
- `preferred_contact_method`
- `is_active`
- `metadata`

علاقات:

- متبرع واحد له عدة تبرعات.
- يمكن ربط المتبرع بتصنيفات أو وسوم.

#### `donor_tags`

- `id`
- `name`
- `color`

#### `donor_donor_tag`

- `donor_id`
- `donor_tag_id`

#### `campaigns`

- `id`
- `name`
- `code`
- `description`
- `start_at`
- `end_at`
- `target_amount`
- `status`
- `restricted_fund_id`

#### `donations`

- `id`
- `donor_id`
- `campaign_id`
- `branch_id`
- `amount`
- `currency_code`
- `donation_date`
- `payment_method`
- `payment_status`
- `receipt_number`
- `receipt_issued_at`
- `restricted_fund_id`
- `notes`

علاقات:

- ترتبط بمتبرع وحملة اختيارية.
- يمكن أن تنشئ سند قبض أو قيدًا محاسبيًا بعد الاعتماد.
- يمكن ربطها بصندوق مقيّد عند اشتراط التبرع.

#### `recurring_donations`

- `id`
- `donor_id`
- `campaign_id`
- `amount`
- `currency_code`
- `frequency`
- `next_run_at`
- `starts_at`
- `ends_at`
- `status`
- `payment_method`

## موديول المستفيدين والبرامج

#### `beneficiaries`

- `id`
- `beneficiary_type`: فرد، أسرة، جهة.
- `name`
- `national_id`
- `phone`
- `email`
- `gender`
- `birth_date`
- `marital_status`
- `address`
- `city`
- `income_level`
- `household_size`
- `status`
- `risk_level`
- `metadata`

#### `beneficiary_cases`

- `id`
- `beneficiary_id`
- `case_number`
- `case_type`
- `opened_at`
- `closed_at`
- `status`
- `assigned_user_id`
- `summary`
- `assessment_score`

#### `programs`

- `id`
- `name`
- `code`
- `description`
- `start_at`
- `end_at`
- `budget_amount`
- `restricted_fund_id`
- `status`

#### `beneficiary_program`

- `beneficiary_id`
- `program_id`
- `enrolled_at`
- `exited_at`
- `status`
- `notes`

#### `aid_requests`

- `id`
- `beneficiary_id`
- `program_id`
- `case_id`
- `request_number`
- `requested_amount`
- `approved_amount`
- `request_type`
- `status`
- `submitted_at`
- `reviewed_at`
- `reviewed_by_id`

#### `aid_disbursements`

- `id`
- `aid_request_id`
- `beneficiary_id`
- `amount`
- `currency_code`
- `disbursement_method`
- `disbursement_date`
- `status`
- `accounting_entry_id`

علاقات:

- الطلب يعتمد على مستفيد وبرنامج وقد يرتبط بحالة اجتماعية.
- الصرف ينتج أثرًا ماليًا وقد يرتبط بقيد محاسبي.

## موديول المحاسبة والمالية

#### `chart_of_accounts`

- `id`
- `parent_id`
- `code`
- `name`
- `account_type`: أصل، التزام، إيراد، مصروف، حقوق.
- `normal_balance`: debit أو credit.
- `is_active`
- `is_postable`

#### `fiscal_years`

- `id`
- `name`
- `starts_on`
- `ends_on`
- `status`
- `closed_at`
- `closed_by_id`

#### `accounting_periods`

- `id`
- `fiscal_year_id`
- `name`
- `starts_on`
- `ends_on`
- `status`

#### `accounting_entries`

- `id`
- `entry_number`
- `entry_date`
- `period_id`
- `source_type`
- `source_id`
- `description`
- `status`: draft, posted, cancelled.
- `posted_at`
- `posted_by_id`

#### `accounting_entry_lines`

- `id`
- `accounting_entry_id`
- `account_id`
- `debit_amount`
- `credit_amount`
- `currency_code`
- `description`
- `cost_center_id`
- `restricted_fund_id`

قيود مهمة:

- كل قيد مرحل يجب أن يكون مجموع debit مساويًا لمجموع credit.
- السطر لا يسمح بأن يحتوي debit و credit معًا.
- الحساب المستخدم في السطر يجب أن يكون `is_postable = true`.

#### `cost_centers`

- `id`
- `parent_id`
- `code`
- `name`
- `is_active`

#### `restricted_funds`

- `id`
- `code`
- `name`
- `description`
- `starts_at`
- `ends_at`
- `status`

#### `bank_accounts`

- `id`
- `bank_name`
- `account_name`
- `iban`
- `account_number`
- `currency_code`
- `is_active`
- `account_id`

#### `payment_transactions`

- `id`
- `transaction_number`
- `transaction_type`: receipt, payment, transfer.
- `amount`
- `currency_code`
- `payment_method`
- `transaction_date`
- `bank_account_id`
- `source_type`
- `source_id`
- `status`
- `reference_number`

## موديول المشتريات والموردين

#### `vendors`

- `id`
- `name`
- `vendor_type`
- `tax_number`
- `commercial_registration`
- `email`
- `phone`
- `address`
- `is_active`

#### `purchase_requests`

- `id`
- `request_number`
- `requested_by_id`
- `department_id`
- `needed_by`
- `status`
- `notes`

#### `purchase_request_lines`

- `id`
- `purchase_request_id`
- `item_description`
- `quantity`
- `estimated_unit_price`
- `cost_center_id`
- `program_id`

#### `purchase_orders`

- `id`
- `po_number`
- `vendor_id`
- `purchase_request_id`
- `order_date`
- `status`
- `total_amount`
- `currency_code`

#### `purchase_order_lines`

- `id`
- `purchase_order_id`
- `item_description`
- `quantity`
- `unit_price`
- `tax_amount`
- `total_amount`

#### `vendor_invoices`

- `id`
- `vendor_id`
- `purchase_order_id`
- `invoice_number`
- `invoice_date`
- `due_date`
- `total_amount`
- `payment_status`
- `accounting_entry_id`

## موديول الموارد البشرية

#### `employees`

- `id`
- `user_id`
- `employee_number`
- `name`
- `national_id`
- `email`
- `phone`
- `department_id`
- `job_title_id`
- `hire_date`
- `termination_date`
- `employment_status`

#### `departments`

- `id`
- `parent_id`
- `name`
- `code`
- `manager_employee_id`
- `is_active`

#### `job_titles`

- `id`
- `name`
- `code`
- `is_active`

#### `employee_contracts`

- `id`
- `employee_id`
- `contract_type`
- `starts_on`
- `ends_on`
- `salary_amount`
- `currency_code`
- `status`

#### `payroll_runs`

- `id`
- `period_id`
- `run_number`
- `status`
- `processed_at`
- `posted_at`
- `accounting_entry_id`

#### `payroll_items`

- `id`
- `payroll_run_id`
- `employee_id`
- `item_type`: earning, deduction, employer_contribution.
- `code`
- `name`
- `amount`
- `account_id`

## موديول التطوع

#### `volunteers`

- `id`
- `user_id`
- `name`
- `national_id`
- `email`
- `phone`
- `skills`
- `availability`
- `status`

#### `volunteer_opportunities`

- `id`
- `program_id`
- `title`
- `description`
- `location`
- `starts_at`
- `ends_at`
- `capacity`
- `status`

#### `volunteer_assignments`

- `id`
- `volunteer_id`
- `opportunity_id`
- `assigned_at`
- `status`

#### `volunteer_hours`

- `id`
- `volunteer_id`
- `opportunity_id`
- `hours`
- `service_date`
- `approved_by_id`
- `approved_at`

## موديول المخزون والأصول

#### `inventory_items`

- `id`
- `sku`
- `name`
- `description`
- `unit_of_measure`
- `category_id`
- `is_active`

#### `inventory_locations`

- `id`
- `name`
- `code`
- `branch_id`
- `is_active`

#### `inventory_movements`

- `id`
- `item_id`
- `location_id`
- `movement_type`: receipt, issue, transfer, adjustment.
- `quantity`
- `movement_date`
- `source_type`
- `source_id`
- `notes`

#### `fixed_assets`

- `id`
- `asset_number`
- `name`
- `category`
- `purchase_date`
- `purchase_cost`
- `currency_code`
- `location_id`
- `custodian_employee_id`
- `status`
- `account_id`

## العلاقات بين الموديولات

- `donations` ترتبط بـ `donors`, `campaigns`, `restricted_funds`, وقد تنتج `payment_transactions` و`accounting_entries`.
- `campaigns` يمكن أن ترتبط بصندوق مقيّد لتوجيه أثر التبرعات.
- `beneficiaries` ترتبط بـ `beneficiary_cases`, `aid_requests`, `programs`.
- `aid_disbursements` ترتبط بطلب مساعدة وتنتج قيدًا محاسبيًا عند الترحيل.
- `programs` ترتبط بالميزانيات والصناديق المقيّدة ومراكز التكلفة.
- `purchase_orders` و`vendor_invoices` تؤثر على الحسابات الدائنة والقيود المحاسبية.
- `payroll_runs` تنتج قيودًا محاسبية حسب حسابات الرواتب والاستقطاعات.
- `volunteer_opportunities` يمكن ربطها بالبرامج لقياس مساهمة المتطوعين في كل برنامج.
- `inventory_movements` يمكن أن ترتبط بالمشتريات أو الصرف العيني عبر `source_type/source_id`.
- `attachments` و`comments` عند إضافتها مستقبلًا يجب أن تدعم الارتباط بعدة كيانات عبر polymorphic relation.

## Enums و Lookups

يفضل استخدام جداول lookup عندما تكون القيم قابلة للإدارة من لوحة التحكم أو تحتاج ترجمة/ترتيب/تعطيل:

- أنواع المستفيدين.
- أنواع المساعدات.
- وسائل الدفع.
- المدن والمناطق.
- تصنيفات البرامج.
- تصنيفات الأصول والمخزون.
- أسباب إغلاق الحالات.

يفضل استخدام PostgreSQL enum أو check constraint للقيم التقنية قليلة التغيير:

- `account_type`
- `normal_balance`
- `entry_status`
- `payment_status`
- `approval_status`
- `gender` إذا كانت السياسة واضحة وثابتة.

قاعدة عملية:

- إذا كانت القيمة تظهر كإعداد قابل للتعديل للمستخدم، استخدم lookup table.
- إذا كانت القيمة جزءًا من منطق التطبيق ولا يتوقع تعديلها من الإدارة، استخدم enum أو check.

## الفهارس

فهارس عامة:

- فهرس على كل foreign key.
- فهرس على `deleted_at` للجداول كثيفة القراءة مع soft delete عند الحاجة.
- فهارس مركبة حسب شاشات Filament وقوائم البحث الشائعة.
- فهارس فريدة جزئية عند الحاجة لمنع التكرار بين السجلات غير المحذوفة فقط.

أمثلة مهمة:

- `uq_donors_national_id` على `donors(national_id)` حيث `national_id is not null and deleted_at is null`.
- `idx_donors_phone` على `donors(phone)`.
- `idx_donations_donor_date` على `donations(donor_id, donation_date desc)`.
- `idx_donations_campaign_status` على `donations(campaign_id, payment_status)`.
- `uq_donations_receipt_number` على `donations(receipt_number)` حيث `receipt_number is not null`.
- `idx_beneficiaries_national_id` على `beneficiaries(national_id)`.
- `idx_aid_requests_status_submitted` على `aid_requests(status, submitted_at desc)`.
- `uq_chart_of_accounts_code` على `chart_of_accounts(code)`.
- `idx_accounting_entries_period_status` على `accounting_entries(period_id, status)`.
- `idx_accounting_entry_lines_account` على `accounting_entry_lines(account_id)`.
- `idx_payment_transactions_source` على `payment_transactions(source_type, source_id)`.
- `idx_attachments_attachable` على `attachments(attachable_type, attachable_id, collection)`.
- GIN index على أعمدة `metadata` أو `settings` فقط عندما توجد استعلامات فعلية عليها.

## قيود البيانات

- المبالغ المالية يجب أن تكون أكبر من أو تساوي صفر، مع السماح بالسالب فقط في سياقات محددة مثل التسويات وبقيود واضحة.
- كل رقم مستند مهم يجب أن يكون فريدًا ضمن نطاقه: سند التبرع، القيد، أمر الشراء، طلب المساعدة.
- يمنع تعديل القيود المحاسبية المرحلة إلا عبر قيد عكسي أو تسوية.
- تواريخ البداية يجب أن تسبق تواريخ النهاية.
- لا يسمح بصرف مساعدة غير معتمدة.
- لا يسمح بترحيل قيد إلى فترة محاسبية مغلقة.
- يمكن استخدام optimistic locking عبر `lock_version` للجداول ذات التحرير المتزامن العالي.

## ملاحظات Migration

- ابدأ بموديولات الهوية والإعدادات والملفات، ثم المتبرعين والمستفيدين، ثم المالية، ثم الموديولات التابعة.
- عرّف lookups الأساسية قبل الجداول التي تعتمد عليها.
- أضف القيود والفهارس بعد إنشاء الجداول والعلاقات لتقليل تعقيد ترتيب migrations.
- استخدم UUIDs بشكل موحد من البداية، وتأكد من تفعيل امتداد PostgreSQL المناسب مثل `pgcrypto` لاستخدام `gen_random_uuid()`.
- عند استخدام soft deletes مع unique constraints، استخدم partial unique indexes بدل unique عادي.
- عند إضافة enum جديد، خطط لطريقة rollback أو استخدم check constraints إذا كان التغيير المتوقع متكررًا.
- لا تعتمد على `cascade delete` في الجداول المالية أو سجلات audit. استخدم `restrict` أو `set null` حسب المعنى.
- عند ترحيل بيانات legacy، ضع mapping واضحًا للحالات والأنواع قبل تحميل البيانات.
- افصل migrations الخاصة بالبنية عن seeders الخاصة بالقيم الافتراضية.
- حافظ على compatibility مع Filament من خلال أسماء علاقات واضحة وفهارس تدعم البحث والفرز في الجداول الإدارية.

## أسئلة مفتوحة قبل الاعتماد

- هل ستحتاج النسخة الواحدة إلى أكثر من كيان قانوني داخل الجمعية؟
- هل المطلوب محاسبة كاملة مزدوجة من اليوم الأول أم تكامل تدريجي مع نظام مالي خارجي؟
- هل سندات التبرع يجب أن تتبع متطلبات فوترة أو ضريبة محددة؟
- هل يوجد تكامل دفع إلكتروني يتطلب تخزين حالات webhook ومحاولات الدفع؟
- هل بيانات المستفيدين تتطلب تصنيف حساسية أو تشفير أعمدة محددة؟
- ما سياسة الاحتفاظ بالبيانات والأرشفة بعد إغلاق الحالات أو انتهاء البرامج؟

## الملفات التي تم تغييرها

- `docs/architecture/database-schema-draft-ar.md`
