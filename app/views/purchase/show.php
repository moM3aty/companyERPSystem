<?php
// app/views/purchase/show.php
$p = $data['purchase'] ?? null;
$items = $data['items'] ?? [];
?>

<div class="card" style="max-width: 900px; margin: 0 auto; border:none; box-shadow: var(--shadow-md);">
    <!-- شريط الإجراءات (لا يظهر في الطباعة) -->
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center border-bottom">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-file-invoice text-primary"></i> أمر شراء (Purchase Order)</h3>
        <div class="d-flex gap-2">
            <button class="btn btn-dark btn-sm fw-bold" onclick="window.print()"><i class="fas fa-print"></i> طباعة (PDF)</button>
            <a href="<?php echo URLROOT; ?>/purchase/index" class="btn btn-secondary btn-sm">رجوع</a>
        </div>
    </div>
    
    <!-- محتوى الفاتورة للطباعة -->
    <div class="card-body p-5 bg-white" id="printArea">
        
        <!-- الترويسة -->
        <div class="row border-bottom pb-4 mb-4">
            <div class="col-sm-6">
                <h2 class="fw-black text-primary mb-1" style="letter-spacing: 1px;">PURCHASE ORDER</h2>
                <div class="text-muted font-monospace mb-2">رقم الأمر: <span class="fw-bold text-dark fs-5"><?php echo htmlspecialchars($p->order_number); ?></span></div>
                <div class="text-muted mb-1">تاريخ الطلب: <span class="font-monospace fw-bold text-dark"><?php echo $p->order_date; ?></span></div>
                <div class="text-muted mb-1">الحالة: <span class="badge bg-secondary"><?php echo htmlspecialchars($p->status); ?></span></div>
            </div>
            
            <div class="col-sm-6 text-left" style="text-align: left; direction: ltr;">
                <h5 class="fw-bold text-muted mb-2">مُوجه إلى المورد (Vendor)</h5>
                <h4 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($p->supplier_name); ?></h4>
                <?php if(!empty($p->supplier_phone)): ?>
                    <div class="text-muted"><i class="fas fa-phone-alt ms-1"></i> <?php echo htmlspecialchars($p->supplier_phone); ?></div>
                <?php endif; ?>
                <?php if(!empty($p->supplier_email)): ?>
                    <div class="text-muted"><i class="fas fa-envelope ms-1"></i> <?php echo htmlspecialchars($p->supplier_email); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- جدول الأصناف -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered text-center align-middle mb-0">
                <thead style="background-color: var(--slate-100);">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 45%; text-align: right;">وصف الصنف / المنتج</th>
                        <th style="width: 15%;">الكمية</th>
                        <th style="width: 15%;">سعر الوحدة</th>
                        <th style="width: 20%;">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach($items as $item): ?>
                    <tr>
                        <td class="text-muted"><?php echo $i++; ?></td>
                        <td class="fw-bold text-dark text-right" style="text-align: right;"><?php echo htmlspecialchars($item->product_name); ?></td>
                        <td class="font-monospace"><?php echo number_format($item->quantity, 2); ?></td>
                        <td class="font-monospace" style="direction:ltr;"><?php echo number_format($item->unit_price, 2); ?></td>
                        <td class="font-monospace fw-bold text-primary" style="direction:ltr;"><?php echo number_format($item->total_price, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- الإجماليات والملاحظات -->
        <div class="row">
            <div class="col-sm-7">
                <?php if(!empty($p->notes)): ?>
                <div class="p-3 bg-light rounded text-dark mt-2" style="border-right: 4px solid var(--primary); font-size:13px; line-height:1.6;">
                    <strong class="d-block mb-1 text-muted">ملاحظات وشروط:</strong>
                    <?php echo nl2br(htmlspecialchars($p->notes)); ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="col-sm-5">
                <table class="table table-borderless text-left" style="text-align: left; direction: ltr;">
                    <tr>
                        <td class="fw-bold text-muted fs-5">الإجمالي (Grand Total)</td>
                        <td class="font-monospace fw-black text-danger fs-3 text-right" style="text-align: right; border-bottom: 2px solid var(--danger);">
                            SAR <?php echo number_format($p->total_amount, 2); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- التوقيعات (تظهر في الطباعة فقط بشكل واضح) -->
        <div class="mt-5 pt-5 text-center d-none d-print-flex justify-content-around" style="page-break-inside: avoid;">
            <div style="width: 200px;">
                <div class="fw-bold text-muted mb-4">مسؤول المشتريات</div>
                <div style="border-bottom: 1px dashed #94a3b8;"></div>
            </div>
            <div style="width: 200px;">
                <div class="fw-bold text-muted mb-4">مدير الإدارة / الاعتماد</div>
                <div style="border-bottom: 1px dashed #94a3b8;"></div>
            </div>
        </div>

        <div class="text-center text-muted mt-5 pt-3 border-top" style="font-size: 11px;">
            تم إصدار هذا الأمر بواسطة: <?php echo htmlspecialchars($p->user_name); ?> في <?php echo date('Y-m-d H:i', strtotime($p->created_at)); ?>
        </div>

    </div>
</div>

<style>
@media print { 
    body { background:#fff !important; } 
    .d-print-none, .sidebar, .topbar { display: none !important; } 
    .main-content { margin: 0 !important; padding: 0 !important; } 
    .card { box-shadow: none !important; border: none !important; } 
    .d-print-flex { display: flex !important; }
}
</style>