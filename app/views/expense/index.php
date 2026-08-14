<?php
// app/views/expense/index.php
$expenses = $data['expenses'] ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-danger"></i> المصروفات التشغيلية (Expenses)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة ومراقبة المصروفات النقدية المسددة من الصناديق والبنوك.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-print"></i> طباعة السجل</button>
        <a href="<?php echo URLROOT; ?>/expense/create" class="btn btn-danger"><i class="fas fa-plus"></i> تسجيل مصروف جديد</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 table-hover align-middle text-center">
            <thead class="bg-light">
                <tr>
                    <th>التاريخ</th>
                    <th>الخزنة / البنك</th>
                    <th>التصنيف</th>
                    <th>البيان والملاحظات</th>
                    <th class="text-left">الإجمالي (شامل الضريبة)</th>
                    <th>المسؤول</th>
                    <th class="text-center d-print-none">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($expenses as $e): ?>
                <tr>
                    <td class="font-monospace text-muted"><?php echo $e->expense_date; ?></td>
                    <td class="fw-bold text-dark"><i class="fas fa-vault text-muted me-1"></i> <?php echo htmlspecialchars($e->treasury_name ?? '—'); ?></td>
                    <td><span class="badge badge-secondary"><?php echo htmlspecialchars($e->category_name ?? $e->category ?? 'مصروف'); ?></span></td>
                    <td>
                        <div class="text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($e->notes); ?>">
                            <?php echo htmlspecialchars($e->notes); ?>
                        </div>
                    </td>
                    <td class="text-left font-monospace fw-black text-danger fs-5" style="direction:ltr;">
                        <?php echo number_format($e->total_amount, 2); ?>
                    </td>
                    <td><small class="text-muted"><?php echo htmlspecialchars($e->user_name); ?></small></td>
                    <td class="text-center d-print-none">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <a href="<?php echo URLROOT; ?>/expense/show/<?php echo $e->id; ?>" class="btn-icon view text-primary" style="border-color:var(--primary);"><i class="fas fa-eye"></i></a>
                            <?php if(Session::hasRole('admin') || Session::hasRole('super_admin')): ?>
                            <form action="<?php echo URLROOT; ?>/expense/delete/<?php echo $e->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد مسح المصروف؟ سيتم استرجاع المبلغ للخزنة وإلغاء القيد.');">
                                <button type="submit" class="btn-icon delete"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; if(empty($expenses)): ?>
                    <tr><td colspan="7" class="text-center text-muted p-5"><i class="fas fa-file-invoice-dollar fs-1 opacity-25 mb-3 d-block"></i>لا توجد مصروفات مسجلة.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>