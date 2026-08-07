<?php
// app/views/expenses/index.php
$expenses = $expenses ?? ($data['expenses'] ?? []);
$totalExpenses = $totalExpenses ?? ($data['total_expenses'] ?? 0);
$isAdmin = $isAdmin ?? ($data['is_admin'] ?? false);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-money-bill-transfer text-danger"></i> سجل المصروفات التشغيلية</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع المصروفات الإدارية والتشغيلية للمنشأة.</p>
    </div>
    <div>
        <a href="<?php echo URLROOT; ?>/expense/create" class="btn btn-danger">
            <i class="fas fa-plus"></i> تسجيل مصروف
        </a>
    </div>
</div>

<div class="card mb-4" style="background: linear-gradient(135deg, var(--danger-light), #fef2f2); border: 1px solid var(--danger); box-shadow: var(--shadow-sm);">
    <div class="card-body d-flex align-items-center gap-3">
        <div style="width: 50px; height: 50px; background: var(--danger); color: #fff; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
            <i class="fas fa-wallet"></i>
        </div>
        <div>
            <h4 style="margin: 0; font-size: 24px; font-weight: 800; color: var(--danger);" class="font-monospace text-right" style="direction:ltr;"><?php echo number_format($totalExpenses, 2); ?></h4>
            <span class="text-muted" style="font-size: 12px; font-weight: 700;">إجمالي المصروفات المسجلة (ر.س)</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>تاريخ المصروف</th>
                        <th>التصنيف</th>
                        <th class="text-left">المبلغ</th>
                        <th>البيان / الملاحظات</th>
                        <th>بواسطة</th>
                        <?php if ($isAdmin): ?><th class="text-center">إجراءات</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($expenses)): foreach($expenses as $exp): ?>
                    <tr>
                        <td class="text-muted fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($exp->expense_date)); ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($exp->category_name ?? 'غير مصنف'); ?></div>
                            <div style="font-size:11px; color:var(--text-muted);"><i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($exp->reference_no ?? 'بدون مرجع'); ?></div>
                        </td>
                        <td class="font-monospace fw-bold text-danger fs-5" style="direction:ltr; text-align:right;">
                            <?php echo number_format($exp->amount, 2); ?>
                        </td>
                        <td class="text-muted" style="font-size: 13px;">
                            <?php echo htmlspecialchars($exp->notes ?? '—'); ?>
                        </td>
                        <td class="text-muted fs-6"><i class="fas fa-user-pen"></i> <?php echo htmlspecialchars($exp->created_by_name ?? 'النظام'); ?></td>
                        
                        <?php if ($isAdmin): ?>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/expense/edit/<?php echo $exp->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <form action="<?php echo URLROOT; ?>/expense/delete/<?php echo $exp->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الحذف نهائياً؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="<?php echo $isAdmin ? '6' : '5'; ?>" class="text-center text-muted" style="padding: 40px;">لا توجد مصروفات مسجلة بعد.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>