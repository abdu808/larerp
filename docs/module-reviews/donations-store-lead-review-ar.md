# مراجعة قائد موديول المشاريع والتبرعات والمتجر

تاريخ المراجعة: 2026-05-07  
النطاق: Projects, Campaigns, Donations داخل مشروع LarERP.  
نوع المراجعة: مراجعة تخصصية للجاهزية المعمارية والتشغيلية دون تعديل أي كود تطبيقي.

## الملفات التي تمت مراجعتها

- Models:
  - `app/Models/Project.php`
  - `app/Models/Campaign.php`
  - `app/Models/Donation.php`
- Migration:
  - `database/migrations/2026_05_07_070000_create_project_campaign_donation_tables.php`
- Factories:
  - `database/factories/ProjectFactory.php`
  - `database/factories/CampaignFactory.php`
  - `database/factories/DonationFactory.php`
- Filament Resources:
  - `app/Filament/Resources/Projects/*`
  - `app/Filament/Resources/Campaigns/*`
  - `app/Filament/Resources/Donations/*`
- Tests:
  - `tests/Feature/Projects/ProjectDonationRecordsTest.php`
- وثيقة سياقية:
  - `docs/workstreams/donations-store-workstream-ar.md`

## الملخص التنفيذي

الموديول الحالي يمثل نواة بيانات أولية جيدة للمشاريع والحملات والتبرعات: توجد الجداول الأساسية، علاقات Eloquent المباشرة، Factories قابلة للاستخدام، وموارد Filament لإدارة CRUD. لكنه لا يزال في مستوى "سجل إداري أولي" وليس "منظومة تبرعات ومدفوعات ومتجر جاهزة للإنتاج".

أهم نقاط القوة هي وضوح الكيانات الأساسية، ربط الحملة بالمشروع، وربط التبرع بالمشروع والحملة، مع فهارس أولية على الحالة والقناة وطريقة الدفع. أهم المخاطر هي أن الحقول المالية وحالات الدفع قابلة للتعديل اليدوي دون ضوابط، وأن `collected_amount` مخزن دون آلية موثوقة للتحديث، وأن التبرع قد يرتبط بحملة لا تتبع نفس المشروع عند الإدخال اليدوي، وأن دورة الدفع لا تستند إلى سجل معاملات مستقل قابل للتدقيق.

الخلاصة: مناسب كمرحلة تأسيسية داخلية، غير كاف قبل استقبال تبرعات حقيقية أو بناء متجر تبرعات عام دون طبقة مدفوعات، إيصالات، صلاحيات، قيود حالة، وتدقيق مالي.

## تقييم النموذج الحالي

### Project

النموذج يحتوي على بيانات أساسية: العنوان، الكود، الوصف، التصنيف، الحالة، الهدف المالي، المحصل، تواريخ البداية والنهاية، التمييز، والترتيب. العلاقات الموجودة:

- `Project hasMany Campaign`
- `Project hasMany Donation`

التقييم:

- العلاقة مع الحملات سليمة كبداية.
- العلاقة المباشرة مع التبرعات مفيدة لدعم التبرع للمشروع دون حملة.
- لا توجد حقول نشر عامة مثل `slug`, `visibility`, `cover_image`, `summary`, أو بيانات SEO، وهذا يؤخر جاهزية العرض العام والمتجر.
- `collected_amount` موجود كقيمة مخزنة لكنه غير مشتق أو محمي، ولا توجد آلية تضمن مطابقته للتبرعات المدفوعة فقط.
- لا يوجد `softDeletes`، وهذا حساس ماليًا وتشغيليًا عند وجود حملات أو تبرعات مرتبطة.
- الحالة نصية مفتوحة نسبيًا ولا توجد Enum أو قواعد انتقال بين `draft`, `active`, `paused`, `completed`.

### Campaign

الحملة مرتبطة إلزاميًا بمشروع، وتحتوي على عنوان، كود، وصف، حالة، هدف، محصل، فترة زمنية، قناة، تمييز، وترتيب. العلاقات الموجودة:

- `Campaign belongsTo Project`
- `Campaign hasMany Donation`

التقييم:

