<?php
// app/views/contract/index.php
$contracts = $data['contracts'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-signature text-primary"></i> إدارة العقود</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع العقود، تواريخ الانتهاء، وقيمتها المالية.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/contract/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة عقد جديد
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
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>رقم العقد</th>
                        <th style="width: 25%;">الموضوع / العنوان</th>
                        <th>الطرف الثاني (العميل)</th>
                        <th>تاريخ الانتهاء</th>
                        <th>القيمة</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contracts as $c) : 
                        $statusClass = match($c->status ?? 'draft') {
                            'active' => 'badge-success',
                            'expired' => 'badge-danger',
                            'terminated' => 'badge-dark',
                            'pending' => 'badge-warning',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($c->status ?? 'draft') {
                            'active' => 'ساري المفعول',
                            'expired' => 'منتهي',
                            'terminated' => 'مفسوخ / ملغي',
                            'pending' => 'قيد الانتظار',
                            default => 'مسودة'
                        };
                        
                        // التحقق من الانتهاء
                        $isExpired = false;
                        if (!empty($c->end_date)) {
                            $daysRemaining = ceil((strtotime($c->end_date) - time()) / 86400);
                            if ($daysRemaining < 0 && $c->status == 'active') {
                                $statusClass = 'badge-danger';
                                $statusLabel = 'منتهي';
                            }
                        }
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($c->contract_number); ?></td>
                        <td>
                            <div class="fw-bold text-primary"><?php echo htmlspecialchars($c->title); ?></div>
                        </td>
                        <td><i class="fas fa-user-tie text-muted me-1"></i> <?php echo htmlspecialchars($c->customer_name ?? '—'); ?></td>
                        <td class="text-muted font-monospace fs-6">
                            <?php echo $c->end_date ? date('Y-m-d', strtotime($c->end_date)) : '—'; ?>
                        </td>
                        <td class="font-monospace fw-bold text-success" style="direction:ltr; text-align:right;">
                            <?php echo number_format($c->value, 2); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/contract/show/<?php echo $c->id; ?>" class="btn-icon view" title="عرض العقد"><i class="fas fa-file-contract"></i></a>
                                <a href="<?php echo URLROOT; ?>/contract/edit/<?php echo $c->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/contract/delete/<?php echo $c->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف العقد نهائياً؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($contracts)) : ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted p-5">
                            <i class="fas fa-file-signature fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد عقود مسجلة بعد.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>