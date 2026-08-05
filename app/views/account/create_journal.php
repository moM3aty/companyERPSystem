<?php
$flash = $data['flash'] ?? null;
$accounts = $data['accounts'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="form-card">
        <form action="<?php echo URL_ROOT; ?>/account/create-journal" method="POST" id="journalForm">
            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <label>تاريخ القيد</label>
                        <input type="date" name="entry_date" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>البيان</label>
                        <input type="text" name="description" class="form-input" placeholder="وصف القيد" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4>سطور القيد</h4>
                <div id="lines-container">
                    <div class="line-row">
                        <select name="lines[0][account_id]" class="form-input" style="width:30%;">
                            <option value="">-- حساب --</option>
                            <?php foreach ($accounts as $acc) : ?>
                                <option value="<?php echo $acc->id; ?>"><?php echo $acc->code . ' - ' . $acc->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="lines[0][debit]" placeholder="مدين" class="form-input" style="width:20%;" step="0.01">
                        <input type="number" name="lines[0][credit]" placeholder="دائن" class="form-input" style="width:20%;" step="0.01">
                        <input type="text" name="lines[0][description]" placeholder="بيان السطر" class="form-input" style="width:25%;">
                        <button type="button" onclick="removeLine(this)" class="btn-danger">حذف</button>
                    </div>
                </div>
                <button type="button" onclick="addLine()" class="btn-add-row">+ إضافة سطر</button>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> حفظ القيد</button>
                <a href="<?php echo URL_ROOT; ?>/account/ledger" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<script>
    let lineIndex = 1;
    function addLine() {
        const container = document.getElementById('lines-container');
        const newRow = document.createElement('div');
        newRow.className = 'line-row';
        newRow.innerHTML = `
            <select name="lines[${lineIndex}][account_id]" class="form-input" style="width:30%;">
                <option value="">-- حساب --</option>
                <?php foreach ($accounts as $acc) : ?>
                    <option value="<?php echo $acc->id; ?>"><?php echo $acc->code . ' - ' . $acc->name; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="lines[${lineIndex}][debit]" placeholder="مدين" class="form-input" style="width:20%;" step="0.01">
            <input type="number" name="lines[${lineIndex}][credit]" placeholder="دائن" class="form-input" style="width:20%;" step="0.01">
            <input type="text" name="lines[${lineIndex}][description]" placeholder="بيان السطر" class="form-input" style="width:25%;">
            <button type="button" onclick="removeLine(this)" class="btn-danger">حذف</button>
        `;
        container.appendChild(newRow);
        lineIndex++;
    }

    function removeLine(btn) {
        if (document.querySelectorAll('.line-row').length > 1) {
            btn.parentElement.remove();
        } else {
            alert('يجب أن يكون هناك سطر واحد على الأقل');
        }
    }
</script>