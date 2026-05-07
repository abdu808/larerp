# مسار تنفيذ Projects, Donations, Payments, Public Website, Charity Store

## الهدف والنطاق

يهدف هذا المسار إلى بناء منظومة موحدة لإدارة المشاريع والحملات الخيرية، استقبال التبرعات عبر الموقع العام والمتجر الخيري، تتبع عمليات الدفع، إصدار الإيصالات، وربط الأثر المالي والتقارير التشغيلية مع وحدات Finance وReports.

النطاق يشمل:

- إدارة المشاريع والحملات من لوحة داخلية مبنية على Laravel وFilament.
- واجهة عامة مبنية على Livewire وTailwind لعرض الحملات والمشاريع والتبرع والشراء.
- checkout موحد للتبرعات ومشتريات المتجر الخيري.
- سجل payment_transactions قابل للتدقيق والمطابقة المالية.
- إصدار receipts إلكترونية للتبرعات والمبيعات.
- دورة اعتماد approval_status للحملات قبل نشرها.
- صلاحيات دقيقة للفِرق الداخلية.
- اختبارات قبول وتكامل تغطي التدفقات الحرجة.
- نقاط تكامل واضحة مع Finance وReports دون تنفيذ كود Laravel في هذه الوثيقة.

## المبادئ المعمارية

- PostgreSQL هو مصدر الحقيقة للبيانات التشغيلية والمالية الأولية.
- كل عملية دفع يجب أن تمر عبر payment_transactions حتى لو فشلت أو ألغيت.
- لا يتم نشر حملة عامة إلا بعد اكتمال دورة approval_status.
- فصل مسؤولية المحتوى العام عن القيد المالي: الحملة تعرض الهدف، بينما Finance يعتمد الأثر المالي النهائي.
- checkout يخدم التبرع والشراء مع اختلاف نوع السلة ومصدر الإيراد.
- كل إيصال receipt يجب أن يكون قابلا للتتبع من المتبرع أو العميل إلى transaction ثم إلى السجل المالي.
- الاعتماد على Filament للعمليات الداخلية، وLivewire للمكونات العامة التفاعلية، وTailwind للواجهة المتجاوبة.

## الجداول المقترحة

### projects

يمثل المشروع الخيري طويل أو متوسط المدى.

الحقول الأساسية:

- id
- code فريد وقابل للقراءة الداخلية
- title_ar
- title_en اختياري
- slug فريد للواجهة العامة
- summary_ar
- description_ar
- category_id
- location_id اختياري
- target_amount
- collected_amount كمؤشر مشتق أو مخزن مع سياسة تحديث واضحة
- starts_at
- ends_at
- status مثل draft, active, paused, completed, archived
- visibility مثل public, private
- featured_until اختياري
- cover_image_path
- created_by
- updated_by
- timestamps
- soft_deletes

العلاقات:

- المشروع يحتوي عدة campaigns.
- المشروع يحتوي عدة donations مباشرة عند السماح بالتبرع للمشروع دون حملة.
- المشروع يرتبط بتصنيفات وتقارير الأثر.

### campaigns

يمثل حملة قابلة للنشر والتبرع، وقد ترتبط بمشروع أو تكون مستقلة.

الحقول الأساسية:

- id
- project_id اختياري
- code
- title_ar
- slug
- summary_ar
- description_ar
- target_amount
- minimum_donation_amount
- suggested_amounts بصيغة JSONB
- collected_amount
- donor_count
- starts_at
- ends_at
- approval_status مثل draft, submitted, under_review, approved, rejected, changes_requested
- approval_notes
- approved_by
- approved_at
- publish_status مثل unpublished, scheduled, published, hidden
- published_at
- allow_recurring_donations
- allow_anonymous_donations
- zakat_eligible
- sadaqah_eligible
- cover_image_path
- metadata بصيغة JSONB
- timestamps
- soft_deletes

الفهارس والقيود:

