<?php
// app/views/payroll/create.php
$currentMonth = date('n');
$currentYear = date('Y');
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-cogs text-success"></i> توليد مسير رواتب جديد</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/payroll/create" method="POST" id="payrollForm">
        <div class="card-body">
            <div class="alert alert-info mb-4" style="font-size: 13px;">
                <i class="fas fa-info-circle"></i> سيقوم النظام آلياً بجلب رواتب الموظفين الأساسية وخصم السلف والجزاءات המعتمدة لهذا الشهر لتوليد الصافي النهائي لكل موظف.
            </div>

            <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group mb-0">
                    <label class="form-label text-primary">الشهر المالي <span class="required">*</span></label>
                    <select name="month" class="form-control fw-bold font-monospace text-center fs-5" required>
                        <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo $m == $currentMonth ? 'selected' : ''; ?>><?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label text-primary">السنة <span class="required">*</span></label>
                    <input type="number" name="year" class="form-control font-monospace fw-bold text-center fs-5" value="<?php echo $currentYear; ?>" required>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <i class="fas fa-robot text-muted fa-3x opacity-25"></i>
                <p class="text-muted mt-2 font-monospace" style="font-size: 11px;">Nour HR Payroll Engine Engine v1.0</p>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-success" id="btnSubmit"><i class="fas fa-bolt"></i> ابدأ الحساب والتوليد</button>
            <a href="<?php echo URLROOT; ?>/payroll/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('payrollForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري جمع البيانات والحساب...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
    });
</script>