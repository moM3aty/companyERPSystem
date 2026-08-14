<?php
// app/views/grn/index.php
$grns = $data['grns'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-dolly text-success"></i> مذكرات استلام البضائع (Goods Received Note)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">توثيق استلام المخزون من الموردين بناءً على أوامر الشراء (PO).</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-print"></i> طباعة السجل</button>
        <a href="<?php echo URLROOT; ?>/grn/create" class="btn btn-success"><i class="fas fa-plus"></i> استلام بضاعة جديدة (GRN)</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th>رقم الـ GRN</th>
                    <th>المورد (Supplier)</th>
                    <th>المستودع (Warehouse)</th>
                    <th class="text-center">تاريخ الاستلام</th>
                    <th class="text-center">مرتبط بـ PO</th>
                    <th class="text-center d-print-none">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grns as $g): ?>
                <tr>
                    <td>
                        <div class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($g->grn_number); ?></div>
                    </td>
                    <td class="fw-bold"><i class="fas fa-building text-muted me-1"></i> <?php echo htmlspecialchars($g->supplier_name ?? '—'); ?></td>
                    <td><span class="badge badge-secondary"><i class="fas fa-warehouse"></i> <?php echo htmlspecialchars($g->warehouse_name ?? '—'); ?></span></td>
                    <td class="text-center font-monospace fs-6 text-muted"><?php echo $g->delivery_date; ?></td>
                    <td class="text-center font-monospace fw-bold text-primary">
                        <?php echo htmlspecialchars($g->po_number ?? 'بدون أمر شراء'); ?>
                    </td>
                    <td class="text-center d-print-none">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <a href="<?php echo URLROOT; ?>/grn/show/<?php echo $g->id; ?>" class="btn-icon view text-success" style="border-color:var(--success);"><i class="fas fa-eye"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; if(empty($grns)): ?>
                    <tr><td colspan="6" class="text-center text-muted p-5"><i class="fas fa-box-open fs-1 opacity-25 mb-3 d-block"></i>لم يتم استلام أي بضائع بعد.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>