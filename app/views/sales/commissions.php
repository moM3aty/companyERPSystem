<?php
// app/views/sales/commissions.php
$commissions = $data['commissions'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-hand-holding-dollar text-success"></i> تقرير عمولات المبيعات (Commissions)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تحليل أداء موظفي المبيعات وحساب العمولة المقدرة (بنسبة 5% افتراضياً).</p>
    </div>
    <button onclick="window.print()" class="btn btn-dark">
        <i class="fas fa-print"></i> طباعة التقرير
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>مندوب المبيعات (Sales Rep)</th>
                        <th class="text-center">إجمالي الفواتير المجراة</th>
                        <th class="text-left">إجمالي المبيعات (Revenue)</th>
                        <th class="text-left text-success">العمولة المستحقة (Commission)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandSales = 0; $grandCommission = 0;
                    if(!empty($commissions)): foreach($commissions as $comm): 
                        $grandSales += $comm->total_sales;
                        $grandCommission += $comm->estimated_commission;
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-user-tie text-primary me-2"></i><?php echo htmlspecialchars($comm->rep_name); ?></td>
                        <td class="text-center"><span class="badge badge-secondary"><?php echo $comm->invoice_count; ?></span></td>
                        <td class="font-monospace fw-bold text-left" style="direction:ltr;"><?php echo number_format($comm->total_sales, 2); ?></td>
                        <td class="font-monospace fw-bold text-success text-left" style="direction:ltr;"><?php echo number_format($comm->estimated_commission, 2); ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="4" class="text-center text-muted" style="padding:40px;">لم يتم تسجيل أي فواتير مربوطة بمندوبي مبيعات حتى الآن.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="2" class="fw-bold text-dark" style="padding:16px;">الإجمالي الكلي:</td>
                        <td class="font-monospace fw-bold text-dark fs-5 text-left" style="direction:ltr; padding:16px;"><?php echo number_format($grandSales, 2); ?></td>
                        <td class="font-monospace fw-bold text-success fs-5 text-left" style="direction:ltr; padding:16px;"><?php echo number_format($grandCommission, 2); ?></td>
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
        body { background: #fff !important; }
    }
</style>