- فهرس فريد على slug.
- فهرس على approval_status وpublish_status.
- فهرس على starts_at وends_at لعرض الحملات النشطة.

### campaign_approval_events

يسجل تاريخ دورة الاعتماد للحملات.

الحقول الأساسية:

- id
- campaign_id
- from_status
- to_status
- actor_id
- note
- created_at

الاستخدام:

- تتبع من أرسل الحملة، من راجعها، ومن اعتمد أو رفض.
- دعم التدقيق الإداري وتقارير زمن الاعتماد.

### donors

يمثل المتبرع أو العميل عند الشراء من المتجر.

الحقول الأساسية:

- id
- user_id اختياري عند وجود حساب
- full_name
- email
- phone
- national_id اختياري حسب المتطلبات النظامية
- preferred_language
- accepts_marketing
- donor_type مثل individual, company
- metadata بصيغة JSONB
- timestamps

### donations

يمثل نية التبرع وسجلها التشغيلي.

الحقول الأساسية:

- id
- donor_id
- project_id اختياري
- campaign_id اختياري
- checkout_id اختياري
- amount
- currency
- donation_type مثل general, campaign, project, zakat, sadaqah
- frequency مثل one_time, monthly
- is_anonymous
- dedication_name اختياري
- status مثل pending, paid, failed, refunded, cancelled
- payment_transaction_id اختياري
- receipt_id اختياري
- paid_at
- metadata بصيغة JSONB
- timestamps

قيود مهمة:

- يجب وجود campaign_id أو project_id أو donation_type عام.
- لا تنتقل donation إلى paid إلا عند نجاح payment_transaction.

### store_products

يمثل منتجات المتجر الخيري، مثل بطاقات إهداء، منتجات رمزية، أو سلع فعلية.

الحقول الأساسية:

- id
- sku فريد
- name_ar
- slug
- description_ar
- product_type مثل digital, physical, donation_bundle
- price
- currency
- stock_quantity اختياري
- is_active
- requires_shipping
- campaign_id اختياري عند ربط المنتج بحملة
- image_path
- metadata بصيغة JSONB
- timestamps
- soft_deletes

### carts و cart_items

تدعم checkout العام قبل إنشاء الطلب أو التبرع النهائي.

cart fields:

- id
- session_id
- user_id اختياري
- donor_id اختياري
- status مثل active, checked_out, abandoned
- currency
- expires_at
- timestamps

cart_items fields:

- id
- cart_id
- item_type مثل donation, product
- campaign_id اختياري
- project_id اختياري
- product_id اختياري
- quantity
- unit_amount
- total_amount
- metadata بصيغة JSONB
- timestamps

### orders و order_items

تسجل مشتريات المتجر الخيري بعد checkout.

order fields:

- id
- order_number فريد
- donor_id
- checkout_id
- status مثل pending_payment, paid, fulfilled, cancelled, refunded
- subtotal
- discount_total
- tax_total عند الحاجة
- shipping_total
- total
- currency
- payment_transaction_id اختياري
- receipt_id اختياري
- paid_at
- metadata بصيغة JSONB
- timestamps

order_items fields:

- id
- order_id
- product_id
- quantity
- unit_price
- total
- fulfillment_status
- metadata بصيغة JSONB
- timestamps

### checkouts

يوحد رحلة الدفع للتبرعات والمتجر.

الحقول الأساسية:

- id
- checkout_number فريد
- donor_id اختياري في البداية ثم يثبت قبل الدفع
- cart_id اختياري
- source_type مثل donation, store, mixed
- status مثل initiated, awaiting_payment, paid, failed, cancelled, expired
- subtotal
- fees_amount اختياري
- total
- currency
- payment_provider
- payment_reference اختياري
- return_url
- cancel_url
- expires_at
- metadata بصيغة JSONB
- timestamps

### payment_transactions

جدول محوري لكل محاولات الدفع.

الحقول الأساسية:

