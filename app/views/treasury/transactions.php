<?php
// app/views/treasury/transactions.php
$transactions = $data['transactions'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-list-check text-primary"></i> السجل المالي</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">جميع سندات القبض والصرف التي أثرت على الخزائن.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/treasury/createTransaction" class="btn btn-primary">
        <i class="fas fa-plus"></i> تسجيل حركة مالية
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم المعاملة</th>
                        <th>التاريخ</th>
                        <th>الخزنة / البنك</th>
                        <th>البيان والمرجع</th>
                        <th class="text-center">النوع</th>
                        <th>المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($transactions)): foreach($transactions as $t): 
                        $isReceipt = $t->transaction_type === 'receipt';
                        $typeClass = $isReceipt ? 'badge-success' : 'badge-danger';
                        $typeIcon = $isReceipt ? '<i class="fas fa-arrow-down"></i> سند قبض' : '<i class="fas fa-arrow-up"></i> سند صرف';
                        $amountColor = $isReceipt ? 'text-success' : 'text-danger';
                        $amountSign = $isReceipt ? '+' : '-';
                    ?>
                    <tr>
                        <td class="font-monospace text-muted fw-bold">TRX-<?php echo str_pad($t->id, 5, '0', STR_PAD_LEFT); ?></td>
                        <td class="text-muted fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($t->transaction_date)); ?></td>
                        <td class="fw-bold text-dark"><i class="fas fa-vault text-primary me-1"></i> <?php echo htmlspecialchars($t->treasury_name); ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($t->description); ?></div>
                            <?php if($t->reference): ?>
                                <span class="badge badge-secondary mt-1"><i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($t->reference); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><span class="badge <?php echo $typeClass; ?>"><?php echo $typeIcon; ?></span></td>
                        <td class="font-monospace fw-bold fs-5 <?php echo $amountColor; ?>" style="direction: ltr; text-align: right;">
                            <?php echo $amountSign . number_format($t->amount, 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding: 40px;">لا توجد حركات مالية مسجلة بعد.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>