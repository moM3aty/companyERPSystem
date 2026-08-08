<?php
// app/views/journal/edit.php
$entry = $data['entry'] ?? null;
$lines = $data['lines'] ?? [];
$accounts = $data['accounts'] ?? [];
?>

<div class="card" style="max-width: 1000px; margin: 0 auto;">
    <div class="card-header bg-warning text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-pen"></i> تعديل القيد المحاسبي: <?php echo htmlspecialchars($entry->entry_number); ?></h3>
    </div>

    <?php 
        $flash = Session::getFlash();
        if ($flash): 
    ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>" style="margin: 20px 20px 0;">
            <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo URLROOT; ?>/journal/edit/<?php echo $entry->id; ?>" method="POST" id="journalForm">
        <div class="card-body">
            
            <?php if(!empty($entry->reference_type)): ?>
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-triangle fa-lg"></i>
                <strong>تنبيه هام!</strong> هذا القيد تم توليده آلياً بواسطة النظام (<?php echo htmlspecialchars($entry->reference_type); ?>). تعديله يدوياً قد يؤثر على التطابق بين التقارير المالية والوحدات التشغيلية.
            </div>
            <?php endif; ?>

            <div class="form-grid mb-4 border-bottom pb-4">
                <div class="form-group">
                    <label class="form-label">رقم القيد</label>
                    <input type="text" class="form-control font-monospace bg-light" value="<?php echo htmlspecialchars($entry->entry_number); ?>" readonly disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ القيد <span class="required">*</span></label>
                    <input type="date" name="entry_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($entry->entry_date)); ?>" required>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">البيان العام (وصف القيد) <span class="required">*</span></label>
                    <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($entry->description ?? ''); ?>" required>
                </div>
                
                <!-- الحقول المخفية للحفاظ على بيانات المرجع -->
                <input type="hidden" name="reference_type" value="<?php echo htmlspecialchars($entry->reference_type ?? ''); ?>">
                <input type="hidden" name="reference_id" value="<?php echo htmlspecialchars($entry->reference_id ?? ''); ?>">
            </div>

            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-list-ol text-muted"></i> تفاصيل الأسطر (المدين والدائن)</h5>
            
            <div class="table-responsive" style="overflow-x: visible;">
                <table class="table" id="journalLinesTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 35%;">الحساب <span class="text-danger">*</span></th>
                            <th style="width: 30%;">بيان السطر (اختياري)</th>
                            <th style="width: 15%; text-align: center;">مدين (ر.س)</th>
                            <th style="width: 15%; text-align: center;">دائن (ر.س)</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="linesContainer">
                        <?php foreach($lines as $index => $line): ?>
                        <tr class="j-row">
                            <td>
                                <select name="account_id[]" class="form-control" required>
                                    <option value="">-- اختر حساباً --</option>
                                    <?php foreach($accounts as $acc): ?>
                                        <option value="<?php echo $acc->id; ?>" <?php echo $acc->id == $line->account_id ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($acc->code . ' - ' . $acc->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="line_description[]" class="form-control" value="<?php echo htmlspecialchars($line->description ?? ''); ?>" placeholder="شرح مبسط..."></td>
                            <td><input type="number" step="0.01" name="debit[]" class="form-control input-debit font-monospace text-center text-primary fw-bold" value="<?php echo $line->debit > 0 ? $line->debit : ''; ?>" placeholder="0.00" oninput="calcTotals()"></td>
                            <td><input type="number" step="0.01" name="credit[]" class="form-control input-credit font-monospace text-center text-danger fw-bold" value="<?php echo $line->credit > 0 ? $line->credit : ''; ?>" placeholder="0.00" oninput="calcTotals()"></td>
                            <td class="text-center">
                                <button type="button" class="btn-icon delete text-danger" onclick="this.closest('tr').remove(); calcTotals();" tabindex="-1"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="2">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="addNewRow()"><i class="fas fa-plus"></i> إضافة سطر جديد</button>
                            </td>
                            <td class="text-center font-monospace fs-5 fw-bold text-primary" id="totalDebitDisplay">0.00</td>
                            <td class="text-center font-monospace fs-5 fw-bold text-danger" id="totalCreditDisplay">0.00</td>
                            <td></td>
                        </tr>
                        <tr id="balanceWarning" style="display:none; background: #fef2f2;">
                            <td colspan="5" class="text-center text-danger fw-bold py-2">
                                <i class="fas fa-triangle-exclamation"></i> 
                                <span id="balanceMsg">تحذير: القيد غير متزن! الفرق: <span id="balanceDiff" class="font-monospace"></span></span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning" id="btnSubmit"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/journal/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<template id="rowTemplate">
    <tr class="j-row">
        <td>
            <select name="account_id[]" class="form-control" required>
                <option value="">-- اختر حساباً --</option>
                <?php foreach($accounts as $acc): ?>
                    <option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->code . ' - ' . $acc->name); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="text" name="line_description[]" class="form-control" placeholder="شرح مبسط..."></td>
        <td><input type="number" step="0.01" name="debit[]" class="form-control input-debit font-monospace text-center text-primary fw-bold" placeholder="0.00" oninput="calcTotals()"></td>
        <td><input type="number" step="0.01" name="credit[]" class="form-control input-credit font-monospace text-center text-danger fw-bold" placeholder="0.00" oninput="calcTotals()"></td>
        <td class="text-center">
            <button type="button" class="btn-icon delete text-danger" onclick="this.closest('tr').remove(); calcTotals();" tabindex="-1"><i class="fas fa-times"></i></button>
        </td>
    </tr>
</template>

<script>
    function addNewRow() {
        const template = document.getElementById('rowTemplate');
        const container = document.getElementById('linesContainer');
        container.appendChild(template.content.cloneNode(true));
    }

    function calcTotals() {
        let tDebit = 0, tCredit = 0;
        
        document.querySelectorAll('.input-debit').forEach(el => { tDebit += parseFloat(el.value) || 0; });
        document.querySelectorAll('.input-credit').forEach(el => { tCredit += parseFloat(el.value) || 0; });
        
        document.getElementById('totalDebitDisplay').innerText = tDebit.toFixed(2);
        document.getElementById('totalCreditDisplay').innerText = tCredit.toFixed(2);

        const diff = Math.abs(tDebit - tCredit);
        const warning = document.getElementById('balanceWarning');
        const btnSubmit = document.getElementById('btnSubmit');

        if (diff > 0.001) { 
            warning.style.display = 'table-row';
            document.getElementById('balanceMsg').innerHTML = `تحذير: القيد غير متزن! الفرق: <span id="balanceDiff" class="font-monospace">${diff.toFixed(2)}</span>`;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-lock"></i> القيد غير متزن';
            btnSubmit.classList.add('btn-secondary');
            btnSubmit.classList.remove('btn-warning');
        } else {
            warning.style.display = 'none';
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-save"></i> حفظ التعديلات';
            btnSubmit.classList.add('btn-warning');
            btnSubmit.classList.remove('btn-secondary');
        }
    }

    document.addEventListener('DOMContentLoaded', calcTotals);

    document.getElementById('journalForm').addEventListener('submit', function(e) {
        if(document.querySelectorAll('.j-row').length < 2) {
            e.preventDefault();
            const warning = document.getElementById('balanceWarning');
            warning.style.display = 'table-row';
            document.getElementById('balanceMsg').innerHTML = 'يجب إضافة سطرين على الأقل للقيد.';
            return;
        }
    });
</script>