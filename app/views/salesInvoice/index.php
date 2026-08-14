<?php
// app/views/salesInvoice/index.php
$invoices = $data['invoices'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-primary"></i> فواتير المبيعات الضريبية (Sales Invoices)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تُخصم الكميات من المخزون وتُنشئ قيود الإيرادات آلياً بمجرد الحفظ.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-print"></i> طباعة السجل</button>
        <a href="<?php echo URLROOT; ?>/salesInvoice/create" class="btn btn-primary"><i class="fas fa-plus"></i> إنشاء فاتورة مبيعات</a>
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
                    <th>رقم الفاتورة (INV)</th>
                    <th>العميل (Customer)</th>
                    <th class="text-center">تاريخ الإصدار</th>
                    <th class="text-left">الإجمالي المستحق</th>
                    <th class="text-center">حالة الدفع</th>
                    <th class="text-center d-print-none">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $inv): 
                    $statusClass = match($inv->payment_status) { 'Unpaid' => 'badge-danger', 'Partial' => 'badge-warning', 'Paid' => 'badge-success', default => 'badge-secondary' };
                    $statusLabel = match($inv->payment_status) { 'Unpaid' => 'آجلة (غير مدفوعة)', 'Partial' => 'دفع جزئي', 'Paid' => 'مدفوعة بالكامل', default => $inv->payment_status };
                ?>
                <tr>
                    <td>
                        <div class="font-monospace fw-bold text-primary"><?php echo htmlspecialchars($inv->invoice_number); ?></div>
                        <?php if($inv->so_number): ?>
                            <div class="text-muted font-monospace mt-1" style="font-size: 11px;">مستوردة من: <?php echo htmlspecialchars($inv->so_number); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="fw-bold"><i class="fas fa-user-circle text-muted me-1"></i> <?php echo htmlspecialchars($inv->customer_name ?? '—'); ?></td>
                    <td class="text-center font-monospace fs-6 text-muted"><?php echo $inv->invoice_date; ?></td>
                    <td class="text-left font-monospace fw-black text-dark" style="direction:ltr;"><?php echo number_format($inv->grand_total, 2); ?></td>
                    <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                    <td class="text-center d-print-none">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <a href="<?php echo URLROOT; ?>/salesInvoice/show/<?php echo $inv->id; ?>" class="btn-icon view text-primary" style="border-color:var(--primary);"><i class="fas fa-eye"></i></a>
                            <?php if(Session::hasRole('admin')): ?>
                            <form action="<?php echo URLROOT; ?>/salesInvoice/delete/<?php echo $inv->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد مسح الفاتورة؟ سيتم استرجاع المخزون وإلغاء القيد المحاسبي.');">
                                <button type="submit" class="btn-icon delete"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; if(empty($invoices)): ?>
                    <tr><td colspan="6" class="text-center text-muted p-5"><i class="fas fa-file-invoice-dollar fs-1 opacity-25 mb-3 d-block"></i>لا توجد فواتير مبيعات مسجلة.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>