<?php
// app/views/payment/index.php
$payments = $data['payments'] ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-money-bill-transfer text-danger"></i> سندات الصرف والقبض (Payments & Receipts)</h3>
    </div>
    <a href="<?php echo URLROOT; ?>/payment/create" class="btn btn-danger"><i class="fas fa-plus"></i> إصدار سند جديد</a>
</div>

<?php $flash = Session::getFlash();
if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th>رقم السند</th>
                    <th class="text-center">النوع</th>
                    <th>التاريخ</th>
                    <th>الصندوق/البنك</th>
                    <th>الجهة (المورد/العميل)</th>
                    <th class="text-left">المبلغ</th>
                    <th class="text-center">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $p):
                    $typeClass = $p->payment_type == 'Out' ? 'badge-danger' : 'badge-success';
                    $typeLabel = $p->payment_type == 'Out' ? 'سند صرف (دفعة لمورد)' : 'سند قبض (تحصيل من عميل)';
                    $amtClass = $p->payment_type == 'Out' ? 'text-danger' : 'text-success';
                    $party = $p->supplier_name ?: $p->customer_name ?: 'أخرى';
                ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($p->voucher_number); ?></td>
                        <td class="text-center"><span class="badge <?php echo $typeClass; ?>"><?php echo $typeLabel; ?></span></td>
                        <td class="font-monospace text-muted fs-6"><?php echo $p->payment_date; ?></td>
                        <td class="fw-bold"><i class="fas fa-vault text-muted me-1"></i> <?php echo htmlspecialchars($p->treasury_name); ?></td>
                        <td><?php echo htmlspecialchars($party); ?></td>
                        <td class="font-monospace fw-black <?php echo $amtClass; ?> fs-5 text-left" style="direction:ltr;"><?php echo number_format($p->amount, 2); ?></td>
                        <td class="text-center"><a href="<?php echo URLROOT; ?>/payment/show/<?php echo $p->id; ?>" class="btn-icon view"><i class="fas fa-eye"></i></a>
                            <!-- ضع هذا الكود بجانب زر (view) داخل حلقة foreach -->
                            <?php if (Session::getUserRole() === 'admin' || Session::getUserRole() === 'super_admin'): ?>
                                <form action="<?php echo URLROOT; ?>/payment/delete/<?php echo $p->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا السند؟ سيتم عكس المبالغ في الخزنة وحساب العميل/المورد تلقائياً.');">
                                    <button type="submit" class="btn-icon delete text-danger" style="border:none; background:none; cursor:pointer;" title="حذف السند">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach;
                if (empty($payments)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted p-5">لا توجد سندات مسجلة.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>