<?php
// المسار: app/views/quotes/create.php
$customers = $customers ?? ($data['customers'] ?? []);
$products = $products ?? ($data['products'] ?? []);
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-file-signature text-primary"></i> إنشاء عرض سعر رسمي للعملاء</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/quote/create" method="POST" id="quoteForm">
        <div class="card-body border-bottom">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">العميل المستهدف <span class="required">*</span></label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">-- يرجى اختيار العميل --</option>
                        <?php foreach ($customers as $c) : ?>
                            <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body">
            <h4 class="mb-3" style="font-size: 15px; font-weight: 700;">الأصناف والأسعار المقترحة</h4>
            
            <div class="table-responsive">
                <table class="table border rounded" id="itemsTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 45%;">المنتج</th>
                            <th style="width: 15%;" class="text-center">الكمية</th>
                            <th style="width: 20%;">السعر المقترح</th>
                            <th style="width: 15%;">الإجمالي</th>
                            <th style="width: 5%;" class="text-center">إزالة</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td>
                                <select name="product_id[]" class="form-control prod-select" onchange="updatePrice(this)" required>
                                    <option value="">-- اختر المنتج --</option>
                                    <?php foreach ($products as $prod) : ?>
                                        <option value="<?php echo $prod->id; ?>" data-price="<?php echo $prod->price; ?>">
                                            <?php echo htmlspecialchars($prod->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" name="quantity[]" class="form-control font-monospace text-center qty-input" min="1" value="1" oninput="calculateRow(this)" required></td>
                            <td><input type="number" name="unit_price[]" step="0.01" min="0" class="form-control font-monospace price-input" oninput="calculateRow(this)" required></td>
                            <td><input type="text" class="form-control font-monospace subtotal-input text-primary fw-bold" value="0.00" readonly style="background:transparent; border:none;"></td>
                            <td class="text-center"><button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="3" class="text-left fw-bold">إجمالي العرض المقترح:</td>
                            <td colspan="2"><span id="grandTotal" class="font-monospace fs-5 fw-bold text-success">0.00</span> <small>ر.س</small></td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" onclick="addRow()" class="btn btn-secondary mt-3"><i class="fas fa-plus"></i> إضافة صنف للعرض</button>
            </div>
        </div>

        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-primary" id="btnSubmit"><i class="fas fa-save"></i> حفظ وإصدار العرض</button>
            <a href="<?php echo URLROOT; ?>/quote/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    const productOptions = `
        <option value="">-- اختر المنتج --</option>
        <?php foreach($products as $p): ?>
            <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->price; ?>"><?php echo addslashes(htmlspecialchars($p->name)); ?></option>
        <?php endforeach; ?>
    `;

    function updatePrice(select) {
        const price = select.options[select.selectedIndex].getAttribute('data-price');
        const row = select.closest('tr');
        if(price) row.querySelector('.price-input').value = price;
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
            <td><select name="product_id[]" class="form-control prod-select" onchange="updatePrice(this)" required>${productOptions}</select></td>
            <td><input type="number" name="quantity[]" class="form-control font-monospace text-center qty-input" min="1" value="1" oninput="calculateRow(this)" required></td>
            <td><input type="number" name="unit_price[]" step="0.01" min="0" class="form-control font-monospace price-input" oninput="calculateRow(this)" required></td>
            <td><input type="text" class="form-control font-monospace subtotal-input text-primary fw-bold" value="0.00" readonly style="background:transparent; border:none;"></td>
            <td class="text-center"><button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
    }

    function removeRow(btn) {
        if(document.querySelectorAll('.item-row').length > 1) {
            btn.closest('tr').remove(); calculateGrandTotal();
        } else { alert('يجب أن يحتوي العرض على صنف واحد على الأقل.'); }
    }
</script>