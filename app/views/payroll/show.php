<?php
// app/views/payroll/show.php
$payroll = $data['payroll'] ?? null;
$details = $data['details'] ?? [];

$statusBadge = match($payroll->status) { 'approved' => 'badge-primary', 'paid' => 'badge-success', default => 'badge-secondary' };
$statusLabel = match($payroll->status) { 'approved' => 'معتمد (جاهز للصرف)', 'paid' => 'تم الصرف بنجاح', default => 'مسودة (تحت المراجعة)' };
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div class="d-flex align-items-center gap-3">
        <h3 class="mb-0 text-dark"><i class="fas fa-file-invoice text-success"></i> مسير رواتب: <?php echo str_pad($payroll->month, 2, '0', STR_PAD_LEFT); ?> / <?php echo $payroll->year; ?></h3>
        <span class="badge <?php echo $statusBadge; ?> fs-6"><?php echo $statusLabel; ?></span>
    </div>
    <div class="d-flex gap-2">
        <?php if($payroll->status === 'draft'): ?>
            <form action="<?php echo URLROOT; ?>/payroll/updateStatus/<?php echo $payroll->id; ?>" method="POST">
                <input type="hidden" name="status" value="approved">
                <button type="submit" class="btn btn-primary"><i class="fas fa-check-double"></i> اعتماد الكشف للمدير المالي</button>
            </form>
        <?php elseif($payroll->status === 'approved'): ?>
            <form action="<?php echo URLROOT; ?>/payroll/updateStatus/<?php echo $payroll->id; ?>" method="POST" onsubmit="return confirm('تأكيد صرف الرواتب؟ سيتم إقفال الكشف نهائياً ولن تتمكن من مسحه أو التعديل عليه.');">
                <input type="hidden" name="status" value="paid">
                <button type="submit" class="btn btn-success"><i class="fas fa-money-bill-wave"></i> تأكيد الصرف الفعلي (إقفال)</button>
            </form>
        <?php endif; ?>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-print"></i> كشف الرواتب (PDF)</button>
        <a href="<?php echo URLROOT; ?>/payroll/index" class="btn btn-secondary">العودة</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<!-- رأس الكشف للطباعة -->
<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">كشف مسير الرواتب الموحد (Payroll Report)</h2>
    <h5 class="text-muted font-monospace">عن شهر: <?php echo str_pad($payroll->month, 2, '0', STR_PAD_LEFT); ?> / <?php echo $payroll->year; ?></h5>
</div>