- إلزامية `project_id` مناسبة إذا كان قرار المنتج أن كل حملة تتبع مشروعًا. لكنها أقل مرونة من سيناريو "حملة مستقلة" أو حملة عامة.
- لا توجد دورة اعتماد أو نشر منفصلة. الحالة الحالية تخلط بين التشغيل الداخلي والظهور العام.
- لا توجد حقول مهمة للحملة العامة مثل `slug`, `minimum_donation_amount`, `suggested_amounts`, `allow_anonymous_donations`, `zakat_eligible`, `sadaqah_eligible`, `published_at`.
- `channel` جيد كبداية تسويقية، لكنه لا يكفي لتتبع مصدر التبرع أو رابط حملة محدد.
- حذف المشروع يؤدي إلى حذف الحملات `cascadeOnDelete`، وهذا خطر إذا أصبحت الحملة مرتبطة بتبرعات أو إيصالات لاحقًا.

### Donation

التبرع يحتوي على ارتباط اختياري بالمشروع والحملة، بيانات متبرع نصية، مبلغ، عملة، حالة دفع، طريقة دفع، مرجع، تاريخ تبرع، وملاحظات. العلاقات الموجودة:

- `Donation belongsTo Project`
- `Donation belongsTo Campaign`

التقييم:

- الحقول كافية لتسجيل تبرع يدوي بسيط.
- العلاقة الاختيارية بكل من المشروع والحملة تسمح بسجلات عامة، لكنها تحتاج قيدًا يمنع السجل غير المصنف إلا إذا أضيف نوع تبرع عام صريح.
- لا يوجد ضمان أن `donations.project_id` يساوي مشروع `campaign_id`. الـ Factory `forCampaign` يضبط ذلك، لكن النموذج وFilament والهجرة لا يفرضونه.
- `payment_status` في التبرع يحاول تمثيل حالة الدفع مباشرة، وهذا غير كاف للإنتاج. يلزم فصل التبرع عن محاولة الدفع عبر `payment_transactions`.
- لا يوجد `donor_id` أو كيان Donor، وبالتالي تتكرر بيانات المتبرع ولا يمكن بناء تاريخ متبرع أو تفضيلات أو موافقات تسويقية.
- لا يوجد دعم للتبرع المجهول، الزكاة/الصدقة، التكرار، الإهداء، الإيصال، الاسترداد، رسوم بوابة الدفع، أو صافي المبلغ.
- `reference` فريد ومفيد، لكنه لا يميز بين مرجع داخلي ومرجع مزود الدفع.

## تقييم حالات الدفع

الحالات الحالية: `pending`, `paid`, `failed`, `refunded`.

هذه الحالات مناسبة للواجهة الأولية، لكنها غير كافية كنموذج دفع حقيقي. النواقص:

- لا توجد حالات `authorized`, `captured`, `cancelled`, `expired`, `partially_refunded`, `reconciled`.
- لا توجد قواعد انتقال؛ يمكن إداريًا تحويل أي تبرع إلى `paid` أو `refunded` من نموذج Filament.
- لا يوجد سجل لمحاولات الدفع الفاشلة أو المتكررة.
- لا توجد Idempotency لمعالجة Webhooks.
- لا يوجد ربط بإيصال أو قيد مالي أو مطابقة بنكية.
- لا يوجد تمييز بين الدفع اليدوي والتحويل البنكي وبوابات الدفع الإلكترونية من حيث الإثبات والمراجعة.

التوصية: إبقاء `payment_status` كمؤشر ملخص على التبرع فقط، وإضافة جدول `payment_transactions` كمصدر الحقيقة لحالة الدفع، ثم تحديث التبرع بناءً على معاملات موثوقة وقابلة للتدقيق.

## ربط المشروع بالحملة والتبرع

الوضع الحالي:

- المشروع يمتلك حملات وتبرعات.
- الحملة تتبع مشروعًا واحدًا.
- التبرع قد يتبع مشروعًا وقد يتبع حملة وقد يجمع بينهما.
- Factory التبرع للحملة تضبط المشروع من الحملة بشكل صحيح.

الفجوات:

- لا يوجد قيد تطبيقي أو اختبار يمنع ربط تبرع بمشروع وحملة من مشروع مختلف.
- في نموذج Filament، اختيار الحملة غير مفلتر حسب المشروع المختار.
- يمكن إنشاء تبرع بلا مشروع وبلا حملة.
- عند حذف مشروع، الحملات تحذف، أما التبرعات فيتم تصفير `project_id` أو `campaign_id` حسب العلاقة. هذا يحمي السجل جزئيًا لكنه قد يفقد سياق التبرع إذا لم تحفظ snapshot.
- لا توجد علاقة عكسية أو واجهات Relation Managers داخل صفحة المشروع أو الحملة لعرض التبرعات والحملات التابعة.

التوصية: تعريف سياسة واضحة:

