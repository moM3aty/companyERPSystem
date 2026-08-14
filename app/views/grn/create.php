<?php
// app/views/grn/create.php
$suppliers = $data['suppliers'] ?? [];
$warehouses = $data['warehouses'] ?? [];
$products = $data['products'] ?? [];
$poData = $data['po_data'] ?? null;
$poItems = $data['po_items'] ?? [];
$auto_grn_num = $data['auto_grn_num'] ?? 'GRN-' . date('Ymd') . '-' . rand(10,99);

$supplierId = $poData ? $poData->supplier_id : '';
$poId = $poData ? $poData->id : '';
?>

<div class="card" style="max-width: 1200px; margin: 0 auto;">
    <div class="card-header bg-success-light border-success">
        <h3 class="card-title text-success-dark mb-0"><i class="fas fa-dolly"></i> نموذج استلام بضاعة (GRN)</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/grn/create" method="POST" enctype="multipart/form-data" id="grnForm">
        <input type="hidden" name="po_id" value="<?php echo $poId; ?>">
        <div class="card-body">
            
            <?php if($poData): ?>
            <div class="alert alert-info mb-4">
                <i class="fas fa-link"></i> يتم الآن استلام البضائع بناءً على أمر الشراء <strong>(#<?php echo htmlspecialchars($poData->po_number); ?>)</strong>.
            </div>
            <?php endif; ?>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">بيانات الاستلام الأساسية</h5>
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
                    <label class="form-label">رقم الـ GRN</label>
                    <input type="text" name="grn_number" class="form-control font-monospace bg-light" value="<?php echo $auto_grn_num; ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الاستلام الفعلي</label>
                    <input type="date" name="delivery_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">المستودع (Warehouse) <span class="required">*</span></label>
                    <select name="warehouse_id" class="form-control fw-bold text-success" required>
                        <option value="">-- حدد مستودع الاستلام --</option>
                        <?php foreach($warehouses as $wh): ?>
                            <option value="<?php echo $wh->id; ?>"><?php echo htmlspecialchars($wh->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">رقم بوليصة الشحن/التوصيل (Delivery Note No)</label>
                    <input type="text" name="delivery_note" class="form-control font-monospace" placeholder="رقم الإيصال المستلم من السائق...">
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="fas fa-boxes"></i> مطابقة وتوثيق الكميات المستلمة</h5>
            <div class="alert alert-warning py-2 mb-3" style="font-size:12px;"><i class="fas fa-exclamation-triangle"></i> <strong>المطابقة الثلاثية (3-Way Match):</strong> قم بإدخال الكمية المستلمة فعلياً والتالفة بدقة ليتم تحديث أرصدة المخزون بشكل صحيح.</div>
            <div class="table-responsive" style="overflow-x:visible;">
                <table class="table table-bordered mb-0">
                    <thead class="bg-success-dark text-white">
                        <tr>
                            <th style="width: 25%; color:#fff;">الصنف (المنتج)</th>
                            <th style="width: 10%; color:#fff; text-align:center;">المطلوب(PO)</th>
                            <th style="width: 12%; color:#fff; text-align:center;">المستلم الفعلي</th>
                            <th style="width: 12%; color:#fff; text-align:center;">كمية تالفة</th>
                            <th style="width: 12%; color:#fff; text-align:center;">الكمية المقبولة</th>
                            <th style="width: 14%; color:#fff; text-align:center;">التشغيلة (Batch)</th>
                            <th style="width: 15%; color:#fff; text-align:center;">تاريخ الانتهاء</th>
                        </tr>
                    </thead>
                    <tbody id="grnItemsBody">
                        <?php if(!empty($poItems)): foreach($poItems as $poItem): 
                            if(empty($poItem->product_id)) continue; // استلام المنتجات الفيزيائية فقط
                        ?>
                        <tr class="grn-row">
                            <td>
                                <input type="hidden" name="product_id[]" value="<?php echo $poItem->product_id; ?>">
                                <input type="hidden" name="ordered_qty[]" value="<?php echo $poItem->quantity; ?>">
                                <strong class="text-dark"><?php echo htmlspecialchars($poItem->description); ?></strong>
                                <div class="text-muted font-monospace" style="font-size:11px;">SKU: <?php echo htmlspecialchars($poItem->product_sku); ?></div>
                            </td>
                            <td class="text-center font-monospace fw-bold text-muted align-middle"><?php echo $poItem->quantity; ?></td>
                            <td><input type="number" step="0.01" name="received_qty[]" class="form-control form-control-sm text-center rec-qty fw-bold text-primary" value="<?php echo $poItem->quantity; ?>" required oninput="calcGRN(this)"></td>
                            <td><input type="number" step="0.01" name="damaged_qty[]" class="form-control form-control-sm text-center dam-qty fw-bold text-danger" value="0" required oninput="calcGRN(this)"></td>
                            <td class="text-center align-middle font-monospace fw-bold text-success acc-qty" style="direction:ltr; font-size:16px;"><?php echo $poItem->quantity; ?></td>
                            <td><input type="text" name="batch_number[]" class="form-control form-control-sm font-monospace" placeholder="Lot / Serial"></td>
                            <td><input type="date" name="expiry_date[]" class="form-control form-control-sm"></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <!-- إدخال يدوي إن لم يكن هناك PO -->
                        <tr class="grn-row">
                            <td>
                                <select name="product_id[]" class="form-control form-control-sm" required>
                                    <option value="">-- حدد المنتج --</option>
                                    <?php foreach($products as $p): ?><option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option><?php endforeach; ?>
                                </select>
                                <input type="hidden" name="ordered_qty[]" value="0">
                            </td>
                            <td class="text-center font-monospace fw-bold text-muted align-middle">0</td>
                            <td><input type="number" step="0.01" name="received_qty[]" class="form-control form-control-sm text-center rec-qty fw-bold text-primary" value="1" required oninput="calcGRN(this)"></td>
                            <td><input type="number" step="0.01" name="damaged_qty[]" class="form-control form-control-sm text-center dam-qty fw-bold text-danger" value="0" required oninput="calcGRN(this)"></td>
                            <td class="text-center align-middle font-monospace fw-bold text-success acc-qty" style="direction:ltr; font-size:16px;">1.00</td>
                            <td><input type="text" name="batch_number[]" class="form-control form-control-sm font-monospace" placeholder="Lot / Serial"></td>
                            <td><input type="date" name="expiry_date[]" class="form-control form-control-sm"></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="flex-1">
                    <?php if(!$poData): ?>
                        <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addGRNRow()"><i class="fas fa-plus"></i> إضافة صنف</button>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="form-label">ملاحظات المستودع</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="ملاحظات حول حالة البضاعة..."></textarea>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="form-group border p-3 rounded bg-light">
                        <label class="form-label text-primary"><i class="fas fa-camera"></i> إرفاق بوليصة الاستلام (Delivery Note Copy)</label>
                        <input type="file" name="attachment" class="form-control bg-white" accept="image/*,.pdf">
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-success" id="btnSubmit" onclick="return confirm('هل أنت متأكد من صحة الكميات؟ سيتم إضافة المقبول إلى رصيد المستودع فوراً.');"><i class="fas fa-check-double"></i> اعتماد وإدخال للمخزون</button>
            <a href="<?php echo URLROOT; ?>/grn/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<template id="grnRowTemplate">
    <tr class="grn-row">
        <td>
            <select name="product_id[]" class="form-control form-control-sm" required>
                <option value="">-- حدد المنتج --</option>
                <?php foreach($products as $p): ?><option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option><?php endforeach; ?>
            </select>
            <input type="hidden" name="ordered_qty[]" value="0">
        </td>
        <td class="text-center font-monospace fw-bold text-muted align-middle">0</td>
        <td><input type="number" step="0.01" name="received_qty[]" class="form-control form-control-sm text-center rec-qty fw-bold text-primary" value="1" required oninput="calcGRN(this)"></td>
        <td><input type="number" step="0.01" name="damaged_qty[]" class="form-control form-control-sm text-center dam-qty fw-bold text-danger" value="0" required oninput="calcGRN(this)"></td>
        <td class="text-center align-middle font-monospace fw-bold text-success acc-qty" style="direction:ltr; font-size:16px;">1.00</td>
        <td><input type="text" name="batch_number[]" class="form-control form-control-sm font-monospace" placeholder="Lot / Serial"></td>
        <td><input type="date" name="expiry_date[]" class="form-control form-control-sm"></td>
    </tr>
</template>

<script>
    function addGRNRow() {
        const temp = document.getElementById('grnRowTemplate');
        document.getElementById('grnItemsBody').appendChild(temp.content.cloneNode(true));
    }

    function calcGRN(el) {
        let row = el.closest('.grn-row');
        let rec = parseFloat(row.querySelector('.rec-qty').value) || 0;
        let dam = parseFloat(row.querySelector('.dam-qty').value) || 0;
        if(dam > rec) {
            alert('الكمية التالفة لا يمكن أن تكون أكبر من المستلمة!');
            row.querySelector('.dam-qty').value = 0;
            dam = 0;
        }
        let acc = rec - dam;
        row.querySelector('.acc-qty').innerText = acc.toFixed(2);
    }

    document.getElementById('grnForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري إدخال البضاعة للمستودع...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
    });
</script>