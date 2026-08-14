<?php
// app/views/purchaseInvoice/create.php
$suppliers = $data['suppliers'] ?? [];
$products = $data['products'] ?? [];
$grnData = $data['grn_data'] ?? null;
$poData = $data['po_data'] ?? null;
$grnItems = $data['grn_items'] ?? [];
$auto_inv_num = $data['auto_inv_num']   ?? 'PI-' . date('Ymd') . '-' . rand(10,99);

$supplierId = $grnData ? $grnData->supplier_id : '';
$poId = $poData ? $poData->id : '';
$grnId = $grnData ? $grnData->id : '';
?>

<div class="card" style="max-width: 1200px; margin: 0 auto;">
    <div class="card-header bg-danger-light border-danger">
        <h3 class="card-title text-danger-dark mb-0"><i class="fas fa-file-invoice-dollar"></i> إدخال فاتورة مورد والمطابقة (Supplier Invoice)</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/purchaseInvoice/create" method="POST" enctype="multipart/form-data" id="invForm">
        <input type="hidden" name="po_id" value="<?php echo $poId; ?>">
        <input type="hidden" name="grn_id" value="<?php echo $grnId; ?>">
        <div class="card-body">
            
            <?php if($grnData): ?>
            <div class="alert alert-success mb-4 d-flex align-items-center gap-3">
                <i class="fas fa-check-circle fa-2x text-success"></i>
                <div>
                    <strong>المطابقة الثلاثية (3-Way Match) مفعلة:</strong> 
                    يتم استيراد الكميات من إيصال الاستلام <strong>(<?php echo htmlspecialchars($grnData->grn_number); ?>)</strong>
                    <?php if($poData): ?> والأسعار من أمر الشراء <strong>(<?php echo htmlspecialchars($poData->po_number); ?>)</strong><?php endif; ?>.
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-warning mb-4">
                <i class="fas fa-exclamation-triangle"></i> إدخال مباشر بدون مطابقة مسبقة. تأكد من صحة الأسعار والكميات.
            </div>
            <?php endif; ?>

            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">المورد (Supplier) <span class="required">*</span></label>
                    <select name="supplier_id" class="form-control fw-bold" required>
                        <option value="">-- اختر المورد --</option>
                        <?php foreach($suppliers as $sup): ?>
                            <option value="<?php echo $sup->id; ?>" <?php echo $sup->id == $supplierId ? 'selected' : ''; ?>><?php echo htmlspecialchars($sup->company_name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">الرقم الداخلي (Internal No)</label>
                    <input type="text" name="invoice_number" class="form-control font-monospace bg-light" value="<?php echo $auto_inv_num; ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label text-danger">رقم فاتورة المورد (Supplier Inv No) <span class="required">*</span></label>
                    <input type="text" name="supplier_invoice_no" class="form-control font-monospace fw-bold" required placeholder="مثال: INV-90021" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الفاتورة</label>
                    <input type="date" name="invoice_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الاستحقاق (Due Date)</label>
                    <input type="date" name="due_date" class="form-control text-danger fw-bold" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="fas fa-boxes"></i> تفاصيل الفاتورة ومطابقة الكميات</h5>
            <div class="table-responsive" style="overflow-x:visible;">
                <table class="table table-bordered mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th style="width: 25%; color:#fff;">الصنف (منتج النظام)</th>
                            <th style="width: 25%; color:#fff;">الوصف (كما بالفاتورة)</th>
                            <th style="width: 10%; color:#fff; text-align:center;">الكمية المستلمة</th>
                            <th style="width: 12%; color:#fff; text-align:center;">سعر الوحدة</th>
                            <th style="width: 8%; color:#fff; text-align:center;">ضريبة%</th>
                            <th style="width: 15%; color:#fff; text-align:center;">المجموع</th>
                        </tr>
                    </thead>
                    <tbody id="invItemsBody">
                        <?php if(!empty($grnItems)): foreach($grnItems as $gItem): 
                            $price = 0; // لتبسيط الإدخال يمكن وضع السعر 0 وتعديله يدوياً حسب الفاتورة الورقية
                        ?>
                        <tr class="inv-row">
                            <td>
                                <select name="product_id[]" class="form-control form-control-sm">
                                    <option value="">-- غير مرتبط --</option>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?php echo $p->id; ?>" <?php echo $p->id == $gItem->product_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($p->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="item_description[]" class="form-control form-control-sm" required value="<?php echo htmlspecialchars($gItem->product_name); ?>"></td>
                            <td><input type="number" step="0.01" name="item_quantity[]" class="form-control form-control-sm text-center qty-calc fw-bold text-primary" value="<?php echo $gItem->accepted_qty; ?>" required oninput="calcInv()"></td>
                            <td><input type="number" step="0.01" name="item_price[]" class="form-control form-control-sm text-center price-calc font-monospace" value="<?php echo $price; ?>" required placeholder="السعر بالفاتورة" oninput="calcInv()"></td>
                            <td><input type="number" step="0.01" name="item_tax[]" class="form-control form-control-sm text-center tax-calc" value="15.00" oninput="calcInv()"></td>
                            <td class="text-center align-middle font-monospace fw-bold text-danger sub-calc" style="direction:ltr;">0.00</td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr class="inv-row">
                            <td>
                                <select name="product_id[]" class="form-control form-control-sm">
                                    <option value="">-- غير مرتبط --</option>
                                    <?php foreach($products as $p): ?><option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option><?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="item_description[]" class="form-control form-control-sm" required placeholder="وصف السلعة"></td>
                            <td><input type="number" step="0.01" name="item_quantity[]" class="form-control form-control-sm text-center qty-calc fw-bold text-primary" value="1" required oninput="calcInv()"></td>
                            <td><input type="number" step="0.01" name="item_price[]" class="form-control form-control-sm text-center price-calc font-monospace" value="0.00" required oninput="calcInv()"></td>
                            <td><input type="number" step="0.01" name="item_tax[]" class="form-control form-control-sm text-center tax-calc" value="15.00" oninput="calcInv()"></td>
                            <td class="text-center align-middle font-monospace fw-bold text-danger sub-calc" style="direction:ltr;">0.00</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="flex-1">
                    <?php if(!$grnData): ?>
                        <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addInvRow()"><i class="fas fa-plus"></i> إضافة سطر</button>
                    <?php endif; ?>
                    <div class="form-group border p-3 rounded bg-light mt-3">
                        <label class="form-label text-danger"><i class="fas fa-file-pdf"></i> إرفاق صورة الفاتورة (إلزامي للضرائب)</label>
                        <input type="file" name="attachment" class="form-control bg-white" accept="image/*,.pdf">
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">ملاحظات المحاسب</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div style="width: 300px; padding: 20px; background: var(--danger-light); border: 1px solid #fecaca; border-radius: 8px;">
                    <table style="width: 100%; font-size: 14px;">
                        <tr><td class="text-danger-dark fw-bold pb-2">المجموع الفرعي:</td><td class="font-monospace text-dark text-left pb-2" id="sumSubtotal">0.00</td></tr>
                        <tr style="border-bottom: 1px dashed #fca5a5;"><td class="text-danger-dark fw-bold pb-2">ضريبة المدخلات:</td><td class="font-monospace text-dark text-left pb-2" id="sumTax">0.00</td></tr>
                        <tr><td class="fw-black text-danger pt-3 fs-5">إجمالي الفاتورة:</td><td class="font-monospace fw-black text-danger text-left pt-3 fs-4" id="grandTotalLabel">0.00</td></tr>
                    </table>
                </div>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-danger" id="btnSubmit"><i class="fas fa-check-circle"></i> اعتماد وتسجيل المديونية للمورد</button>
            <a href="<?php echo URLROOT; ?>/purchaseInvoice/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<template id="invRowTemplate">
    <tr class="inv-row">
        <td>
            <select name="product_id[]" class="form-control form-control-sm">
                <option value="">-- غير مرتبط --</option>
                <?php foreach($products as $p): ?><option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option><?php endforeach; ?>
            </select>
        </td>
        <td><input type="text" name="item_description[]" class="form-control form-control-sm" required placeholder="وصف السلعة"></td>
        <td><input type="number" step="0.01" name="item_quantity[]" class="form-control form-control-sm text-center qty-calc fw-bold text-primary" value="1" required oninput="calcInv()"></td>
        <td><input type="number" step="0.01" name="item_price[]" class="form-control form-control-sm text-center price-calc font-monospace" value="0.00" required oninput="calcInv()"></td>
        <td><input type="number" step="0.01" name="item_tax[]" class="form-control form-control-sm text-center tax-calc" value="15.00" oninput="calcInv()"></td>
        <td class="text-center align-middle font-monospace fw-bold text-danger sub-calc" style="direction:ltr;">0.00</td>
    </tr>
</template>

<script>
    function addInvRow() {
        const temp = document.getElementById('invRowTemplate');
        document.getElementById('invItemsBody').appendChild(temp.content.cloneNode(true));
    }

    function calcInv() {
        let tSub = 0, tTax = 0;
        document.querySelectorAll('.inv-row').forEach(row => {
            let qty = parseFloat(row.querySelector('.qty-calc').value) || 0;
            let price = parseFloat(row.querySelector('.price-calc').value) || 0;
            let taxR = parseFloat(row.querySelector('.tax-calc').value) || 0;
            
            let itemSub = (qty * price);
            let itemTax = itemSub * (taxR / 100);
            let rowTotal = itemSub + itemTax;
            
            row.querySelector('.sub-calc').innerText = rowTotal.toFixed(2);
            tSub += itemSub;
            tTax += itemTax;
        });

        let grand = tSub + tTax;

        document.getElementById('sumSubtotal').innerText = tSub.toFixed(2);
        document.getElementById('sumTax').innerText = tTax.toFixed(2);
        document.getElementById('grandTotalLabel').innerText = grand.toFixed(2);
    }

    window.onload = calcInv;

    document.getElementById('invForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> يتم الاعتماد...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
    });
</script>