- إن كان `campaign_id` موجودًا، يجب أن يتطابق `project_id` مع مشروع الحملة أو يتم اشتقاقه آليًا.
- إن لم يوجد مشروع أو حملة، يجب وجود `donation_type = general` أو صندوق/مصرف عام واضح.
- حفظ snapshot لاسم المشروع والحملة وقت الدفع في metadata أو حقول مخصصة حتى لا يضيع السياق عند تعديل المحتوى لاحقًا.

## قابلية بناء متجر تبرعات لاحقًا

النواة الحالية لا تمنع بناء متجر، لكنها ليست مجهزة له بعد. المتجر يحتاج فصلًا أوضح بين:

- التبرع كنية أو مساهمة.
- المنتج الخيري كعنصر قابل للبيع أو الإهداء أو الشحن.
- السلة و checkout.
- الطلب order.
- معاملة الدفع.
- الإيصال.

المطلوب لاحقًا:

- `store_products` مع `sku`, `product_type`, `price`, `stock_quantity`, `requires_shipping`, وربط اختياري بحملة أو مشروع.
- `carts` و `cart_items` لدعم تبرعات ومنتجات في عملية واحدة.
- `checkouts` كمظلة للدفع سواء كان Donation فقط أو Store فقط أو Mixed.
- `orders` و `order_items` للمتجر.
- `payment_transactions` كمصدر الحقيقة للمدفوعات.
- `receipts` قابلة للإصدار والإلغاء وإعادة الإرسال.
- `refunds` كاملة وجزئية.

التصميم الحالي للتبرع يمكن أن يبقى، لكنه يحتاج أن يصبح جزءًا من تدفق checkout بدل أن يحمل وحده كل معنى الدفع.

## النواقص المالية والشرعية والتشغيلية

### نواقص مالية

- لا يوجد سجل معاملات دفع مستقل.
- لا يوجد قيد يمنع احتساب التبرعات غير المدفوعة ضمن `collected_amount`.
- لا توجد آلية آمنة لتحديث إجمالي المشروع والحملة داخل Database Transaction.
- لا يوجد تتبع رسوم الدفع أو صافي المبلغ.
- لا يوجد ربط مع Finance أو رقم قيد مالي أو حالة مطابقة.
- لا توجد إيصالات أو أرقام إيصال متسلسلة.
- لا توجد استردادات مفصلة أو استرداد جزئي.
- لا توجد سياسة حذف تحفظ الأثر المالي كاملًا.

### نواقص شرعية

- لا يوجد تصنيف واضح للتبرع: زكاة، صدقة، عام، كفارة، وقف، حملة مخصصة.
- لا توجد أهلية شرعية للحملة أو المشروع مثل `zakat_eligible`.
- لا يوجد حفظ لنية المتبرع أو شرطه وقت الدفع.
- لا يوجد منع لاستخدام مبلغ مخصص في مشروع/مصرف مختلف من ناحية البيانات.
- لا توجد مراجعة/اعتماد شرعي للحملات الحساسة قبل النشر.

### نواقص تشغيلية

- لا توجد صلاحيات دقيقة لإنشاء التبرعات اليدوية أو تعديل حالات الدفع.
- لا توجد Audit Trail لتغيير الحالات أو المبالغ.
- لا توجد فلاتر Filament كافية للحالة، المشروع، الحملة، الفترة، طريقة الدفع.
- لا توجد إجراءات تشغيلية مثل pause/complete/archive أو approve/publish.
- لا توجد معالجة للترميز العربي المشوه الظاهر في بعض النصوص الحالية داخل الاختبار وملفات Filament عند قراءتها من الطرفية، ويجب التأكد من سلامة الترميز في المستودع والمحررات.
- لا توجد لوحات متابعة لمعدلات النجاح، إجماليات اليوم، الحملات الأعلى، أو السجلات المعلقة.

## خارطة تطوير مرحلية

### Phase A: تثبيت أساس التبرعات الداخلي

الهدف: جعل السجلات الحالية آمنة ومتسقة قبل أي دفع عام.

- تعريف Enums أو ثوابت مركزية لحالات المشاريع والحملات والتبرعات.
- إضافة قواعد تحقق تمنع تبرعًا بحملة لا تتبع المشروع المختار.
- تحديد سياسة التبرع العام عند غياب المشروع والحملة.
- جعل `collected_amount` مشتقًا أو تحديثه عبر خدمة موحدة داخل Transaction.
- إضافة فلاتر Filament الأساسية للحالة، المشروع، الحملة، التاريخ، طريقة الدفع.
- إضافة Relation Managers لعرض حملات المشروع وتبرعات المشروع وتبرعات الحملة.
- إضافة صلاحيات أولية لمن يستطيع إنشاء تبرع يدوي أو تعديل حالته.
- إضافة اختبارات قاعدة بيانات وعلاقات وحالات.

