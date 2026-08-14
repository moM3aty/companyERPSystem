<?php
// app/views/sales_order/create.php
$customers = $data['customers'] ?? [];
$products = $data['products'] ?? [];
$auto_order_num = $data['auto_order_num'] ?? 'SO-' . date('Ymd') . '-' . rand(100, 999);
?>

<div class="card" style="max-width: 1000px; margin: 0 auto; box-shadow: var(--shadow-md);">
    <div class="card-header bg-light border-bottom">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-shopping-cart text-primary"></i> إنشاء أمر بيع جديد (Sales Order)</h3>
    </div>
    
    <?php $flash = Session::getFlash(); if ($flash): ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?> m-3 mb-0"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
    <?php endif; ?>

    <form action="<?php echo URLROOT; ?>/salesOrder/create" method="POST" id="soForm">
        <div class="card-body">
            
            <!-- معلومات العميل والأمر -->
            <div class="form-grid mb-4 p-3 bg-light rounded border">
                <div class="form-group full-width">
                    <label class="form-label text-success fw-bold"><i class="fas fa-user-tie"></i> العميل (Customer) <span class="required">*</span></label>
                    <select name="customer_id" class="form-control fw-bold text-dark" required>
                        <option value="">-- اختر العميل من القائمة --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label fw-bold">رقم أمر البيع (SO Number)</label>
                    <input type="text" name="order_number" class="form-control font-monospace bg-white text-primary fw-bold" value="<?php echo $auto_order_num; ?>" required readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label fw-bold">تاريخ الطلب</label>
                    <input type="date" name="order_date" class="form-control font-monospace" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label fw-bold text-primary">تاريخ التسليم المتوقع</label>
                    <input type="date" name="expected_delivery" class="form-control font-monospace" value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>">
                </div>
            </div>

            <!-- جدول الأصناف الديناميكي -->
            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-box-open text-primary"></i> الأصناف المطلوبة (من المخزون)</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered text-center align-middle" id="itemsTable">
                    <thead class="bg-slate-50">
                        <tr>
                            <th style="width: 45%;">اختيار الصنف / المنتج</th>
                            <th style="width: 15%;">الكمية</th>
                            <th style="width: 20%;">سعر البيع للوحدة</th>
                            <th style="width: 15%;">الإجمالي</th>
                            <th style="width: 5%;">إزالة</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr>
                            <td>
                                <!-- القائمة المنسدلة لاختيار الصنف -->
                                <select name="product_id[]" class="form-control fw-bold product-select text-dark" required>
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

            <!-- إجمالي الفاتورة -->
            <div class="d-flex justify-content-end mb-4">
                <div class="p-3 border rounded text-center bg-light" style="width: 300px; border-color: var(--primary) !important;">
                    <div class="text-muted fw-bold mb-1">الإجمالي النهائي (SAR)</div>
                    <input type="text" name="grand_total" id="grandTotal" class="form-control text-center font-monospace fs-3 fw-black text-primary bg-white" value="0.00" readonly>
                </div>
            </div>

            <!-- الملاحظات -->
            <div class="form-group">
                <label class="form-label fw-bold">ملاحظات وشروط التوصيل (اختياري)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="اكتب هنا أي تفاصيل تخص الشحن أو متطلبات العميل الخاصة..."></textarea>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-primary fw-bold px-5" id="btnSubmit"><i class="fas fa-check-circle"></i> اعتماد وحفظ أمر البيع</button> 
            <a href="<?php echo URLROOT; ?>/salesOrder/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<!-- كود JavaScript للتعامل مع الجدول بشكل ديناميكي كامل -->
<script>
// تجهيز مصفوفة المنتجات لإنشاء أسطر جديدة
const productsList = <?php echo json_encode($products); ?>;
let productOptions = '<option value="">-- اختر من المخزون --</option>';
productsList.forEach(p => {
    productOptions += `<option value="${p.id}" data-name="${p.name}" data-price="${p.price || 0}">${p.name}</option>`;
});

document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('itemsBody');
    const addRowBtn = document.getElementById('addRowBtn');
    const grandTotalInput = document.getElementById('grandTotal');

    // دالة حساب الإجماليات
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

    // جلب بيانات الصنف تلقائياً عند اختياره من القائمة
    tableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const row = e.target.closest('tr');
            
            if (selectedOption) {
                row.querySelector('.product-name-input').value = selectedOption.getAttribute('data-name') || '';
                row.querySelector('.price-input').value = selectedOption.getAttribute('data-price') || 0;
                calculateTotals();
            }
        }
    });

    // إضافة سطر جديد
    addRowBtn.addEventListener('click', function() {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="product_id[]" class="form-control fw-bold product-select text-dark" required>
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
        
        // تفعيل زر الحذف للسطر الأول إذا زاد العدد
        if(tableBody.querySelectorAll('tr').length > 1) {
            tableBody.querySelector('tr:first-child .remove-row').disabled = false;
        }
    });

    // الاستماع لتغييرات الكمية والسعر
    tableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
            calculateTotals();
        }
    });

    // حذف سطر
    tableBody.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-row');
        if (btn) {
            if (tableBody.querySelectorAll('tr').length > 1) {
                btn.closest('tr').remove();
                calculateTotals();
                // تعطيل الحذف إذا تبقى سطر واحد
                if(tableBody.querySelectorAll('tr').length === 1) {
                    tableBody.querySelector('.remove-row').disabled = true;
                }
            }
        }
    });

    // منع الحفظ المزدوج
    document.getElementById('soForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري اعتماد الأمر...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
    });
});
</script>