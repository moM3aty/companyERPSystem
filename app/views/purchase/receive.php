<?php
// المسار: app/views/purchase/receive.php
$order =$order ?? ($data['order'] ?? null);
$items = $items ?? ($data['items'] ?? null);
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-success text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white"><i class="fas fa-box-open"></i> استلام بضاعة لأمر شراء #<?php echo htmlspecialchars($order->po_number); ?></h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/purchase/receive/<?php echo $order->id; ?>" method="POST" id="receiveForm">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> أدخل الكميات التي تم استلامها فعلياً لتحديث المخزون وإضافتها لحساب المورد.
            </div>

            <div class="table-responsive mt-4">
                <table class="table border rounded">
                    <thead class="bg-light">
                        <tr>
                            <th>المنتج</th>
                            <th class="text-center">مطلوب</th>
                            <th class="text-center">تم استلامه مسبقاً</th>
                            <th class="text-center">المتبقي</th>
                            <th style="width: 150px;">استلام الآن</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): 
                            $remaining = $item->quantity_ordered - $item->quantity_received;
                            if ($remaining < 0) $remaining = 0;
                        ?>
                        <tr>
                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($item->product_name); ?></td>
                            <td class="text-center font-monospace"><?php echo $item->quantity_ordered; ?></td>
                            <td class="text-center font-monospace text-success"><?php echo $item->quantity_received; ?></td>
                            <td class="text-center font-monospace text-danger fw-bold"><?php echo $remaining; ?></td>
                            <td>
                                <input type="number" name="received_items[<?php echo $item->product_id; ?>][quantity_received]" 
                                       class="form-control font-monospace text-center" 
                                       min="0" max="<?php echo $remaining; ?>" value="<?php echo $remaining > 0 ? $remaining : 0; ?>" 
                                       <?php echo $remaining == 0 ? 'readonly' : ''; ?>>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-success" id="btnSubmit"><i class="fas fa-check"></i> تأكيد الاستلام</button>
            <a href="<?php echo URLROOT; ?>/purchase/show/<?php echo $order->id; ?>" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('receiveForm').addEventListener('submit', () => {
        document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري التحديث...';
    });
</script>