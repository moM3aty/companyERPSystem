<?php
// app/views/fixedAsset/index.php
$assets = $data['assets'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-building text-info"></i> الأصول الثابتة والإهلاك (Fixed Assets)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة أصول الشركة وحساب قيمتها الدفترية والإهلاك تلقائياً.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-print"></i> طباعة السجل</button>
        <a href="<?php echo URLROOT; ?>/fixedAsset/create" class="btn btn-info text-white"><i class="fas fa-plus"></i> تسجيل أصل جديد</a>
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
                    <th>كود الأصل</th>
                    <th>اسم الأصل / التصنيف</th>
                    <th class="text-center">تاريخ الشراء</th>
                    <th class="text-left">تكلفة الشراء</th>
                    <th class="text-center">العهدة (المسؤول)</th>
                    <th class="text-center">الحالة</th>
                    <th class="text-center d-print-none">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assets as $a): 
                    $statusClass = match($a->status) { 'Active' => 'badge-success', 'Disposed' => 'badge-danger', 'Sold' => 'badge-warning', default => 'badge-secondary' };
                    $statusLabel = match($a->status) { 'Active' => 'نشط', 'Disposed' => 'مستبعد/تالف', 'Sold' => 'مُباع', default => $a->status };
                ?>
                <tr>
                    <td><div class="font-monospace fw-bold text-muted"><?php echo htmlspecialchars($a->asset_code); ?></div></td>
                    <td>
                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($a->name); ?></div>
                        <div class="text-muted font-monospace" style="font-size:11px;"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($a->category); ?></div>
                    </td>
                    <td class="text-center font-monospace fs-6 text-muted"><?php echo $a->purchase_date; ?></td>
                    <td class="text-left font-monospace fw-black text-info fs-5" style="direction:ltr;"><?php echo number_format($a->purchase_cost, 2); ?></td>
                    <td class="text-center">
                        <?php if($a->employee_name): ?>
                            <span class="badge badge-secondary"><i class="fas fa-user"></i> <?php echo htmlspecialchars($a->employee_name); ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                    <td class="text-center d-print-none">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <a href="<?php echo URLROOT; ?>/fixedAsset/show/<?php echo $a->id; ?>" class="btn-icon view text-info" style="border-color:var(--info);"><i class="fas fa-calculator"></i></a>
                            <a href="<?php echo URLROOT; ?>/fixedAsset/edit/<?php echo $a->id; ?>" class="btn-icon edit"><i class="fas fa-pen"></i></a>
                            <?php if(Session::hasRole('admin')): ?>
                            <form action="<?php echo URLROOT; ?>/fixedAsset/delete/<?php echo $a->id; ?>" method="POST" style="display:inline;">
                                <button type="submit" class="btn-icon delete"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; if(empty($assets)): ?>
                    <tr><td colspan="7" class="text-center text-muted p-5"><i class="fas fa-building fs-1 opacity-25 mb-3 d-block"></i>لا توجد أصول ثابتة مسجلة.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>