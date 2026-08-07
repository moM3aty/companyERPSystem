<?php
// app/views/sales/create.php
$products = $data['products'] ?? [];
$customers = $data['customers'] ?? [];
?>

<div class="card" style="max-width: 1000px; margin: 0 auto;">
    <div class="card-header bg-success text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white"><i class="fas fa-file-invoice-dollar"></i> إصدار فاتورة مبيعات جديدة</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/sale/create" method="POST" id="posForm">
        <div class="card-body border-bottom">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">العميل المسجل (اختياري)</label>
                    <select name="customer_id" class="form-control" onchange="updateCustomerName(this)">
                        <option value="">-- عميل نقدي (بدون تسجيل) --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">اسم العميل <span class="required">*</span></label>
                    <input type="text" name="customer_name" id="customerName" class="form-control" value="عميل نقدي" required>
                </div>
            </div>
        </div>

        <div class="card-body">
            <h4 class="mb-3" style="font-size: 15px; font-weight: 700;">الأصناف والمبيعات</h4>
            <div class="table-responsive">
                <table class="table" style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); overflow: hidden;">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th style="width: 40%;">المنتج</th>
                            <th style="width: 20%;">السعر (ر.س)</th>
                            <th style="width: 15%;">الكمية</th>
                            <th style="width: 15%;">الإجمالي</th>
                            <th style="width: 10%; text-align: center;">إزالة</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td style="padding: 12px;">
                                <select name="product_id[]" class="form-control" onchange="updatePrice(this)" required>
                                    <option value="" data-price="0" data-qty="0">اختر المنتج...</option>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->price; ?>" data-qty="<?php echo $p->quantity; ?>"><?php echo htmlspecialchars($p->name); ?> (بالمخزن: <?php echo $p->quantity; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="padding: 12px;"><input type="number" name="price[]" step="0.01" min="0" class="form-control font-monospace price-input" oninput="calculateRow(this)" required></td>
                            <td style="padding: 12px;"><input type="number" name="quantity[]" min="1" value="1" class="form-control font-monospace qty-input" oninput="calculateRow(this)" required></td>
                            <td style="padding: 12px;"><input type="text" class="form-control font-monospace subtotal-input text-success fw-bold" style="border:none; background:transparent;" value="0.00" readonly></td>
                            <td style="padding: 12px; text-align: center;"><button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-left fw-bold" style="padding: 16px;">الإجمالي الكلي:</td>
                            <td colspan="2" style="padding: 16px;"><span id="grandTotal" class="font-monospace fs-4 fw-bold text-success">0.00</span> ر.س</td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-secondary mt-3" onclick="addRow()"><i class="fas fa-plus"></i> إضافة صنف</button>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 mt-4">
            <button type="submit" class="btn btn-success" id="submitBtn"><i class="fas fa-check-double"></i> إصدار الفاتورة</button>
            <a href="<?php echo URLROOT; ?>/sale/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    function updateCustomerName(select) {
        const nameInput = document.getElementById('customerName');
        if (select.value) {
            nameInput.value = select.options[select.selectedIndex].text;
            nameInput.readOnly = true;
        } else {
            nameInput.value = 'عميل نقدي';
            nameInput.readOnly = false;
        }
    }

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
        
        if (parseInt(qtyInput.value) > parseInt(maxQty)) {
            qtyInput.value = maxQty;
            alert('الكمية المطلوبة تتجاوز المخزون المتاح.');
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
            alert('لا يمكنك بيع كمية تتجاوز المتوفر بالمخزن (' + maxQty + ')');
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
            <td style="padding: 12px;"><select name="product_id[]" class="form-control" onchange="updatePrice(this)" required>${productOptions}</select></td>
            <td style="padding: 12px;"><input type="number" name="price[]" step="0.01" min="0" class="form-control font-monospace price-input" oninput="calculateRow(this)" required></td>
            <td style="padding: 12px;"><input type="number" name="quantity[]" min="1" value="1" class="form-control font-monospace qty-input" oninput="calculateRow(this)" required></td>
            <td style="padding: 12px;"><input type="text" class="form-control font-monospace subtotal-input text-success fw-bold" style="border:none; background:transparent;" value="0.00" readonly></td>
            <td style="padding: 12px; text-align: center;"><button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
    }

    function removeRow(btn) {
        if(document.querySelectorAll('.item-row').length > 1) {
            btn.closest('tr').remove(); calculateGrandTotal();
        } else { alert('يجب إضافة صنف واحد على الأقل.'); }
    }
</script>