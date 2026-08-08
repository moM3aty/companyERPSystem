<?php
// app/views/salesOrder/show.php
$order = $data['order'] ?? null;
$items = $data['items'] ?? [];

if (!$order) {
    echo "<div class='alert alert-danger m-4'>أمر البيع غير متاح.</div>";
    return;
}

// --- جلب بيانات الشركة للترويسة ---
$db = Database::getInstance();
$cid = Session::get('company_id') ?: 1;
$db->query("SELECT setting_key, setting_value FROM settings WHERE company_id = :cid OR company_id IS NULL");
$db->bind(':cid', $cid);
$sysSettings = $db->resultSet();

$companyName = 'اسم الشركة';
$vatNumber = 'غير مسجل';
$taxRate = 15;

foreach ($sysSettings as $s) {
    if ($s->setting_key === 'company_name' && !empty($s->setting_value)) $companyName = $s->setting_value;
    if (in_array($s->setting_key, ['tax_number', 'vat_number']) && !empty($s->setting_value)) $vatNumber = $s->setting_value;
    if ($s->setting_key === 'tax_rate' && is_numeric($s->setting_value)) $taxRate = (float)$s->setting_value;
}

$subTotal = (float)$order->total_amount;
$taxAmount = $subTotal * ($taxRate / 100);
$grandTotal = $subTotal + $taxAmount;
?>