- id
- transaction_number فريد
- checkout_id
- donor_id اختياري
- provider مثل stripe, hyperpay, moyasar, bank_transfer, manual
- provider_transaction_id
- provider_session_id
- amount
- currency
- status مثل initiated, pending, authorized, captured, failed, cancelled, refunded, partially_refunded
- failure_code
- failure_message
- payment_method_type مثل card, mada, apple_pay, bank_transfer
- raw_request بصيغة JSONB مع مراعاة عدم تخزين بيانات حساسة
- raw_response بصيغة JSONB مع مراعاة عدم تخزين بيانات حساسة
- webhook_received_at
- reconciled_at
- finance_entry_id اختياري بعد الربط مع Finance
- timestamps

قيود وضوابط:

- لا تخزن أرقام بطاقات أو بيانات حساسة.
- provider_transaction_id يجب أن يكون مفهرسا لمنع تكرار webhooks.
- كل webhook يعالج بطريقة idempotent.

### receipts

يمثل إيصال تبرع أو شراء.

الحقول الأساسية:

- id
- receipt_number فريد ومتسلسل حسب سياسة المؤسسة
- donor_id
- receipt_type مثل donation, store_order, mixed
- donation_id اختياري
- order_id اختياري
- payment_transaction_id
- amount
- currency
- issued_at
- voided_at اختياري
- void_reason اختياري
- pdf_path اختياري
- sent_to_email_at اختياري
- sent_to_sms_at اختياري
- metadata بصيغة JSONB
- timestamps

### refunds

يسجل المرتجعات والاستردادات المالية.

الحقول الأساسية:

- id
- payment_transaction_id
- donation_id اختياري
- order_id اختياري
- amount
- currency
- reason
- status مثل requested, approved, rejected, processed, failed
- provider_refund_id
- approved_by
- processed_at
- finance_entry_id اختياري
- timestamps

### public_pages و content_blocks

اختياري لإدارة محتوى الموقع العام من Filament.

public_pages fields:

- id
- slug
- title_ar
- seo_title_ar
- seo_description_ar
- status
- published_at
- timestamps

content_blocks fields:

- id
- public_page_id
- block_type
- sort_order
- payload بصيغة JSONB
- is_active
- timestamps

## الشاشات الداخلية في Filament

### إدارة المشاريع

الشاشات المطلوبة:

- قائمة المشاريع مع البحث بالعنوان والكود والحالة.
- إنشاء وتعديل المشروع.
- صفحة تفاصيل تعرض الهدف، المحصل، عدد التبرعات، الحملات التابعة، وآخر العمليات.
- إجراءات pause, complete, archive حسب الصلاحيات.
- تبويب للوسائط والصور.
- تبويب للتقارير المختصرة.

حقول مهمة في النماذج:

- بيانات تعريفية.
- نطاق زمني.
- هدف مالي.
- حالة التشغيل.
- إعدادات الظهور العام.
- ربط بالتصنيف والموقع.

### إدارة الحملات

الشاشات المطلوبة:

- قائمة الحملات مع فلاتر approval_status وpublish_status والمشروع.
- إنشاء مسودة حملة.
- شاشة مراجعة المحتوى والأهداف قبل الإرسال للاعتماد.
- إجراءات submitted, approve, reject, request changes.
- سجل campaign_approval_events داخل صفحة الحملة.
- معاينة قبل النشر للواجهة العامة.

قواعد الشاشة:

- المستخدم العادي ينشئ draft ويرسلها للمراجعة.
- المراجع يرى الحملات submitted وunder_review.
- الناشر لا يستطيع نشر حملة غير approved.
- عند rejection أو changes_requested يجب إدخال ملاحظة إلزامية.

### إدارة التبرعات

الشاشات المطلوبة:

- قائمة donations مع فلاتر الحالة، نوع التبرع، المشروع، الحملة، التاريخ.
- عرض تفاصيل التبرع والمتبرع وpayment_transaction والإيصال.
- إنشاء تبرع يدوي مصرح به فقط للحالات المكتبية أو التحويل البنكي.
- إجراءات mark as paid للتحويل البنكي وفق صلاحية مالية محددة.
- إجراءات refund request مع سبب إلزامي.

