<?php
// app/views/journal/create.php
$accounts = $data['accounts'] ?? [];
?>
<div class="card">
    <div class="card-header bg-light"><h3 class="card-title text-dark"><i class="fas fa-balance-scale text-success"></i> إنشاء قيد يومية (Double-Entry)</h3></div>
    <form action="<?php echo URLROOT; ?>/journal/create" method="POST" id="journalForm">
        <div class="card-body">
            <div class="form-grid mb-4">
                <div class="form-group"><label class="form-label">تاريخ القيد <span class="required">*</span></label><input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
                <div class="form-group full-width"><label class="form-label">بيان القيد (Description) <span class="required">*</span></label><input type="text" name="description" class="form-control" required placeholder="وصف عام لسبب القيد..."></div>
            </div>

            <table class="table table-bordered" id="entryTable">
                <thead class="bg-light">
                    <tr><th>الحساب (Account)</th><th>البيان (سطر)</th><th>المركز المالي (Cost Center)</th><th class="text-success text-center">مدين (Debit)</th><th class="text-danger text-center">دائن (Credit)</th><th></th></tr>
                </thead>
                <tbody id="itemsBody">
                    <!-- سطرين افتراضيين للقيد المزدوج -->
                    <?php for($i=0; $i<2; $i++): ?>
                    <tr>
                        <td>
                            <select name="account_id[]" class="form-control" required>
                                <option value="">-- اختر الحساب --</option>
                                <?php foreach($accounts as $acc): ?><option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->account_code.' - '.$acc->account_name); ?></option><?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="text" name="line_desc[]" class="form-control form-control-sm"></td>
                        <td><input type="text" name="cost_center[]" class="form-control form-control-sm"></td>
                        <td><input type="number" step="0.01" name="debit[]" class="form-control form-control-sm text-center debit-calc" value="0.00" oninput="calcTotals()"></td>
                        <td><input type="number" step="0.01" name="credit[]" class="form-control form-control-sm text-center credit-calc" value="0.00" oninput="calcTotals()"></td>
                        <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); calcTotals();"><i class="fas fa-times"></i></button></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-light">
                        <th colspan="3" class="text-end">الإجمالي (Totals):</th>
                        <th class="text-center font-monospace fs-5 text-success" id="totalDebit">0.00</th>
                        <th class="text-center font-monospace fs-5 text-danger" id="totalCredit">0.00</th>
                        <th><button type="button" class="btn btn-primary btn-sm" onclick="addRow()"><i class="fas fa-plus"></i></button></th>
                    </tr>
                </tfoot>
            </table>
            
            <div id="balanceAlert" class="alert alert-danger mt-3" style="display:none;"><i class="fas fa-exclamation-triangle"></i> القيد غير متزن! يرجى مساواة المدين مع الدائن.</div>
        </div>
        <div class="card-footer bg-light"><button type="submit" class="btn btn-success" id="submitBtn" disabled><i class="fas fa-save"></i> حفظ واعتماد القيد</button></div>
    </form>
</div>

<script>
    function addRow() {
        const tr = document.querySelector('#itemsBody tr').cloneNode(true);
        tr.querySelectorAll('input').forEach(i => i.value = (i.type==='number'?'0.00':''));
        document.getElementById('itemsBody').appendChild(tr);
    }

    function calcTotals() {
        let tDeb = 0, tCred = 0;
        document.querySelectorAll('.debit-calc').forEach(i => tDeb += parseFloat(i.value || 0));
        document.querySelectorAll('.credit-calc').forEach(i => tCred += parseFloat(i.value || 0));
        
        document.getElementById('totalDebit').innerText = tDeb.toFixed(2);
        document.getElementById('totalCredit').innerText = tCred.toFixed(2);
        
        const btn = document.getElementById('submitBtn');
        const alert = document.getElementById('balanceAlert');
        
        if (tDeb === tCred && tDeb > 0) {
            btn.disabled = false; alert.style.display = 'none';
        } else {
            btn.disabled = true; alert.style.display = 'block';
        }
    }
</script>