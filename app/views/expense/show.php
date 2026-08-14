<?php
// app/views/expense/show.php
$e = $data['expense'] ?? null;
?>
<div class="card" style="max-width: 800px; margin: 0 auto; box-shadow: var(--shadow-md); border:none;">
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center border-bottom">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-file-invoice-dollar text-danger"></i> إيصال صرف (مصروف تشغيلي)</h3>
        <div class="d-flex gap-2">
            <button class="btn btn-dark btn-sm" onclick="window.print()"><i class="fas fa-print"></i> طباعة (PDF)</button>
            <a href="<?php echo URLROOT; ?>/expense/index" class="btn btn-secondary btn-sm">رجوع للجدول</a>
        </div>
    </div>
    
    <div class="card-body p-5 bg-white">
        <div class="row mb-4 border-bottom pb-4">
            <div class="col-md-6">
                <h2 class="fw-black text-danger mb-1">EXPENSE VOUCHER</h2>
                <div class="text-muted font-monospace mb-3">رقم المرجع: <?php echo htmlspecialchars($e->reference ?: 'EXP-'.$e->id); ?></div>
                <div class="text-muted mb-1">التاريخ: <span class="font-monospace text-dark fw-bold"><?php echo $e->expense_date; ?></span></div>
                <div class="text-muted mb-1">حساب السداد: <span class="badge badge-info fs-6"><?php echo htmlspecialchars($e->treasury_name); ?></span></div>
            </div>
            <div class="col-md-6 text-left" style="text-align: left; direction: ltr;">
                <div class="text-muted mb-2">إجمالي المصروف</div>
                <div class="font-monospace fw-black text-danger" style="font-size: 36px;">
                    <?php echo number_format($e->total_amount, 2); ?> <span class="fs-6 text-muted">SAR</span>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="fw-bold text-muted mb-2">التصنيف ومركز التكلفة</h6>
                <div class="p-3 bg-light rounded border">
                    <div class="mb-2"><span class="text-muted">التصنيف:</span> <strong class="text-primary"><?php echo htmlspecialchars($e->category_name ?? $e->category ?? 'مصروف عام'); ?></strong></div>
                    <div><span class="text-muted">مركز التكلفة:</span> <strong><?php echo htmlspecialchars($e->cost_center ?: 'عام (General)'); ?></strong></div>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold text-muted mb-2">التحليل المالي</h6>
                <table class="table table-bordered mb-0 text-center">
                    <thead class="bg-slate-50">
                        <tr><th>المبلغ الأساسي</th><th>الضريبة المضافة</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-monospace fw-bold" style="direction:ltr;"><?php echo number_format($e->amount, 2); ?></td>
                            <td class="font-monospace text-danger" style="direction:ltr;"><?php echo number_format($e->tax_amount, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <h6 class="fw-bold text-muted mb-2">البيان والملاحظات التفصيلية</h6>
        <div class="p-4 bg-light rounded text-dark mb-4" style="border-right: 4px solid var(--danger); font-size:14px; line-height:1.6;">
            <?php echo nl2br(htmlspecialchars($e->notes)); ?>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
            <div class="text-muted" style="font-size: 12px;">
                <i class="fas fa-user-edit me-1"></i> مُسجل الحركة: <strong><?php echo htmlspecialchars($e->user_name); ?></strong><br>
                <i class="fas fa-clock me-1"></i> وقت التسجيل: <span class="font-monospace"><?php echo date('Y-m-d H:i', strtotime($e->created_at)); ?></span>
            </div>
            <?php if($e->is_reconciled): ?>
                <div class="badge badge-success px-3 py-2"><i class="fas fa-check-double me-1"></i> تمت التسوية البنكية</div>
            <?php else: ?>
                <div class="badge badge-warning px-3 py-2"><i class="fas fa-hourglass-half me-1"></i> معلق (لم تتم التسوية)</div>
            <?php endif; ?>
        </div>
        
        <div class="mt-5 pt-5 d-none d-print-block" style="page-break-inside: avoid;">
            <div class="row text-center">
                <div class="col-6"><div class="fw-bold text-dark mb-4">المحاسب (المُعد)</div><div style="border-bottom: 1px dashed #ccc; width: 60%; margin: 0 auto;"></div></div>
                <div class="col-6"><div class="fw-bold text-dark mb-4">اعتماد الإدارة</div><div style="border-bottom: 1px dashed #ccc; width: 60%; margin: 0 auto;"></div></div>
            </div>
        </div>
    </div>
</div>
<style>
@media print { body { background:#fff !important; } .d-print-none, .sidebar, .topbar { display: none !important; } .main-content { margin: 0 !important; } .card { box-shadow: none !important; border: 1px solid #ddd !important; } }
</style>