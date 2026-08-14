<?php
// app/views/reconciliation/index.php
$recs = $data['reconciliations'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-check-double text-primary"></i> تسويات البنوك (Bank Reconciliation)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">مقارنة أرصدة النظام (ERP) مع كشوفات الحسابات البنكية الفعلية لضمان الدقة.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/reconciliation/create" class="btn btn-primary"><i class="fas fa-plus"></i> إجراء تسوية جديدة</a>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th>تاريخ كشف البنك</th>
                    <th>اسم البنك</th>
                    <th class="text-left">رصيد النظام</th>
                    <th class="text-left">رصيد البنك الفعلي</th>
                    <th class="text-left">الفرق (التباين)</th>
                    <th class="text-center">الحالة</th>
                    <th class="text-center">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recs as $r): ?>
                <tr>
                    <td class="font-monospace fw-bold text-dark"><i class="far fa-calendar-alt text-muted"></i> <?php echo $r->statement_date; ?></td>
                    <td><span class="badge badge-info fs-6"><i class="fas fa-building-columns"></i> <?php echo htmlspecialchars($r->bank_name); ?></span></td>
                    <td class="text-left font-monospace text-muted" style="direction:ltr;"><?php echo number_format($r->system_balance, 2); ?></td>
                    <td class="text-left font-monospace fw-bold text-primary" style="direction:ltr;"><?php echo number_format($r->statement_balance, 2); ?></td>
                    <td class="text-left font-monospace fw-black <?php echo $r->difference == 0 ? 'text-success' : 'text-danger'; ?>" style="direction:ltr;"><?php echo number_format($r->difference, 2); ?></td>
                    <td class="text-center"><span class="badge badge-success"><i class="fas fa-check"></i> <?php echo $r->status; ?></span></td>
                    <td class="text-center"><a href="<?php echo URLROOT; ?>/reconciliation/show/<?php echo $r->id; ?>" class="btn-icon view"><i class="fas fa-eye"></i></a></td>
                </tr>
                <?php endforeach; if(empty($recs)): ?>
                    <tr><td colspan="7" class="text-center text-muted p-5"><i class="fas fa-check-double fs-1 opacity-25 mb-3 d-block"></i>لا توجد تسويات بنكية سابقة.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>