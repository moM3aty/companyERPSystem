<?php
// المسار: app/views/purchase_requests/create.php
$products = $products ?? ($data['products'] ?? []);
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-signature text-primary"></i> رفع طلب شراء داخلي للإدارة</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/purchaseRequest/create" method="POST" id="prForm">
        <div class="card-body border-bottom">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">تاريخ الطلب <span class="required">*</span></label>
                    <input type="date" name="request_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">السبب / ملاحظات الطلب <span class="required">*</span></label>
                    <input type="text" name="notes" class="form-control" required placeholder="مثال: لتعويض نقص المخزون في الفرع الرئيسي...">
                </div>
            </div>
        </div>

        <div class="card-body">
            <h4 class="mb-3" style="font-size: 15px; font-weight: 700;">الأصناف المطلوبة وتسعيرها التقريبي</h4>
            <div class="table-responsive">
                <table class="table border rounded" id="itemsTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 45%;">المنتج / الصنف</th>
                            <th style="width: 15%;">الكمية المطلوبة</th>
                            <th style="width: 20%;">السعر التقريبي (ر.س)</th>
                            <th style="width: 20%;">الإجمالي التقديري</th>
                            <th style="width: 10%;" class="text-center">إزالة</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td>
                                <select name="product_id[]" class="form-control" onchange="updatePrice(this)" required>
                                    <option value="" data-price="0">اختر المنتج...</option>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->price; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" name="quantity[]" min="1" value="1" class="form-control font-monospace qty-input" oninput="calculateRow(this)" required></td>
                            <td><input type="number" name="estimated_price[]" step="0.01" min="0" class="form-control font-monospace price-input" oninput="calculateRow(this)"></td>
                            <td><input type="text" class="form-control font-monospace subtotal-input text-primary fw-bold" value="0.00" readonly style="background:transparent; border:none;"></td>
                            <td class="text-center"><button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="3" class="text-left fw-bold" style="padding: 16px;">التكلفة التقديرية الكلية للطلب:</td>
                            <td colspan="2" style="padding: 16px;"><span id="grandTotal" class="font-monospace fs-4 fw-bold text-dark">0.00</span> ر.س</td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-secondary mt-3" onclick="addRow()"><i class="fas fa-plus"></i> إضافة صنف</button>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-paper-plane"></i> إرسال الطلب للاعتماد</button>
            <a href="<?php echo URLROOT; ?>/purchaseRequest/index" class="btn btn-secondary">إلغاء</a>
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
            <td><select name="product_id[]" class="form-control" onchange="updatePrice(this)" required>${productOptions}</select></td>
            <td><input type="number" name="quantity[]" min="1" value="1" class="form-control font-monospace qty-input" oninput="calculateRow(this)" required></td>
            <td><input type="number" name="estimated_price[]" step="0.01" min="0" class="form-control font-monospace price-input" oninput="calculateRow(this)"></td>
            <td><input type="text" class="form-control font-monospace subtotal-input text-primary fw-bold" value="0.00" readonly style="background:transparent; border:none;"></td>
            <td class="text-center"><button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
    }

    function removeRow(btn) {
        if(document.querySelectorAll('.item-row').length > 1) {
            btn.closest('tr').remove(); calculateGrandTotal();
        } else { alert('يجب إضافة صنف واحد على الأقل.'); }
    }
</script>