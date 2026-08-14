<?php
// app/views/salesOrder/edit.php
$order = $data['order'] ?? null;
$items = $data['items'] ?? [];
$customers = $data['customers'] ?? [];
$products = $data['products'] ?? [];
?>

<div class="card" style="max-width: 1200px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning">
        <h3 class="card-title text-warning-dark mb-0"><i class="fas fa-pen"></i> تعديل أمر البيع #<?php echo htmlspecialchars($order->so_number); ?></h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/salesOrder/edit/<?php echo $order->id; ?>" method="POST" id="soForm">
        <div class="card-body">
            
            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">بيانات العميل والتوصيل</h5>
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">العميل (Customer) <span class="required">*</span></label>
                    <select name="customer_id" class="form-control fw-bold" required>
                        <option value="">-- اختر العميل --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>" <?php echo $c->id == $order->customer_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">رقم الأمر (SO No)</label>
                    <input type="text" class="form-control font-monospace bg-light" value="<?php echo htmlspecialchars($order->so_number); ?>" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الإصدار</label>
                    <input type="date" name="so_date" class="form-control" value="<?php echo $order->so_date; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label text-success">متوقع التسليم</label>
                    <input type="date" name="expected_delivery_date" class="form-control text-success fw-bold" value="<?php echo $order->expected_delivery_date; ?>">
                </div>
                <div class="form-group full-width">
                    <label class="form-label">عنوان التوصيل</label>
                    <input type="text" name="delivery_address" class="form-control" value="<?php echo htmlspecialchars($order->delivery_address ?? ''); ?>">
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="fas fa-boxes"></i> جدول المنتجات والخدمات المطلوبة</h5>
            <div class="table-responsive" style="overflow-x:visible;">
                <table class="table table-bordered mb-0">
                    <thead class="bg-warning-dark text-white">
                        <tr>
                            <th style="width: 25%; color:#fff;">الصنف (منتج النظام)</th>
                            <th style="width: 25%; color:#fff;">الوصف (يظهر للعميل)</th>
                            <th style="width: 10%; color:#fff; text-align:center;">الكمية</th>
                            <th style="width: 12%; color:#fff; text-align:center;">السعر</th>
                            <th style="width: 8%; color:#fff; text-align:center;">خصم</th>
                            <th style="width: 8%; color:#fff; text-align:center;">ضريبة%</th>
                            <th style="width: 12%; color:#fff; text-align:center;">المجموع</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="soItemsBody">
                        <?php foreach($items as $i): ?>
                        <tr class="so-row">
                            <td>
                                <select name="product_id[]" class="form-control form-control-sm">
                                    <option value="">-- خدمة/صنف حر --</option>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?php echo $p->id; ?>" <?php echo $p->id == $i->product_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($p->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="item_description[]" class="form-control form-control-sm" required value="<?php echo htmlspecialchars($i->description); ?>"></td>
                            <td><input type="number" step="0.01" name="item_quantity[]" class="form-control form-control-sm text-center qty-calc fw-bold" value="<?php echo $i->quantity; ?>" required oninput="calcSO()"></td>
                            <td><input type="number" step="0.01" name="item_price[]" class="form-control form-control-sm text-center price-calc font-monospace" value="<?php echo $i->unit_price; ?>" oninput="calcSO()"></td>
                            <td><input type="number" step="0.01" name="item_discount[]" class="form-control form-control-sm text-center disc-calc" value="<?php echo $i->discount; ?>" oninput="calcSO()"></td>
                            <td><input type="number" step="0.01" name="item_tax[]" class="form-control form-control-sm text-center tax-calc" value="<?php echo $i->tax_rate; ?>" oninput="calcSO()"></td>
                            <td class="text-center align-middle font-monospace fw-bold text-primary sub-calc" style="direction:ltr;"><?php echo number_format($i->subtotal, 2); ?></td>
                            <td class="text-center align-middle"><button type="button" class="btn-icon delete text-danger" onclick="this.closest('tr').remove(); calcSO();" tabindex="-1"><i class="fas fa-times"></i></button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="flex-1">
                    <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addSORow()"><i class="fas fa-plus"></i> إضافة سطر جديد</button>
                    <div class="form-group">
                        <label class="form-label">ملاحظات داخلية أو شروط للعميل</label>
                        <textarea name="notes" class="form-control" rows="4"><?php echo htmlspecialchars($order->notes ?? ''); ?></textarea>
                    </div>
                </div>
                <div style="width: 300px; padding: 20px; background: var(--slate-50); border: 1px solid var(--border-color); border-radius: 8px;">
                    <table style="width: 100%; font-size: 14px;">
                        <tr><td class="text-muted fw-bold pb-2">المجموع الفرعي:</td><td class="font-monospace text-dark text-left pb-2" id="sumSubtotal"><?php echo number_format($order->subtotal, 2); ?></td></tr>
                        <tr><td class="text-muted fw-bold pb-2">إجمالي الخصم:</td><td class="font-monospace text-danger text-left pb-2" id="sumDiscount"><?php echo number_format($order->discount, 2); ?></td></tr>
                        <tr style="border-bottom: 1px dashed var(--border-color);"><td class="text-muted fw-bold pb-2">إجمالي الضريبة:</td><td class="font-monospace text-dark text-left pb-2" id="sumTax"><?php echo number_format($order->tax_amount, 2); ?></td></tr>
                        <tr><td class="fw-black text-primary pt-3 fs-5">الإجمالي النهائي:</td><td class="font-monospace fw-black text-primary text-left pt-3 fs-4" id="grandTotalLabel"><?php echo number_format($order->grand_total, 2); ?></td></tr>
                    </table>
                </div>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/salesOrder/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<template id="soRowTemplate">
    <tr class="so-row">
        <td>
            <select name="product_id[]" class="form-control form-control-sm">
                <option value="">-- خدمة/صنف حر --</option>
                <?php foreach($products as $p): ?><option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option><?php endforeach; ?>
            </select>
        </td>
        <td><input type="text" name="item_description[]" class="form-control form-control-sm" required placeholder="وصف الخدمة أو المنتج"></td>
        <td><input type="number" step="0.01" name="item_quantity[]" class="form-control form-control-sm text-center qty-calc fw-bold" value="1" required oninput="calcSO()"></td>
        <td><input type="number" step="0.01" name="item_price[]" class="form-control form-control-sm text-center price-calc font-monospace" value="0.00" oninput="calcSO()"></td>
        <td><input type="number" step="0.01" name="item_discount[]" class="form-control form-control-sm text-center disc-calc" value="0.00" oninput="calcSO()"></td>
        <td><input type="number" step="0.01" name="item_tax[]" class="form-control form-control-sm text-center tax-calc" value="15.00" oninput="calcSO()"></td>
        <td class="text-center align-middle font-monospace fw-bold text-primary sub-calc" style="direction:ltr;">0.00</td>
        <td class="text-center align-middle"><button type="button" class="btn-icon delete text-danger" onclick="this.closest('tr').remove(); calcSO();" tabindex="-1"><i class="fas fa-times"></i></button></td>
    </tr>