<div class="form-grid mb-4" style="grid-template-columns: repeat(3, 1fr);">
    <div class="card mb-0 bg-light border-0 shadow-sm text-center p-4 border-bottom" style="border-bottom-width:4px !important; border-bottom-color:var(--success) !important;">
        <div class="text-muted fw-bold mb-2">إجمالي الرواتب الصافية للصرف</div>
        <div class="font-monospace fs-1 fw-bold text-success" style="direction:ltr;"><?php echo number_format($payroll->total_net_amount, 2); ?> <span class="fs-6 text-muted">SAR</span></div>
    </div>
    <div class="card mb-0 bg-light border-0 shadow-sm text-center p-4 border-bottom" style="border-bottom-width:4px !important; border-bottom-color:var(--primary) !important;">
        <div class="text-muted fw-bold mb-2">إجمالي عدد الموظفين المستحقين</div>
        <div class="font-monospace fs-1 fw-bold text-primary"><?php echo $payroll->total_employees; ?> <span class="fs-6 text-muted">موظف</span></div>
    </div>
    <div class="card mb-0 bg-light border-0 shadow-sm p-4 border-bottom" style="border-bottom-width:4px !important; border-bottom-color:var(--slate-500) !important;">
        <table class="table table-borderless table-sm mb-0">
            <tr><td class="text-muted fw-bold">الرقم المرجعي:</td><td class="font-monospace text-dark text-end fw-bold"><?php echo $payroll->reference_no; ?></td></tr>
            <tr><td class="text-muted fw-bold">تاريخ التوليد:</td><td class="font-monospace text-dark text-end"><?php echo date('Y-m-d', strtotime($payroll->created_at)); ?></td></tr>
            <tr><td class="text-muted fw-bold">المُعدّ (بواسطة):</td><td class="text-dark text-end"><?php echo htmlspecialchars($payroll->creator_name ?? 'النظام'); ?></td></tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white border-bottom-0"><h3 class="card-title fs-6"><i class="fas fa-list text-muted"></i> التفاصيل الفردية للموظفين</h3></div>
    <div class="table-responsive">
        <table class="table table-bordered mb-0" style="font-size: 12px;">
            <thead class="bg-light">
                <tr>
                    <th rowspan="2" class="align-middle text-center" style="width:50px;">الرقم</th>
                    <th rowspan="2" class="align-middle">اسم الموظف</th>
                    <th rowspan="2" class="align-middle text-center">الأساسي</th>
                    <th colspan="3" class="text-center bg-info-light text-info-dark border-bottom-0" style="border-bottom: 2px solid var(--info) !important;">الاستحقاقات والبدلات (Additions)</th>
                    <th colspan="3" class="text-center bg-danger-light text-danger-dark border-bottom-0" style="border-bottom: 2px solid var(--danger) !important;">الاستقطاعات (Deductions)</th>
                    <th rowspan="2" class="align-middle text-center bg-success-light text-success-dark fs-6" style="border-bottom: 2px solid var(--success) !important;">صافي الراتب</th>
                </tr>
                <tr>
                    <th class="text-center bg-info-light">البدلات</th>
                    <th class="text-center bg-info-light">العمولات</th>
                    <th class="text-center bg-info-light">إضافي</th>
                    <th class="text-center bg-danger-light">سلف</th>
                    <th class="text-center bg-danger-light">غياب</th>
                    <th class="text-center bg-danger-light">جزاءات</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sumBase = 0; $sumAllw = 0; $sumComm = 0; $sumOvrt = 0; 
                $sumAdv = 0; $sumAbs = 0; $sumSanc = 0; $sumNet = 0;
                foreach($details as $d): 
                    $allowances = $d->housing_allowance + $d->transport_allowance + $d->other_allowances;
                    $sumBase += $d->base_salary; $sumAllw += $allowances; $sumComm += $d->commissions; $sumOvrt += $d->overtime_amount;
                    $sumAdv += $d->advance_deduction; $sumAbs += $d->absence_deduction; $sumSanc += $d->sanction_deduction; $sumNet += $d->net_salary;
                ?>
                <tr>
                    <td class="text-center text-muted font-monospace"><?php echo htmlspecialchars($d->employee_number ?? '—'); ?></td>
                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($d->employee_name); ?></td>
                    <td class="text-center font-monospace fw-bold"><?php echo number_format($d->base_salary, 2); ?></td>
                    <td class="text-center font-monospace text-primary"><?php echo number_format($allowances, 2); ?></td>
                    <td class="text-center font-monospace text-primary"><?php echo number_format($d->commissions, 2); ?></td>
                    <td class="text-center font-monospace text-primary"><?php echo number_format($d->overtime_amount, 2); ?></td>
                    <td class="text-center font-monospace text-danger"><?php echo number_format($d->advance_deduction, 2); ?></td>
                    <td class="text-center font-monospace text-danger"><?php echo number_format($d->absence_deduction, 2); ?></td>
                    <td class="text-center font-monospace text-danger"><?php echo number_format($d->sanction_deduction, 2); ?></td>
                    <td class="text-center font-monospace fw-black text-success fs-6" style="background:#f0fdf4;"><?php echo number_format($d->net_salary, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-dark text-white font-monospace fw-bold text-center">
                <tr>
                    <td colspan="2" class="text-end text-white">الإجماليات الكلية (Totals):</td>
                    <td class="text-white"><?php echo number_format($sumBase, 2); ?></td>
                    <td class="text-info-light"><?php echo number_format($sumAllw, 2); ?></td>
                    <td class="text-info-light"><?php echo number_format($sumComm, 2); ?></td>
                    <td class="text-info-light"><?php echo number_format($sumOvrt, 2); ?></td>
                    <td class="text-danger-light"><?php echo number_format($sumAdv, 2); ?></td>
                    <td class="text-danger-light"><?php echo number_format($sumAbs, 2); ?></td>
                    <td class="text-danger-light"><?php echo number_format($sumSanc, 2); ?></td>
                    <td class="text-success-light fs-5" style="background:var(--success-dark);"><?php echo number_format($sumNet, 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="text-center mt-4 d-print-none text-muted" style="font-size: 12px;">
    * يتم حساب خصم أيام الغياب تلقائياً بالاعتماد على سجل الحضور والانصراف لنفس الشهر المحدد، وتُخصم من الراتب الأساسي نسبة وتناسباً.
</div>

<style>
    @media print { 
        body { background: #fff !important; } 
        .d-print-none, .sidebar, .topbar { display: none !important; } 
        .main-content { margin: 0 !important; } 
        .card { box-shadow: none !important; border: none !important; margin: 0 !important; }
        .table th, .table td { border: 1px solid #000 !important; color:#000 !important; font-size:11px; }
        .bg-light, .bg-dark, .bg-info-light, .bg-danger-light, .bg-success-light { background: transparent !important; color: #000 !important; }
    }
</style>