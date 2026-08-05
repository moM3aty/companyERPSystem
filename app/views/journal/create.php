<?php
// المسار: app/views/journal/create.php
$accounts = $data['accounts'] ?? [];
$defaultEntryNumber = $data['default_entry_number'] ?? '';
?>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:24px;">
    <div style="padding:24px 30px; border-bottom:1px solid var(--border); background:#f8fafc;">
        <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-pen-nib" style="color:var(--primary);"></i> إنشاء قيد يومية مزدوج
        </h3>
    </div>

    <form action="<?php echo URL_ROOT; ?>/journal/create" method="POST" id="journalForm">
        <div style="padding:30px; display:grid; grid-template-columns:repeat(3, 1fr); gap:20px; border-bottom:1px solid var(--border);">
            
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">رقم القيد <span style="color:var(--danger);">*</span></label>
                <input type="text" name="entry_number" value="<?php echo $defaultEntryNumber; ?>" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:monospace; font-size:14px; outline:none; direction:ltr; text-align:right;">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">تاريخ القيد <span style="color:var(--danger);">*</span></label>
                <input type="date" name="entry_date" value="<?php echo date('Y-m-d'); ?>" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">المرجع (اختياري)</label>
                <div style="display:flex; gap:10px;">
                    <select name="reference_type" style="padding:10px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; width:40%;">
                        <option value="">نوع المرجع</option>
                        <option value="invoice">فاتورة مبيعات</option>
                        <option value="purchase_order">أمر شراء</option>
                        <option value="payroll">رواتب</option>
                    </select>
                    <input type="text" name="reference_id" placeholder="رقم المعرف" style="padding:10px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; width:60%; direction:ltr; text-align:right;">
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">البيان الرئيسي للقيد <span style="color:var(--danger);">*</span></label>
                <input type="text" name="description" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;" placeholder="مثال: إثبات مبيعات يوم كذا...">
            </div>
        </div>

        <div style="padding:30px;">
            <h4 style="margin:0 0 15px; font-size:15px; font-weight:700; color:var(--text-dark);">سطور القيد</h4>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; text-align:right;" id="journalTable">
                    <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                        <tr>
                            <th style="padding:10px; font-size:12px; color:var(--text-muted); width:35%;">الحساب</th>
                            <th style="padding:10px; font-size:12px; color:var(--text-muted); width:15%;">مدين (Debit)</th>
                            <th style="padding:10px; font-size:12px; color:var(--text-muted); width:15%;">دائن (Credit)</th>
                            <th style="padding:10px; font-size:12px; color:var(--text-muted); width:25%;">البيان (اختياري)</th>
                            <th style="padding:10px; font-size:12px; color:var(--text-muted); width:10%; text-align:center;">إجراء</th>
                        </tr>
                    </thead>
                    <tbody id="journalLines">
                        <!-- سطر مدين مبدئي -->
                        <tr class="line-row">
                            <td style="padding:8px;">
                                <select name="account_id[]" required style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
                                    <option value="">اختر الحساب...</option>
                                    <?php foreach($accounts as $acc): ?>
                                        <option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->code . ' - ' . $acc->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="padding:8px;">
                                <input type="number" name="debit[]" step="0.01" value="0.00" class="debit-input" onchange="calculateTotals()" style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-family:monospace; text-align:right; direction:ltr;">
                            </td>
                            <td style="padding:8px;">
                                <input type="number" name="credit[]" step="0.01" value="0.00" class="credit-input" onchange="calculateTotals()" style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-family:monospace; text-align:right; direction:ltr;">
                            </td>
                            <td style="padding:8px;">
                                <input type="text" name="line_description[]" placeholder="بيان السطر" style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
                            </td>
                            <td style="padding:8px; text-align:center;">
                                <button type="button" onclick="removeLine(this)" disabled style="width:30px; height:30px; border-radius:6px; border:1px solid var(--border); background:#fff; color:var(--text-muted); cursor:not-allowed;"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <!-- سطر دائن مبدئي -->
                        <tr class="line-row">
                            <td style="padding:8px;">
                                <select name="account_id[]" required style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
                                    <option value="">اختر الحساب...</option>
                                    <?php foreach($accounts as $acc): ?>
                                        <option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->code . ' - ' . $acc->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="padding:8px;">
                                <input type="number" name="debit[]" step="0.01" value="0.00" class="debit-input" onchange="calculateTotals()" style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-family:monospace; text-align:right; direction:ltr;">
                            </td>
                            <td style="padding:8px;">
                                <input type="number" name="credit[]" step="0.01" value="0.00" class="credit-input" onchange="calculateTotals()" style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-family:monospace; text-align:right; direction:ltr;">
                            </td>
                            <td style="padding:8px;">
                                <input type="text" name="line_description[]" placeholder="بيان السطر" style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
                            </td>
                            <td style="padding:8px; text-align:center;">
                                <button type="button" onclick="removeLine(this)" disabled style="width:30px; height:30px; border-radius:6px; border:1px solid var(--border); background:#fff; color:var(--text-muted); cursor:not-allowed;"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot style="background:#f1f5f9; font-weight:700;">
                        <tr>
                            <td style="padding:12px; text-align:left;">الإجمالي:</td>
                            <td style="padding:12px;"><span id="totalDebit" style="color:var(--text-dark); font-family:monospace; font-size:15px;">0.00</span></td>
                            <td style="padding:12px;"><span id="totalCredit" style="color:var(--text-dark); font-family:monospace; font-size:15px;">0.00</span></td>
                            <td colspan="2" style="padding:12px; text-align:center;"><span id="balanceStatus" style="color:var(--success); font-size:12px; padding:4px 10px; background:var(--success-light); border-radius:6px; display:none;">متزن</span></td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" onclick="addLine()" style="margin-top:15px; padding:8px 16px; background:var(--page-bg); border:1px dashed var(--border); color:var(--text-body); border-radius:6px; cursor:pointer; font-family:'Cairo'; font-weight:600;"><i class="fas fa-plus"></i> إضافة سطر جديد</button>
            </div>
        </div>
        
        <div style="padding:20px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; gap:12px;">
            <button type="submit" id="submitBtn" style="padding:10px 24px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; cursor:pointer;"><i class="fas fa-save"></i> حفظ القيد</button>
            <a href="<?php echo URL_ROOT; ?>/journal/index" style="padding:10px 24px; background:transparent; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600;">إلغاء</a>
        </div>
    </form>