</template>

<script>
    function addSORow() {
        const temp = document.getElementById('soRowTemplate');
        document.getElementById('soItemsBody').appendChild(temp.content.cloneNode(true));
    }

    function calcSO() {
        let tSub = 0, tDisc = 0, tTax = 0;
        document.querySelectorAll('.so-row').forEach(row => {
            let qty = parseFloat(row.querySelector('.qty-calc').value) || 0;
            let price = parseFloat(row.querySelector('.price-calc').value) || 0;
            let disc = parseFloat(row.querySelector('.disc-calc').value) || 0;
            let taxR = parseFloat(row.querySelector('.tax-calc').value) || 0;
            
            let itemSub = (qty * price) - disc;
            let itemTax = itemSub * (taxR / 100);
            let rowTotal = itemSub + itemTax;
            
            row.querySelector('.sub-calc').innerText = rowTotal.toFixed(2);
            tSub += (qty * price);
            tDisc += disc;
            tTax += itemTax;
        });

        let grand = (tSub - tDisc) + tTax;

        document.getElementById('sumSubtotal').innerText = tSub.toFixed(2);
        document.getElementById('sumDiscount').innerText = tDisc.toFixed(2);
        document.getElementById('sumTax').innerText = tTax.toFixed(2);
        document.getElementById('grandTotalLabel').innerText = grand.toFixed(2);
    }
</script>