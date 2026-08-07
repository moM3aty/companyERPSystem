<?php
// المسار: app/views/purchase/view.php
$order =$order ?? ($data['order'] ?? null);
$items = $items ?? ($data['items'] ?? null);
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white"><i class="fas fa-file-invoice"></i> أمر شراء #<?php echo htmlspecialchars($order->po_number); ?></h3>
        <span class="badge badge-secondary" style="background: rgba(255,255,255,0.2); border:none;"><?php echo date('Y-m-d', strtotime($order->created_at)); ?></span>
    </div>
    
    <div class="card-body">
        <div class="form-grid mb-4">
            <div>
                <div class="text-muted fs-6 fw-bold text-uppercase mb-1">المورد</div>
                <div class="fs-5 fw-bold text-dark"><i class="fas fa-truck-field text-primary"></i> <?php echo htmlspecialchars($order->supplier_name); ?></div>
                <div class="text-muted fs-6 mt-1"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order->supplier_phone ?? '—'); ?></div>
            </div>
            <div class="text-left">
                <div class="text-muted fs-6 fw-bold text-uppercase mb-1">حالة الطلب</div>
                <?php 
                    $statusClass = match($order->status) {
                        'pending' => 'badge-warning', 'approved' => 'badge-info', 'ordered' => 'badge-primary', 'delivered' => 'badge-success', 'cancelled' => 'badge-danger', default => 'badge-secondary'
                    };
                    $statusLabel = match($order->status) {
                        'pending' => 'قيد الانتظار', 'approved' => 'معتمد', 'ordered' => 'تم الطلب', 'delivered' => 'مستلم', 'cancelled' => 'ملغي', default => $order->status
                    };
                ?>
                <span class="badge <?php echo $statusClass; ?> fs-6 py-2 px-3"><?php echo $statusLabel; ?></span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table border rounded">
                <thead class="bg-light">
                    <tr>
                        <th>المنتج / الصنف</th>
                        <th class="text-center">الكمية المطلوبة</th>
                        <th class="text-center">الكمية المستلمة</th>
                        <th>سعر الوحدة</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                    <tr>
                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($item->product_name); ?> <br><span class="text-muted font-monospace fs-6"><?php echo htmlspecialchars($item->sku); ?></span></td>
                        <td class="text-center font-monospace fw-bold"><?php echo $item->quantity_ordered; ?></td>
                        <td class="text-center font-monospace fw-bold text-success"><?php echo $item->quantity_received; ?></td>
                        <td class="font-monospace"><?php echo number_format($item->unit_price, 2); ?></td>
                        <td class="font-monospace fw-bold text-primary"><?php echo number_format($item->total, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <div class="p-3 bg-light border rounded text-left" style="min-width: 250px;">
                <div class="text-muted fw-bold mb-1">الإجمالي الكلي</div>
                <div class="font-monospace fs-3 fw-bold text-success"><?php echo number_format($order->total_amount, 2); ?> <span class="fs-6 text-muted">ر.س</span></div>
            </div>
        </div>
        
        <?php if($order->notes): ?>
            <div class="mt-4 p-3 bg-info-light border rounded">
                <strong>ملاحظات:</strong> <?php echo nl2br(htmlspecialchars($order->notes)); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="card-footer d-flex justify-content-between">
        <a href="<?php echo URLROOT; ?>/purchase/index" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> عودة للقائمة</a>
        <div class="d-flex gap-2">
            <button class="btn btn-secondary" onclick="window.print()"><i class="fas fa-print"></i> طباعة</button>
            <?php if (in_array($order->status, ['pending', 'approved', 'ordered'])) : ?>
                <a href="<?php echo URLROOT; ?>/purchase/receive/<?php echo $order->id; ?>" class="btn btn-success"><i class="fas fa-box-open"></i> استلام البضاعة</a>
            <?php endif; ?>
        </div>
    </div>
</div>