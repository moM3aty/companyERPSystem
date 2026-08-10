<?php
// app/views/payroll/index.php
$payrolls =$data['payrolls'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-money-check-dollar text-success"></i> مسيرات الرواتب (Payroll)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">توليد واعتماد وصرف رواتب الموظفين الشهرية بشكل آلي.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/payroll/create" class="btn btn-success">
        <i class="fas fa-cogs"></i> توليد مسير رواتب آلياً
    </a>
</div>

<?php 
    $flash = Session::getFlash();
    if ($flash): 
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
                        <th>المرجع (رقم المسير)</th>
                        <th class="text-center">الشهر والسنة</th>
                        <th class="text-center">عدد الموظفين</th>
                        <th class="text-left">إجمالي الرواتب (ر.س)</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payrolls as$pay) : 
                        $statusClass = match($pay->status) {
                            'draft' => 'badge-secondary',
                            'approved' => 'badge-warning',
                            'paid' => 'badge-success',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($pay->status) {
                            'draft' => 'مسودة (مراجعة)',
                            'approved' => 'معتمد (جاهز للصرف)',
                            'paid' => 'تم الصرف',
                            default => $pay->status
                        };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($pay->reference_no); ?></td>
                        <td class="text-center font-monospace fw-bold text-primary">
                            <?php echo str_pad($pay->month, 2, '0', STR_PAD_LEFT) . ' / ' .$pay->year; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info fs-6"><i class="fas fa-users"></i> <?php echo $pay->total_employees; ?></span>
                        </td>
                        <td class="font-monospace fw-bold text-success text-left fs-5" style="direction:ltr;">
                            <?php echo number_format($pay->total_net_amount, 2); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/payroll/show/<?php echo $pay->id; ?>" class="btn-icon view text-success" style="border-color:var(--success);" title="عرض التفاصيل والطباعة"><i class="fas fa-file-invoice"></i></a>
                                
                                <?php if($pay->status === 'draft' && Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/payroll/delete/<?php echo $pay->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه المسودة؟ يمكنك إعادة توليدها لاحقاً.');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($payrolls)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-money-bills fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد مسيرات رواتب مصدرة بعد.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>