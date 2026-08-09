<?php
// app/views/purchaseReturn/edit.php
$return = $data['return'] ?? null;
$items = $data['items'] ?? [];
$products = $data['products'] ?? [];
$suppliers = $data['suppliers'] ?? [];
?>

<div class="card" style="max-width: 1000px; margin: 0 auto;">
    <div class="card-header bg-warning text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-pen"></i> تعديل مرتجع المشتريات: <?php echo htmlspecialchars($return->return_number ?? ''); ?></h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/purchaseReturn/edit/<?php echo $return->id; ?>" method="POST" id="prtForm">
        <div class="card-body">
            
            <div class="alert alert-warning mb-3">
                <i class="fas fa-info-circle"></i> سيتم إعادة حساب المخزون آلياً بناءً على الأصناف المعدلة في حال كانت الحالة (معتمد).
            </div>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">البيانات الأساسية</h5>
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">رقم المرتجع</label>
                    <input type="text" class="form-control font-monospace bg-light" value="<?php echo htmlspecialchars($return->return_number); ?>" disabled readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">المورد المُسجل</label>
                    <select name="supplier_id" class="form-control" onchange="document.getElementById('sname').value = this.options[this.selectedIndex].text;">
                        <option value="">-- مورد غير مسجل / نقدي --</option>
                        <?php foreach($suppliers as $s): ?>
                            <option value="<?php echo $s->id; ?>" <?php echo ($return->supplier_id == $s->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($s->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">اسم المورد (للطباعة) <span class="required">*</span></label>
                    <input type="text" name="supplier_name" id="sname" class="form-control" value="<?php echo htmlspecialchars($return->supplier_name); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ المرتجع <span class="required">*</span></label>
                    <input type="date" name="return_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($return->return_date)); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">حالة المرتجع</label>
                    <select name="status" class="form-control fw-bold">
                        <option value="draft" <?php echo ($return->status == 'draft') ? 'selected' : ''; ?>>مسودة (لا تؤثر عالمخزون)</option>
                        <option value="approved" <?php echo ($return->status == 'approved') ? 'selected' : ''; ?>>معتمد (يخصم من المخزون)</option>
                        <option value="cancelled" <?php echo ($return->status == 'cancelled') ? 'selected' : ''; ?>>ملغي</option>
                    </select>
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">الأصناف المرتجعة</h5>
            
            <div class="table-responsive" style="overflow-x: visible;">
                <table class="table table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 40%;">المنتج <span class="text-danger">*</span></th>
                            <th style="width: 15%; text-align: center;">الكمية</th>
                            <th style="width: 20%; text-align: center;">تكلفة الوحدة (ر.س)</th>
                            <th style="width: 20%; text-align: center;">المجموع (ر.س)</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsContainer">
                        <?php if(empty($items)): ?>
                            <tr class="prt-row">
                                <td>
                                    <select name="product_id[]" class="form-control prod-select" required onchange="updateRow(this)">
                                        <option value="" data-cost="0">-- اختر منتجاً --</option>
                                        <?php foreach($products as $p): ?>
                                            <option value="<?php echo $p->id; ?>" data-cost="<?php echo $p->cost ?? 0; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" step="1" min="1" name="quantity[]" class="form-control qty-input text-center fw-bold" value="1" oninput="updateRow(this)"></td>
                                <td><input type="number" step="0.01" min="0" name="cost[]" class="form-control cost-input text-center font-monospace" value="0" oninput="updateRow(this)" style="direction:ltr;"></td>
                                <td class="text-center align-middle font-monospace fw-bold text-danger subtotal-display" style="direction:ltr;">0.00</td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn-icon delete text-danger" onclick="removeRow(this)" tabindex="-1"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($items as $item): ?>
                            <tr class="prt-row">
                                <td>
                                    <select name="product_id[]" class="form-control prod-select" required onchange="updateRow(this)">
                                        <option value="" data-cost="0">-- اختر منتجاً --</option>
                                        <?php foreach($products as $p): ?>
                                            <option value="<?php echo $p->id; ?>" data-cost="<?php echo $p->cost ?? 0; ?>" <?php echo ($item->product_id == $p->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" step="1" min="1" name="quantity[]" class="form-control qty-input text-center fw-bold" value="<?php echo $item->quantity; ?>" oninput="updateRow(this)"></td>
                                <td><input type="number" step="0.01" min="0" name="cost[]" class="form-control cost-input text-center font-monospace" value="<?php echo $item->unit_cost; ?>" oninput="updateRow(this)" style="direction:ltr;"></td>
                                <td class="text-center align-middle font-monospace fw-bold text-danger subtotal-display" style="direction:ltr;"><?php echo number_format($item->subtotal, 2, '.', ''); ?></td>
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
                            <td class="text-left fw-bold">إجمالي المرتجع:</td>
                            <td class="text-center font-monospace fs-4 fw-bold text-danger" id="grandTotalDisplay" style="direction:ltr;"><?php echo number_format($return->total_amount, 2); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="form-group full-width mt-4">
                <label class="form-label">سبب الارتجاع / ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2"><?php echo htmlspecialchars($return->notes ?? ''); ?></textarea>
            </div>

        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning" id="btnSubmit"><i class="fas fa-save"></i> حفظ وتحديث المرتجع</button>
            <a href="<?php echo URLROOT; ?>/purchaseReturn/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<!-- Template for JS -->
<template id="rowTemplate">
    <tr class="prt-row">
        <td>
            <select name="product_id[]" class="form-control prod-select" required onchange="updateRow(this)">
                <option value="" data-cost="0">-- اختر منتجاً --</option>
                <?php foreach($products as $p): ?>
                    <option value="<?php echo $p->id; ?>" data-cost="<?php echo $p->cost ?? 0; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="number" step="1" min="1" name="quantity[]" class="form-control qty-input text-center fw-bold" value="1" oninput="updateRow(this)"></td>
        <td><input type="number" step="0.01" min="0" name="cost[]" class="form-control cost-input text-center font-monospace" value="0" oninput="updateRow(this)" style="direction:ltr;"></td>
        <td class="text-center align-middle font-monospace fw-bold text-danger subtotal-display" style="direction:ltr;">0.00</td>
        <td class="text-center align-middle">
            <button type="button" class="btn-icon delete text-danger" onclick="removeRow(this)" tabindex="-1"><i class="fas fa-times"></i></button>
        </td>
    </tr>
</template>

<script>
    function updateRow(el) {
        let row = el.closest('.prt-row');
        let select = row.querySelector('.prod-select');
        let costInput = row.querySelector('.cost-input');
        let qtyInput = row.querySelector('.qty-input');
        let subtotalDisplay = row.querySelector('.subtotal-display');

        if(el.classList.contains('prod-select')) {
            let option = select.options[select.selectedIndex];
            costInput.value = option.getAttribute('data-cost');
        }

        let qty = parseFloat(qtyInput.value) || 0;
        let cost = parseFloat(costInput.value) || 0;
        let subtotal = qty * cost;
        
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
        let rows = document.querySelectorAll('.prt-row');
        if(rows.length > 1) {
            btn.closest('tr').remove();
            calculateTotal();
        } else {
            alert('يجب أن يحتوي المرتجع على صنف واحد على الأقل.');
        }
    }

    document.getElementById('prtForm').addEventListener('submit', function(e) {
        let rows = document.querySelectorAll('.prt-row');
        let hasProduct = false;
        
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