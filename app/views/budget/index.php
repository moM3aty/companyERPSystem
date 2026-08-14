<?php
// app/views/budget/index.php
$budgets = $data['budgets'] ?? [];
$selectedYear = $data['selected_year'] ?? date('Y');
$totalBudget = 0;
$totalSpent = 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-chart-pie text-warning"></i> الموازنة التقديرية (Budget)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تخصيص ومراقبة ميزانيات المصروفات للسنة المالية.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <form method="GET" action="<?php echo URLROOT; ?>/budget/index" class="d-flex gap-2">
            <select name="year" class="form-control fw-bold" onchange="this.form.submit()">
                <?php 
                $currentY = date('Y');
                for($y = $currentY - 2; $y <= $currentY + 2; $y++): ?>
                    <option value="<?php echo $y; ?>" <?php echo $selectedYear == $y ? 'selected' : ''; ?>>السنة المالية <?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
        </form>
        <a href="<?php echo URLROOT; ?>/budget/create" class="btn btn-warning fw-bold"><i class="fas fa-plus"></i> تخصيص موازنة</a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 align-middle text-center">
            <thead class="bg-light">
                <tr>
                    <th>تصنيف المصروف</th>
                    <th>الموازنة المعتمدة (SAR)</th>
                    <th>المنصرف الفعلي (SAR)</th>
                    <th>المتبقي (SAR)</th>
                    <th style="width: 25%;">مؤشر الاستهلاك</th>
                    <th class="d-print-none">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($budgets as $b): 
                    $totalBudget += $b->amount;
                    $totalSpent += $b->actual_spent;
                    $remaining = $b->amount - $b->actual_spent;
                    $percent = $b->amount > 0 ? min(100, round(($b->actual_spent / $b->amount) * 100, 1)) : 0;
                    
                    $barColor = 'bg-success';
                    if ($percent >= 90) $barColor = 'bg-danger';
                    elseif ($percent >= 75) $barColor = 'bg-warning';
                ?>
                <tr>
                    <td class="fw-bold text-dark text-right" style="text-align: right;"><i class="fas fa-tags text-muted me-1"></i> <?php echo htmlspecialchars($b->category_name ?? 'تصنيف غير معروف'); ?></td>
                    <td class="font-monospace fw-bold text-primary" style="direction:ltr;"><?php echo number_format($b->amount, 2); ?></td>
                    <td class="font-monospace fw-bold text-danger" style="direction:ltr;"><?php echo number_format($b->actual_spent, 2); ?></td>
                    <td class="font-monospace fw-black <?php echo $remaining < 0 ? 'text-danger' : 'text-success'; ?>" style="direction:ltr;">
                        <?php echo number_format($remaining, 2); ?>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar <?php echo $barColor; ?>" role="progressbar" style="width: <?php echo $percent; ?>%;"></div>
                            </div>
                            <small class="font-monospace fw-bold text-muted"><?php echo $percent; ?>%</small>
                        </div>
                    </td>
                    <td class="d-print-none">
                        <?php if(Session::hasRole('admin') || Session::hasRole('super_admin')): ?>
                        <form action="<?php echo URLROOT; ?>/budget/delete/<?php echo $b->id; ?>" method="POST" onsubmit="return confirm('تأكيد حذف هذه الموازنة؟');">
                            <button type="submit" class="btn-icon delete text-danger" style="border:none; background:none;"><i class="fas fa-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; if(empty($budgets)): ?>
                    <tr><td colspan="6" class="text-center text-muted p-5"><i class="fas fa-chart-pie fs-1 opacity-25 mb-3 d-block"></i>لم يتم تخصيص أي موازنات لهذه السنة.</td></tr>
                <?php endif; ?>
            </tbody>
            <?php if(!empty($budgets)): ?>
            <tfoot class="bg-slate-50">
                <tr>
                    <td class="fw-black text-right" style="text-align: right;">الإجمالي</td>
                    <td class="font-monospace fw-black fs-6" style="direction:ltr;"><?php echo number_format($totalBudget, 2); ?></td>
                    <td class="font-monospace fw-black fs-6 text-danger" style="direction:ltr;"><?php echo number_format($totalSpent, 2); ?></td>
                    <td class="font-monospace fw-black fs-6 <?php echo ($totalBudget - $totalSpent) < 0 ? 'text-danger' : 'text-success'; ?>" style="direction:ltr;">
                        <?php echo number_format($totalBudget - $totalSpent, 2); ?>
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>