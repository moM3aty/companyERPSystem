<?php
// app/views/company/index.php
$companies = $data['companies'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-city text-primary"></i> دليل الشركات المشتركة</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">قائمة مفصلة بجميع العملاء (Tenants) المسجلين في نظامك السحابي.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/superadmin/dashboard" class="btn btn-secondary">
            <i class="fas fa-crown text-warning"></i> العودة للوحة المالك
        </a>
    </div>
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
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-list"></i> جميع الشركات</h3>
        <div style="position: relative; width: 300px;">
            <input type="text" id="searchCompany" class="form-control" placeholder="ابحث باسم الشركة أو الإيميل..." style="padding-right: 35px;">
            <i class="fas fa-search text-muted" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%);"></i>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-white">
                    <tr>
                        <th style="width: 80px;">رقم العميل</th>
                        <th>بيانات الشركة (Tenant)</th>
                        <th class="text-center">خطة الاشتراك</th>
                        <th class="text-center">حدود النظام</th>
                        <th class="text-left">تاريخ التجديد</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">التحكم</th>
                    </tr>
                </thead>
                <tbody id="companyTableBody">
                    <?php foreach($companies as $c): 
                        $isExpired = strtotime($c->subscription_end) < time();
                        
                        $statusClasses = [
                            'active' => 'badge-success',
                            'suspended' => 'badge-danger'
                        ];
                        $statusLabels = [
                            'active' => 'نشط',
                            'suspended' => 'مجمد'
                        ];
                        
                        $statusClass = $statusClasses[$c->status] ?? 'badge-secondary';
                        $statusLabel = $statusLabels[$c->status] ?? 'غير معروف';

                        if($isExpired && $c->status === 'active') {
                            $statusClass = 'badge-warning';
                            $statusLabel = 'منتهي (مطلوب تجديد)';
                        }
                    ?>
                    <tr class="company-row" style="<?php echo ($c->status === 'suspended') ? 'background-color: #f8fafc; opacity: 0.7;' : ''; ?>">
                        <td class="font-monospace fw-bold text-muted">
                            #<?php echo str_pad($c->id, 4, '0', STR_PAD_LEFT); ?>
                        </td>
                        <td>
                            <div class="fw-bold text-dark company-name"><i class="fas fa-building text-primary me-1"></i> <?php echo htmlspecialchars($c->name); ?></div>
                            <div class="text-muted font-monospace mt-1 company-email" style="font-size: 11px;"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($c->email ?? '—'); ?></div>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-secondary text-uppercase fs-6">
                                <i class="fas fa-star text-warning"></i> <?php echo htmlspecialchars($c->subscription_plan); ?>
                            </span>
                        </td>
                        <td class="text-center font-monospace" style="font-size: 12px;">
                            <div class="text-dark"><i class="fas fa-users text-muted"></i> Max Users: <strong><?php echo $c->max_users; ?></strong></div>
                            <div class="text-dark mt-1"><i class="fas fa-code-branch text-muted"></i> Max Branches: <strong><?php echo $c->max_branches; ?></strong></div>
                        </td>
                        <td class="font-monospace fw-bold <?php echo $isExpired ? 'text-danger' : 'text-success'; ?> text-left" style="direction:ltr;">
                            <?php echo date('Y-m-d', strtotime($c->subscription_end)); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/superadmin/show/<?php echo $c->id; ?>" class="btn-icon view text-info" style="border-color:var(--info);" title="إحصائيات العميل"><i class="fas fa-chart-line"></i></a>
                                <a href="<?php echo URLROOT; ?>/superadmin/edit/<?php echo $c->id; ?>" class="btn-icon edit" title="تعديل الباقة والحدود"><i class="fas fa-cogs"></i></a>
                                
                                <?php if($c->id != 1): ?>
                                <form action="<?php echo URLROOT; ?>/superadmin/toggleStatus/<?php echo $c->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد تغيير حالة تفعيل الشركة على النظام؟');">
                                    <?php if($c->status === 'active'): ?>
                                        <button type="submit" class="btn-icon delete text-warning" style="border-color:var(--warning);" title="تجميد وايقاف الشركة"><i class="fas fa-ban"></i></button>
                                    <?php else: ?>
                                        <button type="submit" class="btn-icon view text-success" title="إعادة تفعيل الشركة"><i class="fas fa-play"></i></button>
                                    <?php endif; ?>
                                </form>
                                
                                <form action="<?php echo URLROOT; ?>/superadmin/deleteTenant/<?php echo $c->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('⚠️ تحذير خطير جداً ⚠️\n\nهل أنت متأكد من حذف هذه الشركة بالكامل؟\nسيتم مسح كافة البيانات نهائياً ولا يمكن استرجاعها أبداً!');">
                                    <button type="submit" class="btn-icon delete text-danger" title="حذف نهائي للشركة وكافة بياناتها"><i class="fas fa-trash-can"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($companies)) : ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted p-5">
                            <i class="fas fa-building-circle-xmark fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد شركات مسجلة في الدليل.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchCompany').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#companyTableBody .company-row');

        rows.forEach(row => {
            let name = row.querySelector('.company-name').textContent.toLowerCase();
            let email = row.querySelector('.company-email').textContent.toLowerCase();
            
            if (name.includes(filter) || email.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>