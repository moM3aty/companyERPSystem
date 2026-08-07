<?php
// app/views/payroll/view.php
$payroll = $payroll ?? ($data['payroll'] ?? null);
$details = $details ?? ($data['details'] ?? []);
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-dark text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-file-invoice"></i> كشف رواتب شهر <?php echo $payroll->month . ' / ' . $payroll->year; ?></h3>
        <span class="font-monospace fs-6 text-white" style="opacity: 0.8;">Ref: <?php echo htmlspecialchars($payroll->reference_no); ?></span>
    </div>
    
    <div class="card-body">
        <div class="form-grid mb-4">
            <div>
                <div class="text-muted fs-6 fw-bold text-uppercase mb-1">بيانات المسير</div>
                <div class="fs-5 fw-bold text-dark">رواتب الموظفين (الشهرية)</div>
                <div class="text-muted fs-6 mt-1"><i class="fas fa-users text-primary"></i> العدد: <?php echo $payroll->total_employees; ?> موظفين</div>
            </div>
            <div class="text-left">
                <div class="text-muted fs-6 fw-bold text-uppercase mb-1">الاعتماد</div>
                <div class="fs-6 fw-bold text-dark"><i class="far fa-calendar-alt"></i> تاريخ الإصدار: <?php echo date('Y-m-d', strtotime($payroll->created_at)); ?></div>
                <div class="text-muted fs-6 mt-1"><i class="fas fa-user-shield text-success"></i> بواسطة: <?php echo htmlspecialchars($payroll->created_by_name ?? 'النظام'); ?></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table border rounded">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف المستفيد</th>
                        <th class="text-center">الأساسي</th>
                        <th class="text-center text-success">المكافآت</th>
                        <th class="text-center text-danger">الخصومات</th>
                        <th class="text-left">صافي الاستحقاق</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($details as $d): ?>
                    <tr>
                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($d->employee_name); ?></td>
                        <td class="text-center font-monospace text-muted"><?php echo number_format($d->base_salary, 2); ?></td>
                        <td class="text-center font-monospace fw-bold text-success"><?php echo $d->bonuses > 0 ? '+'.number_format($d->bonuses, 2) : '—'; ?></td>
                        <td class="text-center font-monospace fw-bold text-danger"><?php echo $d->deductions > 0 ? '-'.number_format($d->deductions, 2) : '—'; ?></td>
                        <td class="text-left font-monospace fw-bold fs-5 text-primary" style="direction:ltr;"><?php echo number_format($d->net_salary, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <div class="p-3 bg-light border rounded text-left" style="min-width: 300px; box-shadow: var(--shadow-sm);">
                <div class="text-muted fw-bold mb-1">إجمالي المستحقات الواجب صرفها</div>
                <div class="font-monospace fs-3 fw-bold text-success" style="direction:ltr;">
                    <?php echo number_format($payroll->total_net_amount, 2); ?> <span class="fs-6 text-muted">SAR</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-footer d-flex justify-content-between mt-4">
        <a href="<?php echo URLROOT; ?>/payroll/index" class="btn btn-secondary d-print-none"><i class="fas fa-arrow-right"></i> عودة للقائمة</a>
        <button class="btn btn-dark d-print-none" onclick="window.print()"><i class="fas fa-print"></i> طباعة الكشف للمالية</button>
    </div>
</div>

<style>
    @media print {
        body { background: #fff !important; }
        .d-print-none, .sidebar, .topbar { display: none !important; }
        .main-content { margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; max-width: 100% !important;}
        .card-header { background: #fff !important; color: #000 !important; border-bottom: 2px solid #000; }
        .card-title, .text-white { color: #000 !important; }
    }
</style>