<div class="card" style="max-width: 900px; margin: 0 auto; border: none; box-shadow: var(--shadow-md);">
    
    <!-- شريط الأزرار (يختفي عند الطباعة) -->
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <div class="d-flex align-items-center gap-3">
            <h3 class="card-title text-dark mb-0"><i class="fas fa-file-contract text-primary"></i> أمر بيع (Sales Order)</h3>
            <?php 
                $statusBadge = match($order->status) {
                    'draft' => 'badge-secondary', 'approved' => 'badge-success', 'sent' => 'badge-info', 'cancelled' => 'badge-danger', default => 'badge-secondary'
                };
                $statusLabel = match($order->status) {
                    'draft' => 'مسودة', 'approved' => 'معتمد', 'sent' => 'مُرسل للعميل', 'cancelled' => 'ملغي', default => $order->status
                };
            ?>
            <span class="badge <?php echo $statusBadge; ?> fs-6"><?php echo $statusLabel; ?></span>
        </div>
        
        <div class="d-flex gap-2">
            <button class="btn btn-dark btn-sm" onclick="window.print()"><i class="fas fa-print"></i> طباعة / PDF</button>
            <a href="<?php echo URLROOT; ?>/salesOrder/index" class="btn btn-secondary btn-sm">رجوع</a>
        </div>
    </div>

    <!-- ورقة أمر البيع -->
    <div class="card-body p-5 bg-white" style="border-radius: 0 0 var(--radius-md) var(--radius-md);">
        
        <!-- الترويسة -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 900; color: var(--primary-dark); margin-bottom: 5px;">أمر بيع</h1>
                <div class="text-muted font-monospace fs-5">SALES ORDER</div>
            </div>
            
            <div class="text-left" style="direction: ltr; text-align: left;">
                <h2 style="font-size: 24px; font-weight: 900; color: var(--text-dark); margin-bottom: 5px;"><?php echo htmlspecialchars($companyName); ?></h2>
                <div class="text-muted fs-6">
                    الرقم الضريبي: <span class="font-monospace text-dark fw-bold"><?php echo htmlspecialchars($vatNumber); ?></span>
                </div>
            </div>
        </div>

        <!-- تفاصيل العميل والأمر -->
        <div class="row mb-5" style="display: flex; justify-content: space-between;">
            <div style="width: 48%;">
                <div class="text-muted fs-6 fw-bold text-uppercase mb-2">مطلوب إلى العميل:</div>
                <div class="fs-4 fw-bold text-dark mb-1"><i class="fas fa-user-circle text-muted"></i> <?php echo htmlspecialchars($order->customer_name ?? 'عميل غير مسجل'); ?></div>
                <div class="text-muted fs-6"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($order->address ?? 'العنوان غير مسجل'); ?></div>
                <div class="text-muted fs-6 mt-1"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order->phone ?? '—'); ?></div>
            </div>
            <div style="width: 48%; text-align: left; background: #f8fafc; padding: 15px; border-radius: 8px;">
                <table style="width: 100%; font-size: 14px;">
                    <tr>
                        <td class="text-muted fw-bold pb-2">رقم أمر البيع:</td>
                        <td class="font-monospace fw-bold text-dark text-left pb-2" style="direction:ltr;"><?php echo htmlspecialchars($order->order_number); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold pb-2">تاريخ الإصدار:</td>
                        <td class="font-monospace text-dark text-left pb-2" style="direction:ltr;"><?php echo date('Y-m-d', strtotime($order->created_at)); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold">أنشئ بواسطة:</td>
                        <td class="text-dark text-left fw-bold"><?php echo htmlspecialchars($order->creator_name ?? 'النظام'); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- جدول المنتجات -->
        <table class="table" style="border: 1px solid var(--border-color); width: 100%;">
            <thead style="background: var(--slate-100);">
                <tr>
                    <th style="padding: 12px; color: var(--slate-700);">المنتج / الوصف</th>
                    <th class="text-center" style="padding: 12px; color: var(--slate-700);">الكمية</th>
                    <th style="padding: 12px; color: var(--slate-700); text-align:left;">سعر الوحدة</th>
                    <th style="padding: 12px; color: var(--slate-700); text-align:left;">الإجمالي الفرعي</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 15px;">
                        <strong class="text-dark"><?php echo htmlspecialchars($item->product_name ?? 'منتج غير معروف'); ?></strong>
                        <div class="text-muted font-monospace" style="font-size: 11px;">SKU: <?php echo htmlspecialchars($item->sku ?? '—'); ?></div>
                    </td>
                    <td class="text-center font-monospace fw-bold" style="padding: 15px;"><?php echo $item->quantity; ?></td>
                    <td class="font-monospace" style="padding: 15px; direction:ltr; text-align:left;"><?php echo number_format($item->unit_price, 2); ?></td>
                    <td class="font-monospace fw-bold text-dark" style="padding: 15px; direction:ltr; text-align:left;"><?php echo number_format($item->subtotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($items)): ?>
                    <tr><td colspan="4" class="text-center text-muted p-4">لا توجد أصناف في هذا الأمر.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- المجاميع -->
        <div style="display: flex; justify-content: space-between; margin-top: 30px;">
            <div style="width: 50%;">
                <?php if(!empty($order->notes)): ?>
                    <h6 class="fw-bold text-dark mb-2">ملاحظات:</h6>
                    <p class="text-muted" style="font-size: 13px; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($order->notes)); ?></p>
                <?php endif; ?>
            </div>
            <div style="width: 350px;">
                <table style="width: 100%; font-size: 15px;">
                    <tr style="border-bottom: 1px dashed var(--border-color);">
                        <td class="text-muted fw-bold py-2">الإجمالي (قبل الضريبة):</td>
                        <td class="font-monospace text-dark fw-bold text-left py-2" style="direction:ltr;"><?php echo number_format($subTotal, 2); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px dashed var(--border-color);">
                        <td class="text-muted fw-bold py-2">ضريبة القيمة المضافة (<?php echo $taxRate; ?>%):</td>
                        <td class="font-monospace text-dark text-left py-2" style="direction:ltr;"><?php echo number_format($taxAmount, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold py-3" style="font-size: 18px; color: var(--primary-dark);">إجمالي الأمر:</td>
                        <td class="font-monospace fw-bold text-left py-3" style="font-size: 22px; color: var(--success-dark); direction:ltr;">
                            <?php echo number_format($grandTotal, 2); ?> <span style="font-size: 12px; color: var(--text-muted);">ر.س</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="text-center mt-5 pt-4 text-muted" style="border-top: 1px solid var(--border-color); font-size: 12px;">
            <p>هذه الوثيقة عبارة عن أمر بيع داخلي / موجه للعميل، ولا تُعتبر فاتورة ضريبية نهائية حتى يتم تحويلها والإقرار بها.</p>
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