### Phase B: المدفوعات والإيصالات والاعتماد

الهدف: نقل الموديول من CRUD إداري إلى دورة تبرع قابلة للتدقيق.

- إضافة `donors`.
- إضافة `payment_transactions` وربطها بالتبرعات.
- إضافة `receipts` وربطها بالتبرع ومعاملة الدفع.
- إضافة `refunds` مع دعم كامل وجزئي.
- فصل `payment_status` في التبرع عن حالة transaction المصدرية.
- إضافة دورة اعتماد للحملات: draft, submitted, under_review, approved, rejected, published.
- إضافة `campaign_approval_events`.
- إضافة حقول النشر العام: slug, visibility, published_at, cover image, summary.
- منع نشر الحملات غير المعتمدة.
- إضافة Audit Trail لتغييرات المبالغ والحالات الحساسة.

### Phase C: المتجر الخيري والcheckout الموحد

الهدف: بناء متجر تبرعات قابل للتوسع دون كسر نموذج التبرعات.

- إضافة `store_products`.
- إضافة `carts` و `cart_items`.
- إضافة `checkouts` لدعم donation, store, mixed.
- إضافة `orders` و `order_items`.
- توحيد بوابات الدفع عبر checkout و payment transactions.
- دعم المنتجات الرقمية والرمزية والفعلية مع fulfillment.
- دعم الإهداء، التبرع المجهول، المبالغ المقترحة، والتبرع المتكرر إذا كان ضمن نطاق المنتج.
- إضافة تكاملات لاحقة مع Finance و Reports عبر أحداث أو Outbox دون خلطها داخل CRUD.

## مهام قابلة للتوزيع على مطورين لاحقًا

- مطور بيانات: تصميم وإضافة قيود الاتساق بين `donations.project_id` و `campaigns.project_id`، وسياسة التبرع العام.
- مطور Backend: بناء خدمة `DonationAllocationService` لتحديث المحصلات من التبرعات المدفوعة فقط.
- مطور Backend: إضافة Enums لحالات المشروع والحملة والتبرع ومعاملات الدفع.
- مطور Filament: تحسين النماذج بفلاتر dependent selects بحيث تتفلتر الحملات حسب المشروع.
- مطور Filament: إضافة Relation Managers للمشاريع والحملات.
- مطور صلاحيات: تعريف Policies أو Permissions للإنشاء والتعديل والحذف وتغيير حالة الدفع.
- مطور Payments: تصميم `payment_transactions` وواجهات idempotent webhooks في Phase B.
- مطور Receipts: تصميم الإيصالات وأرقامها وسياسة الإلغاء وإعادة الإرسال.
- مطور Store: تصميم منتجات المتجر والسلة والطلبات في Phase C.
- مطور QA: بناء حزمة اختبارات Feature وFilament وDatabase للحالات الحرجة.
- مطور تقارير: تعريف مصادر بيانات أولية للإجماليات حسب المشروع والحملة والحالة وطريقة الدفع.

## اختبارات إضافية لازمة قبل الإنتاج

- منع إنشاء تبرع بحملة من مشروع مختلف.
- إنشاء تبرع لحملة يشتق `project_id` تلقائيًا أو يرفض القيم المتعارضة.
- منع تبرع بلا مشروع أو حملة إلا عند نوع تبرع عام معتمد.
- احتساب `collected_amount` من التبرعات `paid` فقط.
- عدم احتساب `pending`, `failed`, `refunded` ضمن المحصل.
- تحديث محصل المشروع والحملة داخل Transaction عند نجاح الدفع.
- اختبار race condition عند تسجيل تبرعين متزامنين لنفس الحملة.
- منع تعديل `amount` أو `payment_status` لمستخدم غير مخول.
- اختبار فلاتر Filament للتبرعات حسب الحالة والمشروع والحملة والتاريخ.
- اختبار حذف المشروع أو الحملة مع وجود تبرعات، والتأكد من عدم فقدان الأثر المالي.
- اختبار uniqueness لكل من `projects.code`, `campaigns.code`, `donations.reference`.
- اختبار حالات الانتقال المسموحة للتبرع: pending إلى paid أو failed، paid إلى refunded وفق صلاحية.
- اختبار أن refund لا يتجاوز المبلغ المدفوع.
- اختبار عدم إصدار إيصال لتبرع غير مدفوع عند إضافة الإيصالات لاحقًا.
- اختبار Webhook مكرر لا يكرر التبرع أو الإيصال عند إضافة المدفوعات.
- اختبار صلاحيات إنشاء التبرع اليدوي والتحويل البنكي واعتماد الاسترداد.
- اختبار سلامة الترميز العربي في Factories وFilament وTests داخل بيئة CI.

