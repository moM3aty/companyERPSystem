<?php
// app/views/sales/view.php

$invoice = $data['invoice'] ?? null;
$items = $data['items'] ?? [];

// جلب إعدادات الشركة (الرقم الضريبي والاسم)
$db = Database::getInstance();
$db->query("SELECT setting_key, setting_value FROM settings WHERE company_id = :cid");
$db->bind(':cid', Session::get('company_id'));
$sysSettings = $db->resultSet();

$companyName = 'شركة غير مسماة';
$vatNumber = '000000000000000';
$taxRate = 15; // النسبة الافتراضية
$companyLogo = '';

foreach ($sysSettings as $s) {
    if ($s->setting_key === 'company_name') $companyName = $s->setting_value;
    if ($s->setting_key === 'vat_number') $vatNumber = $s->setting_value;
    if ($s->setting_key === 'tax_rate') $taxRate = (float)$s->setting_value;
    if ($s->setting_key === 'company_logo') $companyLogo = URLROOT . $s->setting_value;
}

// حسابات الفاتورة الأساسية
$subTotal = 0;
foreach($items as $item) {
    $subTotal += $item->subtotal;
}

// إذا كان النظام يسجل إجمالي الفاتورة كـ شامل الضريبة أو غير شامل
// للتبسيط: سنفترض أن total_amount هو الإجمالي شامل الضريبة
$grandTotal = $invoice->total_amount;
// الحسبة العكسية لمعرفة مبلغ الضريبة (بافتراض الضريبة 15%)
// المبلغ قبل الضريبة = الإجمالي / 1.15
$amountBeforeTax = $grandTotal / (1 + ($taxRate / 100));
$vatAmount = $grandTotal - $amountBeforeTax;

// 🟢 توليد كود الـ QR الخاص بهيئة الزكاة والدخل (ZATCA) 🟢
require_once APP_ROOT . '/app/helpers/ZatcaHelper.php';
$invoiceDateISO = date('Y-m-d\TH:i:s\Z', strtotime($invoice->created_at)); // ISO 8601 Format
$zatcaQrBase64 = ZatcaHelper::generateQrCode(
    $companyName, 
    $vatNumber, 
    $invoiceDateISO, 
    number_format($grandTotal, 2, '.', ''), 
    number_format($vatAmount, 2, '.', '')
);
?>

