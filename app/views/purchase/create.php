<?php
$flash = $data['flash'] ?? null;
$suppliers = $data['suppliers'] ?? [];
$products = $data['products'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="form-card">
        <form action="<?php echo URL_ROOT; ?>/purchase/create" method="POST" id="purchaseForm">
            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <label>المورد</label>
                        <select name="supplier_id" class="form-input" required>
                            <option value="">-- اختر مورد --</option>
                            <?php foreach ($suppliers as $sup) : ?>
                                <option value="<?php echo $sup->id; ?>"><?php echo $sup->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-input" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4>الأصناف</h4>
                <div id="items-container">
                    <div class="item-row">
                        <select name="product_id[]" class="form-input" style="width:40%;">
                            <option value="">-- منتج --</option>
                            <?php foreach ($products as $prod) : ?>
                                <option value="<?php echo $prod->id; ?>"><?php echo $prod->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="quantity[]" placeholder="الكمية" class="form-input" style="width:20%;" min="1">
                        <input type="number" name="unit_price[]" placeholder="سعر الوحدة" class="form-input" style="width:25%;" step="0.01">
                        <button type="button" onclick="removeRow(this)" class="btn-danger">حذف</button>
                    </div>
                </div>
                <button type="button" onclick="addRow()" class="btn-add-row">+ إضافة صنف</button>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> حفظ الأمر</button>
                <a href="<?php echo URL_ROOT; ?>/purchase/index" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<script>
    function addRow() {
        const container = document.getElementById('items-container');
        const newRow = document.createElement('div');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <select name="product_id[]" class="form-input" style="width:40%;">
                <option value="">-- منتج --</option>
                <?php foreach ($products as $prod) : ?>
                    <option value="<?php echo $prod->id; ?>"><?php echo $prod->name; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="quantity[]" placeholder="الكمية" class="form-input" style="width:20%;" min="1">
            <input type="number" name="unit_price[]" placeholder="سعر الوحدة" class="form-input" style="width:25%;" step="0.01">
            <button type="button" onclick="removeRow(this)" class="btn-danger">حذف</button>
        `;
        container.appendChild(newRow);
    }

    function removeRow(btn) {
        if (document.querySelectorAll('.item-row').length > 1) {
            btn.parentElement.remove();
        } else {
            alert('يجب أن يكون هناك صنف واحد على الأقل');
        }
    }
</script>