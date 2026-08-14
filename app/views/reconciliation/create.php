<?php
// app/views/reconciliation/create.php
$banks = $data['banks'] ?? [];
$selectedBank = $data['selected_bank'] ?? null;
$statementDate = $data['statement_date'] ?? null;
$transactions = $data['transactions'] ?? [];

$systemBalance = $selectedBank ? $selectedBank->current_balance : 0;
?>

<div class="card mb-4 border-primary">
    <div class="card-header bg-primary-light border-primary"><h3 class="card-title text-primary-dark mb-0"><i class="fas fa-filter"></i> تحديد البنك وفترة كشف الحساب</h3></div>
    <div class="card-body bg-light">
        <form action="<?php echo URLROOT; ?>/reconciliation/create" method="GET" class="d-flex gap-3 align-items-end">
            <div style="flex: 1;">
                <label class="form-label">اختر الحساب البنكي</label>
                <select name="bank_id" class="form-control fw-bold" required>
                    <option value="">-- البنوك المسجلة --</option>
                    <?php foreach($banks as $b): ?>
                        <option value="<?php echo $b->id; ?>" <?php echo ($selectedBank && $selectedBank->id == $b->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="flex: 1;">
                <label class="form-label">تاريخ نهاية كشف الحساب الفعلي</label>
                <input type="date" name="date" class="form-control font-monospace" value="<?php echo htmlspecialchars($statementDate); ?>" required>
            </div>
            <div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> جلب العمليات المعلقة</button>
            </div>
        </form>
    </div>
</div>

<?php if($selectedBank): ?>
<form action="<?php echo URLROOT; ?>/reconciliation/create" method="POST" id="recForm">
    <input type="hidden" name="bank_id" value="<?php echo $selectedBank->id; ?>">
    <input type="hidden" name="statement_date" value="<?php echo htmlspecialchars($statementDate); ?>">
    
    <div class="row">
        <!-- جدول العمليات -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><h5 class="fw-bold text-dark m-0"><i class="fas fa-list"></i> العمليات غير المسواة (Uncleared Transactions)</h5></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-center align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>مطابق؟</th>
                                <th>التاريخ</th>
                                <th>النوع</th>
                                <th>البيان والمرجع</th>
                                <th>مقبوضات (+)</th>
                                <th>مدفوعات (-)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($transactions as $t): 
                                $isIn = $t['type'] == 'In';
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input clear-checkbox" style="width: 20px; height: 20px;" 
                                           data-amount="<?php echo $t['amount']; ?>" 
                                           data-type="<?php echo $t['type']; ?>" onchange="calculateReconciliation()">
                                           
                                    <!-- حقول مخفية تُرسل فقط إذا تم التحديد -->
                                    <input type="hidden" class="hidden-input" name="cleared_source[]" value="<?php echo $t['source']; ?>" disabled>
                                    <input type="hidden" class="hidden-input" name="cleared_id[]" value="<?php echo $t['id']; ?>" disabled>
                                    <input type="hidden" class="hidden-input" name="cleared_amount[]" value="<?php echo $t['amount']; ?>" disabled>
                                    <input type="hidden" class="hidden-input" name="cleared_type[]" value="<?php echo $t['type']; ?>" disabled>
                                </td>
                                <td class="font-monospace text-muted" style="font-size:12px;"><?php echo $t['date']; ?></td>
                                <td><span class="badge <?php echo $isIn ? 'badge-success' : 'badge-danger'; ?>"><?php echo $t['source']; ?></span></td>
                                <td class="text-right" style="font-size: 13px;">
                                    <strong><?php echo htmlspecialchars($t['ref']); ?></strong><br>
                                    <span class="text-muted"><?php echo htmlspecialchars($t['desc'] ?? ''); ?></span>
                                </td>
                                <td class="font-monospace text-success fw-bold"><?php echo $isIn ? number_format($t['amount'], 2) : '-'; ?></td>
                                <td class="font-monospace text-danger fw-bold"><?php echo !$isIn ? number_format($t['amount'], 2) : '-'; ?></td>
                            </tr>
                            <?php endforeach; if(empty($transactions)): ?>
                                <tr><td colspan="6" class="p-5 text-muted">لا توجد عمليات معلقة لهذا البنك قبل هذا التاريخ.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- أداة المطابقة والميزان -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-primary h-100 position-sticky" style="top: 20px;">
                <div class="card-header bg-primary text-white text-center py-3"><h5 class="fw-bold m-0 text-white"><i class="fas fa-calculator"></i> ميزان التسوية</h5></div>
                <div class="card-body bg-light">
                    
                    <div class="mb-4">
                        <label class="form-label text-dark fw-bold">الرصيد الفعلي في كشف حساب البنك (ر.س) <span class="required">*</span></label>
                        <input type="number" step="0.01" name="statement_balance" id="statementBalance" class="form-control font-monospace fs-4 text-center border-primary" required placeholder="0.00" oninput="calculateReconciliation()" style="direction:ltr;">
                    </div>

                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted fw-bold">رصيد النظام الحالي (ERP):</span>
                        <input type="hidden" name="system_balance" value="<?php echo $systemBalance; ?>">
                        <span class="font-monospace fw-bold text-dark fs-5" style="direction:ltr;"><?php echo number_format($systemBalance, 2); ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-success fw-bold">إجمالي المقبوضات غير المودعة (+):</span>
                        <span class="font-monospace fw-bold text-success fs-5" id="sumIn" style="direction:ltr;">0.00</span>
                    </div>

                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-danger fw-bold">إجمالي الشيكات/الحوالات المعلقة (-):</span>
                        <span class="font-monospace fw-bold text-danger fs-5" id="sumOut" style="direction:ltr;">0.00</span>
                    </div>

                    <div class="d-flex justify-content-between mb-4 p-2 rounded bg-white border">
                        <span class="text-primary fw-black fs-5">الفرق (Difference):</span>
                        <input type="hidden" name="difference" id="hiddenDifference" value="0">
                        <span class="font-monospace fw-black fs-4 text-danger" id="displayDifference" style="direction:ltr;">0.00</span>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">ملاحظات التسوية</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>

                    <button type="submit" id="btnSubmit" class="btn btn-primary w-100 fw-bold py-2 disabled" style="cursor: not-allowed;"><i class="fas fa-check-double"></i> اعتماد وحفظ التسوية</button>
                    <div id="warnText" class="text-danger text-center mt-2" style="font-size:11px; font-weight:bold;">لا يمكن الحفظ إلا إذا كان الفرق (صفر). قم بتعليم العمليات المعلقة لمطابقتها.</div>

                </div>
            </div>
        </div>
    </div>
</form>

<script>
function calculateReconciliation() {
    let sysBal = <?php echo $systemBalance; ?>;
    let statBal = parseFloat(document.getElementById('statementBalance').value) || 0;
    
    let sumIn = 0;
    let sumOut = 0;

    document.querySelectorAll('.clear-checkbox').forEach(cb => {
        let tr = cb.closest('tr');
        // تفعيل المدخلات المخفية إذا تم التحديد للإرسال مع الـ Form
        tr.querySelectorAll('.hidden-input').forEach(input => {
            input.disabled = !cb.checked;
        });

        // إذا كانت العملية غير مسواة (أي لم نعلم عليها صح)، نأخذها في الحسبان لمعادلة الرصيد الدفتري
        if (!cb.checked) {
            let amt = parseFloat(cb.dataset.amount);
            if (cb.dataset.type === 'In') sumIn += amt;
            else sumOut += amt;
        }
    });

    document.getElementById('sumIn').innerText = sumIn.toFixed(2);
    document.getElementById('sumOut').innerText = sumOut.toFixed(2);

    // معادلة التسوية: رصيد كشف البنك + الإيداعات المعلقة - الشيكات المعلقة = رصيد النظام المتوقع
    let expectedSysBal = statBal + sumIn - sumOut;
    let diff = sysBal - expectedSysBal;

    document.getElementById('hiddenDifference').value = diff.toFixed(2);
    let diffDisplay = document.getElementById('displayDifference');
    diffDisplay.innerText = Math.abs(diff).toFixed(2);

    const btn = document.getElementById('btnSubmit');
    const warn = document.getElementById('warnText');

    if (Math.abs(diff) < 0.01 && statBal > 0) {
        diffDisplay.className = "font-monospace fw-black fs-4 text-success";
        btn.classList.remove('disabled');
        btn.style.cursor = 'pointer';
        warn.style.display = 'none';
    } else {
        diffDisplay.className = "font-monospace fw-black fs-4 text-danger";
        btn.classList.add('disabled');
        btn.style.cursor = 'not-allowed';
        warn.style.display = 'block';
    }
}
window.onload = calculateReconciliation;
</script>
<?php endif; ?>