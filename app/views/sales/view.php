<?php
// app/views/sales/show.php
$invoice =$invoice ?? ($data['invoice'] ?? null);
$items =$items ?? ($data['items'] ?? null);

// --- جلب إعدادات الشركة ديناميكياً ---
$db = Database::getInstance();
$cid = Session::get('company_id') ?: 1;
$db->query("SELECT setting_key, setting_value FROM settings WHERE company_id = :cid OR company_id IS NULL");
$db->bind(':cid', $cid);
$sysSettings = $db->resultSet();

$companyName = 'اسم المؤسسة غير محدد';
$vatNumber = 'غير مسجل';
$taxRate = 15; // الضريبة الافتراضية

foreach ($sysSettings as $s) {
    if ($s->setting_key === 'company_name' && !empty($s->setting_value)) $companyName = $s->setting_value;
    if ($s->setting_key === 'tax_number' && !empty($s->setting_value)) $vatNumber = $s->setting_value;
    if ($s->setting_key === 'tax_rate' && is_numeric($s->setting_value)) $taxRate = (float)$s->setting_value;
}

// حساب المجاميع بشكل صحيح (السعر في الفاتورة يُعتبر السعر الأساسي، وتُضاف الضريبة عليه)
$subTotal = (float)$invoice->total_amount;
$taxAmount = $subTotal * ($taxRate / 100);
$grandTotal = $subTotal + $taxAmount;

// توليد بيانات الـ QR Code المبسطة (اسم الشركة، الرقم الضريبي، التاريخ، الإجمالي، الضريبة)
// في الأنظمة الحقيقية يجب تشفيرها بـ TLV Base64 لهيئة الزكاة
$qrData = "الشركة: $companyName\nالرقم الضريبي: $vatNumber\nالتاريخ: {$invoice->created_at}\nالإجمالي: $grandTotal\nالضريبة: $taxAmount";
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
?>

