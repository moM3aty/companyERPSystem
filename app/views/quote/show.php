<?php
// app/views/quote/show.php
$q = $data['quote'] ?? null;
$items = $data['items'] ?? [];

// تحديد الجهة المستهدفة (عميل مسجل أم عميل محتمل)
$targetName = $q->customer_name ?: $q->lead_name;
$targetType = $q->customer_name ? 'عميل مسجل' : 'عميل محتمل (Lead)';
$targetPhone = $q->customer_phone ?: $q->lead_phone;
$targetEmail = $q->customer_email ?: $q->lead_email;
?>

<div class="card" style="max-width: 900px; margin: 0 auto; border:none; box-shadow: var(--shadow-md);">
    
    <!-- شريط الإجراءات (يختفي عند الطباعة) -->
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center border-bottom">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-file-signature text-primary"></i> تفاصيل عرض السعر</h3>
        <div class="d-flex gap-2">
            <button class="btn btn-dark btn-sm fw-bold" onclick="window.print()"><i class="fas fa-print"></i> طباعة العرض (PDF)</button>
            <a href="<?php echo URLROOT; ?>/quote/index" class="btn btn-secondary btn-sm">العودة للسجل</a>
        </div>
    </div>
    
    <!-- محتوى الفاتورة للطباعة -->
    <div class="card-body p-5 bg-white" id="printArea">
        
        <!-- الترويسة العليا -->
        <div class="row border-bottom pb-4 mb-4">
            <div class="col-sm-6">
                <h2 class="fw-black text-primary mb-2" style="letter-spacing: 1px;">QUOTATION</h2>
                <div class="text-muted font-monospace mb-1">رقم العرض: <span class="fw-bold text-dark fs-5"><?php echo htmlspecialchars($q->quote_number); ?></span></div>
                <div class="text-muted mb-1">تاريخ الإصدار: <span class="font-monospace fw-bold text-dark"><?php echo $q->quote_date; ?></span></div>
                <?php if(!empty($q->expiry_date)): ?>
                    <div class="text-danger mb-1">تاريخ الانتهاء (صلاحية): <span class="font-monospace fw-bold"><?php echo $q->expiry_date; ?></span></div>
                <?php endif; ?>
            </div>
            
            <div class="col-sm-6 text-left" style="text-align: left; direction: ltr;">
                <h5 class="fw-bold text-muted mb-2">مقدم إلى (Client Info)</h5>
                <h4 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($targetName); ?> <span class="badge bg-light text-muted fs-6 ms-2"><?php echo $targetType; ?></span></h4>
                <?php if(!empty($targetPhone)): ?>
                    <div class="text-muted"><i class="fas fa-phone-alt ms-1"></i> <?php echo htmlspecialchars($targetPhone); ?></div>
                <?php endif; ?>
                <?php if(!empty($targetEmail)): ?>
                    <div class="text-muted"><i class="fas fa-envelope ms-1"></i> <?php echo htmlspecialchars($targetEmail); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- جدول المنتجات (من المخزون) -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered text-center align-middle mb-0">
                <thead style="background-color: var(--primary); color: #fff;">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 45%; text-align: right;">المنتج / وصف الخدمة</th>
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

        <!-- الإجمالي الكلي والملاحظات -->
        <div class="row">
            <div class="col-sm-7">
                <?php if(!empty($q->notes)): ?>
                <div class="p-3 bg-light rounded text-dark mt-2" style="border-right: 4px solid var(--primary); font-size:14px; line-height:1.7;">
                    <strong class="d-block mb-1 text-primary">الشروط والأحكام:</strong>
                    <?php echo nl2br(htmlspecialchars($q->notes)); ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="col-sm-5">
                <table class="table table-borderless text-left" style="text-align: left; direction: ltr;">
                    <tr>
                        <td class="fw-bold text-dark fs-5">الإجمالي (Grand Total)</td>
                        <td class="font-monospace fw-black text-primary fs-3 text-right" style="text-align: right; border-bottom: 2px solid var(--primary);">
                            SAR <?php echo number_format($q->total_amount, 2); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- تذييل الفاتورة للطباعة -->
        <div class="text-center text-muted mt-5 pt-4 border-top d-print-block" style="font-size: 12px;">
            نأمل أن يحوز عرضنا على رضاكم. لمزيد من الاستفسارات يرجى التواصل معنا.<br>
            <span class="font-monospace">Generated by ERP System - <?php echo date('Y-m-d H:i'); ?></span>
        </div>

    </div>
</div>

<style>
@media print { 
    body { background:#fff !important; } 
    .d-print-none, .sidebar, .topbar { display: none !important; } 
    .main-content { margin: 0 !important; padding: 0 !important; } 
    .card { box-shadow: none !important; border: none !important; } 
}
</style>