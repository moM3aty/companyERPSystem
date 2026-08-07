<?php
// app/views/sales/view.php
$invoice = $data['invoice'];
$items = $data['items'];
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-success text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white"><i class="fas fa-file-invoice"></i> فاتورة مبيعات #<?php echo htmlspecialchars($invoice->invoice_number); ?></h3>
        <span class="badge badge-secondary" style="background: rgba(255,255,255,0.2); border:none; color:#fff;"><?php echo date('Y-m-d H:i', strtotime($invoice->created_at)); ?></span>
    </div>
    
    <div class="card-body">
        <div class="form-grid mb-4">
            <div>
                <div class="text-muted fs-6 fw-bold text-uppercase mb-1">بيانات العميل</div>
                <div class="fs-5 fw-bold text-dark"><i class="fas fa-user-circle text-success"></i> <?php echo htmlspecialchars($invoice->customer_name); ?></div>
                <div class="text-muted fs-6 mt-1"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($invoice->phone ?? 'غير مسجل'); ?></div>
                <div class="text-muted fs-6 mt-1"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($invoice->address ?? 'غير مسجل'); ?></div>
            </div>
            <div class="text-left">
                <div class="text-muted fs-6 fw-bold text-uppercase mb-1">المندوب (البائع)</div>
                <div class="fs-6 fw-bold text-dark"><i class="fas fa-user-tie text-primary"></i> <?php echo htmlspecialchars($invoice->sales_rep_name ?? 'النظام'); ?></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table border rounded">
                <thead class="bg-light">
                    <tr>
                        <th>المنتج / الصنف</th>
                        <th class="text-center">الكمية</th>
                        <th>سعر الوحدة</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                    <tr>
                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($item->product_name); ?> <br><span class="text-muted font-monospace fs-6"><?php echo htmlspecialchars($item->sku); ?></span></td>
                        <td class="text-center font-monospace fw-bold"><?php echo $item->quantity; ?></td>
                        <td class="font-monospace"><?php echo number_format($item->price, 2); ?></td>
                        <td class="font-monospace fw-bold text-success"><?php echo number_format($item->subtotal, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <div class="p-3 bg-success-light border border-success rounded text-left" style="min-width: 250px;">
                <div class="text-success fw-bold mb-1">الإجمالي الكلي للفاتورة</div>
                <div class="font-monospace fs-3 fw-bold text-success"><?php echo number_format($invoice->total_amount, 2); ?> <span class="fs-6 text-muted">ر.س</span></div>
            </div>
        </div>
    </div>
    
    <div class="card-footer d-flex justify-content-between mt-4">
        <a href="<?php echo URLROOT; ?>/sale/index" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> عودة للقائمة</a>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-print"></i> طباعة الفاتورة</button>
    </div>
</div>

<style>
    @media print {
        body { background: #fff !important; }
        .sidebar, .topbar, .btn { display: none !important; }
        .main-content { margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .card-header { background: #fff !important; color: #000 !important; border-bottom: 2px solid #000; }
        .card-title { color: #000 !important; }
    }
</style>