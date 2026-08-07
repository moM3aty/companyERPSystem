<?php
// المسار: app/views/purchase_requests/view.php
$request = $request ?? ($data['request'] ?? null);
$items = $items ?? ($data['items'] ?? []);
$isAdmin = $isAdmin ?? ($data['is_admin'] ?? false);
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-file-signature text-primary"></i> طلب شراء داخلي (PR)</h3>
        <span class="badge badge-secondary fs-6 font-monospace py-2"><?php echo htmlspecialchars($request->request_number); ?></span>
    </div>
    
    <div class="card-body">
        <div class="form-grid mb-4">
            <div>
                <div class="text-muted fs-6 fw-bold text-uppercase mb-1">تفاصيل الطلب</div>
                <div class="fs-6 fw-bold text-dark"><i class="fas fa-user-tie text-primary"></i> مُقدم الطلب: <?php echo htmlspecialchars($request->requested_by_name); ?></div>
                <div class="text-muted fs-6 mt-1"><i class="far fa-calendar-alt"></i> التاريخ: <?php echo date('Y-m-d', strtotime($request->request_date)); ?></div>
            </div>
            <div class="text-left">
                <div class="text-muted fs-6 fw-bold text-uppercase mb-1">حالة الاعتماد</div>
                <?php 
                    $statusClass = match($request->status) {
                        'pending' => 'badge-warning', 'approved' => 'badge-success', 'rejected' => 'badge-danger', 'ordered' => 'badge-primary', default => 'badge-secondary'
                    };
                    $statusLabel = match($request->status) {
                        'pending' => 'قيد المراجعة والاعتماد', 'approved' => 'معتمد وجاهز للطلب', 'rejected' => 'مرفوض', 'ordered' => 'تم إصدار أمر شراء', default => $request->status
                    };
                ?>
                <span class="badge <?php echo $statusClass; ?> fs-6 py-2 px-3"><?php echo $statusLabel; ?></span>
            </div>
        </div>

        <div class="alert alert-secondary mb-4">
            <strong>ملاحظات ومبررات الطلب:</strong><br>
            <?php echo nl2br(htmlspecialchars($request->notes)); ?>
        </div>

        <?php if($request->status != 'pending'): ?>
            <div class="alert alert-info mb-4">
                <i class="fas fa-stamp"></i> تم <?php echo $request->status == 'approved' || $request->status == 'ordered' ? 'الاعتماد' : 'الرفض'; ?> بواسطة: <strong><?php echo htmlspecialchars($request->approved_by_name); ?></strong> في (<?php echo date('Y-m-d H:i', strtotime($request->approved_at)); ?>)
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table border rounded">
                <thead class="bg-light">
                    <tr>
                        <th>الصنف (المنتج)</th>
                        <th class="text-center">الكمية</th>
                        <th>السعر التقديري</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandTotal = 0;
                    foreach($items as $item): 
                        $subtotal = $item->quantity * $item->estimated_price;
                        $grandTotal += $subtotal;
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($item->product_name); ?> <br><span class="text-muted font-monospace fs-6"><?php echo htmlspecialchars($item->sku); ?></span></td>
                        <td class="text-center font-monospace fw-bold"><?php echo $item->quantity; ?></td>
                        <td class="font-monospace text-muted"><?php echo number_format($item->estimated_price, 2); ?></td>
                        <td class="font-monospace fw-bold text-primary"><?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="3" class="fw-bold text-dark" style="padding: 16px;">إجمالي التكلفة التقديرية للطلب:</td>
                        <td class="font-monospace fs-5 fw-bold text-dark" style="direction:ltr; padding: 16px; border-bottom:4px double var(--text-dark);"><?php echo number_format($grandTotal, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <div class="card-footer d-flex justify-content-between align-items-center bg-light">
        <a href="<?php echo URLROOT; ?>/purchaseRequest/index" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> عودة للقائمة</a>
        
        <?php if($isAdmin && $request->status == 'pending'): ?>
        <div class="d-flex gap-2">
            <form action="<?php echo URLROOT; ?>/purchaseRequest/reject/<?php echo $request->id; ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من رفض هذا الطلب؟');">
                <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> رفض الطلب</button>
            </form>
            <form action="<?php echo URLROOT; ?>/purchaseRequest/approve/<?php echo $request->id; ?>" method="POST" onsubmit="return confirm('باعتمادك للطلب سيتمكن قسم المشتريات من تحويله لأمر شراء. متابعة؟');">
                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> اعتماد والموافقة</button>
            </form>
        </div>
        <?php elseif($request->status == 'approved'): ?>
            <a href="<?php echo URLROOT; ?>/purchase/create?request_id=<?php echo $request->id; ?>" class="btn btn-primary"><i class="fas fa-cart-arrow-down"></i> تحويل إلى أمر شراء (PO)</a>
        <?php endif; ?>
    </div>
</div>