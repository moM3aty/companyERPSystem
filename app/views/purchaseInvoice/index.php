<?php
// app/views/purchaseInvoice/index.php
$invoices = $data['invoices'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-danger"></i> فواتير الموردين (Supplier Invoices)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">المطابقة الثلاثية، تسجيل الذمم الدائنة، والقيود المحاسبية الآلية.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-print"></i> طباعة السجل</button>
        <a href="<?php echo URLROOT; ?>/purchaseInvoice/create" class="btn btn-danger"><i class="fas fa-plus"></i> إدخال فاتورة مورد</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th>رقم الفاتورة (الداخلي)</th>
                    <th>رقم فاتورة المورد</th>
                    <th>المورد (Supplier)</th>
                    <th class="text-center">مرتبطة بـ (PO / GRN)</th>
                    <th class="text-left">الإجمالي المستحق</th>
                    <th class="text-center">حالة الدفع</th>
                    <th class="text-center d-print-none">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $inv): 
                    $statusClass = match($inv->payment_status) { 'Unpaid' => 'badge-danger', 'Partial' => 'badge-warning', 'Paid' => 'badge-success', default => 'badge-secondary' };
                    $statusLabel = match($inv->payment_status) { 'Unpaid' => 'غير مدفوعة', 'Partial' => 'دفع جزئي', 'Paid' => 'مدفوعة', default => $inv->payment_status };
                ?>
                <tr>
                    <td><div class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($inv->invoice_number); ?></div></td>
                    <td><span class="badge badge-secondary font-monospace"><?php echo htmlspecialchars($inv->supplier_invoice_no ?? '—'); ?></span></td>
                    <td class="fw-bold"><i class="fas fa-building text-muted me-1"></i> <?php echo htmlspecialchars($inv->supplier_name ?? '—'); ?></td>
                    <td class="text-center font-monospace" style="font-size:11px;">
                        <?php if($inv->po_number): ?><div class="text-primary mb-1">PO: <?php echo htmlspecialchars($inv->po_number); ?></div><?php endif; ?>
                        <?php if($inv->grn_number): ?><div class="text-success">GRN: <?php echo htmlspecialchars($inv->grn_number); ?></div><?php endif; ?>
                        <?php if(!$inv->po_number && !$inv->grn_number): ?><span class="text-muted">—</span><?php endif; ?>
                    </td>
                    <td class="text-left font-monospace fw-black text-danger fs-5" style="direction:ltr;"><?php echo number_format($inv->grand_total, 2); ?></td>
                    <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                    <td class="text-center d-print-none">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <a href="<?php echo URLROOT; ?>/purchaseInvoice/show/<?php echo $inv->id; ?>" class="btn-icon view text-danger" style="border-color:var(--danger);"><i class="fas fa-eye"></i></a>
                            <?php if(Session::hasRole('admin')): ?>
                            <form action="<?php echo URLROOT; ?>/purchaseInvoice/delete/<?php echo $inv->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد مسح الفاتورة وعكس رصيد المورد؟');">
                                <button type="submit" class="btn-icon delete"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; if(empty($invoices)): ?>
                    <tr><td colspan="7" class="text-center text-muted p-5"><i class="fas fa-file-invoice-dollar fs-1 opacity-25 mb-3 d-block"></i>لا توجد فواتير موردين مسجلة.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>