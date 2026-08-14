<?php
// app/views/payroll/index.php
$payrolls = $data['payrolls'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-money-check-dollar text-primary"></i> مسيرات الرواتب (Payroll)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">توليد رواتب الموظفين بشكل آلي مع حساب الاستقطاعات والغيابات.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" onclick="document.getElementById('generateForm').style.display='block';">
            <i class="fas fa-bolt"></i> توليد مسير آلي
        </button>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<!-- نموذج التوليد السريع المخفي -->
<div id="generateForm" class="card border-primary mb-4" style="display: none; box-shadow: var(--shadow-lg);">
    <div class="card-header bg-primary-light border-primary"><h3 class="card-title text-primary-dark mb-0"><i class="fas fa-cogs"></i> تحديد فترة المسير</h3></div>
    <form action="<?php echo URLROOT; ?>/payroll/generate" method="POST">
        <div class="card-body d-flex gap-3 align-items-end p-4">
            <div style="flex: 1;">
                <label class="form-label">الشهر</label>
                <select name="month" class="form-control fw-bold text-center">
                    <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $i == date('n') ? 'selected' : ''; ?>><?php echo date('F', mktime(0,0,0,$i,10)); ?> (<?php echo $i; ?>)</option>
                    <?php endfor; ?>
                </select>
            </div>
            <div style="flex: 1;">
                <label class="form-label">السنة</label>
                <input type="number" name="year" class="form-control fw-bold text-center font-monospace" value="<?php echo date('Y'); ?>" min="2020" max="2050">
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="height: 46px;"><i class="fas fa-rocket"></i> توليد الآن</button>
                <button type="button" class="btn btn-secondary" style="height: 46px;" onclick="document.getElementById('generateForm').style.display='none';">إلغاء</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th>رقم المرجع</th>
                    <th class="text-center">الفترة (شهر/سنة)</th>
                    <th class="text-center">عدد الموظفين</th>
                    <th class="text-left">صافي الرواتب (ر.س)</th>
                    <th class="text-center">الحالة</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payrolls as $p) : 
                    $statusClass = match($p->status) { 'approved' => 'badge-primary', 'paid' => 'badge-success', default => 'badge-secondary' };
                    $statusLabel = match($p->status) { 'approved' => 'معتمد للصرف', 'paid' => 'مصروف ومدفوع', default => 'مسودة (Draft)' };
                ?>
                <tr>
                    <td class="font-monospace fw-bold text-muted"><?php echo htmlspecialchars($p->reference_no); ?></td>
                    <td class="text-center font-monospace fw-bold fs-5 text-dark"><?php echo str_pad($p->month, 2, '0', STR_PAD_LEFT); ?> / <?php echo $p->year; ?></td>
                    <td class="text-center font-monospace text-muted fs-5"><i class="fas fa-users fs-6"></i> <?php echo $p->total_employees; ?></td>
                    <td class="font-monospace fw-bold text-success text-left fs-5" style="direction:ltr;">
                        <?php echo number_format($p->total_net_amount, 2); ?>
                    </td>
                    <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                    <td class="text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <a href="<?php echo URLROOT; ?>/payroll/show/<?php echo $p->id; ?>" class="btn-icon view text-primary" style="border-color:var(--primary);"><i class="fas fa-file-invoice"></i></a>
                            <?php if(Session::hasRole('admin')): ?>
                            <form action="<?php echo URLROOT; ?>/payroll/delete/<?php echo $p->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف مسير الرواتب بالكامل؟');">
                                <button type="submit" class="btn-icon delete" <?php echo $p->status === 'paid' ? 'disabled style="opacity:0.3;cursor:not-allowed;" title="لا يمكن حذف مسير مدفوع"' : ''; ?>><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if (empty($payrolls)) : ?>
                <tr><td colspan="6" class="text-center text-muted p-5"><i class="fas fa-money-check-dollar fs-1 mb-3 opacity-50 d-block"></i>لا توجد مسيرات رواتب مصدرة. اضغط على "توليد مسير آلي" للبدء.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>