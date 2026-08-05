<?php
// المسار: app/views/reports/sales.php
/** @var array $data */
$data = $data ?? [];
$sales = $data['sales'] ?? [];
$startDate = $data['start_date'] ?? date('Y-m-01');
$endDate = $data['end_date'] ?? date('Y-m-t');
?>

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-file-invoice-dollar" style="color:var(--success);"></i> تقرير المبيعات التفصيلي
        </h3>
    </div>
    
    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <form action="<?php echo URL_ROOT; ?>/report/sales" method="GET" style="display:flex; align-items:center; gap:8px; background:var(--card-bg); padding:6px 12px; border-radius:8px; border:1px solid var(--border);">
            <input type="date" name="start_date" value="<?php echo htmlspecialchars((string)$startDate); ?>" style="border:none; outline:none; font-family:'Cairo'; font-size:13px; color:var(--text-dark);">
            <span style="color:var(--text-muted); font-size:12px;">إلى</span>
            <input type="date" name="end_date" value="<?php echo htmlspecialchars((string)$endDate); ?>" style="border:none; outline:none; font-family:'Cairo'; font-size:13px; color:var(--text-dark);">
            <button type="submit" style="background:var(--page-bg); border:1px solid var(--border); padding:4px 12px; border-radius:6px; cursor:pointer; color:var(--text-body); font-family:'Cairo'; font-weight:600; font-size:12px;">تصفية</button>
        </form>

        <a href="<?php echo URL_ROOT; ?>/report/exportCsv/sales?start_date=<?php echo urlencode((string)$startDate); ?>&end_date=<?php echo urlencode((string)$endDate); ?>" style="padding:8px 16px; background:#10b981; color:#fff; border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 10px rgba(16,185,129,0.2);">
            <i class="fas fa-file-excel"></i> تصدير Excel
        </a>
        
        <button onclick="window.print()" style="padding:8px 16px; background:var(--text-dark); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-file-pdf"></i> طباعة PDF
        </button>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="padding:20px; border-bottom:1px solid var(--border); background:#f8fafc; text-align:center;" class="print-header">
        <h2 style="margin:0 0 5px; font-size:20px; font-weight:800;">تقرير مبيعات فترة المبيعات</h2>
        <p style="margin:0; font-size:13px; color:var(--text-muted);">من: <?php echo htmlspecialchars((string)$startDate); ?> — إلى: <?php echo htmlspecialchars((string)$endDate); ?></p>
    </div>
    
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#fff; border-bottom:2px solid var(--text-dark);">
                <tr>
                    <th style="padding:14px 20px; font-size:12px; font-weight:800; color:var(--text-body);">رقم الفاتورة</th>
                    <th style="padding:14px 20px; font-size:12px; font-weight:800; color:var(--text-body);">التاريخ والوقت</th>
                    <th style="padding:14px 20px; font-size:12px; font-weight:800; color:var(--text-body);">العميل</th>
                    <th style="padding:14px 20px; font-size:12px; font-weight:800; color:var(--text-body);">مندوب المبيعات</th>
                    <th style="padding:14px 20px; font-size:12px; font-weight:800; color:var(--text-body); text-align:left;">إجمالي الفاتورة</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grandTotal = 0;
                if(!empty($sales)): foreach($sales as $s): 
                    $grandTotal += $s->total_amount;
                ?>
                <tr style="border-bottom:1px dashed var(--border);">
                    <td style="padding:12px 20px; font-family:monospace; font-weight:700; color:var(--primary-dark);"><?php echo htmlspecialchars($s->invoice_number); ?></td>
                    <td style="padding:12px 20px; font-size:13px; color:var(--text-muted);"><?php echo date('Y-m-d H:i', strtotime($s->created_at)); ?></td>
                    <td style="padding:12px 20px; font-weight:600; color:var(--text-dark);"><?php echo htmlspecialchars($s->customer_name); ?></td>
                    <td style="padding:12px 20px; font-size:13px; color:var(--text-body);"><i class="fas fa-user-tie" style="color:var(--border);"></i> <?php echo htmlspecialchars($s->sales_rep ?? 'غير محدد'); ?></td>
                    <td style="padding:12px 20px; font-family:monospace; font-weight:800; color:var(--success); direction:ltr; text-align:right; font-size:15px;"><?php echo number_format($s->total_amount, 2); ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:var(--text-muted);">لا توجد مبيعات مسجلة في هذه الفترة المحددة.</td>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot style="background:#f1f5f9; border-top:2px solid var(--text-dark);">
                <tr>
                    <td colspan="4" style="padding:16px 20px; font-weight:800; font-size:16px; color:var(--text-dark);">الإجمالي الكلي للفترة:</td>
                    <td style="padding:16px 20px; font-family:monospace; font-weight:900; font-size:18px; color:var(--text-dark); direction:ltr; text-align:right;"><?php echo number_format($grandTotal, 2); ?> ر.س</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
    .print-header { display: none; }
    @media print {
        .print-header { display: block !important; }
        body { background: #fff; }
        .toolbar { display: none !important; }
    }
</style>