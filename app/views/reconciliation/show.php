<?php
// app/views/reconciliation/show.php
$r = $data['reconciliation'] ?? null;
?>
<div class="card" style="max-width: 700px; margin: 0 auto; border:none; box-shadow:var(--shadow-md);">
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center">
        <h3 class="mb-0 text-dark fw-bold"><i class="fas fa-check-double text-primary"></i> تقرير تسوية بنكية معتمد</h3>
        <div class="d-flex gap-2">
            <button class="btn btn-dark btn-sm" onclick="window.print()"><i class="fas fa-print"></i> طباعة (PDF)</button>
            <a href="<?php echo URLROOT; ?>/reconciliation/index" class="btn btn-secondary btn-sm">رجوع</a>
        </div>
    </div>
    
    <div class="card-body p-5">
        <div class="text-center border-bottom pb-4 mb-4">
            <h2 class="fw-black text-dark mb-1">Bank Reconciliation Report</h2>
            <h5 class="text-primary font-monospace fw-bold m-0"><?php echo htmlspecialchars($r->bank_name); ?></h5>
            <div class="text-muted mt-1 font-monospace">A/C: <?php echo htmlspecialchars($r->account_number ?? '—'); ?></div>
        </div>

        <table class="table table-bordered mb-4 text-center">
            <thead class="bg-slate-50">
                <tr><th>تاريخ كشف البنك (Statement Date)</th><th>تاريخ إتمام المطابقة (System Date)</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-monospace fw-bold text-danger fs-5"><?php echo $r->statement_date; ?></td>
                    <td class="font-monospace fw-bold text-primary fs-5"><?php echo date('Y-m-d', strtotime($r->created_at)); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="p-4 bg-light rounded border border-primary mb-4">
            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                <span class="text-muted fw-bold">الرصيد بموجب كشف البنك (Statement Balance):</span>
                <span class="font-monospace fw-black text-dark fs-4" style="direction:ltr;"><?php echo number_format($r->statement_balance, 2); ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                <span class="text-muted fw-bold">الرصيد الدفتري بالنظام (System Balance):</span>
                <span class="font-monospace fw-black text-primary fs-4" style="direction:ltr;"><?php echo number_format($r->system_balance, 2); ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fw-black fs-5">الفرق (Variance):</span>
                <span class="font-monospace fw-black text-success fs-3" style="direction:ltr;"><?php echo number_format($r->difference, 2); ?></span>
            </div>
        </div>

        <?php if($r->notes): ?>
            <div class="text-muted" style="font-size: 13px;"><strong>ملاحظات المحاسب:</strong><br><?php echo nl2br(htmlspecialchars($r->notes)); ?></div>
        <?php endif; ?>

        <div class="mt-5 pt-4 text-center text-muted" style="border-top: 1px dashed var(--border-color); font-size: 12px;">
            تمت المراجعة والتسوية بواسطة المحاسب: <strong><?php echo htmlspecialchars($r->creator_name); ?></strong>
        </div>
    </div>
</div>
<style>
@media print { body { background:#fff !important; } .d-print-none, .sidebar, .topbar { display: none !important; } .main-content { margin: 0 !important; } .card { box-shadow: none !important; border: 1px solid #000 !important; } }
</style>