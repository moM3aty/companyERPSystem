<?php
// app/views/salesOrder/edit.php
$order = $data['order'] ?? null;
$items = $data['items'] ?? [];
$products = $data['products'] ?? [];
$customers = $data['customers'] ?? [];
?>

<div class="card" style="max-width: 1000px; margin: 0 auto;">
    <div class="card-header bg-warning text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-pen"></i> تعديل أمر البيع: <?php echo htmlspecialchars($order->order_number ?? ''); ?></h3>
    </div>
    
    <!-- 🟢 شريط إظهار الأخطاء والتنبيهات 🟢 -->
    <?php 
        $flash = Session::getFlash();
        if ($flash): 
    ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>" style="margin: 20px 20px 0;">
            <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo URLROOT; ?>/salesOrder/edit/<?php echo $order->id; ?>" method="POST" id="soForm">
        <div class="card-body">
            
            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">البيانات الأساسية</h5>
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">رقم الأمر</label>
                    <input type="text" class="form-control font-monospace bg-light" value="<?php echo htmlspecialchars($order->order_number); ?>" disabled readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">العميل المُسجل</label>
                    <select name="customer_id" class="form-control" onchange="document.getElementById('cname').value = this.options[this.selectedIndex].text;">
                        <option value="">-- عميل غير مسجل / نقدي --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>" <?php echo ($order->customer_id == $c->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">اسم العميل (للطباعة) <span class="required">*</span></label>
                    <input type="text" name="customer_name" id="cname" class="form-control" value="<?php echo htmlspecialchars($order->customer_name); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الأمر <span class="required">*</span></label>
                    <input type="date" name="order_date" class="form-control" value="<?php echo $order->order_date ? date('Y-m-d', strtotime($order->order_date)) : date('Y-m-d', strtotime($order->created_at)); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">حالة الأمر</label>
                    <select name="status" class="form-control fw-bold">
                        <option value="draft" <?php echo ($order->status == 'draft') ? 'selected' : ''; ?>>مسودة (Draft)</option>
                        <option value="approved" <?php echo ($order->status == 'approved') ? 'selected' : ''; ?>>معتمد (Approved)</option>
                        <option value="sent" <?php echo ($order->status == 'sent') ? 'selected' : ''; ?>>مُرسل للعميل (Sent)</option>
                        <option value="cancelled" <?php echo ($order->status == 'cancelled') ? 'selected' : ''; ?>>ملغي (Cancelled)</option>
                    </select>
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">الأصناف والمنتجات</h5>
            
            <div class="table-responsive" style="overflow-x: visible;">
                <table class="table table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 40%;">المنتج <span class="text-danger">*</span></th>
                            <th style="width: 15%; text-align: center;">الكمية</th>
                            <th style="width: 20%; text-align: center;">السعر (ر.س)</th>
                            <th style="width: 20%; text-align: center;">المجموع (ر.س)</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsContainer">
                        <?php if(empty($items)): ?>
                        <!-- 🟢 سطر افتراضي في حال كان أمر البيع قديماً وفارغاً 🟢 -->
                        <tr class="so-row">
                            <td>
                                <select name="product_id[]" class="form-control prod-select" required onchange="updateRow(this)">
                                    <option value="" data-price="0">-- اختر منتجاً --</option>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->price; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" step="1" min="1" name="quantity[]" class="form-control qty-input text-center fw-bold" value="1" oninput="updateRow(this)"></td>
                            <td><input type="number" step="0.01" min="0" name="price[]" class="form-control price-input text-center font-monospace" value="0" oninput="updateRow(this)" style="direction:ltr;"></td>
                            <td class="text-center align-middle font-monospace fw-bold text-success subtotal-display" style="direction:ltr;">0.00</td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn-icon delete text-danger" onclick="removeRow(this)" tabindex="-1"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($items as $item): ?>
                            <tr class="so-row">
                                <td>
                                    <select name="product_id[]" class="form-control prod-select" required onchange="updateRow(this)">
                                        <option value="" data-price="0">-- اختر منتجاً --</option>
                                        <?php foreach($products as $p): ?>
                                            <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->price; ?>" <?php echo ($item->product_id == $p->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" step="1" min="1" name="quantity[]" class="form-control qty-input text-center fw-bold" value="<?php echo $item->quantity; ?>" oninput="updateRow(this)"></td>
                                <td><input type="number" step="0.01" min="0" name="price[]" class="form-control price-input text-center font-monospace" value="<?php echo $item->unit_price; ?>" oninput="updateRow(this)" style="direction:ltr;"></td>
                                <td class="text-center align-middle font-monospace fw-bold text-success subtotal-display" style="direction:ltr;"><?php echo number_format($item->subtotal, 2, '.', ''); ?></td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn-icon delete text-danger" onclick="removeRow(this)" tabindex="-1"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="2">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="addRow()"><i class="fas fa-plus"></i> إضافة صنف جديد</button>
                            </td>
                            <td class="text-left fw-bold">الإجمالي الكلي:</td>
                            <td class="text-center font-monospace fs-4 fw-bold text-primary" id="grandTotalDisplay" style="direction:ltr;"><?php echo number_format($order->total_amount, 2); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="form-group full-width mt-4">
                <label class="form-label">ملاحظات / شروط خاصة</label>
                <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($order->notes ?? ''); ?></textarea>
            </div>

        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning" id="btnSubmit"><i class="fas fa-save"></i> تحديث أمر البيع</button>
            <a href="<?php echo URLROOT; ?>/salesOrder/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<!-- Template for JS -->
