<?php
// app/views/quote/create.php
$customers = $data['customers'] ?? [];
$leads = $data['leads'] ?? [];
$products = $data['products'] ?? [];
$auto_quote_num = $data['auto_quote_num'] ?? 'QT-' . date('Ymd') . '-' . rand(100, 999);
?>

<div class="card" style="max-width: 1000px; margin: 0 auto; box-shadow: var(--shadow-md);">
    <div class="card-header bg-light border-bottom">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-file-signature text-primary"></i> إنشاء عرض سعر جديد (Quotation)</h3>
    </div>
    
    <?php $flash = Session::getFlash(); if ($flash): ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?> m-3 mb-0"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
    <?php endif; ?>

    <form action="<?php echo URLROOT; ?>/quote/create" method="POST" id="quoteForm">
        <div class="card-body">
            
            <div class="form-grid mb-4 p-3 bg-light rounded border">
                <div class="form-group">
                    <label class="form-label text-dark fw-bold">الجهة الموجه لها العرض <span class="required">*</span></label>
                    <div class="d-flex gap-3 mt-2">
                        <label class="d-flex align-items-center gap-2 cursor-pointer">
                            <input type="radio" name="target_type" value="customer" checked onchange="toggleTarget()"> 
                            <span class="text-success fw-bold"><i class="fas fa-user-check"></i> عميل حالي (مسجل)</span>
                        </label>
                        <label class="d-flex align-items-center gap-2 cursor-pointer">
                            <input type="radio" name="target_type" value="lead" onchange="toggleTarget()"> 
                            <span class="text-warning fw-bold"><i class="fas fa-user-clock"></i> عميل محتمل (Lead)</span>
                        </label>
                    </div>
                </div>

                <div class="form-group" id="customerSelectDiv">
                    <label class="form-label text-success">اسم العميل <span class="required">*</span></label>
                    <select name="customer_id" id="customer_id" class="form-control fw-bold">
                        <option value="">-- اختر العميل --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" id="leadSelectDiv" style="display: none;">
                    <label class="form-label text-warning text-darken">اسم العميل المحتمل <span class="required">*</span></label>
                    <select name="lead_id" id="lead_id" class="form-control fw-bold">
                        <option value="">-- اختر العميل المحتمل --</option>
                        <?php foreach($leads as $l): ?>
                            <option value="<?php echo $l->id; ?>"><?php echo htmlspecialchars($l->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">رقم العرض</label>
                    <input type="text" name="quote_number" class="form-control font-monospace bg-white" value="<?php echo $auto_quote_num; ?>" required readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label">تاريخ الإصدار</label>
                    <input type="date" name="quote_date" class="form-control font-monospace" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label text-danger">تاريخ الانتهاء</label>
                    <input type="date" name="expiry_date" class="form-control font-monospace" value="<?php echo date('Y-m-d', strtotime('+15 days')); ?>">
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-list-ol text-primary"></i> تفاصيل الخدمات / المنتجات (من المخزون)</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered text-center align-middle" id="itemsTable">
                    <thead class="bg-slate-50">
                        <tr>
                            <th style="width: 45%;">اختيار الصنف / الخدمة</th>
                            <th style="width: 15%;">الكمية</th>
                            <th style="width: 20%;">سعر الوحدة</th>
                            <th style="width: 15%;">الإجمالي</th>
                            <th style="width: 5%;">إزالة</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr>
                            <td>
                                <!-- القائمة المنسدلة لاختيار الصنف -->
                                <select name="product_id[]" class="form-control fw-bold product-select" required>
                                    <option value="">-- اختر من المخزون --</option>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?php echo $p->id; ?>" data-name="<?php echo htmlspecialchars($p->name); ?>" data-price="<?php echo $p->price ?? 0; ?>">
                                            <?php echo htmlspecialchars($p->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="product_name[]" class="product-name-input">
                            </td>
                            <td><input type="number" name="quantity[]" class="form-control text-center font-monospace qty-input" value="1" min="1" step="0.01" required></td>
                            <td><input type="number" name="unit_price[]" class="form-control text-center font-monospace price-input" value="0.00" min="0" step="0.01" required></td>
                            <td><input type="text" name="total_price[]" class="form-control text-center font-monospace total-input bg-light" value="0.00" readonly></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row" disabled><i class="fas fa-times"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary fw-bold mt-2" id="addRowBtn"><i class="fas fa-plus"></i> إضافة سطر جديد</button>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <div class="p-3 border rounded text-center bg-light" style="width: 300px; border-color: var(--primary) !important;">
                    <div class="text-muted fw-bold mb-1">الإجمالي النهائي (SAR)</div>
                    <input type="text" name="grand_total" id="grandTotal" class="form-control text-center font-monospace fs-3 fw-black text-primary bg-white" value="0.00" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">ملاحظات العميل</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-primary fw-bold px-5" id="btnSubmit"><i class="fas fa-paper-plane"></i> إصدار عرض السعر</button> 
            <a href="<?php echo URLROOT; ?>/quote/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<!-- مصفوفة الأصناف لتشغيل الجافاسكربت -->
<script>
const productsList = <?php echo json_encode($products); ?>;
let productOptions = '<option value="">-- اختر من المخزون --</option>';
productsList.forEach(p => {
    productOptions += `<option value="${p.id}" data-name="${p.name}" data-price="${p.price || 0}">${p.name}</option>`;
});

function toggleTarget() {
    const type = document.querySelector('input[name="target_type"]:checked').value;
    const custDiv = document.getElementById('customerSelectDiv');
    const leadDiv = document.getElementById('leadSelectDiv');
    if(type === 'customer') {
        custDiv.style.display = 'block';
        leadDiv.style.display = 'none';
        document.getElementById('lead_id').value = '';
    } else {
        custDiv.style.display = 'none';
        leadDiv.style.display = 'block';
        document.getElementById('customer_id').value = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('itemsBody');
    const addRowBtn = document.getElementById('addRowBtn');
    const grandTotalInput = document.getElementById('grandTotal');

    function calculateTotals() {
        let grandTotal = 0;
        tableBody.querySelectorAll('tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = qty * price;
            row.querySelector('.total-input').value = total.toFixed(2);
            grandTotal += total;
        });
        grandTotalInput.value = grandTotal.toFixed(2);
    }

    // 🟢 تعبئة السعر واسم الصنف آلياً عند اختيار المنتج من القائمة 🟢
    tableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const row = e.target.closest('tr');
            const nameInput = row.querySelector('.product-name-input');
            const priceInput = row.querySelector('.price-input');
            
            if (selectedOption && nameInput) {
                nameInput.value = selectedOption.getAttribute('data-name') || '';
                priceInput.value = selectedOption.getAttribute('data-price') || 0;
                calculateTotals();
            }
        }
    });

    addRowBtn.addEventListener('click', function() {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="product_id[]" class="form-control fw-bold product-select" required>
                    ${productOptions}
                </select>
                <input type="hidden" name="product_name[]" class="product-name-input">
            </td>
            <td><input type="number" name="quantity[]" class="form-control text-center font-monospace qty-input" value="1" min="1" step="0.01" required></td>
            <td><input type="number" name="unit_price[]" class="form-control text-center font-monospace price-input" value="0.00" min="0" step="0.01" required></td>
            <td><input type="text" name="total_price[]" class="form-control text-center font-monospace total-input bg-light" value="0.00" readonly></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button></td>
        `;
        tableBody.appendChild(newRow);
        
        if(tableBody.querySelectorAll('tr').length > 1) {
            tableBody.querySelector('tr:first-child .remove-row').disabled = false;
        }
    });

    tableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
            calculateTotals();
        }
    });

    tableBody.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-row');
        if (btn) {
            if (tableBody.querySelectorAll('tr').length > 1) {
                btn.closest('tr').remove();
                calculateTotals();
                if(tableBody.querySelectorAll('tr').length === 1) {
                    tableBody.querySelector('.remove-row').disabled = true;
                }
            }
        }
    });

    document.getElementById('quoteForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإصدار...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
    });
});
</script>