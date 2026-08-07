<?php
// app/views/journal/create.php
$accounts = $data['accounts'] ?? [];
$defaultEntryNumber = $data['default_entry_number'] ?? '';
?>

<div class="card" style="max-width: 1000px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen-nib text-primary"></i> إنشاء قيد يومية مزدوج</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/journal/create" method="POST" id="journalForm">
        <div class="card-body border-bottom">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">رقم القيد <span class="required">*</span></label>
                    <input type="text" name="entry_number" class="form-control font-monospace" value="<?php echo $defaultEntryNumber; ?>" required style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ القيد <span class="required">*</span></label>
                    <input type="date" name="entry_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">البيان الرئيسي للقيد <span class="required">*</span></label>
                    <input type="text" name="description" class="form-control" required placeholder="مثال: إثبات فاتورة مبيعات رقم كذا...">
                </div>
            </div>
        </div>

        <div class="card-body">
            <h4 class="mb-3" style="font-size: 15px; font-weight: 700;">سطور القيد (المدين والدائن)</h4>
            <div class="table-responsive">
                <table class="table" style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); overflow: hidden;">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th style="width: 35%;">الحساب</th>
                            <th style="width: 15%;">مدين (Debit)</th>
                            <th style="width: 15%;">دائن (Credit)</th>
                            <th style="width: 25%;">البيان (اختياري)</th>
                            <th style="width: 10%; text-align: center;">إزالة</th>
                        </tr>
                    </thead>
                    <tbody id="journalLines">
                        <tr class="line-row">
                            <td style="padding: 12px;">
                                <select name="account_id[]" class="form-control" required>
                                    <option value="">اختر الحساب...</option>
                                    <?php foreach($accounts as $acc): ?>
                                        <option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->code . ' - ' . $acc->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="padding: 12px;"><input type="number" name="debit[]" step="0.01" value="0.00" class="form-control font-monospace debit-input" onchange="calculateTotals()" style="direction:ltr; text-align:right;"></td>
                            <td style="padding: 12px;"><input type="number" name="credit[]" step="0.01" value="0.00" class="form-control font-monospace credit-input" onchange="calculateTotals()" style="direction:ltr; text-align:right;"></td>
                            <td style="padding: 12px;"><input type="text" name="line_description[]" class="form-control" placeholder="بيان السطر"></td>
                            <td style="padding: 12px; text-align: center;"><button type="button" class="btn-icon delete" onclick="removeLine(this)" disabled><i class="fas fa-trash"></i></button></td>
                        </tr>
                        <tr class="line-row">
                            <td style="padding: 12px;">
                                <select name="account_id[]" class="form-control" required>
                                    <option value="">اختر الحساب...</option>
                                    <?php foreach($accounts as $acc): ?>
                                        <option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->code . ' - ' . $acc->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="padding: 12px;"><input type="number" name="debit[]" step="0.01" value="0.00" class="form-control font-monospace debit-input" onchange="calculateTotals()" style="direction:ltr; text-align:right;"></td>
                            <td style="padding: 12px;"><input type="number" name="credit[]" step="0.01" value="0.00" class="form-control font-monospace credit-input" onchange="calculateTotals()" style="direction:ltr; text-align:right;"></td>
                            <td style="padding: 12px;"><input type="text" name="line_description[]" class="form-control" placeholder="بيان السطر"></td>
                            <td style="padding: 12px; text-align: center;"><button type="button" class="btn-icon delete" onclick="removeLine(this)" disabled><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                    <tfoot style="background:#f8fafc; font-weight:700;">
                        <tr>
                            <td style="padding: 16px;">الإجمالي:</td>
                            <td style="padding: 16px;"><span id="totalDebit" class="font-monospace fs-5">0.00</span></td>
                            <td style="padding: 16px;"><span id="totalCredit" class="font-monospace fs-5">0.00</span></td>
                            <td colspan="2" style="padding: 16px; text-align: center;"><span id="balanceStatus" class="badge badge-secondary fs-6">غير متزن</span></td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-secondary mt-3" onclick="addLine()"><i class="fas fa-plus"></i> إضافة سطر جديد</button>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary" id="submitBtn" disabled><i class="fas fa-save"></i> حفظ القيد المزدوج</button>
            <a href="<?php echo URLROOT; ?>/journal/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    const accountOptions = `
        <option value="">اختر الحساب...</option>
        <?php foreach($accounts as $acc): ?>
            <option value="<?php echo $acc->id; ?>"><?php echo addslashes(htmlspecialchars($acc->code . ' - ' . $acc->name)); ?></option>
        <?php endforeach; ?>
    `;

    function addLine() {
        const tbody = document.getElementById('journalLines');
        const tr = document.createElement('tr');
        tr.className = 'line-row';
        tr.innerHTML = `
            <td style="padding: 12px;"><select name="account_id[]" class="form-control" required>${accountOptions}</select></td>
            <td style="padding: 12px;"><input type="number" name="debit[]" step="0.01" value="0.00" class="form-control font-monospace debit-input" onchange="calculateTotals()" style="direction:ltr; text-align:right;"></td>
            <td style="padding: 12px;"><input type="number" name="credit[]" step="0.01" value="0.00" class="form-control font-monospace credit-input" onchange="calculateTotals()" style="direction:ltr; text-align:right;"></td>
            <td style="padding: 12px;"><input type="text" name="line_description[]" class="form-control" placeholder="بيان السطر"></td>
            <td style="padding: 12px; text-align: center;"><button type="button" class="btn-icon delete" onclick="removeLine(this)"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        updateDeleteButtons();
    }

    function removeLine(btn) {
        btn.closest('tr').remove();
        updateDeleteButtons();
        calculateTotals();
    }

    function updateDeleteButtons() {
        const rows = document.querySelectorAll('.line-row');
        rows.forEach((row) => {
            const btn = row.querySelector('button');
            if (rows.length <= 2) {
                btn.disabled = true;
                btn.style.opacity = '0.5';
            } else {
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        });
    }

    function calculateTotals() {
        let tDebit = 0; let tCredit = 0;
        document.querySelectorAll('.debit-input').forEach(input => tDebit += parseFloat(input.value) || 0);
        document.querySelectorAll('.credit-input').forEach(input => tCredit += parseFloat(input.value) || 0);

        document.getElementById('totalDebit').textContent = tDebit.toFixed(2);
        document.getElementById('totalCredit').textContent = tCredit.toFixed(2);

        const statusLabel = document.getElementById('balanceStatus');
        const submitBtn = document.getElementById('submitBtn');

        if (tDebit.toFixed(2) === tCredit.toFixed(2) && tDebit > 0) {
            statusLabel.textContent = 'متزن';
            statusLabel.className = 'badge badge-success fs-6';
            submitBtn.disabled = false;
        } else {
            statusLabel.textContent = 'غير متزن';
            statusLabel.className = 'badge badge-danger fs-6';
            submitBtn.disabled = true;
        }
    }
</script>