## ملاحظات قائد الموديول

- يجب اعتبار الجداول الحالية MVP داخليًا لا نموذجًا ماليًا نهائيًا.
- أكبر قرار معماري عاجل هو فصل التبرع عن معاملة الدفع قبل أي تكامل بوابة دفع.
- يجب عدم الاعتماد على `collected_amount` كحقيقة مالية إلا بعد تحديد آلية تحديث صارمة أو جعله مشتقًا من استعلامات موثوقة.
- لا ينبغي حذف السجلات المالية حذفًا فعليًا في مراحل الإنتاج؛ يفضل Soft Deletes أو منع الحذف واستعمال حالات أرشفة/إلغاء.
- قابلية المتجر جيدة إذا بني فوق checkout موحد، وضعيفة إذا تم توسيع `donations` ليحمل الطلبات والمنتجات مباشرة.

## أوامر الفحص المستخدمة

- `Get-ChildItem -Path app,database,tests -Recurse -File | Where-Object { $_.Name -match '(Project|Campaign|Donation|project|campaign|donation)' -or $_.FullName -match '(Projects|Campaigns|Donations|projects|campaigns|donations)' } | Select-Object -ExpandProperty FullName`
- `Get-ChildItem -Path app\Models,app\Filament,database\migrations,database\factories,tests -Recurse -File -ErrorAction SilentlyContinue | Select-String -Pattern 'Project|Campaign|Donation|projects|campaigns|donations' -List | Select-Object -ExpandProperty Path | Sort-Object -Unique`
- `Get-Content -Path app\Models\Project.php,app\Models\Campaign.php,app\Models\Donation.php`
- `Get-Content -Path database\migrations\2026_05_07_070000_create_project_campaign_donation_tables.php`
- `Get-Content -Path database\factories\ProjectFactory.php,database\factories\CampaignFactory.php,database\factories\DonationFactory.php`
- `Get-Content -Path tests\Feature\Projects\ProjectDonationRecordsTest.php`
- `Get-Content -Path app\Filament\Resources\Projects\ProjectResource.php,app\Filament\Resources\Projects\Schemas\ProjectForm.php,app\Filament\Resources\Projects\Tables\ProjectsTable.php,app\Filament\Resources\Projects\Pages\CreateProject.php,app\Filament\Resources\Projects\Pages\EditProject.php,app\Filament\Resources\Projects\Pages\ListProjects.php`
- `Get-Content -Path app\Filament\Resources\Campaigns\CampaignResource.php,app\Filament\Resources\Campaigns\Schemas\CampaignForm.php,app\Filament\Resources\Campaigns\Tables\CampaignsTable.php,app\Filament\Resources\Campaigns\Pages\CreateCampaign.php,app\Filament\Resources\Campaigns\Pages\EditCampaign.php,app\Filament\Resources\Campaigns\Pages\ListCampaigns.php`
- `Get-Content -Path app\Filament\Resources\Donations\DonationResource.php,app\Filament\Resources\Donations\Schemas\DonationForm.php,app\Filament\Resources\Donations\Tables\DonationsTable.php,app\Filament\Resources\Donations\Pages\CreateDonation.php,app\Filament\Resources\Donations\Pages\EditDonation.php,app\Filament\Resources\Donations\Pages\ListDonations.php`
- `Get-ChildItem -Path app,database,tests -Recurse -File | Select-String -Pattern "payment_status|collected_amount|project_id|campaign_id|Donation::|Campaign::|Project::" -List | Select-Object -ExpandProperty Path | Sort-Object -Unique`
- `Get-ChildItem -Path app -Recurse -File | Where-Object { $_.FullName -match '(Enum|Policy|Observer|Service|Action|Event|Listener)' } | Select-Object -ExpandProperty FullName`
- `Get-Content -Path docs\workstreams\donations-store-workstream-ar.md`

## الملف الناتج

تمت كتابة هذه المراجعة في:

`docs/module-reviews/donations-store-lead-review-ar.md`
