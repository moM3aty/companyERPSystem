<?php
// app/views/sales/commissions.php
$commissions = $data['commissions'] ?? [];
?>
<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark);">
            <i class="fas fa-hand-holding-dollar" style="color:var(--success);"></i> تقرير عمولات المبيعات (Commissions)
        </h3>
        <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">تحليل أداء موظفي المبيعات وحساب العمولة المقدرة (بنسبة 5% افتراضياً).</p>
    </div>
    <button onclick="window.print()" style="padding:10px 20px; background:var(--text-dark); color:#fff; border-radius:8px; border:none; font-family:'Cairo'; font-weight:bold; cursor:pointer;"><i class="fas fa-print"></i> طباعة التقرير</button>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <table style="width:100%; border-collapse:collapse; text-align:right;">
        <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
            <tr>
                <th style="padding:14px 20px; font-size:12px; color:var(--text-muted);">مندوب المبيعات (Sales Rep)</th>
                <th style="padding:14px 20px; font-size:12px; color:var(--text-muted); text-align:center;">إجمالي الفواتير المجراة</th>
                <th style="padding:14px 20px; font-size:12px; color:var(--text-muted); text-align:left;">إجمالي المبيعات (Revenue)</th>
                <th style="padding:14px 20px; font-size:12px; color:var(--success); text-align:left;">العمولة المستحقة (Commission)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grandSales = 0; $grandCommission = 0;
            if(!empty($commissions)): foreach($commissions as $comm): 
                $grandSales += $comm->total_sales;
                $grandCommission += $comm->estimated_commission;
            ?>
            <tr style="border-bottom:1px solid var(--border);">
                <td style="padding:14px 20px; font-weight:700; color:var(--text-dark);"><i class="fas fa-user-tie" style="color:var(--primary); margin-left:8px;"></i><?php echo htmlspecialchars($comm->rep_name); ?></td>
                <td style="padding:14px 20px; text-align:center; font-weight:bold;"><span style="background:var(--page-bg); padding:4px 10px; border-radius:20px; border:1px solid var(--border);"><?php echo $comm->invoice_count; ?></span></td>
                <td style="padding:14px 20px; text-align:left; font-family:monospace; font-size:15px; font-weight:bold; direction:ltr;"><?php echo number_format($comm->total_sales, 2); ?></td>
                <td style="padding:14px 20px; text-align:left; font-family:monospace; font-size:15px; font-weight:900; color:var(--success); direction:ltr;"><?php echo number_format($comm->estimated_commission, 2); ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="4" style="text-align:center; padding:40px; color:var(--text-muted);">لم يتم تسجيل أي فواتير مربوطة بمندوبي مبيعات حتى الآن. (تأكد من اختيار المندوب عند إصدار الفاتورة).</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot style="background:#f1f5f9; border-top:2px solid var(--text-dark);">
            <tr>
                <td colspan="2" style="padding:15px; font-weight:800; font-size:16px;">الإجمالي الكلي:</td>
                <td style="padding:15px; text-align:left; font-family:monospace; font-size:18px; font-weight:900; direction:ltr;"><?php echo number_format($grandSales, 2); ?></td>
                <td style="padding:15px; text-align:left; font-family:monospace; font-size:18px; font-weight:900; color:var(--success); direction:ltr;"><?php echo number_format($grandCommission, 2); ?></td>
            </tr>
        </tfoot>
    </table>
</div>