### إدارة المدفوعات

الشاشات المطلوبة:

- قائمة payment_transactions.
- تفاصيل الطلب من مزود الدفع.
- عرض raw_response بشكل منسق ومحدود للحقول غير الحساسة.
- إعادة معالجة webhook داخليا عند فشل المعالجة، بصلاحية عالية.
- تعليم transaction كمطابق ماليا بعد التكامل مع Finance أو reconciliation.

### إدارة الإيصالات

الشاشات المطلوبة:

- قائمة receipts مع فلاتر النوع والتاريخ والإلغاء.
- عرض الإيصال وربطه بالتبرع أو الطلب.
- إعادة إرسال الإيصال بالبريد أو الرسائل.
- إلغاء إيصال void بصلاحية مالية مع سبب إلزامي.
- تنزيل PDF عند توفره.

### إدارة المتجر الخيري

الشاشات المطلوبة:

- منتجات المتجر.
- المخزون للمنتجات الفعلية.
- الطلبات order management.
- fulfillment للطلبات التي تحتاج تسليم.
- ربط المنتجات بحملات عند كون المنتج مصدر دعم مباشر.

### لوحة متابعة تشغيلية

مؤشرات مختصرة:

- إجمالي التبرعات اليوم والشهر.
- الحملات بانتظار الاعتماد.
- العمليات الفاشلة.
- الإيصالات غير المرسلة.
- أعلى الحملات تحصيلا.
- طلبات المتجر بانتظار التنفيذ.

## الواجهة العامة Livewire وTailwind

### صفحات المشاريع والحملات

الصفحات المطلوبة:

- الصفحة الرئيسية للموقع العام.
- قائمة المشاريع.
- تفاصيل مشروع.
- قائمة الحملات.
- تفاصيل حملة.
- صفحة زكاة أو صدقة عند الحاجة.
- صفحة البحث والتصفية.

المكونات:

- بطاقة حملة تعرض العنوان، الصورة، نسبة التقدم، المبلغ المستهدف، المبلغ المحصل، وعدد المتبرعين.
- شريط تقدم مالي.
- مبالغ تبرع مقترحة.
- اختيار التبرع لمرة واحدة أو متكرر عند تفعيلها.
- إخفاء اسم المتبرع.
- تبرع سريع من البطاقة.

متطلبات تجربة المستخدم:

- تصميم متجاوب بالكامل.
- دعم RTL للعربية.
- عدم عرض الحملات إلا عند approval_status = approved وpublish_status = published وضمن التواريخ المسموحة.
- رسائل خطأ واضحة عند فشل الدفع.
- صفحة نجاح تحتوي رقم الإيصال ورابط تنزيله عند توفره.

### صفحات المتجر الخيري

الصفحات المطلوبة:

- قائمة المنتجات.
- تفاصيل المنتج.
- السلة.
- checkout.
- صفحة نجاح الطلب.
- صفحة تتبع طلب مبسطة عند الحاجة.

قواعد العرض:

- لا تعرض المنتجات غير النشطة.
- لا تسمح بشراء كمية أعلى من المخزون للمنتجات الفعلية.
- المنتجات الرقمية لا تطلب عنوان شحن.
- المنتجات المرتبطة بحملة تعرض أثر الشراء على الحملة.

## checkout

### مراحل checkout

