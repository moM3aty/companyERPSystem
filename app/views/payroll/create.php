<?php
// app/views/payroll/create.php
$employees = $data['employees'] ?? [];
$selMonth = $data['selected_month'] ?? date('n');
$selYear = $data['selected_year'] ?? date('Y');
?>

<!-- شريط اختيار الشهر والسنة لجلب الاستقطاعات -->
<div class="card mb-4" style="background: var(--slate-50); border: 1px solid var(--border-color);">
    <div class="card-body">
        <form action="<?php echo URLROOT; ?>/payroll/create" method="GET" class="d-flex align-items-end gap-3 flex-wrap">
            <div style="flex:1; min-width:200px;">
                <label class="form-label text-muted mb-1"><i class="fas fa-calendar-alt"></i> جلب السلف والغيابات لشهر:</label>
                <select name="month" class="form-control" onchange="this.form.submit()">
                    <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $i == $selMonth ? 'selected' : ''; ?>>شهر <?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div style="flex:1; min-width:200px;">
                <label class="form-label text-muted mb-1"><i class="fas fa-calendar-check"></i> السنة:</label>
                <input type="number" name="year" class="form-control font-monospace" value="<?php echo $selYear; ?>" onchange="this.form.submit()">
            </div>
            <div style="margin-bottom: 4px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-sync"></i> تحديث وجلب الاستقطاعات</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-success text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-money-check-dollar"></i> اعتماد مسير الرواتب (<?php echo $selMonth.'/'.$selYear; ?>)</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/payroll/create" method="POST" id="payrollForm">
        <input type="hidden" name="month" value="<?php echo $selMonth; ?>">
        <input type="hidden" name="year" value="<?php echo $selYear; ?>">

        <div class="card-body">
            <div class="alert alert-info mb-4">
                <i class="fas fa-robot"></i> تم جلب السلف المعتمدة، والجزاءات، وأيام الغياب آلياً بناءً على سجلات النظام للشهر المحدد. يمكنك تعديلها يدوياً إذا دعت الحاجة.
                <br><strong>ملاحظة:</strong> عند الاعتماد سيتم إنشاء قيد محاسبي تلقائي وخصم السلف من سجل الموظفين.
            </div>

            <div class="table-responsive">
                <table class="table border rounded" id="payrollTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">تضمين</th>
                            <th>الموظف</th>
                            <th style="width: 15%;">الأساسي (ر.س)</th>
                            <th style="width: 25%;">استقطاعات (غياب/سلف/جزاء)</th>
                            <th style="width: 15%;">مكافآت وإضافي</th>
                            <th style="width: 15%;">الصافي (Net)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($employees as $emp): 
                            $autoDed = $emp->auto_deduction ?? 0;
                            $netInitial = max(0, $emp->salary - $autoDed);
                        ?>
                        <tr class="emp-row">
                            <td class="text-center align-middle">
                                <input type="checkbox" name="emp_ids[]" value="<?php echo $emp->id; ?>" class="form-check-input row-check" checked onchange="toggleRow(this)" style="width:18px; height:18px; cursor:pointer;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($emp->name); ?></div>
                                <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($emp->position ?? '—'); ?></div>
                            </td>
                            <td class="align-middle">
                                <input type="number" name="base_salaries[]" class="form-control font-monospace base-input" value="<?php echo $emp->salary; ?>" readonly style="background:transparent; border:none; color:var(--text-dark); font-weight:bold; outline:none; text-align:right;">
                            </td>
                            <td class="align-middle">
                                <div class="input-group d-flex flex-column gap-1">
                                    <input type="number" name="deductions[]" step="0.01" min="0" class="form-control font-monospace ded-input text-danger w-100" value="<?php echo number_format($autoDed, 2, '.', ''); ?>" oninput="calcRow(this)" style="text-align:right;">
                                    <?php if($autoDed > 0): ?>
                                    <div style="font-size:10px; color:var(--danger); line-height:1.4;">
                                        <?php if($emp->advances_val > 0) echo "• سلف: {$emp->advances_val}<br>"; ?>
                                        <?php if($emp->sanctions_val > 0) echo "• جزاءات: {$emp->sanctions_val}<br>"; ?>
                                        <?php if($emp->absences_val > 0) echo "• غياب ({$emp->absent_days} يوم): " . number_format($emp->absences_val, 2) . "<br>"; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <input type="number" name="bonuses[]" step="0.01" min="0" class="form-control font-monospace bon-input text-success" value="0.00" oninput="calcRow(this)" style="text-align:right;">
                            </td>
                            <td class="align-middle">
                                <input type="text" name="net_salaries[]" class="form-control font-monospace net-input text-primary fw-bold fs-5" value="<?php echo number_format($netInitial, 2, '.', ''); ?>" readonly style="background:transparent; border:none; text-align:right; padding:0;">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-light border-top">
                        <tr>
                            <td colspan="5" class="text-left fw-bold text-dark" style="padding: 20px; font-size:16px;">إجمالي المسير المطلوب (قيمة القيد):</td>
                            <td style="padding: 20px;"><span id="grandTotal" class="font-monospace fs-4 fw-bold text-success">0.00</span> <small class="text-muted">ر.س</small></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-success" id="submitBtn"><i class="fas fa-check-double"></i> اعتماد وإصدار المسير</button>
            <a href="<?php echo URLROOT; ?>/payroll/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    function toggleRow(checkbox) {
        const row = checkbox.closest('tr');
        const inputs = row.querySelectorAll('input[type="number"]');
        if (checkbox.checked) {
            row.style.opacity = '1';
            row.style.background = '';
            inputs.forEach(inp => { if(!inp.classList.contains('base-input')) inp.disabled = false; });
        } else {
            row.style.opacity = '0.4';
            row.style.background = '#f8fafc';
            inputs.forEach(inp => { if(!inp.classList.contains('base-input')) inp.disabled = true; });
        }
        calcGrandTotal();
    }

    function calcRow(el) {
        const row = el.closest('tr');
        if (!row.querySelector('.row-check').checked) return;

        const base = parseFloat(row.querySelector('.base-input').value) || 0;
        const ded = parseFloat(row.querySelector('.ded-input').value) || 0;
        const bon = parseFloat(row.querySelector('.bon-input').value) || 0;
        
        let net = base + bon - ded;
        if (net < 0) net = 0;

        row.querySelector('.net-input').value = net.toFixed(2);
        calcGrandTotal();
    }

    function calcGrandTotal() {
        let total = 0;
        document.querySelectorAll('.emp-row').forEach(row => {
            if (row.querySelector('.row-check').checked) {
                total += parseFloat(row.querySelector('.net-input').value) || 0;
            }
        });
        document.getElementById('grandTotal').textContent = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits:2});
    }

    // 초기 الحساب عند التحميل
    calcGrandTotal();

    document.getElementById('payrollForm').addEventListener('submit', function() {
        // تمكين الحقول قبل الإرسال حتى تصل في הـ POST
        document.querySelectorAll('.net-input, .base-input').forEach(inp => inp.disabled = false);
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري توليد القيود...';
        btn.style.pointerEvents = 'none';
    });
</script>