<?php
// app/views/stocktake/index.php
$stocktakes = $data['stocktakes'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-clipboard-check text-primary"></i> إدارة عمليات الجرد (Stocktake)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع ومراجعة ومطابقة الأرصدة الفعلية للمخزون مع النظام.</p>
    </div>
    <form action="<?php echo URLROOT; ?>/stocktake/create" method="POST">
        <button type="submit" class="btn btn-primary" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> جاري البدء...'; this.style.pointerEvents='none';">
            <i class="fas fa-plus"></i> بدء عملية جرد جديدة
        </button>
    </form>
</div>

<?php 
    $flash = Session::getFlash();
    if ($flash): 
?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>المرجع</th>
                        <th>تاريخ الجرد</th>
                        <th>ملاحظات</th>
                        <th>بواسطة</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stocktakes as $st) : 
                        $statusClass = match($st->status ?? 'draft') {
                            'draft' => 'badge-secondary',
                            'in_progress' => 'badge-warning',
                            'completed' => 'badge-success',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($st->status ?? 'draft') {
                            'draft' => 'مسودة',
                            'in_progress' => 'قيد التنفيذ',
                            'completed' => 'معتمد ومكتمل',
                            default => $st->status
                        };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($st->reference); ?></td>
                        <td><i class="far fa-calendar-alt text-muted"></i> <?php echo date('Y-m-d', strtotime($st->stocktake_date)); ?></td>
                        <td class="text-muted" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php echo htmlspecialchars($st->notes ?? '—'); ?>
                        </td>
                        <td><i class="fas fa-user-circle text-primary"></i> <?php echo htmlspecialchars($st->creator_name ?? 'النظام'); ?></td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/stocktake/show/<?php echo $st->id; ?>" class="btn-icon view" title="إدارة الجرد وتفاصيله"><i class="fas fa-eye"></i></a>
                                
                                <?php if(($st->status ?? '') !== 'completed' || Session::hasRole('admin')): ?>
                                    <a href="<?php echo URLROOT; ?>/stocktake/edit/<?php echo $st->id; ?>" class="btn-icon edit" title="تعديل الإعدادات"><i class="fas fa-pen"></i></a>
                                <?php endif; ?>

                                <?php if(Session::hasRole('admin')): ?>
                                    <form action="<?php echo URLROOT; ?>/stocktake/delete/<?php echo $st->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف عملية الجرد هذه نهائياً؟ لا يمكن التراجع!');">
                                        <button type="submit" class="btn-icon delete" title="حذف الجرد" <?php echo (($st->status ?? '') === 'completed') ? 'disabled style="opacity:0.3;cursor:not-allowed;"' : ''; ?>><i class="fas fa-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($stocktakes)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-clipboard-check fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد عمليات جرد سابقة. ابدأ عملية جديدة الآن.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>