1. بناء السلة من تبرع أو منتج أو كلاهما.
2. جمع بيانات المتبرع أو العميل: الاسم، الجوال، البريد، وخيارات الخصوصية.
3. التحقق من صلاحية عناصر السلة والأسعار والحملة أو المنتج.
4. إنشاء checkout بحالة initiated.
5. إنشاء payment_transaction بحالة initiated.
6. إرسال المستخدم إلى مزود الدفع أو عرض وسيلة الدفع المناسبة.
7. استقبال return_url أو webhook من مزود الدفع.
8. تحديث payment_transaction.
9. عند نجاح الدفع:
   - تحديث checkout إلى paid.
   - إنشاء donations بحالة paid أو تحديثها.
   - إنشاء orders بحالة paid عند وجود منتجات.
   - تحديث collected_amount وdonor_count بطريقة آمنة.
   - إصدار receipt.
   - إرسال إشعار للمتبرع أو العميل.
   - إرسال حدث تكامل إلى Finance وReports.
10. عند الفشل:
   - تحديث checkout إلى failed أو awaiting_payment حسب الحالة.
   - حفظ failure_code وfailure_message.
   - إظهار رسالة مناسبة للمستخدم.

### قواعد idempotency

- provider_transaction_id لا يعالج أكثر من مرة.
- webhook يعالج وفق مفتاح فريد من المزود.
- إصدار receipt يتم مرة واحدة لكل payment_transaction ناجح.
- تحديث collected_amount يجب أن يكون transactional لمنع السباق.

### حالات checkout المختلطة

عند وجود تبرع ومنتجات في نفس checkout:

- يقسم المبلغ داخليا على donations وorder_items.
- يصدر receipt من نوع mixed أو إيصالين حسب سياسة المؤسسة.
- يرسل Finance قيودا مفصلة بمصادر الإيراد.
- Reports يجب أن يعرف مصدر كل بند داخل checkout.

## payment_transactions

### حالات الدفع

- initiated: تم إنشاء محاولة الدفع.
- pending: بانتظار رد مزود الدفع أو تحويل بنكي.
- authorized: تم التفويض ولم يتم السحب النهائي عند دعم المزود.
- captured: تمت العملية بنجاح.
- failed: فشلت العملية.
- cancelled: ألغاها المستخدم أو المزود.
- refunded: تم استرداد كامل المبلغ.
- partially_refunded: تم استرداد جزئي.

### معالجة webhooks

متطلبات المعالجة:

- التحقق من توقيع webhook.
- تسجيل وقت الاستلام.
- حفظ payload غير الحساس.
- تطبيق idempotency.
- ربط الحدث بالcheckout والtransaction.
- إطلاق أحداث داخلية للتبرعات والطلبات والإيصالات والمالية.

### المطابقة المالية

- reconciled_at يعبأ بعد مطابقة العملية مع كشف المزود أو Finance.
- finance_entry_id يحفظ مرجع القيد المالي.
- الفروقات بين amount المحصل وamount المتوقع تعرض في شاشة استثناءات.

## receipts

### قواعد إصدار الإيصال

- يصدر الإيصال فقط بعد نجاح الدفع أو اعتماد الدفع اليدوي.
- receipt_number يجب أن يكون فريدا ومتسلسلا حسب السنة أو سياسة المؤسسة.
- يجب أن يحتوي على بيانات المؤسسة، المتبرع، المبلغ، العملة، التاريخ، نوع العملية، ومرجع الدفع.
- الإيصال الملغى لا يحذف بل يوسم voided_at وvoid_reason.
- عند refund كامل، يجب إظهار علاقة الاسترداد بالإيصال.

### قنوات الإرسال

- البريد الإلكتروني.
- الرسائل النصية عند وجود مزود.
- تنزيل PDF من صفحة النجاح أو حساب المتبرع.

## approval_status للحملات

### دورة الاعتماد

- draft: الحملة قيد التحرير.
- submitted: تم إرسالها للمراجعة.
- under_review: بدأ المراجع العمل عليها.
- changes_requested: تحتاج تعديلات.
- approved: معتمدة للنشر.
- rejected: مرفوضة.

### قواعد الانتقال

- draft إلى submitted بواسطة منشئ الحملة أو مدير المشاريع.
- submitted إلى under_review بواسطة المراجع.
- under_review إلى approved بواسطة صاحب صلاحية اعتماد الحملات.
- under_review إلى rejected أو changes_requested مع ملاحظة إلزامية.
- approved لا تعني published تلقائيا إلا إذا اعتمدت المؤسسة النشر التلقائي.
- أي تعديل جوهري على حملة approved يعيدها إلى draft أو changes_requested حسب السياسة.

