<?php
// app/views/customer/show.php
$c = $data['customer'] ?? null;
$statement = $data['statement'] ?? [];
$aging = $data['aging'] ?? (object)[
    'current_due' => 0,
    'days_30' => 0,
    'days_60' => 0,
    'days_90_plus' => 0
];

$limitExceeded = $c->current_balance > $c->credit_limit && $c->credit_limit > 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div class="d-flex align-items-center gap-3">
        <div style="width: 70px; height: 70px; border-radius: 12px; background: linear-gradient(135deg, var(--primary), var(--info)); color: white; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: bold; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <i class="fas fa-user-tie"></i>
        </div>
        <div>
            <h2 class="mb-0 text-dark fw-black"><?php echo htmlspecialchars($c->name); ?></h2>
            <div class="text-muted mt-1 font-monospace" style="font-size: 14px;">
                <i class="fas fa-hashtag text-primary"></i> <?php echo htmlspecialchars($c->customer_number); ?> 
                <?php if($c->company_name): ?> | <i class="fas fa-building text-info"></i> <?php echo htmlspecialchars($c->company_name); ?><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/customer/edit/<?php echo $c->id; ?>" class="btn btn-secondary"><i class="fas fa-pen"></i> تعديل</a>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-print"></i> طباعة كشف الحساب</button>
    </div>
</div>

<!-- ملخص الرصيد والحد الائتماني -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card mb-0 bg-light border-0 shadow-sm text-center p-4 border-bottom h-100" style="border-bottom-width:4px !important; border-bottom-color:var(--success) !important;">
            <div class="text-muted fw-bold mb-2">الرصيد الحالي المستحق للشركة</div>
            <div class="font-monospace fs-1 fw-bold <?php echo $c->current_balance > 0 ? 'text-success' : 'text-muted'; ?>" style="direction:ltr;"><?php echo number_format($c->current_balance, 2); ?> <span class="fs-6 text-muted"><?php echo $c->currency; ?></span></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-0 bg-light border-0 shadow-sm text-center p-4 border-bottom h-100" style="border-bottom-width:4px !important; border-bottom-color:var(--danger) !important;">
            <div class="text-muted fw-bold mb-2">الحد الائتماني (Credit Limit)</div>
            <div class="font-monospace fs-1 fw-bold text-danger" style="direction:ltr;"><?php echo $c->credit_limit > 0 ? number_format($c->credit_limit, 2) : 'بدون حد'; ?> <span class="fs-6 text-muted"><?php echo $c->credit_limit > 0 ? $c->currency : ''; ?></span></div>
            <?php if($limitExceeded): ?>
                <div class="badge badge-danger mt-2 px-3 py-2 animate-pulse"><i class="fas fa-exclamation-triangle"></i> تجاوز العميل الحد الائتماني المسموح به!</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-0 bg-light border-0 shadow-sm p-3 border-bottom h-100" style="border-bottom-width:4px !important; border-bottom-color:var(--info) !important;">
            <table class="table table-borderless table-sm mb-0 mt-2">
                <tr><td class="text-muted fw-bold"><i class="fas fa-phone text-info"></i> الجوال:</td><td class="text-dark font-monospace text-end"><?php echo htmlspecialchars($c->phone ?? '—'); ?></td></tr>
                <tr><td class="text-muted fw-bold"><i class="fas fa-envelope text-info"></i> الإيميل:</td><td class="text-dark font-monospace text-end"><?php echo htmlspecialchars($c->email ?? '—'); ?></td></tr>
                <tr><td class="text-muted fw-bold"><i class="fas fa-file-invoice text-info"></i> الرقم الضريبي:</td><td class="text-dark font-monospace text-end fw-bold"><?php echo htmlspecialchars($c->vat_number ?? '—'); ?></td></tr>
            </table>
        </div>
    </div>
</div>

