<?php
// app/views/collections/index.php
$collections = $data['collections'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-hand-holding-dollar text-success"></i> سجل التحصيلات (Receipts)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع المبالغ المحصلة من العملاء والمسددة للفواتير.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/collection/create" class="btn btn-success">
        <i class="fas fa-plus"></i> تسجيل تحصيل جديد
    </a>
</div>

<?php 
    $flash = Session::getFlash();
    if ($flash) : 
?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>رقم السند</th>
                        <th>رقم الفاتورة</th>
                        <th>الخزنة / البنك</th>
                        <th class="text-center">طريقة الدفع</th>
                        <th class="text-left">المبلغ المُحصّل</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($collections)): foreach($collections as $col): 
                        // معالجة القيم في حالة كانت المصفوفة كائن (Object) أو مصفوفة (Array)
                        $receiptNum = is_object($col) ? $col->receipt_number : $col['receipt_number'];
                        $invoiceNum = is_object($col) ? $col->invoice_number : $col['invoice_number'];
                        $treasuryName = is_object($col) ? $col->treasury_name : $col['treasury_name'];
                        $payMethod = is_object($col) ? $col->payment_method : $col['payment_method'];
                        $amount = is_object($col) ? $col->amount : $col['amount'];
                        $colDate = is_object($col) ? $col->collection_date : $col['collection_date'];

                        $methodLabel = match($payMethod) {
                            'cash' => 'نقدي', 'bank_transfer' => 'تحويل بنكي', 'check' => 'شيك', default => $payMethod
                        };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($receiptNum); ?></td>
                        <td class="font-monospace fw-bold text-primary"><?php echo htmlspecialchars($invoiceNum ?? '—'); ?></td>
                        <td class="fw-bold"><i class="fas fa-vault text-muted me-1"></i> <?php echo htmlspecialchars($treasuryName ?? '—'); ?></td>
                        <td class="text-center"><span class="badge badge-secondary"><?php echo $methodLabel; ?></span></td>
                        <td class="font-monospace fw-bold text-success fs-5 text-left" style="direction:ltr;">
                            +<?php echo number_format($amount, 2); ?>
                        </td>
                        <td class="text-muted fs-6 font-monospace"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($colDate)); ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted p-5"><i class="fas fa-receipt fa-3x mb-3 opacity-50 d-block"></i> لا توجد عمليات تحصيل مسجلة بعد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>