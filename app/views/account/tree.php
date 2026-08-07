<?php
// app/views/account/tree.php
$accounts = $data['accounts'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-sitemap text-primary"></i> دليل الحسابات (Chart of Accounts)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">هيكلة الحسابات وتأسيس الدليل المالي للمنشأة.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/account/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة حساب جديد
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم الحساب (الكود)</th>
                        <th>اسم الحساب</th>
                        <th class="text-center">النوع (الطبيعة)</th>
                        <th>الحساب الرئيسي</th>
                        <th class="text-left">الرصيد الافتتاحي</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($accounts)): foreach($accounts as $acc): 
                        $typeClass = match($acc->type) {
                            'asset' => 'badge-info',
                            'liability' => 'badge-danger',
                            'equity' => 'badge-purple',
                            'revenue' => 'badge-success',
                            'expense' => 'badge-warning',
                            default => 'badge-secondary'
                        };
                        $typeLabel = match($acc->type) {
                            'asset' => 'أصول', 'liability' => 'خصوم (التزامات)', 'equity' => 'حقوق ملكية', 'revenue' => 'إيرادات', 'expense' => 'مصروفات', default => $acc->type
                        };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($acc->code); ?></td>
                        <td class="fw-bold text-dark"><i class="fas fa-folder text-muted me-1"></i> <?php echo htmlspecialchars($acc->name); ?></td>
                        <td class="text-center"><span class="badge <?php echo $typeClass; ?>"><?php echo $typeLabel; ?></span></td>
                        <td class="text-muted fs-6">
                            <?php echo $acc->parent_name ? '<i class="fas fa-level-up-alt fa-rotate-90 text-muted me-1"></i> ' . htmlspecialchars($acc->parent_name) : '<i class="fas fa-star text-muted" style="font-size:10px;"></i> حساب رئيسي'; ?>
                        </td>
                        <td class="font-monospace fw-bold text-dark text-left" style="direction:ltr;">
                            <?php echo number_format($acc->balance, 2); ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/account/edit/<?php echo $acc->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if (Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/account/delete/<?php echo $acc->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف الحساب نهائياً؟ قد يؤثر ذلك على الميزانية إذا كانت هناك قيود مرتبطة.');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding:60px;">
                            <i class="fas fa-sitemap fs-1 text-muted mb-3 d-block"></i>
                            دليل الحسابات فارغ
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>