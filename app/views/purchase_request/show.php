<?php
$req = $data['request'] ?? null;
$items = $data['items'] ?? [];
?>

<div class="card" style="max-width: 900px; margin: 0 auto; border:none; box-shadow: var(--shadow-md);">
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center border-bottom">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-clipboard-list text-primary"></i> طلب احتياج داخلي</h3>
        <div class="d-flex gap-2">
            <button class="btn btn-dark btn-sm fw-bold" onclick="window.print()"><i class="fas fa-print"></i> طباعة الطلب</button>
            <a href="<?php echo URLROOT; ?>/purchaseRequest/index" class="btn btn-secondary btn-sm">رجوع للسجل</a>
        </div>
    </div>
    
    <div class="card-body p-5 bg-white" id="printArea">
        <div class="row border-bottom pb-4 mb-4">
            <div class="col-sm-6">
                <h2 class="fw-black text-primary mb-2" style="letter-spacing: 1px;">PURCHASE REQUEST</h2>
                <div class="text-muted font-monospace mb-1">رقم الطلب: <span class="fw-bold text-dark fs-5"><?php echo htmlspecialchars($req->request_number); ?></span></div>
                <div class="text-muted mb-1">تاريخ الطلب: <span class="font-monospace fw-bold text-dark"><?php echo $req->request_date; ?></span></div>
                <div class="text-muted mt-2">الحالة: <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($req->status); ?></span></div>
            </div>
            <div class="col-sm-6 text-left" style="text-align: left; direction: ltr;">
                <h5 class="fw-bold text-muted mb-2">مُقدم الطلب</h5>
                <h4 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($req->user_name); ?></h4>
                <div class="text-muted">القسم: <span class="fw-bold text-primary"><?php echo htmlspecialchars($req->department ?: 'غير محدد'); ?></span></div>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered text-center align-middle mb-0">
                <thead style="background-color: var(--slate-100);">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 50%; text-align: right;">الصنف المطلوب</th>
                        <th style="width: 15%;">الكمية</th>
                        <th style="width: 15%;">السعر التقديري</th>
                        <th style="width: 15%;">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach($items as $item): ?>
                    <tr>
                        <td class="text-muted"><?php echo $i++; ?></td>
                        <td class="fw-bold text-dark text-right" style="text-align: right;"><?php echo htmlspecialchars($item->product_name); ?></td>
                        <td class="font-monospace fw-bold text-primary"><?php echo number_format($item->quantity, 2); ?></td>
                        <td class="font-monospace" style="direction:ltr;"><?php echo number_format($item->estimated_price, 2); ?></td>
                        <td class="font-monospace" style="direction:ltr;"><?php echo number_format($item->total_price, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-sm-7">
                <?php if(!empty($req->notes)): ?>
                <div class="p-3 bg-light rounded text-dark mt-2" style="border-right: 4px solid var(--primary); font-size:13px;">
                    <strong class="d-block mb-1 text-primary">المبررات / الملاحظات:</strong>
                    <?php echo nl2br(htmlspecialchars($req->notes)); ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-sm-5">
                <table class="table table-borderless text-left" style="text-align: left; direction: ltr;">
                    <tr>
                        <td class="fw-bold text-muted fs-6">التكلفة الإجمالية التقديرية</td>
                        <td class="font-monospace fw-black text-dark fs-4 text-right" style="text-align: right; border-bottom: 2px solid #ccc;">
                            SAR <?php echo number_format($req->total_estimated, 2); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mt-5 pt-5 text-center d-none d-print-flex justify-content-around" style="page-break-inside: avoid;">
            <div style="width: 200px;">
                <div class="fw-bold text-muted mb-4">توقيع الموظف الطالب</div>
                <div style="border-bottom: 1px dashed #94a3b8;"></div>
            </div>
            <div style="width: 200px;">
                <div class="fw-bold text-muted mb-4">اعتماد مدير القسم</div>
                <div style="border-bottom: 1px dashed #94a3b8;"></div>
            </div>
            <div style="width: 200px;">
                <div class="fw-bold text-muted mb-4">اعتماد الإدارة المالية</div>
                <div style="border-bottom: 1px dashed #94a3b8;"></div>
            </div>
        </div>
    </div>
</div>
<style>@media print { body { background:#fff !important; } .d-print-none, .sidebar, .topbar { display: none !important; } .main-content { margin: 0 !important; padding: 0 !important; } .card { box-shadow: none !important; border: none !important; } .d-print-flex { display: flex !important; } }</style>