### سجل التدقيق

كل انتقال يضاف إلى campaign_approval_events مع:

- الحالة السابقة.
- الحالة الجديدة.
- المستخدم المنفذ.
- الملاحظة.
- وقت التنفيذ.

## الصلاحيات

الأدوار المقترحة:

- Super Admin: وصول كامل.
- Projects Manager: إدارة المشاريع والحملات دون اعتماد نهائي مالي.
- Campaign Creator: إنشاء وتعديل المسودات وإرسالها للمراجعة.
- Campaign Reviewer: مراجعة الحملات وطلب التعديلات.
- Campaign Approver: اعتماد أو رفض الحملات.
- Donations Officer: عرض التبرعات ومعالجة الحالات المكتبية.
- Payments Officer: متابعة عمليات الدفع والتحويل البنكي والمطابقة الأولية.
- Finance Manager: اعتماد الاستردادات، إلغاء الإيصالات، وربط القيود المالية.
- Store Manager: إدارة المنتجات والطلبات والتنفيذ.
- Reports Viewer: قراءة التقارير والمؤشرات فقط.
- Public User أو Guest: التصفح والتبرع والشراء من الواجهة العامة.

الصلاحيات الحرجة:

- approve campaigns.
- publish campaigns.
- create manual donations.
- mark bank transfer as paid.
- view payment raw payload.
- reprocess webhook.
- issue receipt manually.
- void receipt.
- approve refund.
- reconcile transaction.
- export financial data.

## التكامل مع Finance

### أحداث التكامل

ترسل الأحداث التالية إلى Finance أو تسجل في جدول outbox عند اعتماد نمط event-driven:

- payment captured.
- donation paid.
- store order paid.
- receipt issued.
- refund approved.
- refund processed.
- receipt voided.
- transaction reconciled.

### بيانات مطلوبة للقيود المالية

- transaction_number.
- receipt_number.
- donor_id.
- source_type: donation أو store أو mixed.
- campaign_id أو project_id عند توفرها.
- amount.
- currency.
- fees_amount إن وجدت.
- net_amount عند توفرها من مزود الدفع.
- payment_provider.
- payment_method_type.
- paid_at.
- cost_center أو fund_code عند ربط المشاريع بمراكز تكلفة.

### قواعد محاسبية مبدئية

- التبرعات الناجحة تقيد كإيراد تبرعات أو صندوق مخصص حسب المشروع أو الحملة.
- مبيعات المتجر تقيد كإيراد متجر خيري مع تفصيل المنتجات.
- رسوم بوابة الدفع تقيد كمصروف أو تخفيض صافي حسب سياسة Finance.
- الاسترداد يعكس القيد الأصلي أو ينشئ قيدا مقابلا.
- لا يتم اعتماد transaction كمطابق إلا بعد reconciliation.

## التكامل مع Reports

### مؤشرات تشغيلية

- إجمالي التبرعات حسب الفترة.
- إجمالي المتبرعين.
- متوسط قيمة التبرع.
- معدل نجاح الدفع.
- أعلى الحملات تحصيلا.
- الحملات المتأخرة في الاعتماد.
- المنتجات الأكثر مبيعا.
- الإيصالات المصدرة والملغاة.
- الاستردادات حسب السبب.

### أبعاد التحليل

- التاريخ.
- المشروع.
- الحملة.
- نوع التبرع.
- طريقة الدفع.
- مزود الدفع.
- المدينة أو الموقع عند توفره.
- نوع المتبرع.
- قناة الوصول مثل public website أو admin أو campaign link.

### مخرجات التقارير

- dashboard يومي.
- تقرير حملات.
- تقرير تبرعات.
- تقرير معاملات دفع.
- تقرير إيصالات.
- تقرير متجر.
- تصدير CSV أو Excel للمستخدمين المخولين.

