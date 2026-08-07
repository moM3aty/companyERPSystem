<?php
// المسار: app/views/purchase/edit.php
$order = $data['order'];
$items = $data['items'];
$suppliers = $data['suppliers'];
$products = $data['products'];
?>

<div class="card" style="max-width: 1000px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل أمر الشراء: <?php echo htmlspecialchars($order->po_number); ?></h3>
    </div>

    <form action="<?php echo URLROOT; ?>/purchase/edit/<?php echo $order->id; ?>" method="POST" id="poForm">
        <div class="card-body border-bottom">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">المورد <span class="required">*</span></label>
                    <select name="supplier_id" class="form-control" required>
                        <option value="">-- اختر المورد --</option>
                        <?php foreach($suppliers as $s): ?>
                            <option value="<?php echo $s->id; ?>" <?php echo $order->supplier_id == $s->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($s->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">ملاحظات وشروط</label>
                    <input type="text" name="notes" class="form-control" value="<?php echo htmlspecialchars($order->notes); ?>">
                </div>
            </div>
        </div>

        <div class="card-body">
            <h4 class="mb-3" style="font-size: 15px; font-weight: 700;">الأصناف المطلوبة</h4>
            <div class="table-responsive">
                <table class="table" style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); overflow: hidden;">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th style="width: 40%;">المنتج</th>
                            <th style="width: 20%;">سعر الوحدة (ر.س)</th>
                            <th style="width: 15%;">الكمية</th>
                            <th style="width: 15%;">الإجمالي</th>
                            <th style="width: 10%; text-align: center;">إزالة</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <?php foreach($items as $item): ?>
                        <tr class="item-row">
                            <td style="padding: 12px;">
                                <select name="product_id[]" class="form-control" onchange="updatePrice(this)" required>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->price; ?>" <?php echo $item->product_id == $p->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($p->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="padding: 12px;"><input type="number" name="unit_price[]" step="0.01" min="0" class="form-control font-monospace price-input" oninput="calculateRow(this)" value="<?php echo $item->unit_price; ?>" required></td>
                            <td style="padding: 12px;"><input type="number" name="quantity[]" min="1" class="form-control font-monospace qty-input" oninput="calculateRow(this)" value="<?php echo $item->quantity_ordered; ?>" required></td>
                            <td style="padding: 12px;"><input type="text" class="form-control font-monospace subtotal-input text-primary fw-bold" style="border:none; background:transparent;" value="<?php echo $item->total; ?>" readonly></td>
                            <td style="padding: 12px; text-align: center;"><button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-left fw-bold" style="padding: 16px;">إجمالي أمر الشراء:</td>
                            <td colspan="2" style="padding: 16px;"><span id="grandTotal" class="font-monospace fs-5 fw-bold text-success"><?php echo number_format($order->total_amount, 2); ?></span> ر.س</td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-secondary mt-3" onclick="addRow()"><i class="fas fa-plus"></i> إضافة صنف آخر</button>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning" id="submitBtn"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/purchase/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    const productOptions = `
        <option value="" data-price="0">اختر المنتج...</option>
        <?php foreach($products as $p): ?>
            <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->price; ?>"><?php echo addslashes(htmlspecialchars($p->name)); ?></option>
        <?php endforeach; ?>
    `;

    function updatePrice(select) {
        const price = select.options[select.selectedIndex].getAttribute('data-price');
        const row = select.closest('tr');
        row.querySelector('.price-input').value = price;
        calculateRow(select);
    }

    function calculateRow(element) {
        const row = element.closest('tr');
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
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
            <td style="padding: 12px;"><select name="product_id[]" class="form-control" onchange="updatePrice(this)" required>${productOptions}</select></td>
            <td style="padding: 12px;"><input type="number" name="unit_price[]" step="0.01" min="0" class="form-control font-monospace price-input" oninput="calculateRow(this)" required></td>
            <td style="padding: 12px;"><input type="number" name="quantity[]" min="1" value="1" class="form-control font-monospace qty-input" oninput="calculateRow(this)" required></td>
            <td style="padding: 12px;"><input type="text" class="form-control font-monospace subtotal-input text-primary fw-bold" style="border:none; background:transparent;" value="0.00" readonly></td>
            <td style="padding: 12px; text-align: center;"><button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
    }

    function removeRow(btn) {
        if(document.querySelectorAll('.item-row').length > 1) {
            btn.closest('tr').remove(); calculateGrandTotal();
        } else { alert('يجب أن يحتوي أمر الشراء على صنف واحد على الأقل.'); }
    }
</script>