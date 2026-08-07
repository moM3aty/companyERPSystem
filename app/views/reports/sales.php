<?php
// app/views/reports/sales.php
$sales = $data['sales'] ?? [];
$startDate = $data['start_date'] ?? date('Y-m-01');
$endDate = $data['end_date'] ?? date('Y-m-t');
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-success"></i> تقرير المبيعات التفصيلي</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع جميع فواتير المبيعات الصادرة خلال فترة محددة.</p>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <form action="<?php echo URLROOT; ?>/report/sales" method="GET" class="d-flex align-items-center gap-2 bg-white border rounded p-1">
            <input type="date" name="start_date" value="<?php echo htmlspecialchars((string)$startDate); ?>" class="form-control border-0" style="padding: 6px; width: 130px; font-size:13px;" required>
            <span class="text-muted" style="font-size: 12px;">إلى</span>
            <input type="date" name="end_date" value="<?php echo htmlspecialchars((string)$endDate); ?>" class="form-control border-0" style="padding: 6px; width: 130px; font-size:13px;" required>
            <button type="submit" class="btn btn-secondary" style="padding: 6px 14px;"><i class="fas fa-filter"></i> تصفية</button>
        </form>

        <a href="<?php echo URLROOT; ?>/report/exportCsv/sales?start_date=<?php echo urlencode((string)$startDate); ?>&end_date=<?php echo urlencode((string)$endDate); ?>" class="btn btn-success">
            <i class="fas fa-file-csv"></i> تصدير Excel
        </a>
        
        <button onclick="window.print()" class="btn btn-dark">
            <i class="fas fa-print"></i> طباعة التقرير
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <!-- ترويسة الطباعة تظهر فقط في وضع الـ Print -->
        <div class="text-center p-4 border-bottom d-none d-print-block">
            <h2 class="mb-2 fw-bold"><i class="fas fa-chart-bar"></i> تقرير المبيعات التفصيلي للشركة</h2>
            <p class="text-muted m-0 fs-6">الفترة من: <?php echo htmlspecialchars((string)$startDate); ?> — إلى: <?php echo htmlspecialchars((string)$endDate); ?></p>
        </div>
        
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>التاريخ والوقت</th>
                        <th>العميل</th>
                        <th>المندوب / المسؤول</th>
                        <th class="text-left">إجمالي الفاتورة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandTotal = 0;
                    if(!empty($sales)): foreach($sales as $s): 
                        $grandTotal += $s->total_amount;
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-primary"><?php echo htmlspecialchars($s->invoice_number); ?></td>
                        <td class="text-muted font-monospace" style="font-size:13px;"><i class="far fa-clock"></i> <?php echo date('Y-m-d H:i', strtotime($s->created_at)); ?></td>
                        <td class="fw-bold text-dark"><i class="fas fa-user-circle text-muted"></i> <?php echo htmlspecialchars($s->customer_name ?? 'عميل نقدي'); ?></td>
                        <td class="text-muted" style="font-size:13px;"><i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($s->sales_rep ?? 'النظام الآلي'); ?></td>
                        <td class="font-monospace fw-bold text-success fs-5" style="direction:ltr; text-align:right;"><?php echo number_format($s->total_amount, 2); ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding:60px;">
                            <i class="fas fa-file-invoice-dollar fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد أي مبيعات مسجلة في هذه الفترة المحددة.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="4" class="fw-bold text-dark p-4 fs-6">إجمالي المبيعات المحققة في هذه الفترة:</td>
                        <td class="font-monospace fw-bold text-dark p-4" style="direction:ltr; text-align:right; font-size:22px; border-bottom:4px double var(--text-dark);"><?php echo number_format($grandTotal, 2); ?> <span class="fs-6 text-muted">ر.س</span></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        .d-print-none, .sidebar, .topbar { display: none !important; }
        .main-content { margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .d-print-block { display: block !important; }
        body { background: #fff !important; }
    }
</style>