## الاختبارات

### اختبارات قاعدة البيانات

- إنشاء project وcampaign بعلاقات صحيحة.
- منع slug مكرر.
- منع نشر حملة غير approved.
- منع donation paid دون payment_transaction ناجح.
- منع تكرار receipt لنفس transaction.
- تطبيق soft deletes دون فقد السجلات المالية.

### اختبارات Filament

- صلاحيات عرض وإنشاء وتعديل المشاريع.
- دورة اعتماد الحملة بالكامل.
- فلاتر قوائم التبرعات والمدفوعات.
- منع المستخدم غير المخول من إلغاء الإيصال.
- منع المستخدم غير المخول من reprocess webhook.
- إدارة منتجات المتجر والمخزون.

### اختبارات Livewire للواجهة العامة

- عرض الحملات المنشورة فقط.
- اختيار مبلغ تبرع مقترح.
- إدخال مبلغ مخصص ضمن الحد الأدنى.
- إضافة منتج للسلة.
- منع شراء منتج نافد.
- تحديث السلة دون إعادة تحميل الصفحة.
- عرض رسائل التحقق بالعربية.

### اختبارات checkout والمدفوعات

- checkout تبرع ناجح.
- checkout متجر ناجح.
- checkout مختلط ناجح.
- فشل الدفع مع حفظ failure_message.
- webhook مكرر لا يكرر donation أو receipt.
- return_url يصل قبل webhook دون كسر الحالة النهائية.
- webhook يصل قبل return_url دون كسر تجربة المستخدم.
- refund كامل وجزئي.

### اختبارات الإيصالات

- إصدار إيصال بعد الدفع الناجح.
- عدم إصدار إيصال بعد الدفع الفاشل.
- إعادة إرسال الإيصال.
- void receipt بصلاحية صحيحة.
- ربط الإيصال بالتبرع أو الطلب وpayment_transaction.

### اختبارات التكامل

- إرسال حدث payment captured إلى Finance.
- إنشاء مرجع finance_entry_id بعد نجاح الربط.
- ظهور التبرع في Reports بالأبعاد الصحيحة.
- مطابقة إجمالي Reports مع payment_transactions الناجحة ضمن الفترة.

## معايير القبول

### المشاريع والحملات

- يستطيع المستخدم المخول إنشاء مشروع وحملة مرتبطة به.
- لا تظهر الحملة في الموقع العام قبل approval_status = approved وpublish_status = published.
- تسجل كل تغييرات الاعتماد في campaign_approval_events.
- يستطيع المدير رؤية المبلغ المستهدف والمحصل ونسبة التقدم.

### التبرعات

- يستطيع الزائر التبرع لحملة منشورة من الموقع العام.
- تنشأ donation بحالة paid فقط بعد نجاح payment_transaction.
- يمكن للمتبرع اختيار التبرع مجهول الهوية.
- تظهر التبرعات في لوحة Filament مع الفلاتر الأساسية.

### checkout

- يدعم checkout التبرع فقط، المتجر فقط، والتدفق المختلط.
- يمنع checkout المنتجات غير النشطة أو الحملات غير المنشورة.
- عند فشل الدفع تظهر رسالة واضحة ولا يصدر إيصال.
- عند نجاح الدفع يتم تحديث كل السجلات المرتبطة داخل معاملة آمنة.

### payment_transactions

- تسجل كل محاولة دفع، ناجحة أو فاشلة.
- لا يعاد تنفيذ webhook مكرر.
- يمكن لمستخدم مخول مراجعة تفاصيل transaction دون بيانات حساسة.
- يمكن ربط transaction بمرجع Finance بعد المطابقة.

### receipts

- يصدر receipt واحد صحيح لكل payment_transaction ناجح وفق السياسة.
- يمكن إعادة إرسال الإيصال.
- يمكن إلغاء الإيصال مع سبب ودون حذف السجل.
- رقم الإيصال فريد وقابل للتدقيق.

