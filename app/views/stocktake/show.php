<?php
// app/views/stocktake/show.php
$stocktake = $data['stocktake'] ?? null;
$items = $data['items'] ?? [];
$products = $data['products'] ?? [];

$isCompleted = ($stocktake->status ?? '') === 'completed';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-clipboard-list text-primary"></i> تفاصيل الجرد: <?php echo htmlspecialchars($stocktake->reference); ?></h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة أصناف الجرد ومقارنة الرصيد الفعلي مع رصيد النظام.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/stocktake/index" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> عودة للقائمة
        </a>
        <?php if (!$isCompleted): ?>
            <form action="<?php echo URLROOT; ?>/stocktake/complete/<?php echo $stocktake->id; ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من اعتماد هذا الجرد؟ سيتم تحديث أرصدة جميع المنتجات الموجودة بالقائمة بشكل نهائي ولا يمكن التراجع!');">
                <button type="submit" class="btn btn-success fw-bold">
                    <i class="fas fa-check-double"></i> اعتماد الجرد وتحديث الأرصدة
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php 
    $flash = Session::getFlash();
    if ($flash): 
?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<!-- معلومات الجرد الأساسية -->
<div class="card mb-4" style="background: var(--slate-50);">
    <div class="card-body">
        <div class="row d-flex justify-content-between">
            <div style="flex: 1;">
                <div class="text-muted fs-6 mb-1">تاريخ الجرد</div>
                <div class="fw-bold text-dark fs-5"><i class="far fa-calendar-alt text-primary"></i> <?php echo date('Y-m-d', strtotime($stocktake->stocktake_date)); ?></div>
            </div>
            <div style="flex: 1;">
                <div class="text-muted fs-6 mb-1">الحالة</div>
                <div>
                    <?php if ($isCompleted): ?>
                        <span class="badge badge-success fs-6"><i class="fas fa-check-circle"></i> معتمد / مكتمل</span>
                    <?php elseif(($stocktake->status ?? '') === 'in_progress'): ?>
                        <span class="badge badge-warning fs-6"><i class="fas fa-sync fa-spin"></i> قيد التنفيذ</span>
                    <?php else: ?>
                        <span class="badge badge-secondary fs-6">مسودة</span>
                    <?php endif; ?>
                </div>
            </div>
            <div style="flex: 2;">
                <div class="text-muted fs-6 mb-1">ملاحظات</div>
                <div class="text-dark fw-bold"><?php echo htmlspecialchars($stocktake->notes ?? 'لا يوجد ملاحظات مسجلة'); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="content-grid" style="grid-template-columns: 2fr 1fr;">
    
    <!-- جدول الأصناف المدخلة -->
    <div class="card mb-0 h-100">
        <div class="card-header bg-light">
            <h3 class="card-title"><i class="fas fa-boxes-stacked text-info"></i> الأصناف المدرجة في هذا الجرد</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-white">
                        <tr>
                            <th>الصنف / الرمز</th>
                            <th class="text-center">رصيد النظام</th>
                            <th class="text-center">الرصيد الفعلي</th>
                            <th class="text-center">الفروقات</th>
                            <?php if(!$isCompleted): ?><th class="text-center">حذف</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): 
                            $varClass = $item->variance == 0 ? 'text-success' : ($item->variance < 0 ? 'text-danger' : 'text-primary');
                            $varIcon = $item->variance == 0 ? 'fa-check' : ($item->variance < 0 ? 'fa-arrow-down' : 'fa-arrow-up');
                        ?>
                        <tr>
                            <td>
                                <strong class="text-dark d-block"><?php echo htmlspecialchars($item->product_name); ?></strong>
                                <span class="text-muted font-monospace" style="font-size: 11px;">SKU: <?php echo htmlspecialchars($item->sku); ?></span>
                            </td>
                            <td class="text-center font-monospace fw-bold text-muted"><?php echo $item->system_quantity; ?></td>
                            <td class="text-center font-monospace fw-bold fs-5 text-dark"><?php echo $item->actual_quantity; ?></td>
                            <td class="text-center font-monospace fw-bold <?php echo $varClass; ?>">
                                <?php if($item->variance != 0): ?>
                                    <i class="fas <?php echo $varIcon; ?>" style="font-size: 10px;"></i>
                                <?php endif; ?>
                                <?php echo ($item->variance > 0 ? '+' : '') . $item->variance; ?>
                            </td>
                            <?php if(!$isCompleted): ?>
                            <td class="text-center">
                                <form action="<?php echo URLROOT; ?>/stocktake/removeItem/<?php echo $stocktake->id; ?>/<?php echo $item->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('إزالة هذا الصنف من الجرد؟');">
                                    <button type="submit" class="btn-icon delete"><i class="fas fa-times"></i></button>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>

                        <?php if(empty($items)): ?>
                        <tr><td colspan="5" class="text-center text-muted p-5">لا توجد أصناف مدرجة في هذه الجردية حتى الآن.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- نموذج إضافة صنف (يختفي إذا كان الجرد معتمداً) -->
    <?php if(!$isCompleted): ?>
    <div class="card mb-0">
        <div class="card-header bg-light">
            <h3 class="card-title"><i class="fas fa-plus-circle text-success"></i> إضافة صنف للجرد</h3>
        </div>
        <form action="<?php echo URLROOT; ?>/stocktake/addItem" method="POST">
            <div class="card-body d-flex flex-column gap-3">
                <input type="hidden" name="stocktake_id" value="<?php echo $stocktake->id; ?>">
                
                <div class="form-group mb-0">
                    <label class="form-label">المنتج <span class="required">*</span></label>
                    <select name="product_id" id="productSelect" class="form-control" required onchange="updateSysQty()">
                        <option value="">-- اختر المنتج --</option>
                        <?php foreach($products as $p): ?>
                            <option value="<?php echo $p->id; ?>" data-qty="<?php echo $p->quantity; ?>">
                                <?php echo htmlspecialchars($p->name); ?> (SKU: <?php echo htmlspecialchars($p->sku); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- عرض رصيد النظام للتوضيح فقط (لن يرسل، فالمتحكم يجلب الرصيد بنفسه للحماية) -->
                <div class="p-3 rounded mb-2 text-center" style="background: #e0f2fe; border: 1px dashed var(--primary);">
                    <span class="text-muted fw-bold d-block mb-1" style="font-size:12px;">رصيد النظام الحالي</span>
                    <span class="font-monospace fw-bold fs-4 text-primary" id="displaySysQty">--</span>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label">الرصيد الفعلي (الموجود بالمستودع) <span class="required">*</span></label>
                    <input type="number" name="actual_quantity" class="form-control font-monospace fs-5 fw-bold text-center text-dark" required placeholder="0">
                </div>
                
                <div class="form-group mb-0">
                    <label class="form-label">ملاحظات (سبب العجز/الزيادة)</label>
                    <input type="text" name="notes" class="form-control" placeholder="اختياري...">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success w-100"><i class="fas fa-plus"></i> إضافة الصنف للقائمة</button>
            </div>
        </form>
    </div>
    
    <script>
        function updateSysQty() {
            const select = document.getElementById('productSelect');
            const display = document.getElementById('displaySysQty');
            const selectedOption = select.options[select.selectedIndex];
            
            if (select.value === "") {
                display.innerText = "--";
            } else {
                display.innerText = selectedOption.getAttribute('data-qty');
            }
        }
    </script>
    <?php else: ?>
    <!-- رسالة بديلة للنموذج عند الاعتماد -->
    <div class="card mb-0 d-flex align-items-center justify-content-center" style="background: #f0fdf4; border: 2px dashed #86efac;">
        <div class="text-center p-5">
            <i class="fas fa-lock text-success mb-3" style="font-size: 40px; opacity:0.5;"></i>
            <h5 class="fw-bold text-dark">الجرد مغلق ومعتمد</h5>
            <p class="text-muted fs-6 mb-0">تم تحديث الأرصدة ولا يمكن إجراء المزيد من التعديلات.</p>
        </div>
    </div>
    <?php endif; ?>

</div>