<?php
// المسار: app/views/stocktake/index.php
$adjustments = $data['adjustments'] ?? [];
?>

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-clipboard-check" style="color:var(--primary);"></i> سجل تسويات المخزون (Stock Adjustments)
        </h3>
        <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">تتبع حركات الجرد، الفوائض، والتوالف</p>
    </div>
    
    <div style="display:flex; gap:10px;">
        <a href="<?php echo URL_ROOT; ?>/stocktake/create" style="padding:10px 20px; background:var(--primary); color:#fff; border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; transition:0.2s; box-shadow:0 4px 10px rgba(20,184,166,0.2);">
            <i class="fas fa-plus"></i> تسجيل حركة تسوية
        </a>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">الرقم المرجعي</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">التاريخ</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">الصنف</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">نوع الحركة</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">الكمية</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">المسؤول</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($adjustments)): foreach($adjustments as $adj): 
                    $typeClass = match($adj->type) {
                        'addition' => 'badge-success',
                        'subtraction' => 'badge-warning',
                        'damage' => 'badge-danger',
                        'loss' => 'badge-danger',
                        default => 'badge-secondary'
                    };
                    $typeLabel = match($adj->type) {
                        'addition' => '<i class="fas fa-arrow-trend-up"></i> إضافة / فائض',
                        'subtraction' => '<i class="fas fa-arrow-trend-down"></i> خصم / عجز',
                        'damage' => '<i class="fas fa-house-crack"></i> تالف',
                        'loss' => '<i class="fas fa-circle-minus"></i> مفقود',
                        default => $adj->type
                    };
                    $qtySign = $adj->type === 'addition' ? '+' : '-';
                    $qtyColor = $adj->type === 'addition' ? 'var(--success)' : 'var(--danger)';
                ?>
                <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;">
                    <td style="padding:14px 20px; font-family:monospace; font-weight:700; color:var(--text-dark); font-size:13px;"><?php echo htmlspecialchars($adj->reference_no); ?></td>
                    <td style="padding:14px 20px; font-size:13px; color:var(--text-body);"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($adj->date)); ?></td>
                    <td style="padding:14px 20px;">
                        <div style="font-weight:700; color:var(--text-dark); font-size:14px;"><?php echo htmlspecialchars($adj->product_name); ?></div>
                        <div style="font-size:11px; color:var(--text-muted);"><i class="fas fa-barcode"></i> SKU: <?php echo htmlspecialchars($adj->sku); ?></div>
                    </td>
                    <td style="padding:14px 20px;">
                        <span class="badge <?php echo $typeClass; ?>" style="padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold;"><?php echo $typeLabel; ?></span>
                    </td>
                    <td style="padding:14px 20px; font-family:monospace; font-size:15px; font-weight:800; color:<?php echo $qtyColor; ?>; direction:ltr; text-align:right;">
                        <?php echo $qtySign . $adj->quantity; ?>
                    </td>
                    <td style="padding:14px 20px; font-size:12px; color:var(--text-muted);"><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($adj->created_by_name ?? 'النظام'); ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:60px 20px;">
                        <i class="fas fa-clipboard-list" style="font-size:40px; color:var(--border); margin-bottom:12px;"></i>
                        <h4 style="margin:0 0 6px; font-size:15px; color:var(--text-dark);">سجل الجرد فارغ</h4>
                        <p style="margin:0; font-size:13px; color:var(--text-muted);">لم يتم تسجيل أي تسويات مخزنية حتى الآن.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .badge-success { background-color: var(--success-light); color: #15803d; border: 1px solid var(--success); }
    .badge-danger { background-color: var(--danger-light); color: #dc2626; border: 1px solid var(--danger); }
    .badge-warning { background-color: var(--accent-light); color: #b45309; border: 1px solid var(--accent); }
    .badge-secondary { background-color: var(--page-bg); color: var(--text-muted); border: 1px solid var(--border); }
</style>