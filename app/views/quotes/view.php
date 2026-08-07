<?php
// المسار: app/views/quotes/view.php
$quote = $quote ?? ($data['quote'] ?? null);
$items = $items ?? ($data['items'] ?? []);
$taxRate = 15; // الضريبة
$taxAmount = $quote->total_amount * ($taxRate / 100);
$grandTotal = $quote->total_amount + $taxAmount;
?>

<div class="card" style="max-width: 900px; margin: 0 auto; border: none; box-shadow: var(--shadow-md);">
    
    <!-- شريط الأزرار العلوي (يختفي عند الطباعة) -->
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <div class="d-flex align-items-center gap-3">
            <h3 class="card-title text-dark mb-0"><i class="fas fa-file-signature text-primary"></i> عرض سعر #<?php echo htmlspecialchars($quote->quote_number); ?></h3>
            <?php 
                $statusBadge = match($quote->status) {
                    'draft' => 'badge-secondary', 'sent' => 'badge-info', 'accepted' => 'badge-success', 'rejected' => 'badge-danger', default => 'badge-secondary'
                };
                $statusLabel = match($quote->status) {
                    'draft' => 'مسودة', 'sent' => 'مُرسل للعميل', 'accepted' => 'مقبول', 'rejected' => 'مرفوض', default => $quote->status
                };
            ?>
            <span class="badge <?php echo $statusBadge; ?> fs-6"><?php echo $statusLabel; ?></span>
        </div>
        
        <div class="d-flex gap-2">
            <form action="<?php echo URLROOT; ?>/quote/changeStatus/<?php echo $quote->id; ?>" method="POST" class="d-flex gap-2">
                <select name="status" class="form-control form-control-sm" onchange="this.form.submit()" style="width: auto;">
                    <option value="" disabled selected>تغيير الحالة...</option>
                    <option value="sent">تحديد كـ (مُرسل)</option>
                    <option value="accepted">تحديد كـ (مقبول)</option>
                    <option value="rejected">تحديد كـ (مرفوض)</option>
                </select>
            </form>
            <button class="btn btn-dark btn-sm" onclick="window.print()"><i class="fas fa-print"></i> طباعة / PDF</button>
            <a href="<?php echo URLROOT; ?>/quote/index" class="btn btn-secondary btn-sm">رجوع</a>
        </div>
    </div>

    <!-- ورقة عرض السعر (Printable Area) -->
    <div class="card-body p-5 bg-white" style="border-radius: 0 0 var(--radius-md) var(--radius-md);">
        
        <!-- الترويسة -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <h1 style="font-size: 32px; font-weight: 900; color: var(--primary-dark); margin-bottom: 5px;">عرض سعر</h1>
                <div class="text-muted font-monospace fs-5">QUOTATION</div>
            </div>
            <div class="text-left" style="direction: ltr; text-align: left;">
                <h2 style="font-size: 24px; font-weight: 900; color: var(--text-dark); margin-bottom: 5px;">ERP Pro Inc.</h2>
                <div class="text-muted fs-6">
                    الرياض، المملكة العربية السعودية<br>
                    هاتف: +966 500 000 000<br>
                    info@erppro.com
                </div>
            </div>
        </div>

        <!-- معلومات العميل والعرض -->
        <div class="row mb-5" style="display: flex; justify-content: space-between;">
            <div style="width: 48%;">
                <div class="text-muted fs-6 fw-bold text-uppercase mb-2">مقدم إلى العميل:</div>
                <div class="fs-4 fw-bold text-dark mb-1"><?php echo htmlspecialchars($quote->customer_name); ?></div>
                <div class="text-muted fs-6"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($quote->address ?? 'العنوان غير مسجل'); ?></div>
                <div class="text-muted fs-6 mt-1"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($quote->phone ?? 'الهاتف غير مسجل'); ?></div>
            </div>
            <div style="width: 48%; text-align: left; background: #f8fafc; padding: 15px; border-radius: 8px;">
                <table style="width: 100%; font-size: 14px;">
                    <tr>
                        <td class="text-muted fw-bold pb-2">رقم العرض:</td>
                        <td class="font-monospace fw-bold text-dark text-left pb-2" style="direction:ltr;"><?php echo htmlspecialchars($quote->quote_number); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold pb-2">تاريخ الإصدار:</td>
                        <td class="font-monospace text-dark text-left pb-2" style="direction:ltr;"><?php echo date('Y-m-d', strtotime($quote->created_at)); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold pb-2">صالح لغاية:</td>
                        <td class="font-monospace text-dark text-left pb-2" style="direction:ltr;"><?php echo date('Y-m-d', strtotime($quote->created_at . ' + 30 days')); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold">المندوب:</td>
                        <td class="text-dark text-left fw-bold"><?php echo htmlspecialchars($quote->creator_name ?? 'الإدارة'); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- جدول الأصناف -->
        <table class="table" style="border: 1px solid var(--border-color); width: 100%;">
            <thead style="background: var(--primary-dark); color: #fff;">
                <tr>
                    <th style="padding: 12px; color: #fff;">البيان / الصنف</th>
                    <th class="text-center" style="padding: 12px; color: #fff;">الكمية</th>
                    <th style="padding: 12px; color: #fff;">سعر الوحدة</th>
                    <th style="padding: 12px; color: #fff;">الإجمالي الفرعي</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 15px;">
                        <strong class="text-dark"><?php echo htmlspecialchars($item->product_name); ?></strong>
                        <div class="text-muted font-monospace" style="font-size: 11px;">SKU: <?php echo htmlspecialchars($item->sku); ?></div>
                    </td>
                    <td class="text-center font-monospace fw-bold" style="padding: 15px;"><?php echo $item->quantity; ?></td>
                    <td class="font-monospace" style="padding: 15px; direction:ltr; text-align:right;"><?php echo number_format($item->unit_price, 2); ?></td>
                    <td class="font-monospace fw-bold text-dark" style="padding: 15px; direction:ltr; text-align:right;"><?php echo number_format($item->subtotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- المجاميع -->
        <div style="display: flex; justify-content: flex-end; margin-top: 30px;">
            <div style="width: 350px;">
                <table style="width: 100%; font-size: 15px;">
                    <tr style="border-bottom: 1px dashed var(--border-color);">
                        <td class="text-muted fw-bold py-2">الإجمالي (قبل الضريبة):</td>
                        <td class="font-monospace text-dark fw-bold text-left py-2" style="direction:ltr;"><?php echo number_format($quote->total_amount, 2); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px dashed var(--border-color);">
                        <td class="text-muted fw-bold py-2">ضريبة القيمة المضافة (<?php echo $taxRate; ?>%):</td>
                        <td class="font-monospace text-dark text-left py-2" style="direction:ltr;"><?php echo number_format($taxAmount, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold py-3" style="font-size: 18px; color: var(--primary-dark);">الإجمالي المستحق:</td>
                        <td class="font-monospace fw-bold text-left py-3" style="font-size: 22px; color: var(--primary-dark); direction:ltr;">
                            <?php echo number_format($grandTotal, 2); ?> <span style="font-size: 12px; color: var(--text-muted);">ر.س</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- الشروط والأحكام -->
        <div class="mt-5 p-4" style="background: #f8fafc; border-radius: 8px; border-right: 4px solid var(--primary);">
            <h5 class="fw-bold text-dark mb-2" style="font-size: 14px;">الشروط والأحكام:</h5>
            <ul class="text-muted mb-0" style="font-size: 13px; padding-right: 20px; margin: 0;">
                <li>هذا العرض صالح لمدة 30 يوماً من تاريخ الإصدار.</li>
                <li>الأسعار المذكورة أعلاه تشمل ضريبة القيمة المضافة ما لم يُذكر خلاف ذلك.</li>
                <li>التسليم خلال 5 أيام عمل من تاريخ اعتماد أمر الشراء أو توقيع العقد.</li>
            </ul>
        </div>
        
        <div class="text-center mt-5 pt-4 text-muted" style="border-top: 1px solid var(--border-color); font-size: 12px;">
            <p>نشكركم على ثقتكم في التعامل معنا. لأي استفسارات، يرجى التواصل على info@erppro.com</p>
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
    }
</style>