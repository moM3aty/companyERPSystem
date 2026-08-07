<?php
// المسار: app/views/sales_orders/view.php

/** @var array $data */
$order = $data['order'] ?? null;
$items = $data['items'] ?? [];
?>

<div style="max-width:900px; margin:0 auto; background:#fff; border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-md); overflow:hidden;">

    <!-- رأس المستند للطباعة -->
    <div style="padding:40px 40px 20px; display:flex; justify-content:space-between; border-bottom:2px solid var(--primary);">
        <div>
            <h1 style="margin:0; font-size:28px; font-weight:900; color:var(--text-dark);">أمر بيع</h1>
            <p style="margin:5px 0 0; color:var(--text-muted); font-size:14px;">Sales Order</p>
        </div>
        <div style="text-align:left;">
            <div style="font-size:18px; font-weight:800; font-family:monospace; color:var(--primary); direction:ltr;"><?php echo htmlspecialchars($order->order_number); ?></div>
            <div style="font-size:14px; color:var(--text-body); margin-top:5px;">التاريخ: <?php echo date('Y-m-d', strtotime($order->order_date)); ?></div>
            <div style="margin-top:10px;">
                <?php
                $badgeStyle = $order->status == 'draft' ? 'background:#e2e8f0; color:#475569;' : 'background:#dcfce7; color:#15803d;';
                $badgeText = $order->status == 'draft' ? 'مسودة' : 'مؤكد';
                ?>
                <span style="padding:4px 12px; border-radius:20px; font-size:12px; font-weight:bold; <?php echo $badgeStyle; ?>"><?php echo $badgeText; ?></span>
            </div>
        </div>
    </div>

    <!-- معلومات العميل -->
    <div style="padding:30px 40px; background:#f8fafc; border-bottom:1px solid var(--border);">
        <h4 style="margin:0 0 10px; font-size:14px; color:var(--text-muted); text-transform:uppercase;">معلومات العميل</h4>
        <div style="font-size:18px; font-weight:800; color:var(--text-dark); margin-bottom:5px;"><?php echo htmlspecialchars($order->customer_name); ?></div>
        <div style="font-size:14px; color:var(--text-body);"><i class="fas fa-phone fa-fw"></i> <?php echo htmlspecialchars($order->phone ?? 'غير مسجل'); ?></div>
        <div style="font-size:14px; color:var(--text-body);"><i class="fas fa-map-marker-alt fa-fw"></i> <?php echo htmlspecialchars($order->address ?? 'غير مسجل'); ?></div>
    </div>

    <!-- جدول الأصناف -->
    <div style="padding:40px;">
        <table style="width:100%; border-collapse:collapse; text-align:right; margin-bottom:20px;">
            <thead>
                <tr style="background:var(--primary); color:#fff;">
                    <th style="padding:12px 15px; font-size:13px;">المنتج / الوصف</th>
                    <th style="padding:12px 15px; font-size:13px; text-align:center;">الكمية</th>
                    <th style="padding:12px 15px; font-size:13px; text-align:left;">السعر</th>
                    <th style="padding:12px 15px; font-size:13px; text-align:left;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:15px; font-weight:700; color:var(--text-dark);"><?php echo htmlspecialchars($item->product_name); ?> <br><span style="font-size:11px; color:var(--text-muted); font-weight:normal; font-family:monospace;"><?php echo htmlspecialchars($item->sku); ?></span></td>
                        <td style="padding:15px; text-align:center; font-weight:bold;"><?php echo $item->quantity; ?></td>
                        <td style="padding:15px; text-align:left; font-family:monospace;"><?php echo number_format($item->price, 2); ?></td>
                        <td style="padding:15px; text-align:left; font-family:monospace; font-weight:bold; color:var(--primary-dark);"><?php echo number_format($item->subtotal, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- الإجماليات -->
        <div style="display:flex; justify-content:flex-end;">
            <table style="width:300px; border-collapse:collapse;">
                <tr style="border-bottom:2px solid var(--text-dark);">
                    <td style="padding:10px; font-weight:800; font-size:16px;">المبلغ الإجمالي:</td>
                    <td style="padding:10px; text-align:left; font-family:monospace; font-size:20px; font-weight:900; color:var(--success);"><?php echo number_format($order->total_amount, 2); ?> ر.س</td>
                </tr>
            </table>
        </div>

        <?php if ($order->notes): ?>
            <div style="margin-top:40px; padding:15px; background:var(--page-bg); border-radius:8px; border-right:4px solid var(--info);">
                <h4 style="margin:0 0 5px; font-size:13px; color:var(--text-muted);">ملاحظات وشروط:</h4>
                <p style="margin:0; font-size:14px; color:var(--text-dark);"><?php echo nl2br(htmlspecialchars($order->notes)); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- أزرار التحكم (تختفي عند الطباعة) -->
    <div style="padding:20px 40px; background:#f8fafc; border-top:1px solid var(--border); display:flex; justify-content:space-between;" class="d-print-none">
        <a href="<?php echo URLROOT; ?>/salesOrder/index" style="padding:10px 20px; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600; font-size:13px; background:#fff;"><i class="fas fa-arrow-right"></i> رجوع</a>

        <div style="display:flex; gap:10px;">
            <button onclick="window.print()" style="padding:10px 20px; background:var(--text-dark); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:600; cursor:pointer; font-size:13px;"><i class="fas fa-print"></i> طباعة الأمر</button>
            <?php if ($order->status == 'draft'): ?>
                <!-- اختياري: زر لتحويل الأمر إلى فاتورة -->
                <button onclick="alert('سيتم برمجة تحويل الأمر إلى فاتورة مبيعات في خطوة التحديثات.')" style="padding:10px 20px; background:var(--success); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:600; cursor:pointer; font-size:13px;"><i class="fas fa-file-invoice-dollar"></i> تحويل لفاتورة</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    @media print {
        .d-print-none {
            display: none !important;
        }

        body {
            background: #fff;
        }

        .main-content {
            margin: 0;
        }

        .topbar,
        .sidebar {
            display: none;
        }
    }
</style>