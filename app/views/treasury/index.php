<?php
// app/views/treasury/index.php
$treasuries =$data['treasuries'] ?? [];
$transactions =$data['transactions'] ?? [];

$totalBalance = 0;
foreach ($treasuries as$t) {
    $totalBalance += (float)$t->balance;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-vault text-primary"></i> إدارة الخزينة والبنوك</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">متابعة الأرصدة وإدارة حركات الإيداع والسحب لجميع الخزن.</p>
    </div>
    <div class="bg-white px-4 py-2 rounded border shadow-sm text-center">
        <span class="text-muted fw-bold d-block" style="font-size:11px;">إجمالي السيولة النقدية</span>
        <span class="font-monospace fs-4 fw-bold text-success" style="direction:ltr;"><?php echo number_format($totalBalance, 2); ?> ر.س</span>
    </div>
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

<div class="content-grid" style="grid-template-columns: 1fr 2fr; align-items: start;">
    
    <!-- الجانب الأيمن: الخزن ونموذج الإضافة -->
    <div>
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h3 class="card-title"><i class="fas fa-wallet text-info"></i> الخزن والبنوك المتاحة</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" style="border-radius:0;">
                    <?php foreach($treasuries as$t): 
                        $icon =$t->type === 'bank' ? 'fa-building-columns text-primary' : 'fa-cash-register text-success';
                    ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas <?php echo $icon; ?> fs-3"></i>
                                <div>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($t->name); ?></div>
                                    <div class="text-muted font-monospace" style="font-size:12px;"><?php echo $t->type === 'bank' ? 'حساب بنكي' : 'خزنة كاش'; ?></div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="font-monospace fw-bold fs-5 <?php echo $t->balance < 0 ? 'text-danger' : 'text-success'; ?>" style="direction:ltr;">
                                    <?php echo number_format($t->balance, 2); ?>
                                </div>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/treasury/delete/<?php echo $t->id; ?>" method="POST" class="mt-1" onsubmit="return confirm('تأكيد حذف الخزنة؟');">
                                    <button type="submit" class="btn-icon delete" style="width:20px;height:20px;font-size:10px;"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <?php if(Session::hasRole('admin')): ?>
        <div class="card mb-0 bg-light border-dashed">
            <form action="<?php echo URLROOT; ?>/treasury/create" method="POST" class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-plus-circle text-primary"></i> إضافة خزنة/بنك جديد</h6>
                <div class="form-group mb-2">
                    <input type="text" name="name" class="form-control" placeholder="اسم الخزنة / البنك" required>
                </div>
                <div class="form-grid mb-2" style="grid-template-columns:1fr 1fr;">
                    <select name="type" class="form-control">
                        <option value="cash">خزنة كاش</option>
                        <option value="bank">حساب بنكي</option>
                    </select>
                    <input type="number" step="0.01" name="balance" class="form-control" placeholder="رصيد افتتاحي" value="0">
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-2">حفظ الخزنة</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- الجانب الأيسر: الحركات (سحب وإيداع) -->
    <div class="card mb-0 h-100">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h3 class="card-title"><i class="fas fa-money-bill-transfer text-success"></i> حركة السيولة (إيداع / سحب)</h3>
        </div>
        
        <!-- نموذج الإيداع والسحب السريع -->
        <div class="card-body bg-white border-bottom pb-4 mb-3">
            <form action="<?php echo URLROOT; ?>/treasury/createTransaction" method="POST" class="form-grid" style="grid-template-columns: 2fr 1.5fr 1.5fr 1fr; align-items: end;">
                <div class="form-group mb-0">
                    <label class="form-label" style="font-size:11px;">الخزنة / البنك</label>
                    <select name="treasury_id" class="form-control" required>
                        <option value="">-- اختر --</option>
                        <?php foreach($treasuries as$t): ?>
                            <option value="<?php echo $t->id; ?>"><?php echo htmlspecialchars($t->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label" style="font-size:11px;">نوع الحركة</label>
                    <select name="type" class="form-control fw-bold" required>
                        <option value="deposit">إيداع (+)</option>
                        <option value="withdrawal">سحب (-)</option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label" style="font-size:11px;">المبلغ (ر.س)</label>
                    <input type="number" step="0.01" name="amount" class="form-control font-monospace fw-bold" required placeholder="0.00" style="direction:ltr;">
                </div>
                <button type="submit" class="btn btn-success w-100 h-100"><i class="fas fa-check"></i> تنفيذ</button>
                
                <div class="form-group full-width mt-2 mb-0">
                    <input type="text" name="notes" class="form-control bg-light" placeholder="اكتب بياناً أو وصفاً لسبب هذه الحركة..." style="font-size:12px;">
                </div>
            </form>
        </div>

        <!-- جدول الحركات -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>التاريخ</th>
                            <th>الخزنة / البنك</th>
                            <th class="text-center">الحركة</th>
                            <th class="text-left">المبلغ</th>
                            <th>البيان</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($transactions as$tr): 
                            $isDeposit =$tr->type === 'deposit';
                            $typeLabel =$isDeposit ? 'إيداع' : 'سحب';
                            $typeClass =$isDeposit ? 'badge-success' : 'badge-danger';
                            $amtClass =$isDeposit ? 'text-success' : 'text-danger';
                            $sign =$isDeposit ? '+' : '-';
                        ?>
                        <tr>
                            <td class="text-muted font-monospace" style="font-size:12px;"><i class="far fa-clock"></i> <?php echo date('Y-m-d H:i', strtotime($tr->created_at)); ?></td>
                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($tr->treasury_name); ?></td>
                            <td class="text-center"><span class="badge <?php echo $typeClass; ?>"><?php echo $typeLabel; ?></span></td>
                            <td class="font-monospace fw-bold fs-5 <?php echo $amtClass; ?>" style="direction:ltr; text-align:left;">
                                <?php echo $sign . number_format($tr->amount, 2); ?>
                            </td>
                            <td class="text-muted" style="font-size:12px; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo htmlspecialchars($tr->notes ?? ''); ?>">
                                <?php echo htmlspecialchars($tr->notes ?: '—'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($transactions)): ?>
                        <tr><td colspan="5" class="text-center text-muted p-5">لا توجد حركات مالية مسجلة بعد.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>