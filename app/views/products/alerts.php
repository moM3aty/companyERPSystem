<?php
// المسار: app/views/products/alerts.php
$products = $data['products'] ?? [];
?>

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--danger); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-triangle-exclamation"></i> نواقص المخزون (Reorder Alerts)
        </h3>
        <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">أصناف وصلت أو تجاوزت حد إعادة الطلب وتحتاج لشراء فوراً.</p>
    </div>
    
    <div style="display:flex; gap:10px;">
        <!-- ربط مباشر بزر أمر الشراء لتسهيل العمل -->
        <a href="<?php echo URLROOT; ?>/purchase/create" style="padding:10px 20px; background:var(--text-dark); color:#fff; border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; box-shadow:var(--shadow-sm);">
            <i class="fas fa-cart-plus"></i> إنشاء أمر شراء
        </a>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">رمز SKU / الصنف</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">التصنيف</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; text-align:center;">حد إعادة الطلب</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; text-align:center;">الرصيد الفعلي</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">حالة النقص</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($products)): foreach($products as $p): 
                    // حساب الخطورة
                    $diff = $p->quantity - $p->reorder_point;
                    $statusClass = '';
                    $statusText = '';
                    $qtyColor = '';
                    
                    if ($p->quantity <= 0) {
                        $statusClass = 'background:var(--danger); color:#fff;';
                        $statusText = 'نفد تماماً (Out of Stock)';
                        $qtyColor = 'color:var(--danger);';
                    } else {
                        $statusClass = 'background:var(--accent-light); color:#b45309; border:1px solid var(--accent);';
                        $statusText = 'يوشك على النفاد (Low Stock)';
                        $qtyColor = 'color:#b45309;';
                    }
                ?>
                <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;">
                    <td style="padding:14px 20px;">
                        <div style="font-weight:700; color:var(--text-dark); font-size:14px; margin-bottom:4px;"><?php echo htmlspecialchars($p->name); ?></div>
                        <div style="font-family:monospace; font-size:11px; color:var(--text-muted);"><i class="fas fa-barcode"></i> <?php echo htmlspecialchars($p->sku); ?></div>
                    </td>
                    <td style="padding:14px 20px; font-size:12px; color:var(--text-body);">
                        <i class="fas fa-folder-tree" style="color:var(--border);"></i> <?php echo htmlspecialchars($p->cat_name ?? 'غير مصنف'); ?>
                    </td>
                    <td style="padding:14px 20px; text-align:center; font-family:monospace; font-size:14px; font-weight:700; color:var(--text-muted);">
                        <?php echo $p->reorder_point; ?>
                    </td>
                    <td style="padding:14px 20px; text-align:center; font-family:monospace; font-size:16px; font-weight:900; <?php echo $qtyColor; ?>">
                        <?php echo $p->quantity; ?> <span style="font-size:10px; font-family:'Cairo'; font-weight:normal;"><?php echo htmlspecialchars($p->unit); ?></span>
                    </td>
                    <td style="padding:14px 20px;">
                        <span style="padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:60px 20px;">
                        <i class="fas fa-check-circle" style="font-size:48px; color:var(--success); margin-bottom:12px;"></i>
                        <h4 style="margin:0 0 6px; font-size:16px; color:var(--text-dark);">المخزون في حالة ممتازة</h4>
                        <p style="margin:0; font-size:13px; color:var(--text-muted);">لا توجد أي أصناف وصلت لحد إعادة الطلب أو نفدت من المستودع.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>