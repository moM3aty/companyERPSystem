<?php
$o = $data['order'] ?? null;
$items = $data['items'] ?? [];
?>

<div class="card" style="max-width: 900px; margin: 0 auto; border:none; box-shadow: var(--shadow-md);">
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center border-bottom">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-shopping-cart text-primary"></i> أمر بيع (Sales Order)</h3>
        <div class="d-flex gap-2">
            <button class="btn btn-dark btn-sm fw-bold" onclick="window.print()"><i class="fas fa-print"></i> طباعة (PDF)</button>
            <a href="<?php echo URLROOT; ?>/salesOrder/index" class="btn btn-secondary btn-sm">رجوع للسجل</a>
        </div>
    </div>
    
    <div class="card-body p-5 bg-white" id="printArea">
        <div class="row border-bottom pb-4 mb-4">
            <div class="col-sm-6">
                <h2 class="fw-black text-primary mb-2" style="letter-spacing: 1px;">SALES ORDER</h2>
                <div class="text-muted font-monospace mb-1">رقم الأمر: <span class="fw-bold text-dark fs-5"><?php echo htmlspecialchars($o->order_number); ?></span></div>
                <div class="text-muted mb-1">تاريخ الطلب: <span class="font-monospace fw-bold text-dark"><?php echo $o->order_date; ?></span></div>
                <div class="text-muted mb-1">الحالة: <span class="badge bg-secondary"><?php echo htmlspecialchars($o->status); ?></span></div>
            </div>
            
            <div class="col-sm-6 text-left" style="text-align: left; direction: ltr;">
                <h5 class="fw-bold text-muted mb-2">بيانات العميل (Customer)</h5>
                <h4 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($o->customer_name); ?></h4>
                <?php if(!empty($o->customer_phone)): ?><div class="text-muted"><i class="fas fa-phone-alt ms-1"></i> <?php echo htmlspecialchars($o->customer_phone); ?></div><?php endif; ?>
                <?php if(!empty($o->customer_address)): ?><div class="text-muted"><i class="fas fa-map-marker-alt ms-1"></i> <?php echo htmlspecialchars($o->customer_address); ?></div><?php endif; ?>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered text-center align-middle mb-0">
                <thead style="background-color: var(--slate-100);">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 45%; text-align: right;">الصنف / المنتج</th>
                        <th style="width: 15%;">الكمية</th>
                        <th style="width: 15%;">السعر</th>
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

        <div class="row">
            <div class="col-sm-7">
                <?php if(!empty($o->notes)): ?>
                <div class="p-3 bg-light rounded text-dark mt-2" style="border-right: 4px solid var(--primary); font-size:13px;">
                    <strong class="d-block mb-1 text-muted">ملاحظات التوصيل:</strong>
                    <?php echo nl2br(htmlspecialchars($o->notes)); ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="col-sm-5">
                <table class="table table-borderless text-left" style="text-align: left; direction: ltr;">
                    <tr>
                        <td class="fw-bold text-muted fs-5">الإجمالي (Total)</td>
                        <td class="font-monospace fw-black text-dark fs-3 text-right" style="text-align: right; border-bottom: 2px solid #ccc;">
                            SAR <?php echo number_format($o->total_amount, 2); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="text-center text-muted mt-5 pt-3 border-top" style="font-size: 11px;">
            أُصدر بواسطة: <?php echo htmlspecialchars($o->user_name); ?> | <?php echo date('Y-m-d H:i', strtotime($o->created_at)); ?>
        </div>
    </div>
</div>
<style>@media print { body { background:#fff !important; } .d-print-none, .sidebar, .topbar { display: none !important; } .main-content { margin: 0 !important; padding: 0 !important; } .card { box-shadow: none !important; border: none !important; } }</style>