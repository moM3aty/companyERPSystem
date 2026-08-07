<?php
// المسار: app/views/sales_returns/create.php
$invoices = $data['invoices'] ?? [];
$products = $data['products'] ?? [];
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-danger text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-arrow-rotate-left"></i> تسجيل فاتورة مرتجعات (للعميل)</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/saleReturn/create" method="POST" id="retForm">
        <div class="card-body border-bottom bg-light">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">الفاتورة الأصلية <span class="required">*</span></label>
                    <select name="invoice_id" class="form-control" required>
                        <option value="">-- اختر الفاتورة المستهدف إرجاعها --</option>
                        <?php foreach($invoices as $inv): ?>
                            <option value="<?php echo $inv->id; ?>">فاتورة #<?php echo htmlspecialchars($inv->invoice_number); ?> - العميل: <?php echo htmlspecialchars($inv->customer_name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">سبب الإرجاع</label>
                    <input type="text" name="reason" class="form-control" placeholder="مثال: عيب مصنعي، استبدال، خطأ بالطلب...">
                </div>
            </div>
        </div>

        <div class="card-body">
            <h4 class="mb-3" style="font-size: 15px; font-weight: 700;">الأصناف المرتجعة</h4>
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle"></i> تنبيه: سيتم إعادة هذه الكميات إلى المخزون المتاح فور حفظ المستند.
            </div>
            
            <div class="table-responsive">
                <table class="table border rounded" id="itemsTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 40%;">المنتج المراد إرجاعه</th>
                            <th style="width: 15%;">سعر الوحدة المُسترد</th>
                            <th style="width: 15%;">الكمية المرتجعة</th>
                            <th style="width: 20%;">إجمالي الخصم</th>
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
                            <td><input type="number" name="price[]" step="0.01" min="0" class="form-control font-monospace price-input" oninput="calculateRow(this)" required></td>
                            <td><input type="number" name="quantity[]" min="1" value="1" class="form-control font-monospace qty-input" oninput="calculateRow(this)" required></td>
                            <td><input type="text" class="form-control font-monospace subtotal-input text-danger fw-bold" value="0.00" readonly style="background:transparent; border:none;"></td>
                            <td class="text-center"><button type="button" class="btn-icon delete" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="3" class="text-left fw-bold" style="padding: 16px;">إجمالي المسترد للعميل:</td>
                            <td colspan="2" style="padding: 16px;"><span id="grandTotal" class="font-monospace fs-4 fw-bold text-danger">0.00</span> ر.س</td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-secondary mt-3" onclick="addRow()"><i class="fas fa-plus"></i> إضافة صنف للترجيع</button>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-danger" id="submitBtn"><i class="fas fa-save"></i> تأكيد المرتجع</button>
            <a href="<?php echo URLROOT; ?>/saleReturn/index" class="btn btn-secondary">إلغاء</a>
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

        const subtotal = price * qty;
        row.querySelector('.subtotal-input').value = subtotal.toFixed(2);
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