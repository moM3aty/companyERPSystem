<?php
// app/views/purchaseReturn/index.php
$returns = $data['returns'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-boxes-packing text-primary"></i> مرتجعات المشتريات</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة الأصناف المرتجعة للموردين وخصمها من المخزون تلقائياً.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/purchaseReturn/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إنشاء مرتجع مشتريات
    </a>
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
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>رقم المرتجع</th>
                        <th>المورد</th>
                        <th>التاريخ</th>
                        <th class="text-left">الإجمالي (ر.س)</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($returns as $r) : 
                        $safeStatus = strtolower(trim($r->status ?? 'draft'));
                        $statusClass = match($safeStatus) {
                            'approved' => 'badge-success',
                            'draft' => 'badge-secondary',
                            'cancelled' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($safeStatus) {
                            'approved' => 'معتمد (مخصوم)',
                            'draft' => 'مسودة',
                            'cancelled' => 'ملغي',
                            default => $safeStatus
                        };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($r->return_number); ?></td>
                        <td>
                            <div class="fw-bold text-primary"><i class="fas fa-truck text-muted me-1"></i> <?php echo htmlspecialchars($r->supplier_name ?? 'بدون اسم'); ?></div>
                        </td>
                        <td class="text-muted font-monospace fs-6">
                            <?php echo date('Y-m-d', strtotime($r->return_date)); ?>
                        </td>
                        <td class="font-monospace fw-bold text-danger text-left" style="direction:ltr;">
                            <?php echo number_format($r->total_amount, 2); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/purchaseReturn/show/<?php echo $r->id; ?>" class="btn-icon view" title="عرض وطباعة"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo URLROOT; ?>/purchaseReturn/edit/<?php echo $r->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/purchaseReturn/delete/<?php echo $r->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف المرتجع؟ (سيتم إعادة المخزون كما كان)');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($returns)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-boxes-packing fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد مرتجعات مشتريات مسجلة حالياً.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>