<!-- 🟢 تقرير أعمار الديون (Aging Report) كما طلبت بالحرف 🟢 -->
<h4 class="fw-bold text-dark mb-3"><i class="fas fa-chart-bar text-warning"></i> تقرير أعمار الديون (Aging Report)</h4>
<div class="card mb-5 shadow-sm border-warning">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0 text-center">
                <thead class="bg-warning-light text-warning-dark">
                    <tr>
                        <th class="py-3">العميل (Customer)</th>
                        <th class="py-3">حالي (Current)</th>
                        <th class="py-3">1 - 30 يوماً</th>
                        <th class="py-3">31 - 60 يوماً</th>
                        <th class="py-3 text-danger fw-black">90+ يوماً (متأخر جداً)</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr>
                        <td class="fw-bold text-dark py-3"><?php echo htmlspecialchars($c->name); ?></td>
                        <td class="font-monospace fw-bold text-success py-3 fs-5" style="direction:ltr;"><?php echo number_format($aging->current_due, 2); ?></td>
                        <td class="font-monospace fw-bold text-warning-dark py-3 fs-5" style="direction:ltr;"><?php echo number_format($aging->days_30, 2); ?></td>
                        <td class="font-monospace fw-bold text-danger py-3 fs-5" style="direction:ltr;"><?php echo number_format($aging->days_60, 2); ?></td>
                        <td class="font-monospace fw-black text-danger bg-danger-light py-3 fs-4" style="direction:ltr;"><?php echo number_format($aging->days_90_plus, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 🟢 كشف الحساب (Customer Statement) 🟢 -->
<h4 class="fw-bold text-dark mb-3"><i class="fas fa-list text-primary"></i> كشف الحساب والحركات (Customer Statement)</h4>
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="py-3 text-white">التاريخ</th>
                        <th class="py-3 text-white">نوع الحركة</th>
                        <th class="py-3 text-white">المرجع (Ref)</th>
                        <th class="text-center py-3 text-white">مدين (Debit) - فواتير</th>
                        <th class="text-center py-3 text-white">دائن (Credit) - دفعات</th>
                        <th class="text-center py-3 text-white">الرصيد المتراكم</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $runningBalance = $c->opening_balance; 
                    ?>
                    <tr class="bg-light">
                        <td colspan="3" class="text-end fw-bold text-muted">الرصيد الافتتاحي (Opening Balance):</td>
                        <td class="text-center font-monospace fw-bold text-muted">—</td>
                        <td class="text-center font-monospace fw-bold text-muted">—</td>
                        <td class="text-center font-monospace fw-black text-primary fs-5" style="direction:ltr;"><?php echo number_format($runningBalance, 2); ?></td>
                    </tr>
                    <?php foreach($statement as $s): 
                        if ($s->action == 'debit') {
                            $runningBalance += $s->amount;
                            $debit = number_format($s->amount, 2);
                            $credit = '—';
                        } else {
                            $runningBalance -= $s->amount;
                            $debit = '—';
                            $credit = number_format($s->amount, 2);
                        }
                    ?>
                    <tr>
                        <td class="text-muted font-monospace fs-6"><?php echo date('Y-m-d', strtotime($s->date)); ?></td>
                        <td><span class="badge <?php echo $s->type == 'فاتورة' ? 'badge-primary' : 'badge-success'; ?>"><?php echo $s->type; ?></span></td>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($s->ref); ?></td>
                        <td class="text-center font-monospace fw-bold text-danger" style="direction:ltr;"><?php echo $debit; ?></td>
                        <td class="text-center font-monospace fw-bold text-success" style="direction:ltr;"><?php echo $credit; ?></td>
                        <td class="text-center font-monospace fw-black text-primary fs-5" style="direction:ltr;"><?php echo number_format($runningBalance, 2); ?></td>
                    </tr>
                    <?php endforeach; if(empty($statement)): ?>
                        <tr><td colspan="6" class="text-center text-muted p-4">لا توجد حركات مالية مسجلة لهذا العميل.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print { 
    body { background: #fff !important; } 
    .d-print-none, .sidebar, .topbar { display: none !important; } 
    .main-content { margin: 0 !important; } 
    .card { box-shadow: none !important; border: 1px solid #ccc !important; } 
    .table th, .table td { border: 1px solid #000 !important; color:#000 !important; }
}
</style>