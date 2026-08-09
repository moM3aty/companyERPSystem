<?php
// app/views/superadmin/dashboard.php
$companies = $data['companies'] ?? [];
$stats = $data['stats'] ?? ['total'=>0, 'active'=>0, 'revenue'=>0];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-crown text-warning"></i> لوحة تحكم المالك (SaaS HQ)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">التحكم المركزي في الشركات، الباقات، واستهلاك الموارد.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/company/index" class="btn btn-secondary">
        <i class="fas fa-list"></i> دليل جميع الشركات
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

<div class="form-grid mb-4" style="grid-template-columns: repeat(3, 1fr);">
    <div class="card mb-0 border-primary">
        <div class="card-body text-center p-4">
            <h5 class="text-muted fw-bold mb-2">إجمالي المشتركين</h5>
            <div class="font-monospace fs-1 fw-bold text-primary"><?php echo $stats['total']; ?></div>
        </div>
    </div>
    <div class="card mb-0 border-success">
        <div class="card-body text-center p-4">
            <h5 class="text-muted fw-bold mb-2">الشركات النشطة</h5>
            <div class="font-monospace fs-1 fw-bold text-success"><?php echo $stats['active']; ?></div>
        </div>
    </div>
    <div class="card mb-0 bg-dark text-white border-dark">
        <div class="card-body text-center p-4">
            <h5 class="text-light fw-bold mb-2">الإيراد المتوقع (MRR)</h5>
            <div class="font-monospace fs-1 fw-bold text-warning">$<?php echo number_format($stats['revenue']); ?></div>
        </div>
    </div>
</div>

<div class="content-grid" style="grid-template-columns: 1fr 2.5fr; align-items: start;">
    
    <!-- إنشاء شركة جديدة -->
    <div class="card mb-0 border-dashed border-success">
        <div class="card-header bg-light">
            <h3 class="card-title"><i class="fas fa-plus text-success"></i> تسجيل عميل جديد</h3>
        </div>
        <form action="<?php echo URLROOT; ?>/superadmin/createTenant" method="POST">
            <div class="card-body d-flex flex-column gap-3">
                <div class="form-group mb-0">
                    <label class="form-label">اسم المؤسسة <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">البريد الإلكتروني للعميل <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control font-monospace" required style="direction:ltr;">
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">الباقة (Plan)</label>
                    <select name="subscription_plan" class="form-control fw-bold">
                        <option value="basic">أساسية (5 مستخدمين)</option>
                        <option value="premium">متقدمة (15 مستخدم)</option>
                        <option value="enterprise">شركات كبرى (50 مستخدم)</option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">نهاية الاشتراك</label>
                    <input type="date" name="subscription_end" class="form-control" value="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" required>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success w-100"><i class="fas fa-rocket"></i> توليد النظام للشركة</button>
            </div>
        </form>
    </div>

    <!-- جدول الشركات الأخير -->
    <div class="card mb-0 h-100">
        <div class="card-header bg-light">
            <h3 class="card-title"><i class="fas fa-server text-info"></i> الشركات المستضافة في النظام</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-white">
                        <tr>
                            <th>Tenant ID</th>
                            <th>المؤسسة المشتركة</th>
                            <th class="text-center">الباقة</th>
                            <th>تاريخ التجديد</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">التحكم</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                $statusLabel = 'منتهي الصلاحية';
                            }
                        ?>
                        <tr style="<?php echo ($c->status === 'suspended') ? 'opacity: 0.6; background: #f8fafc;' : ''; ?>">
                            <td class="font-monospace fw-bold text-muted">#<?php echo str_pad($c->id, 4, '0', STR_PAD_LEFT); ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($c->name); ?></div>
                                <div class="text-muted font-monospace" style="font-size: 11px;"><?php echo htmlspecialchars($c->email); ?></div>
                            </td>
                            <td class="text-center"><span class="badge badge-secondary text-uppercase"><?php echo htmlspecialchars($c->subscription_plan); ?></span></td>
                            <td class="font-monospace fw-bold <?php echo $isExpired ? 'text-danger' : 'text-dark'; ?>" style="direction:ltr; text-align:right;">
                                <?php echo date('Y-m-d', strtotime($c->subscription_end)); ?>
                            </td>
                            <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="<?php echo URLROOT; ?>/superadmin/show/<?php echo $c->id; ?>" class="btn-icon view text-info" style="border-color:var(--info);" title="مراقبة الاستهلاك"><i class="fas fa-chart-line"></i></a>
                                    <a href="<?php echo URLROOT; ?>/superadmin/edit/<?php echo $c->id; ?>" class="btn-icon edit" title="إدارة الموارد"><i class="fas fa-cogs"></i></a>
                                    
                                    <?php if($c->id != 1): ?>
                                    <form action="<?php echo URLROOT; ?>/superadmin/toggleStatus/<?php echo $c->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد تغيير حالة وصول العميل للنظام؟');">
                                        <?php if($c->status === 'active'): ?>
                                            <button type="submit" class="btn-icon delete text-warning" style="border-color:var(--warning);" title="تجميد الشركة"><i class="fas fa-ban"></i></button>
                                        <?php else: ?>
                                            <button type="submit" class="btn-icon view text-success" title="إعادة تفعيل"><i class="fas fa-play"></i></button>
                                        <?php endif; ?>
                                    </form>
                                    
                                    <!-- الزر الأحمر الخطر -->
                                    <form action="<?php echo URLROOT; ?>/superadmin/deleteTenant/<?php echo $c->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('⚠️ تحذير خطير جداً ⚠️\n\nهل أنت متأكد من حذف هذه الشركة بالكامل؟\nسيتم مسح (جميع المستخدمين، الفواتير، الأصناف، والقيود) المرتبطة بها نهائياً!');">
                                        <button type="submit" class="btn-icon delete text-danger" title="تدمير شامل للشركة"><i class="fas fa-trash-can"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>