<?php
// app/views/salesInvoice/create.php
$customers = $data['customers'] ?? [];
$products = $data['products'] ?? [];
$soData = $data['so_data'] ?? null;
$soItems = $data['so_items'] ?? [];
$auto_inv_num = $data['auto_inv_num'] ?? 'INV-' . date('Ymd') . '-' . rand(10,99);

$customerId = $soData ? $soData->customer_id : '';
$soId = $soData ? $soData->id : '';
?>

<div class="card" style="max-width: 1200px; margin: 0 auto;">
    <div class="card-header bg-primary-light border-primary">
        <h3 class="card-title text-primary-dark mb-0"><i class="fas fa-file-invoice-dollar"></i> إصدار فاتورة مبيعات (Sales Invoice)</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/salesInvoice/create" method="POST" id="invForm">
        <input type="hidden" name="so_id" value="<?php echo $soId; ?>">
        <div class="card-body">
            
            <?php if($soData): ?>
            <div class="alert alert-success mb-4 d-flex align-items-center gap-3">
                <i class="fas fa-check-circle fa-2x text-success"></i>
                <div>
                    <strong>استيراد ذكي مفعل:</strong> 
                    يتم إصدار الفاتورة بناءً على أمر البيع <strong>(#<?php echo htmlspecialchars($soData->so_number); ?>)</strong>. بمجرد الحفظ سيتم خصم المخزون.
                </div>
            </div>
            <?php endif; ?>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">بيانات الفاتورة والعميل</h5>
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">العميل (Customer) <span class="required">*</span></label>
                    <select name="customer_id" class="form-control fw-bold" required>
                        <option value="">-- اختر العميل --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>" <?php echo $c->id == $customerId ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">رقم الفاتورة (Invoice No)</label>
                    <input type="text" name="invoice_number" class="form-control font-monospace bg-light" value="<?php echo $auto_inv_num; ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الإصدار</label>
                    <input type="date" name="invoice_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label text-danger">تاريخ الاستحقاق (Due Date)</label>
                    <input type="date" name="due_date" class="form-control text-danger fw-bold" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="fas fa-boxes"></i> الأصناف (سيتم خصم الكميات من المخزون فوراً)</h5>
            <div class="table-responsive" style="overflow-x:visible;">
                <table class="table table-bordered mb-0">
                    <thead class="bg-primary-dark text-white">
                        <tr>
                            <th style="width: 25%; color:#fff;">الصنف (المنتج في المخزون)</th>
                            <th style="width: 25%; color:#fff;">الوصف (يظهر للعميل)</th>
                            <th style="width: 10%; color:#fff; text-align:center;">الكمية</th>
                            <th style="width: 12%; color:#fff; text-align:center;">السعر</th>
                            <th style="width: 8%; color:#fff; text-align:center;">خصم</th>
                            <th style="width: 8%; color:#fff; text-align:center;">ضريبة%</th>
                            <th style="width: 12%; color:#fff; text-align:center;">المجموع</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="invItemsBody">
                        <?php if(!empty($soItems)): foreach($soItems as $soItem): ?>
                        <tr class="inv-row">
                            <td>
                                <select name="product_id[]" class="form-control form-control-sm">
                                    <option value="">-- خدمة/صنف حر --</option>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?php echo $p->id; ?>" <?php echo $p->id == $soItem->product_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($p->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="item_description[]" class="form-control form-control-sm" required value="<?php echo htmlspecialchars($soItem->description); ?>"></td>
                            <td><input type="number" step="0.01" name="item_quantity[]" class="form-control form-control-sm text-center qty-calc fw-bold" value="<?php echo $soItem->quantity; ?>" required oninput="calcInv()"></td>
                            <td><input type="number" step="0.01" name="item_price[]" class="form-control form-control-sm text-center price-calc font-monospace" value="<?php echo $soItem->unit_price; ?>" oninput="calcInv()"></td>
                            <td><input type="number" step="0.01" name="item_discount[]" class="form-control form-control-sm text-center disc-calc" value="<?php echo $soItem->discount; ?>" oninput="calcInv()"></td>
                            <td><input type="number" step="0.01" name="item_tax[]" class="form-control form-control-sm text-center tax-calc" value="<?php echo $soItem->tax_rate; ?>" oninput="calcInv()"></td>
                            <td class="text-center align-middle font-monospace fw-bold text-primary sub-calc" style="direction:ltr;"><?php echo number_format($soItem->subtotal, 2); ?></td>
                            <td class="text-center align-middle"><button type="button" class="btn-icon delete text-danger" onclick="this.closest('tr').remove(); calcInv();" tabindex="-1"><i class="fas fa-times"></i></button></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr class="inv-row">
                            <td>
                                <select name="product_id[]" class="form-control form-control-sm">
                                    <option value="">-- خدمة/صنف حر --</option>
                                    <?php foreach($products as $p): ?><option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option><?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="item_description[]" class="form-control form-control-sm" required placeholder="وصف الخدمة أو المنتج"></td>
                            <td><input type="number" step="0.01" name="item_quantity[]" class="form-control form-control-sm text-center qty-calc fw-bold" value="1" required oninput="calcInv()"></td>
                            <td><input type="number" step="0.01" name="item_price[]" class="form-control form-control-sm text-center price-calc font-monospace" value="0.00" oninput="calcInv()"></td>
                            <td><input type="number" step="0.01" name="item_discount[]" class="form-control form-control-sm text-center disc-calc" value="0.00" oninput="calcInv()"></td>
                            <td><input type="number" step="0.01" name="item_tax[]" class="form-control form-control-sm text-center tax-calc" value="15.00" oninput="calcInv()"></td>
                            <td class="text-center align-middle font-monospace fw-bold text-primary sub-calc" style="direction:ltr;">0.00</td>
                            <td class="text-center align-middle"><button type="button" class="btn-icon delete text-danger" onclick="this.closest('tr').remove(); calcInv();" tabindex="-1"><i class="fas fa-times"></i></button></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="flex-1">
                    <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addInvRow()"><i class="fas fa-plus"></i> إضافة سطر جديد</button>
                    <div class="form-group">
                        <label class="form-label">ملاحظات وشروط الفاتورة</label>
                        <textarea name="notes" class="form-control" rows="4"></textarea>
                    </div>
                </div>
                <div style="width: 300px; padding: 20px; background: var(--slate-50); border: 1px solid var(--border-color); border-radius: 8px;">
                    <table style="width: 100%; font-size: 14px;">
                        <tr><td class="text-muted fw-bold pb-2">المجموع الفرعي:</td><td class="font-monospace text-dark text-left pb-2" id="sumSubtotal">0.00</td></tr>
                        <tr><td class="text-muted fw-bold pb-2">إجمالي الخصم:</td><td class="font-monospace text-danger text-left pb-2" id="sumDiscount">0.00</td></tr>
                        <tr style="border-bottom: 1px dashed var(--border-color);"><td class="text-muted fw-bold pb-2">إجمالي الضريبة:</td><td class="font-monospace text-dark text-left pb-2" id="sumTax">0.00</td></tr>
                        <tr><td class="fw-black text-primary pt-3 fs-5">الإجمالي النهائي:</td><td class="font-monospace fw-black text-primary text-left pt-3 fs-4" id="grandTotalLabel">0.00</td></tr>
                    </table>
                </div>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary" id="btnSubmit"><i class="fas fa-check"></i> إصدار الفاتورة واعتمادها</button>
            <a href="<?php echo URLROOT; ?>/salesInvoice/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<template id="invRowTemplate">
    <tr class="inv-row">
        <td>
            <select name="product_id[]" class="form-control form-control-sm">
                <option value="">-- خدمة/صنف حر --</option>
                <?php foreach($products as $p): ?><option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option><?php endforeach; ?>
            </select>
        </td>
        <td><input type="text" name="item_description[]" class="form-control form-control-sm" required placeholder="وصف الخدمة أو المنتج"></td>
        <td><input type="number" step="0.01" name="item_quantity[]" class="form-control form-control-sm text-center qty-calc fw-bold" value="1" required oninput="calcInv()"></td>
        <td><input type="number" step="0.01" name="item_price[]" class="form-control form-control-sm text-center price-calc font-monospace" value="0.00" oninput="calcInv()"></td>
        <td><input type="number" step="0.01" name="item_discount[]" class="form-control form-control-sm text-center disc-calc" value="0.00" oninput="calcInv()"></td>
        <td><input type="number" step="0.01" name="item_tax[]" class="form-control form-control-sm text-center tax-calc" value="15.00" oninput="calcInv()"></td>
        <td class="text-center align-middle font-monospace fw-bold text-primary sub-calc" style="direction:ltr;">0.00</td>
        <td class="text-center align-middle"><button type="button" class="btn-icon delete text-danger" onclick="this.closest('tr').remove(); calcInv();" tabindex="-1"><i class="fas fa-times"></i></button></td>
    </tr>
</template>

<script>
    function addInvRow() {
        const temp = document.getElementById('invRowTemplate');
        document.getElementById('invItemsBody').appendChild(temp.content.cloneNode(true));
    }

    function calcInv() {
        let tSub = 0, tDisc = 0, tTax = 0;
        document.querySelectorAll('.inv-row').forEach(row => {
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
    
    window.onload = calcInv;
    
    document.getElementById('invForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الاعتماد...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
    });
</script>