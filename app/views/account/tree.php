<?php
// المسار: app/views/account/tree.php
$accounts = $data['accounts'] ?? [];
?>

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-sitemap" style="color:var(--primary);"></i> شجرة دليل الحسابات (Chart of Accounts)
        </h3>
        <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">هيكل الحسابات المالية للمؤسسة</p>
    </div>
    
    <div style="display:flex; gap:10px;">
        <a href="<?php echo URL_ROOT; ?>/account/create" style="padding:10px 20px; background:var(--primary); color:#fff; border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; transition:0.2s; box-shadow:0 4px 10px rgba(20,184,166,0.2);">
            <i class="fas fa-plus"></i> إضافة حساب
        </a>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">رقم الحساب</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">اسم الحساب</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">النوع (الطبيعة)</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">الحساب الأب / الرئيسي</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; text-align:left;">الرصيد الافتتاحي</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($accounts)): foreach($accounts as $acc): 
                    $typeLabel = match($acc->type) {
                        'asset' => '<span style="color:#0ea5e9; font-weight:700;"><i class="fas fa-building-columns"></i> أصول</span>',
                        'liability' => '<span style="color:#ef4444; font-weight:700;"><i class="fas fa-hand-holding-dollar"></i> خصوم (التزامات)</span>',
                        'equity' => '<span style="color:#8b5cf6; font-weight:700;"><i class="fas fa-scale-balanced"></i> حقوق ملكية</span>',
                        'revenue' => '<span style="color:#10b981; font-weight:700;"><i class="fas fa-arrow-trend-up"></i> إيرادات</span>',
                        'expense' => '<span style="color:#f59e0b; font-weight:700;"><i class="fas fa-arrow-trend-down"></i> مصروفات</span>',
                        default => $acc->type
                    };
                ?>
                <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;">
                    <td style="padding:14px 20px; font-family:monospace; font-size:14px; font-weight:700; color:var(--text-dark);"><?php echo htmlspecialchars($acc->code); ?></td>
                    <td style="padding:14px 20px; font-weight:600; color:var(--text-body);"><?php echo htmlspecialchars($acc->name); ?></td>
                    <td style="padding:14px 20px; font-size:13px;"><?php echo $typeLabel; ?></td>
                    <td style="padding:14px 20px; font-size:13px; color:var(--text-muted);">
                        <?php echo $acc->parent_name ? '<i class="fas fa-level-up-alt fa-rotate-90"></i> ' . htmlspecialchars($acc->parent_name) : '<span style="color:#cbd5e1;">حساب رئيسي</span>'; ?>
                    </td>
                    <td style="padding:14px 20px; text-align:left; font-weight:700; font-variant-numeric:tabular-nums; direction:ltr;">
                        <?php echo number_format($acc->balance, 2); ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:60px 20px;">
                        <i class="fas fa-sitemap" style="font-size:40px; color:var(--border); margin-bottom:12px;"></i>
                        <h4 style="margin:0 0 6px; font-size:15px; color:var(--text-dark);">دليل الحسابات فارغ</h4>
                        <p style="margin:0; font-size:13px; color:var(--text-muted);">قم بإضافة حسابات الأصول والخصوم لتبدأ دورتك المحاسبية.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>