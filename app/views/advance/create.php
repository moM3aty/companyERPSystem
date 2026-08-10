<?php
// app/views/advance/create.php
$employees =$data['employees'] ?? [];
$currentMonth = date('n');$currentYear = date('Y');
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-hand-holding-dollar text-primary"></i> طلب سلفة نقدية</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/advance/create" method="POST">
        <div class="card-body">
            <div class="alert alert-info mb-4" style="font-size: 13px;">
                <i class="fas fa-info-circle"></i> سيتم توجيه هذا الطلب للإدارة المالية / الموارد البشرية للاعتماد قبل الصرف.
            </div>

            <div class="form-grid" style="grid-template-columns: 1fr;">
                
                <div class="form-group">
                    <label class="form-label">الموظف صاحب الطلب <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        <?php foreach($employees as$emp): ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الطلب <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group border border-primary p-3 rounded bg-light">
                    <label class="form-label text-primary">مبلغ السلفة المطلوب (ر.س) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="1" name="amount" class="form-control font-monospace fw-bold text-center text-primary fs-4" placeholder="0.00" required style="direction:ltr;">
                </div>

                <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
                    <div class="form-group mb-0">
                        <label class="form-label text-danger">شهر الخصم (من الراتب) <span class="required">*</span></label>
                        <select name="deduction_month" class="form-control fw-bold font-monospace" required>
                            <?php for($m=1; $m<=12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo $m ==$currentMonth ? 'selected' : ''; ?>><?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label text-danger">سنة الخصم <span class="required">*</span></label>
                        <input type="number" name="deduction_year" class="form-control font-monospace fw-bold" value="<?php echo $currentYear; ?>" required>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label class="form-label">مبررات طلب السلفة</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="اذكر سبب طلب السلفة (ظروف طارئة، سفر...الخ)"></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> إرسال الطلب للاعتماد</button>
            <a href="<?php echo URLROOT; ?>/advance/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>