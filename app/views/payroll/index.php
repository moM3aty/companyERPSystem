<?php
// app/views/payroll/index.php
$payrolls = $payrolls ?? ($data['payrolls'] ?? []);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-money-check-dollar text-success"></i> سجل مسيرات الرواتب</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">توثيق ومتابعة رواتب الموظفين الشهرية.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/payroll/create" class="btn btn-success">
        <i class="fas fa-plus"></i> إصدار مسير جديد
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم المسير (Ref)</th>
                        <th class="text-center">الشهر / السنة</th>
                        <th class="text-center">عدد الموظفين</th>
                        <th>إجمالي الرواتب الصافية</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">عرض</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($payrolls)): foreach($payrolls as $pay): ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($pay->reference_no); ?></td>
                        <td class="text-center fw-bold text-primary">
                            <?php echo $pay->month . ' / ' . $pay->year; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-secondary fs-6"><i class="fas fa-users"></i> <?php echo $pay->total_employees; ?></span>
                        </td>
                        <td class="font-monospace fw-bold text-success fs-5" style="direction:ltr; text-align:right;">
                            <?php echo number_format($pay->total_net_amount, 2); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-success"><i class="fas fa-check-double"></i> معتمد ومُرحّل</span>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo URLROOT; ?>/payroll/show/<?php echo $pay->id; ?>" class="btn-icon view" title="عرض التفاصيل والكشف">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding: 40px;">لا توجد مسيرات رواتب مصدرة بعد.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>