<?php
// app/views/expenseClaim/show.php
$c = $data['claim'] ?? null;
$treasuries = $data['treasuries'] ?? [];
$isAdmin = Session::hasRole('admin') || Session::hasRole('manager');
?>
<div class="card" style="max-width: 800px; margin: 0 auto; box-shadow: var(--shadow-md); border:none;">
    <div class="card-header bg-light d-flex justify-content-between align-items-center border-bottom">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-receipt text-warning"></i> مطالبة #<?php echo htmlspecialchars($c->claim_number); ?></h3>
        <a href="<?php echo URLROOT; ?>/expenseClaim/index" class="btn btn-secondary btn-sm">رجوع</a>
    </div>
    
    <div class="card-body p-4 bg-white">
        <div class="row mb-4">
            <div class="col-md-6 border-end">
                <div class="text-muted mb-1">الموظف:</div>
                <h4 class="fw-bold text-dark mb-3"><i class="fas fa-user-tie text-primary"></i> <?php echo htmlspecialchars($c->employee_name); ?></h4>
                <div class="text-muted mb-1">النوع: <span class="fw-bold text-dark"><?php echo htmlspecialchars($c->expense_type); ?></span></div>
                <div class="text-muted mb-1">التاريخ: <span class="font-monospace text-dark"><?php echo $c->claim_date; ?></span></div>
                <?php if($c->project_name): ?><div class="text-muted mb-1">المشروع: <span class="badge badge-info"><?php echo htmlspecialchars($c->project_name); ?></span></div><?php endif; ?>
            </div>
            <div class="col-md-6 px-4">
                <div class="text-muted mb-2">إجمالي المطالبة (شامل الضريبة):</div>
                <div class="font-monospace fw-black text-danger" style="font-size: 32px; direction:ltr;"><?php echo number_format($c->amount + $c->vat_amount, 2); ?> <span class="fs-6 text-muted">SAR</span></div>
                <div class="mt-3 p-3 bg-light rounded text-dark" style="border-right: 4px solid var(--warning); font-size:13px;"><?php echo nl2br(htmlspecialchars($c->business_purpose)); ?></div>
            </div>
        </div>

        <div class="border-top pt-4">
            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-sitemap"></i> مسار الاعتمادات (Workflow)</h5>
            
            <!-- Manager Approval -->
            <div class="p-3 mb-3 border rounded d-flex justify-content-between align-items-center <?php echo $c->manager_approval == 'Approved' ? 'bg-success-light border-success' : 'bg-light'; ?>">
                <div>
                    <h6 class="fw-bold text-dark mb-1">1. اعتماد مدير القسم (Manager)</h6>
                    <span class="badge <?php echo $c->manager_approval == 'Approved' ? 'badge-success' : 'badge-warning'; ?>"><?php echo $c->manager_approval; ?></span>
                </div>
                <?php if($isAdmin && $c->manager_approval == 'Pending'): ?>
                    <form action="<?php echo URLROOT; ?>/expenseClaim/approve/<?php echo $c->id; ?>" method="POST">
                        <input type="hidden" name="level" value="manager">
                        <button type="submit" name="status" value="Approved" class="btn btn-success btn-sm"><i class="fas fa-check"></i> موافقة</button>
                        <button type="submit" name="status" value="Rejected" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> رفض</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Finance Approval & Payment -->
            <div class="p-3 border rounded <?php echo $c->finance_approval == 'Approved' ? 'bg-primary-light border-primary' : 'bg-light'; ?>">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">2. اعتماد المالية والصرف (Finance)</h6>
                        <span class="badge <?php echo $c->finance_approval == 'Approved' ? 'badge-primary' : 'badge-warning'; ?>">
                            <?php echo $c->finance_approval == 'Approved' ? 'تم الصرف (Paid)' : $c->finance_approval; ?>
                        </span>
                    </div>
                </div>
                
                <?php if($isAdmin && $c->manager_approval == 'Approved' && $c->finance_approval == 'Pending'): ?>
                    <form action="<?php echo URLROOT; ?>/expenseClaim/approve/<?php echo $c->id; ?>" method="POST" class="border-top pt-3 mt-2">
                        <input type="hidden" name="level" value="finance">
                        <input type="hidden" name="status" value="Approved">
                        <label class="form-label fw-bold text-danger">صرف العهدة للموظف من: <span class="required">*</span></label>
                        <div class="d-flex gap-2">
                            <select name="treasury_id" class="form-control fw-bold" required>
                                <option value="">-- اختر البنك / الصندوق للخصم --</option>
                                <?php foreach($treasuries as $t): ?>
                                    <option value="<?php echo $t->id; ?>"><?php echo htmlspecialchars($t->name); ?> (متاح: <?php echo number_format($t->current_balance, 2); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary px-4" onclick="return confirm('سيتم خصم المبلغ من الخزنة وإنشاء القيد تلقائياً. تأكيد؟');"><i class="fas fa-money-bill-wave"></i> صرف وإنهاء</button>
                        </div>
                    </form>
                <?php elseif($c->finance_approval == 'Approved'): ?>
                    <div class="alert alert-success m-0 mt-2 py-2"><i class="fas fa-check-circle"></i> تم صرف المبلغ وتوليد القيد المحاسبي من حساب: <strong><?php echo htmlspecialchars($c->treasury_name); ?></strong></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>