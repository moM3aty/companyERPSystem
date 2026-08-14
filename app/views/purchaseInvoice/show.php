<?php
// app/views/purchaseInvoice/show.php
$inv = $data['invoice'] ?? null;
$items = $data['items'] ?? [];

$db = Database::getInstance();
$db->query("SELECT setting_key, setting_value FROM settings WHERE company_id = :cid OR company_id IS NULL");
$db->bind(':cid', Session::get('company_id') ?: 1);
$sysSettings = $db->resultSet();

$companyName = 'ERP Pro'; $companyVat = ''; $companyAddress = '';
foreach ($sysSettings as $s) {
    if ($s->setting_key === 'company_name' && !empty($s->setting_value)) $companyName = $s->setting_value;
    if ($s->setting_key === 'tax_number' && !empty($s->setting_value)) $companyVat = $s->setting_value;
    if ($s->setting_key === 'company_address' && !empty($s->setting_value)) $companyAddress = $s->setting_value;
}

$statusBadge = match($inv->payment_status) { 'Unpaid' => 'badge-danger', 'Partial' => 'badge-warning', 'Paid' => 'badge-success', default => 'badge-secondary' };
?>

<div class="card" style="max-width: 900px; margin: 0 auto; border: none; box-shadow: var(--shadow-md);">
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <div class="d-flex align-items-center gap-3">
            <h3 class="card-title text-dark mb-0"><i class="fas fa-file-invoice-dollar text-danger"></i> فاتورة مورد #<?php echo htmlspecialchars($inv->invoice_number); ?></h3>
            <span class="badge <?php echo $statusBadge; ?> fs-6"><?php echo $inv->payment_status; ?></span>
        </div>
        <div class="d-flex gap-2">
            <?php if($inv->payment_status !== 'Paid' && Session::hasRole('admin')): ?>
                <!-- 🟢 زر سيتم برمجته لاحقاً في قسم المدفوعات (Payment) 🟢 -->
                <button class="btn btn-danger btn-sm" onclick="alert('سيتوفر هذا الزر عند استكمال شاشة سندات الدفع.')"><i class="fas fa-money-bill"></i> صرف دفعة للمورد</button>
            <?php endif; ?>
            <button class="btn btn-dark btn-sm" onclick="window.print()"><i class="fas fa-print"></i> طباعة (PDF)</button>
            <a href="<?php echo URLROOT; ?>/purchaseInvoice/index" class="btn btn-secondary btn-sm">رجوع</a>
        </div>
    </div>

    <div class="card-body p-5 bg-white">
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 900; color: var(--danger-dark); margin-bottom: 5px;">فاتورة مشتريات</h1>
                <div class="text-muted font-monospace fs-5">PURCHASE INVOICE</div>
                <div class="font-monospace fw-bold text-dark mt-2" style="font-size: 16px;">Internal No: <?php echo htmlspecialchars($inv->invoice_number); ?></div>
            </div>
            <div class="text-left" style="direction: ltr; text-align: left;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-dark); margin-bottom: 5px;"><?php echo htmlspecialchars($companyName); ?></h2>
                <div class="text-muted fs-6">VAT: <span class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($companyVat); ?></span></div>
                <div class="text-muted fs-6 mt-1" style="max-width: 250px;"><?php echo htmlspecialchars($companyAddress); ?></div>
            </div>
        </div>

        <div class="row mb-5" style="display: flex; gap: 20px;">
            <div style="flex: 1; border: 1px solid var(--danger-light); border-radius: 8px; overflow: hidden; background: var(--danger-light);">
                <div class="p-2 fw-bold text-danger-dark border-bottom border-danger text-center">المورد (Supplier)</div>
                <div class="p-3 bg-white">
                    <h4 class="fw-bold text-danger mb-1"><i class="fas fa-building text-muted"></i> <?php echo htmlspecialchars($inv->supplier_name ?? '—'); ?></h4>
                    <div class="text-muted fs-6 font-monospace mb-1">VAT: <?php echo htmlspecialchars($inv->supplier_vat ?? '—'); ?></div>
                    <div class="font-monospace fw-black text-danger-dark mt-2" style="font-size: 16px;">فاتورة المورد: <?php echo htmlspecialchars($inv->supplier_invoice_no); ?></div>
                </div>
            </div>
            
            <div style="flex: 1; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
                <div class="bg-light p-2 fw-bold text-dark border-bottom text-center">المطابقة (3-Way Match)</div>
                <div class="p-3">
                    <table style="width: 100%; font-size: 13px;">
                        <tr><td class="text-muted fw-bold pb-2">تاريخ الفاتورة:</td><td class="font-monospace text-dark text-left pb-2" style="direction:ltr;"><?php echo $inv->invoice_date; ?></td></tr>
                        <tr><td class="text-muted fw-bold pb-2">تاريخ الاستحقاق:</td><td class="font-monospace text-danger text-left pb-2 fw-bold" style="direction:ltr;"><?php echo $inv->due_date; ?></td></tr>
                        <tr><td class="text-muted fw-bold pb-2">أمر الشراء (PO):</td><td class="font-monospace text-info text-left fw-bold pb-2" style="direction:ltr;"><?php echo htmlspecialchars($inv->po_number ?? '—'); ?></td></tr>
                        <tr><td class="text-muted fw-bold">الاستلام (GRN):</td><td class="font-monospace text-success text-left fw-bold" style="direction:ltr;"><?php echo htmlspecialchars($inv->grn_number ?? '—'); ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <table class="table" style="border: 1px solid var(--border-color); width: 100%;">
            <thead style="background: var(--dark); color:#fff;">
                <tr>
                    <th style="padding: 12px; color:#fff;">البيان (Description)</th>
                    <th class="text-center" style="padding: 12px; color:#fff;">الكمية</th>
                    <th style="padding: 12px; color:#fff; text-align:right;">سعر الوحدة</th>
                    <th style="padding: 12px; color:#fff; text-align:center;">الضريبة</th>
                    <th style="padding: 12px; color:#fff; text-align:right;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 15px;">
                        <strong class="text-dark"><?php echo htmlspecialchars($item->description); ?></strong>
                        <?php if($item->product_sku): ?>
                            <div class="text-muted font-monospace" style="font-size: 11px;">SKU: <?php echo htmlspecialchars($item->product_sku); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center font-monospace fw-bold text-dark" style="padding: 15px;"><?php echo $item->quantity; ?></td>
                    <td class="font-monospace text-muted" style="padding: 15px; direction:ltr; text-align:right;"><?php echo number_format($item->unit_price, 2); ?></td>
                    <td class="text-center font-monospace text-muted" style="padding: 15px;"><?php echo $item->tax_rate; ?>%</td>
                    <td class="font-monospace fw-bold text-danger" style="padding: 15px; direction:ltr; text-align:right;"><?php echo number_format($item->subtotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="display: flex; justify-content: space-between; margin-top: 30px;">
            <div style="width: 45%;">
                <div class="alert alert-success d-inline-flex px-3 py-2 fs-6">
                    <i class="fas fa-check-circle me-2"></i> المطابقة: <?php echo $inv->match_status; ?>
                </div>
                <?php if($inv->notes): ?>
                    <h6 class="fw-bold text-dark mt-3 mb-1">ملاحظات:</h6>
                    <div class="text-muted" style="font-size: 13px;"><?php echo nl2br(htmlspecialchars($inv->notes)); ?></div>
                <?php endif; ?>
            </div>
            
            <div style="width: 350px;">
                <table style="width: 100%; font-size: 14px;">
                    <tr><td class="text-muted fw-bold py-1">المجموع الفرعي:</td><td class="font-monospace text-dark text-left py-1" style="direction:ltr;"><?php echo number_format($inv->subtotal, 2); ?></td></tr>
                    <tr style="border-bottom: 1px dashed var(--border-color);"><td class="text-muted fw-bold py-1 pb-2">ضريبة المدخلات:</td><td class="font-monospace text-dark text-left py-1 pb-2" style="direction:ltr;"><?php echo number_format($inv->tax_amount, 2); ?></td></tr>
                    <tr>
                        <td class="fw-black py-3" style="font-size: 18px; color: var(--danger-dark);">المطلوب دفعه:</td>
                        <td class="font-monospace fw-black text-left py-3" style="font-size: 24px; color: var(--danger-dark); direction:ltr;">
                            <?php echo number_format($inv->grand_total, 2); ?> <span style="font-size: 12px; color: var(--text-muted);">SAR</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mt-5 pt-4 text-center text-muted" style="border-top: 1px solid var(--border-color); font-size: 12px;">
            تم إدخال هذه الفاتورة بواسطة <?php echo htmlspecialchars($inv->creator_name); ?> في <?php echo $inv->created_at; ?> وتم توليد القيد المحاسبي آلياً.
        </div>

    </div>
</div>

<style>
    @media print { body { background: #fff !important; } .d-print-none, .sidebar, .topbar { display: none !important; } .main-content { margin: 0 !important; } .card { box-shadow: none !important; border: none !important; max-width: 100% !important; margin: 0 !important;} .card-body { padding: 0 !important; } .table th, .table td { border: 1px solid #000 !important; } }
</style>