<div class="card" style="max-width: 850px; margin: 0 auto; border: none; box-shadow: var(--shadow-md); background: #fff;">
    
    <!-- شريط الأزرار -->
    <div class="card-header bg-white d-print-none d-flex justify-content-between align-items-center" style="border-bottom: 2px solid var(--slate-100);">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-file-invoice text-success"></i> عرض وطباعة الفاتورة الضريبية</h3>
        <div class="d-flex gap-2">
            <button class="btn btn-info text-white" onclick="window.print()"><i class="fas fa-print"></i> طباعة الفاتورة (A4)</button>
            <a href="<?php echo URLROOT; ?>/pos/index" class="btn btn-secondary">العودة لنقطة البيع</a>
        </div>
    </div>

    <!-- ورقة الفاتورة للطباعة -->
    <div class="card-body p-5 invoice-print-area">
        
        <!-- الترويسة العليا -->
        <div class="d-flex justify-content-between align-items-start mb-5 pb-4" style="border-bottom: 2px solid var(--slate-100);">
            
            <!-- بيانات الفاتورة (يسار) -->
            <div>
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-dark); margin-bottom: 5px;">فاتورة ضريبية مبسطة</h2>
                <div class="text-muted font-monospace text-uppercase" style="font-size: 13px; letter-spacing: 1px;">Simplified Tax Invoice</div>
            </div>

            <!-- بيانات الشركة (وسط يمين) -->
            <div class="text-center">
                <h1 style="font-size: 26px; font-weight: 900; color: var(--primary-dark); margin-bottom: 5px;"><?php echo htmlspecialchars($companyName); ?></h1>
                <div class="text-muted" style="font-size: 13px;">
                    الرقم الضريبي (VAT): <span class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($vatNumber); ?></span><br>
                    المملكة العربية السعودية
                </div>
            </div>

            <!-- QR Code (أقصى اليمين) -->
            <div>
                <img src="<?php echo $qrUrl; ?>" alt="QR Code" style="width: 100px; height: 100px; border: 1px solid var(--border-color); padding: 5px; border-radius: 8px;">
            </div>
        </div>

        <!-- معلومات العميل والإصدار -->
        <div class="d-flex justify-content-between mb-4 p-4 rounded" style="background-color: var(--slate-50); border: 1px solid var(--slate-200);">
            <div style="width: 45%;">
                <table style="width: 100%; font-size: 14px;">
                    <tr>
                        <td class="text-muted fw-bold pb-2" style="width: 120px;">رقم الفاتورة:</td>
                        <td class="font-monospace fw-bold text-dark pb-2" style="direction:ltr; text-align:right;"><?php echo htmlspecialchars($invoice->invoice_number); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold pb-2">تاريخ الإصدار:</td>
                        <td class="font-monospace text-dark pb-2" style="direction:ltr; text-align:right;"><?php echo date('Y-m-d H:i', strtotime($invoice->created_at)); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold">البائع / المندوب:</td>
                        <td class="text-dark fw-bold" style="text-align:right;"><?php echo htmlspecialchars($invoice->sales_rep_name ?? 'المدير العام'); ?></td>
                    </tr>
                </table>
            </div>
            
            <div style="width: 45%; border-right: 2px dashed var(--slate-300); padding-right: 20px;">
                <div class="text-muted fw-bold mb-2" style="font-size: 13px;">بيانات العميل:</div>
                <div class="fs-4 fw-bold text-dark mb-1">
                    <i class="fas fa-user-circle text-primary"></i> <?php echo htmlspecialchars($invoice->customer_name ?? 'عميل نقدي'); ?>
                </div>
                <?php if(!empty($invoice->phone)): ?>
                    <div class="text-muted font-monospace mt-2" style="direction:ltr; text-align:right;"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($invoice->phone); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- جدول الأصناف -->
        <table class="table" style="border: 1px solid var(--slate-200); width: 100%; margin-bottom: 30px;">
            <thead style="background: var(--slate-100);">
                <tr>
                    <th style="padding: 15px; color: var(--slate-700); font-size:12px; width:40%;">تفاصيل الصنف / DESCRIPTION</th>
                    <th class="text-center" style="padding: 15px; color: var(--slate-700); font-size:12px;">الكمية / QTY</th>
                    <th style="padding: 15px; color: var(--slate-700); font-size:12px; text-align:left;">سعر الوحدة / UNIT PRICE</th>
                    <th style="padding: 15px; color: var(--slate-700); font-size:12px; text-align:left;">المجموع / SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr style="border-bottom: 1px solid var(--slate-100);">
                    <td style="padding: 15px;">
                        <strong class="text-dark d-block"><?php echo htmlspecialchars($item->product_name); ?></strong>
                        <?php if(!empty($item->sku)): ?>
                            <div class="text-muted font-monospace" style="font-size: 11px;">SKU: <?php echo htmlspecialchars($item->sku); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center font-monospace fw-bold text-dark" style="padding: 15px;"><?php echo $item->quantity; ?></td>
                    <td class="font-monospace text-dark" style="padding: 15px; direction:ltr; text-align:left;"><?php echo number_format($item->price, 2); ?></td>
                    <td class="font-monospace fw-bold text-dark" style="padding: 15px; direction:ltr; text-align:left;"><?php echo number_format($item->subtotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- المجاميع والشروط -->
        <div class="d-flex justify-content-between align-items-start">
            
            <div style="width: 45%; padding: 20px; background: var(--slate-50); border-radius: 8px;">
                <h6 class="fw-bold text-dark mb-2">ملاحظات وشروط الدفع:</h6>
                <ul class="text-muted mb-0" style="font-size: 12px; padding-right: 15px; line-height: 1.8;">
                    <li>البضاعة المباعة لا ترد ولا تستبدل بعد 14 يوماً من تاريخ الفاتورة.</li>
                    <li>الإجمالي الموضح أدناه يشمل ضريبة القيمة المضافة بنسبة <?php echo $taxRate; ?>%.</li>
                </ul>
            </div>

            <div style="width: 45%;">
                <table style="width: 100%; font-size: 15px;">
                    <tr>
                        <td class="text-muted fw-bold py-2">المبلغ الخاضع للضريبة:</td>
                        <td class="font-monospace text-dark fw-bold text-left py-2" style="direction:ltr;"><?php echo number_format($subTotal, 2); ?></td>
                    </tr>
                    <tr style="border-bottom: 2px solid var(--slate-200);">
                        <td class="text-muted fw-bold py-2">ضريبة القيمة المضافة (<?php echo $taxRate; ?>%):</td>
                        <td class="font-monospace text-dark text-left py-2" style="direction:ltr;"><?php echo number_format($taxAmount, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold py-4" style="font-size: 18px; color: var(--text-dark);">الإجمالي شامل الضريبة:</td>
                        <td class="font-monospace fw-bold text-left py-4" style="font-size: 24px; color: var(--success-dark); direction:ltr;">
                            <span style="font-size: 14px; color: var(--text-muted); margin-right:5px; font-family:'Cairo';">ر.س</span>
                            <?php echo number_format($grandTotal, 2); ?>
                        </td>
                    </tr>
                </table>
            </div>

        </div>

        <div class="text-center mt-5 pt-4 text-muted fw-bold" style="border-top: 1px solid var(--slate-200); font-size: 13px;">
            نشكر لكم تسوقكم معنا. تم إصدار هذه الفاتورة إلكترونياً وتوافق متطلبات هيئة الزكاة.
        </div>

    </div>
</div>

<style>
    @media print {
        body { background: #fff !important; }
        .d-print-none, .sidebar, .topbar { display: none !important; }
        .main-content { margin: 0 !important; }
        .card { box-shadow: none !important; border: none !important; max-width: 100% !important; margin: 0 !important;}
        .card-body { padding: 0 !important; }
        .invoice-print-area { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>