<template id="rowTemplate">
    <tr class="so-row">
        <td>
            <select name="product_id[]" class="form-control prod-select" required onchange="updateRow(this)">
                <option value="" data-price="0">-- اختر منتجاً --</option>
                <?php foreach($products as $p): ?>
                    <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->price; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="number" step="1" min="1" name="quantity[]" class="form-control qty-input text-center fw-bold" value="1" oninput="updateRow(this)"></td>
        <td><input type="number" step="0.01" min="0" name="price[]" class="form-control price-input text-center font-monospace" value="0" oninput="updateRow(this)" style="direction:ltr;"></td>
        <td class="text-center align-middle font-monospace fw-bold text-success subtotal-display" style="direction:ltr;">0.00</td>
        <td class="text-center align-middle">
            <button type="button" class="btn-icon delete text-danger" onclick="removeRow(this)" tabindex="-1"><i class="fas fa-times"></i></button>
        </td>
    </tr>
</template>

<script>
    function updateRow(el) {
        let row = el.closest('.so-row');
        let select = row.querySelector('.prod-select');
        let priceInput = row.querySelector('.price-input');
        let qtyInput = row.querySelector('.qty-input');
        let subtotalDisplay = row.querySelector('.subtotal-display');

        if(el.classList.contains('prod-select')) {
            let option = select.options[select.selectedIndex];
            priceInput.value = option.getAttribute('data-price');
        }

        let qty = parseFloat(qtyInput.value) || 0;
        let price = parseFloat(priceInput.value) || 0;
        let subtotal = qty * price;
        
        subtotalDisplay.innerText = subtotal.toFixed(2);
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-display').forEach(el => {
            total += parseFloat(el.innerText) || 0;
        });
        document.getElementById('grandTotalDisplay').innerText = total.toFixed(2);
    }

    function addRow() {
        const template = document.getElementById('rowTemplate');
        document.getElementById('itemsContainer').appendChild(template.content.cloneNode(true));
    }

    function removeRow(btn) {
        let rows = document.querySelectorAll('.so-row');
        if(rows.length > 1) {
            btn.closest('tr').remove();
            calculateTotal();
        } else {
            alert('يجب أن يحتوي الأمر على صنف واحد على الأقل.');
        }
    }

    document.getElementById('soForm').addEventListener('submit', function(e) {
        let rows = document.querySelectorAll('.so-row');
        let hasProduct = false;
        
        // 🟢 منع الحفظ إذا كان الجدول يحتوي على أسطر فارغة 🟢
        rows.forEach(row => {
            let select = row.querySelector('.prod-select');
            if (select.value !== "") hasProduct = true;
        });

        if (!hasProduct) {
            e.preventDefault();
            alert('خطأ: يجب اختيار منتج واحد على الأقل ليتم الحفظ!');
            return false;
        }

        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.8';
    });
</script>