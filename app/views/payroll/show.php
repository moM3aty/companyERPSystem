<?php
// app/views/payroll/show.php
$payroll = $data['payroll'] ?? null;
$details = $data['details'] ?? [];

if (!$payroll) return;
?>

<div class="card" style="max-width: 1000px; margin: 0 auto; border: none; box-shadow: var(--shadow-md);">
    
    <!-- شريط الأزرار والتحكم -->
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <div class="d-flex align-items-center gap-3">
            <h3 class="card-title text-dark mb-0"><i class="fas fa-file-invoice-dollar text-success"></i> مسير رواتب شهر <?php echo str_pad($payroll->month, 2, '0', STR_PAD_LEFT) . ' / ' . $payroll->year; ?></h3>
            <?php 
                $statusBadge = match($payroll->status) {
                    'draft' => 'badge-secondary', 'approved' => 'badge-warning', 'paid' => 'badge-success', default => 'badge-secondary'
                };
                $statusLabel = match($payroll->status) {
                    'draft' => 'مسودة (مراجعة)', 'approved' => 'معتمد', 'paid' => 'تم الصرف', default => $payroll->status
                };
            ?>
            <span class="badge <?php echo $statusBadge; ?> fs-6"><?php echo $statusLabel; ?></span>
        </div>
        
        <div class="d-flex gap-2">
            <?php if($payroll->status === 'draft'): ?>
                <form action="<?php echo URLROOT; ?>/payroll/updateStatus/<?php echo $payroll->id; ?>" method="POST" onsubmit="return confirm('تأكيد اعتماد المسير؟ سيتم إدراج خصومات السلف كمنتهية.');">
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="btn btn-warning btn-sm text-white"><i class="fas fa-check-double"></i> اعتماد المسير النهائي</button>
                </form>
            <?php elseif($payroll->status === 'approved'): ?>
                <form action="<?php echo URLROOT; ?>/payroll/updateStatus/<?php echo $payroll->id; ?>" method="POST" onsubmit="return confirm('تأكيد صرف الرواتب وتحويلها؟');">
                    <input type="hidden" name="status" value="paid">
                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-money-bill-transfer"></i> تأكيد الصرف (Paid)</button>
                </form>
            <?php endif; ?>
            
            <button class="btn btn-dark btn-sm" onclick="window.print()"><i class="fas fa-print"></i> طباعة كشف الرواتب</button>
            <a href="<?php echo URLROOT; ?>/payroll/index" class="btn btn-secondary btn-sm">رجوع</a>
        </div>
    </div>

    <!-- ورقة الطباعة -->
    <div class="card-body p-5 bg-white" style="border-radius: 0 0 var(--radius-md) var(--radius-md);">
        
        <div class="text-center mb-5 border-bottom pb-4">
            <h2 style="font-size: 24px; font-weight: 900; color: var(--text-dark); margin-bottom: 5px;">كشف ومسير رواتب الموظفين (Payroll)</h2>
            <div class="text-muted font-monospace fs-5">شهر: <?php echo str_pad($payroll->month, 2, '0', STR_PAD_LEFT); ?> | سنة: <?php echo $payroll->year; ?></div>
            <div class="text-muted mt-2 font-monospace" style="font-size: 12px;">المرجع: <?php echo $payroll->reference_no; ?></div>
        </div>

        <div class="row mb-5 d-flex gap-4">
            <div class="card flex-1 mb-0 border-0" style="background: var(--slate-50);">
                <div class="card-body text-center p-4">
                    <div class="text-muted fw-bold mb-2">عدد الموظفين في المسير</div>
                    <div class="font-monospace fs-2 fw-bold text-dark"><?php echo $payroll->total_employees; ?></div>
                </div>
            </div>
            <div class="card flex-1 mb-0 border-0" style="background: var(--success-light);">
                <div class="card-body text-center p-4">
                    <div class="text-success-dark fw-bold mb-2">إجمالي الرواتب المستحقة للصرف (ر.س)</div>
                    <div class="font-monospace fs-2 fw-bold text-success-dark" style="direction:ltr;"><?php echo number_format($payroll->total_net_amount, 2); ?></div>
                </div>
            </div>
        </div>

        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-list-check text-muted"></i> تفاصيل رواتب الموظفين</h5>
        <table class="table table-bordered" style="border: 1px solid var(--border-color); width: 100%;">
            <thead style="background: var(--slate-100);">
                <tr>
                    <th style="padding: 12px; color: var(--slate-700);">الرقم</th>
                    <th style="padding: 12px; color: var(--slate-700);">اسم الموظف</th>
                    <th class="text-center" style="padding: 12px; color: var(--slate-700);">الراتب الأساسي</th>
                    <th class="text-center" style="padding: 12px; color: var(--success-dark);">بدلات/إضافي (+)</th>
                    <th class="text-center" style="padding: 12px; color: var(--danger);">سلف/خصومات (-)</th>
                    <th class="text-left" style="padding: 12px; background:var(--primary-dark); color: #fff;">الصافي للصرف (Net)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($details as $d): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td class="font-monospace text-muted" style="padding: 12px;"><?php echo htmlspecialchars($d->employee_number ?? '-'); ?></td>
                    <td class="fw-bold text-dark" style="padding: 12px;"><?php echo htmlspecialchars($d->employee_name); ?></td>
                    <td class="text-center font-monospace" style="padding: 12px; direction:ltr;"><?php echo number_format($d->base_salary, 2); ?></td>
                    <td class="text-center font-monospace text-success" style="padding: 12px; direction:ltr;"><?php echo number_format($d->bonuses, 2); ?></td>
                    <td class="text-center font-monospace text-danger" style="padding: 12px; direction:ltr;"><?php echo number_format($d->deductions, 2); ?></td>
                    <td class="text-left font-monospace fw-bold text-primary" style="padding: 12px; direction:ltr; font-size:16px;"><?php echo number_format($d->net_salary, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- التوقيعات للطباعة -->
        <div class="mt-5 pt-5 d-none d-print-block" style="page-break-inside: avoid;">
            <div class="row" style="display: flex; justify-content: space-around; text-align: center;">
                <div style="flex: 1;">
                    <div class="fw-bold text-dark mb-4">إعداد الموارد البشرية</div>
                    <div style="border-bottom: 1px dashed var(--slate-400); width: 60%; margin: 0 auto 10px;"></div>
                </div>
                <div style="flex: 1;">
                    <div class="fw-bold text-dark mb-4">مراجعة الإدارة المالية</div>
                    <div style="border-bottom: 1px dashed var(--slate-400); width: 60%; margin: 0 auto 10px;"></div>
                </div>
                <div style="flex: 1;">
                    <div class="fw-bold text-dark mb-4">اعتماد المدير العام</div>
                    <div style="border-bottom: 1px dashed var(--slate-400); width: 60%; margin: 0 auto 10px;"></div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    @media print {
        body { background: #fff !important; }
        .d-print-none, .sidebar, .topbar { display: none !important; }
        .main-content { margin: 0 !important; }
        .card { box-shadow: none !important; border: none !important; max-width: 100% !important; margin: 0 !important;}
        .card-body { padding: 0 !important; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
        .d-print-block { display: block !important; }
    }
</style>