</div>

<script>
    // خيارات الحسابات لإضافتها ديناميكياً
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
            <td style="padding:8px;">
                <select name="account_id[]" required style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
                    ${accountOptions}
                </select>
            </td>
            <td style="padding:8px;">
                <input type="number" name="debit[]" step="0.01" value="0.00" class="debit-input" onchange="calculateTotals()" style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-family:monospace; text-align:right; direction:ltr;">
            </td>
            <td style="padding:8px;">
                <input type="number" name="credit[]" step="0.01" value="0.00" class="credit-input" onchange="calculateTotals()" style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-family:monospace; text-align:right; direction:ltr;">
            </td>
            <td style="padding:8px;">
                <input type="text" name="line_description[]" placeholder="بيان السطر" style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
            </td>
            <td style="padding:8px; text-align:center;">
                <button type="button" onclick="removeLine(this)" style="width:30px; height:30px; border-radius:6px; border:1px solid var(--danger-light); background:#fff; color:var(--danger); cursor:pointer;"><i class="fas fa-trash"></i></button>
            </td>
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
        rows.forEach((row, index) => {
            const btn = row.querySelector('button');
            if (rows.length <= 2) {
                btn.disabled = true;
                btn.style.color = 'var(--text-muted)';
                btn.style.borderColor = 'var(--border)';
                btn.style.cursor = 'not-allowed';
            } else {
                btn.disabled = false;
                btn.style.color = 'var(--danger)';
                btn.style.borderColor = 'var(--danger-light)';
                btn.style.cursor = 'pointer';
            }
        });
    }

    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        document.querySelectorAll('.debit-input').forEach(input => {
            totalDebit += parseFloat(input.value) || 0;
        });

        document.querySelectorAll('.credit-input').forEach(input => {
            totalCredit += parseFloat(input.value) || 0;
        });

        document.getElementById('totalDebit').textContent = totalDebit.toFixed(2);
        document.getElementById('totalCredit').textContent = totalCredit.toFixed(2);

        const statusLabel = document.getElementById('balanceStatus');
        const submitBtn = document.getElementById('submitBtn');

        if (totalDebit.toFixed(2) === totalCredit.toFixed(2) && totalDebit > 0) {
            statusLabel.textContent = 'متزن';
            statusLabel.style.display = 'inline-block';
            statusLabel.style.color = 'var(--success)';
            statusLabel.style.background = 'var(--success-light)';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        } else {
            statusLabel.textContent = 'غير متزن';
            statusLabel.style.display = 'inline-block';
            statusLabel.style.color = 'var(--danger)';
            statusLabel.style.background = 'var(--danger-light)';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
        }
    }

    // تهيئة الأزرار عند التحميل
    updateDeleteButtons();
</script>