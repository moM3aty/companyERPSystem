<?php
// المسار: app/views/employee_contracts/index.php
$contracts = $data['contracts'] ?? [];
$isAdmin = Session::hasRole('admin');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-contract text-primary"></i> سجل عقود الموظفين</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">توثيق وحفظ حقوق المنشأة والموظفين.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/employeeContract/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إبرام عقد جديد
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم العقد</th>
                        <th>الموظف المستفيد</th>
                        <th>تاريخ البداية</th>
                        <th>تاريخ الانتهاء</th>
                        <th class="text-center">حالة العقد</th>
                        <?php if ($isAdmin) : ?><th class="text-center">إجراء</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($contracts)): foreach ($contracts as $con) : 
                        $statusClass = match($con->status) {
                            'active' => 'badge-success',
                            'draft' => 'badge-secondary',
                            'expired' => 'badge-danger',
                            'terminated' => 'badge-warning',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($con->status) {
                            'active' => '<i class="fas fa-check-circle"></i> نشط وساري',
                            'draft' => '<i class="fas fa-file-pen"></i> مسودة',
                            'expired' => '<i class="far fa-clock"></i> منتهي الصلاحية',
                            'terminated' => '<i class="fas fa-ban"></i> مفسوخ / مُنهى',
                            default => $con->status
                        };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($con->contract_number); ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($con->employee_name ?? '—'); ?></div>
                            <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($con->title); ?></div>
                        </td>
                        <td class="text-muted fs-6"><i class="far fa-calendar-check text-success"></i> <?php echo date('Y-m-d', strtotime($con->start_date)); ?></td>
                        <td class="text-muted fs-6"><i class="far fa-calendar-times text-danger"></i> <?php echo date('Y-m-d', strtotime($con->end_date)); ?></td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        
                        <?php if ($isAdmin) : ?>
                        <td class="text-center">
                            <?php if ($con->status === 'active') : ?>
                                <form method="POST" action="<?php echo URLROOT; ?>/employeeContract/terminate/<?php echo $con->id; ?>" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من إنهاء وفسخ هذا العقد؟');">
                                    <button type="submit" class="btn-icon delete" title="إنهاء العقد"><i class="fas fa-ban"></i></button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted fs-6"><i class="fas fa-lock"></i> مقفل</span>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="<?php echo $isAdmin ? '6' : '5'; ?>" class="text-center text-muted" style="padding:40px;">لا توجد عقود مسجلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>