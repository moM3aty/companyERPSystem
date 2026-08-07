<?php
// المسار: app/views/payments/index.php
$payments = $payments ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-hand-holding-dollar text-primary"></i> السجل العام للمقبوضات والمدفوعات</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع تسديدات العملاء ومدفوعات الموردين المرتبطة بالفواتير.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/payment/createReceipt" class="btn btn-success">
            <i class="fas fa-arrow-down"></i> سند قبض (تحصيل مبيعات)
        </a>
        <a href="<?php echo URLROOT; ?>/payment/createPayment" class="btn btn-danger">
            <i class="fas fa-arrow-up"></i> سند صرف (سداد مشتريات)
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>النوع</th>
                        <th>رقم المرجع (الفاتورة/الأمر)</th>
                        <th>الطرف (عميل/مورد)</th>
                        <th class="text-center">طريقة الدفع</th>
                        <th class="text-left">المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($payments)): foreach($payments as $p): 
                        $isReceipt = $p->reference_type === 'invoice';
                        $typeClass = $isReceipt ? 'badge-success' : 'badge-danger';
                        $typeLabel = $isReceipt ? '<i class="fas fa-arrow-down"></i> قبض (من عميل)' : '<i class="fas fa-arrow-up"></i> صرف (لمورد)';
                        $amountColor = $isReceipt ? 'text-success' : 'text-danger';
                        $amountSign = $isReceipt ? '+' : '-';
                        
                        $methodLabel = match($p->payment_method) {
                            'cash' => 'نقدي', 'bank_transfer' => 'تحويل بنكي', 'check' => 'شيك', default => $p->payment_method
                        };
                    ?>
                    <tr>
                        <td class="text-muted fs-6"><i class="far fa-clock"></i> <?php echo date('Y-m-d H:i', strtotime($p->created_at)); ?></td>
                        <td><span class="badge <?php echo $typeClass; ?>"><?php echo $typeLabel; ?></span></td>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($p->ref_number ?? '—'); ?></td>
                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($p->party_name ?? '—'); ?></td>
                        <td class="text-center text-muted fs-6"><?php echo $methodLabel; ?></td>
                        <td class="font-monospace fw-bold fs-5 <?php echo $amountColor; ?>" style="direction:ltr; text-align:right;">
                            <?php echo $amountSign . number_format($p->amount, 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding: 40px;">لا توجد حركات سداد مسجلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>