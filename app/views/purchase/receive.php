<?php
$flash = $data['flash'] ?? null;
$order = $data['order'] ?? null;
$items = $data['items'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <h3>استلام بضاعة - أمر الشراء: <?php echo $order->po_number; ?></h3>
    <div class="form-card">
        <form action="<?php echo URL_ROOT; ?>/purchase/receive/<?php echo $order->id; ?>" method="POST">
            <div class="form-section">
                <p>المورد: <strong><?php echo htmlspecialchars($order->supplier_name); ?></strong></p>
                <p>تاريخ الأمر: <?php echo $order->created_at; ?></p>
                <p>الإجمالي: <?php echo number_format($order->total_amount, 2); ?></p>
            </div>
            <div class="form-section">
                <h4>الأصناف المستلمة</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>الكمية المطلوبة</th>
                            <th>الكمية المستلمة سابقًا</th>
                            <th>الكمية المستلمة الآن</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item->product_name ?? 'منتج #' . $item->product_id); ?></td>
                            <td><?php echo $item->quantity_ordered; ?></td>
                            <td><?php echo $item->quantity_received; ?></td>
                            <td>
                                <input type="number" name="received_items[<?php echo $item->product_id; ?>]" 
                                       class="form-input" style="width:100px;" min="0" max="<?php echo $item->quantity_ordered - $item->quantity_received; ?>" 
                                       value="<?php echo $item->quantity_ordered - $item->quantity_received; ?>">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit"><i class="fas fa-check"></i> تأكيد الاستلام</button>
                <a href="<?php echo URL_ROOT; ?>/purchase/index" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>