<?php
// المسار: app/views/purchase_returns/create.php
$suppliers = $data['suppliers'] ?? [];
$products = $data['products'] ?? [];
$pos = $data['pos'] ?? [];
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-danger text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white"><i class="fas fa-truck-ramp-box"></i> تسجيل مرتجع مشتريات (للمورد)</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/purchaseReturn/create" method="POST" id="prForm">
        <div class="card-body border-bottom">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">المورد المستلم <span class="required">*</span></label>
                    <select name="supplier_id" class="form-control" required>
                        <option value="">-- اختر المورد --</option>
                        <?php foreach($suppliers as $s): ?>
                            <option value="<?php echo $s->id; ?>"><?php echo htmlspecialchars($s->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">مرتبط بأمر شراء (اختياري)</label>
                    <select name="po_id" class="form-control">
                        <option value="">-- بدون أمر شراء محدد --</option>
                        <?php foreach($pos as $po): ?>
                            <option value="<?php echo $po->id; ?>"><?php echo htmlspecialchars($po->po_number); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">سبب الإرجاع</label>
                    <textarea name="reason" class="form-control" rows="2" placeholder="مثال: بضاعة تالفة، عدم مطابقة المواصفات..."></textarea>
                </div>
            </div>
        </div>

        <div class="card-body">
            <h4 class="mb-3" style="font-size: 15px; font-weight: 700;">الأصناف المرتجعة</h4>
            <div class="alert alert-warning mb-4">
                <i class="fas fa-exclamation-triangle"></i> تنبيه: سيتم خصم هذه الكميات من المخزون فور حفظ المستند بشكل نهائي.
            </div>
            
            <div class="table-responsive">
                <table class="table border rounded" id="itemsTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 40%;">المنتج</th>
                            <th style="width: 15%;">سعر الشراء (ر.س)</th>
                            <th style="width: 15%;">الكمية المرتجعة</th>
                            <th style="width: 20%;">الإجمالي المخصوم</th>
                            <th style="width: 10%;" class="text-center">إزالة</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td>
                                <select name="product_id[]" class="form-control" onchange="updatePrice(this)" required>
                                    <option value="" data-price="0" data-qty="0">اختر المنتج...</option>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->price; ?>" data-qty="<?php echo $p->quantity; ?>"><?php echo htmlspecialchars($p->name); ?> (بالمخزن: <?php echo $p->quantity; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" name="price[]" step="0.01" min="0" class="form-control font-monospace price-input" oninput="calculateRow(this)" required></td>
                            <td><input type="number" name="quantity[]" min="1" value="1" class="form-control font-monospace qty-input" oninput="calculateRow(this)" required></td>
                            <td><input type="text" class="form-control font-monospace subtotal-input text-danger fw-bold" value="0.00" readonly style="background:transparent; border:none;"></td>
                            <td class="text-center"><button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="3" class="text-left fw-bold" style="padding: 16px;">إجمالي المستحق من المورد:</td>
                            <td colspan="2" style="padding: 16px;"><span id="grandTotal" class="font-monospace fs-4 fw-bold text-danger">0.00</span> ر.س</td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-secondary mt-3" onclick="addRow()"><i class="fas fa-plus"></i> إضافة صنف للترجيع</button>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-danger" id="submitBtn"><i class="fas fa-save"></i> تأكيد وخصم المرتجع</button>
            <a href="<?php echo URLROOT; ?>/purchaseReturn/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    const productOptions = `
        <option value="" data-price="0" data-qty="0">اختر المنتج...</option>
        <?php foreach($products as $p): ?>
            <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->price; ?>" data-qty="<?php echo $p->quantity; ?>"><?php echo addslashes(htmlspecialchars($p->name)); ?> (بالمخزن: <?php echo $p->quantity; ?>)</option>
        <?php endforeach; ?>
    `;

    function updatePrice(select) {
        const option = select.options[select.selectedIndex];
        const price = option.getAttribute('data-price');
        const maxQty = option.getAttribute('data-qty');
        
        const row = select.closest('tr');
        row.querySelector('.price-input').value = price;
        const qtyInput = row.querySelector('.qty-input');
        
        qtyInput.setAttribute('max', maxQty);
        if(parseInt(qtyInput.value) > parseInt(maxQty)) {
            qtyInput.value = maxQty;
            alert('الكمية المرتجعة لا يمكن أن تتجاوز الرصيد الفعلي في المخزون.');
        }
        calculateRow(select);
    }

    function calculateRow(element) {
        const row = element.closest('tr');
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const qtyInput = row.querySelector('.qty-input');
        const qty = parseInt(qtyInput.value) || 0;
        const maxQty = parseInt(qtyInput.getAttribute('max')) || 9999;
        
        if (qty > maxQty) {
            alert('لا يمكنك إرجاع كمية تتجاوز المتوفر بالمخزن (' + maxQty + ')');
            qtyInput.value = maxQty;
            calculateRow(element);
            return;
        }

        row.querySelector('.subtotal-input').value = (price * qty).toFixed(2);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-input').forEach(input => total += parseFloat(input.value) || 0);
        document.getElementById('grandTotal').textContent = total.toFixed(2);
    }

    function addRow() {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td><select name="product_id[]" class="form-control" onchange="updatePrice(this)" required>${productOptions}</select></td>
            <td><input type="number" name="price[]" step="0.01" min="0" class="form-control font-monospace price-input" oninput="calculateRow(this)" required></td>
            <td><input type="number" name="quantity[]" min="1" value="1" class="form-control font-monospace qty-input" oninput="calculateRow(this)" required></td>
            <td><input type="text" class="form-control font-monospace subtotal-input text-danger fw-bold" value="0.00" readonly style="background:transparent; border:none;"></td>
            <td class="text-center"><button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
    }

    function removeRow(btn) {
        if(document.querySelectorAll('.item-row').length > 1) {
            btn.closest('tr').remove(); calculateGrandTotal();
        } else { alert('يجب أن يحتوي المرتجع على صنف واحد على الأقل.'); }
    }
</script>