<div class="card bg-white border-0 shadow-sm" style="max-width: 950px; margin: 0 auto; color: #000;">
    
    <!-- شريط التحكم العلوي -->
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-file-invoice text-success"></i> عرض وطباعة الفاتورة الضريبية</h3>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> طباعة الفاتورة (A4)</button>
            <a href="<?php echo URLROOT; ?>/sale/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </div>

    <!-- ورقة الفاتورة (للطباعة) -->
    <div class="card-body p-5" id="printable-invoice">
        
        <!-- الترويسة و اللوجو و الـ QR -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div class="d-flex gap-4">
                <!-- ZATCA QR Code Container -->
                <div id="qrcode" style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff;"></div>
                
                <div>
                    <h1 style="font-size: 26px; font-weight: 900; color: #0f172a; margin-bottom: 5px;"><?php echo htmlspecialchars($companyName); ?></h1>
                    <div class="text-muted fs-6 mb-1">الرقم الضريبي (VAT): <span class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($vatNumber); ?></span></div>
                    <div class="text-muted fs-6 mb-1">المملكة العربية السعودية</div>
                </div>
            </div>

            <div class="text-left" style="direction: ltr; text-align: left;">
                <?php if(!empty($companyLogo)): ?>
                    <img src="<?php echo $companyLogo; ?>" alt="Logo" style="max-height: 80px; object-fit: contain; margin-bottom: 10px;">
                <?php endif; ?>
                <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 2px;">فاتورة ضريبية مبسطة</h2>
                <div class="text-muted font-monospace fs-5">SIMPLIFIED TAX INVOICE</div>
            </div>
        </div>

        <!-- بيانات الفاتورة والعميل -->
        <div class="d-flex justify-content-between mb-5 bg-light p-4 rounded border">
            <div>
                <div class="text-muted fs-6 fw-bold text-uppercase mb-2">بيانات العميل:</div>
                <div class="fs-5 fw-bold text-dark mb-1"><i class="fas fa-user-circle text-muted"></i> <?php echo htmlspecialchars($invoice->customer_name ?? 'عميل نقدي'); ?></div>
                <?php if(!empty($invoice->phone)): ?>
                    <div class="text-muted fs-6"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($invoice->phone); ?></div>
                <?php endif; ?>
            </div>
            <div class="text-left">
                <table style="width: 100%; font-size: 14px;">
                    <tr>
                        <td class="text-muted fw-bold pb-2" style="padding-left: 20px;">رقم الفاتورة:</td>
                        <td class="font-monospace fw-bold text-dark text-left pb-2" style="direction:ltr;"><?php echo htmlspecialchars($invoice->invoice_number); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold pb-2">تاريخ الإصدار:</td>
                        <td class="font-monospace text-dark text-left pb-2" style="direction:ltr;"><?php echo date('Y-m-d H:i', strtotime($invoice->created_at)); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold">البائع / المندوب:</td>
                        <td class="text-dark text-left fw-bold"><?php echo htmlspecialchars($invoice->sales_rep_name ?? 'إدارة المبيعات'); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- جدول الأصناف -->
        <table class="table" style="border: 1px solid var(--border-color); width: 100%;">
            <thead style="background: #0f172a; color: #fff;">
                <tr>
                    <th style="padding: 12px; color: #fff; width: 50%;">تفاصيل الصنف / Description</th>
                    <th class="text-center" style="padding: 12px; color: #fff;">الكمية / Qty</th>
                    <th style="padding: 12px; color: #fff;">سعر الوحدة / Unit Price</th>
                    <th style="padding: 12px; color: #fff;">المجموع / Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 15px;">
                        <strong class="text-dark d-block"><?php echo htmlspecialchars($item->product_name); ?></strong>
                        <span class="text-muted font-monospace" style="font-size: 11px;">SKU: <?php echo htmlspecialchars($item->sku); ?></span>
                    </td>
                    <td class="text-center font-monospace fw-bold text-dark" style="padding: 15px;"><?php echo $item->quantity; ?></td>
                    <td class="font-monospace text-dark" style="padding: 15px; direction:ltr; text-align:right;"><?php echo number_format($item->price, 2); ?></td>
                    <td class="font-monospace fw-bold text-dark" style="padding: 15px; direction:ltr; text-align:right;"><?php echo number_format($item->subtotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- المجاميع والضرائب -->
        <div style="display: flex; justify-content: space-between; margin-top: 30px;">
            <!-- مساحة للملاحظات -->
            <div style="width: 50%;">
                <h5 class="fw-bold text-dark mb-2" style="font-size: 14px;">ملاحظات وشروط الدفع:</h5>
                <p class="text-muted" style="font-size: 13px; line-height: 1.6;">
                    - البضاعة المباعة لا ترد ولا تستبدل بعد 14 يوماً من تاريخ الفاتورة.<br>
                    - الإجمالي الموضح يشمل ضريبة القيمة المضافة بنسبة <?php echo $taxRate; ?>%.
                </p>
            </div>
            
            <!-- جدول الإجماليات -->
            <div style="width: 350px;">
                <table style="width: 100%; font-size: 15px;">
                    <tr style="border-bottom: 1px dashed #cbd5e1;">
                        <td class="text-muted fw-bold py-2">المبلغ الخاضع للضريبة:</td>
                        <td class="font-monospace text-dark fw-bold text-left py-2" style="direction:ltr;"><?php echo number_format($amountBeforeTax, 2); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px dashed #cbd5e1;">
                        <td class="text-muted fw-bold py-2">ضريبة القيمة المضافة (<?php echo $taxRate; ?>%):</td>
                        <td class="font-monospace text-dark text-left py-2" style="direction:ltr;"><?php echo number_format($vatAmount, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold py-3" style="font-size: 18px; color: #0f172a;">الإجمالي شامل الضريبة:</td>
                        <td class="font-monospace fw-bold text-left py-3" style="font-size: 22px; color: #059669; direction:ltr;">
                            <?php echo number_format($grandTotal, 2); ?> <span style="font-size: 12px; color: var(--text-muted);">ر.س</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="text-center mt-5 pt-4 fw-bold" style="border-top: 1px solid #e2e8f0; font-size: 13px; color: #64748b;">
            نشكر لكم تسوقكم معنا. تم إصدار هذه الفاتورة إلكترونياً.
        </div>

    </div>
</div>

<!-- تضمين مكتبة رسم الـ QR Code -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // نص الـ Base64 الخاص بهيئة الزكاة والدخل
        const zatcaBase64 = "<?php echo $zatcaQrBase64; ?>";
        
        // رسم كود الـ QR
        new QRCode(document.getElementById("qrcode"), {
            text: zatcaBase64,
            width: 100,
            height: 100,
            colorDark : "#0f172a", // لون أسود داكن للاحترافية
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M
        });
    });
</script>

<style>
    @media print {
        body { background: #fff !important; }
        .d-print-none, .sidebar, .topbar { display: none !important; }
        .main-content { margin: 0 !important; }
        .card { box-shadow: none !important; border: none !important; max-width: 100% !important; margin: 0 !important;}
        .card-body { padding: 0 !important; }
        #qrcode { border: none !important; padding: 0 !important; }
    }
</style>