### المتجر الخيري

- يمكن إدارة المنتجات من Filament.
- لا يمكن شراء كمية تتجاوز المخزون.
- تنشأ order عند نجاح checkout الخاص بالمتجر.
- تظهر حالة fulfillment للطلبات التي تحتاج تسليم.

### الصلاحيات

- لا يستطيع منشئ الحملة اعتماد حملته إذا لم تكن لديه صلاحية اعتماد.
- لا يستطيع موظف غير مالي إلغاء إيصال أو اعتماد refund.
- لا يستطيع مستخدم غير مخول عرض payload كامل لبوابة الدفع.
- يمكن لمستخدم التقارير قراءة المؤشرات دون تعديل البيانات.

### Finance وReports

- كل payment captured يرسل أو يسجل كحدث تكامل مالي.
- يمكن تتبع receipt إلى transaction ثم إلى finance_entry_id عند توفره.
- تتطابق مجاميع Reports مع payment_transactions بعد استبعاد الفاشلة والملغاة.
- تظهر الحملات والمتجر كمصادر إيراد منفصلة وقابلة للتصفية.

## خطة التنفيذ المرحلية

### المرحلة 1: الأساس البياني

- اعتماد الجداول الأساسية: projects, campaigns, donors, donations.
- إضافة approval_status وcampaign_approval_events.
- تحديد القيود والفهارس.
- تجهيز seed roles والصلاحيات.
- كتابة اختبارات قواعد البيانات ودورة الاعتماد.

### المرحلة 2: لوحة Filament الداخلية

- بناء موارد المشاريع والحملات.
- إضافة شاشات الموافقة والنشر.
- بناء موارد donations وpayment_transactions للقراءة والمتابعة.
- بناء dashboard تشغيلية أولية.
- تغطية الصلاحيات باختبارات.

### المرحلة 3: الواجهة العامة والتبرع

- بناء صفحات المشاريع والحملات.
- بناء مكونات التبرع السريع.
- بناء checkout للتبرع فقط.
- ربط نجاح الدفع بإصدار donation وreceipt.
- اختبار التدفقات الناجحة والفاشلة.

### المرحلة 4: المتجر الخيري

- بناء store_products وcart وorders.
- إضافة صفحات المتجر والسلة.
- توسيع checkout ليدعم المنتجات.
- دعم checkout المختلط.
- إضافة fulfillment للمنتجات الفعلية.

### المرحلة 5: المدفوعات والإيصالات المتقدمة

- تحسين payment_transactions وwebhooks وidempotency.
- دعم refunds.
- دعم void receipts.
- توليد PDF وإعادة الإرسال.
- بناء شاشات الاستثناءات والمطابقة.

### المرحلة 6: التكامل والتقارير

- إرسال أحداث Finance.
- حفظ finance_entry_id وreconciled_at.
- بناء مصادر بيانات Reports.
- إضافة مؤشرات الحملات والمتجر والمدفوعات.
- إجراء اختبارات مطابقة المجاميع.

## مخاطر وضوابط

- تكرار webhook قد يكرر التبرعات أو الإيصالات إذا لم تطبق idempotency بصرامة.
- تعديل حملة منشورة قد يسبب اختلافا بين ما دفع عليه المتبرع وما يظهر لاحقا؛ يجب حفظ metadata كافية وقت checkout.
- تخزين raw_response يجب ألا يتضمن بيانات دفع حساسة.
- collected_amount قد يتعرض لمشاكل سباق؛ يفضل تحديثه داخل transaction أو احتسابه من مصدر موثوق.
- اختلاف توقيت return_url وwebhook يجب أن يعالج بحالات انتقال واضحة.
- الاستردادات والإيصالات الملغاة يجب أن تظهر في Reports وFinance دون حذف أثر العملية الأصلية.

## الملفات المعدلة

- docs/workstreams/donations-store-workstream-ar.md
