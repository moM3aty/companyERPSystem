<?php
// app/views/companies/index.php
$companies = $companies ?? ($data['companies'] ?? []);
$stats = $stats ?? ($data['stats'] ?? ['total'=>0, 'active'=>0, 'suspended'=>0]);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-building-circle-check text-primary"></i> إدارة الشركات (SaaS Platform)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">التحكم المركزي في الشركات المستأجرة، الاشتراكات، وحالة التفعيل.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/company/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> تسجيل شركة جديدة
    </a>
</div>

<!-- إحصائيات الـ SaaS -->
<div class="form-grid mb-4" style="grid-template-columns: repeat(3, 1fr);">
    <div class="card mb-0 bg-white border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--primary-50); color: var(--primary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-globe"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800; color: var(--text-dark);"><?php echo $stats['total']; ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">إجمالي الشركات المُسجلة</span>
            </div>
        </div>
    </div>
    <div class="card mb-0 bg-white border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--success-50); color: var(--success); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-circle-check"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800; color: var(--text-dark);"><?php echo $stats['active']; ?></h4>
                <span class="text-success" style="font-size: 12px; font-weight: 700;">الشركات النشطة حالياً</span>
            </div>
        </div>
    </div>
    <div class="card mb-0 bg-white border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--danger-50); color: var(--danger); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-ban"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800; color: var(--text-dark);"><?php echo $stats['suspended']; ?></h4>
                <span class="text-danger" style="font-size: 12px; font-weight: 700;">شركات موقوفة أو منتهية</span>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width:5%;">ID</th>
                        <th>بيانات الشركة (Tenant)</th>
                        <th class="text-center">عدد المستخدمين</th>
                        <th>انتهاء الاشتراك</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($companies)): foreach($companies as $c): 
                        $isSuspended = ($c->status === 'suspended');
                        $statusClass = $isSuspended ? 'badge-danger' : 'badge-success';
                        $statusLabel = $isSuspended ? 'موقوفة' : 'نشطة';
                        
                        // التحقق من انتهاء الاشتراك
                        $subEnds = $c->subscription_ends_at ? strtotime($c->subscription_ends_at) : 0;
                        $isExpired = $subEnds > 0 && $subEnds < time();
                    ?>
                    <tr style="<?php echo $isSuspended ? 'background-color: var(--danger-50);' : ''; ?>">
                        <td class="font-monospace fw-bold text-muted"><?php echo $c->id; ?></td>
                        <td>
                            <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($c->name); ?></div>
                            <div class="text-muted mt-1 font-monospace" style="font-size: 11px;">
                                <i class="fas fa-link"></i> <?php echo htmlspecialchars($c->domain ?? 'بدون دومين'); ?> | 
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($c->email ?? '—'); ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-primary px-3 py-1 fs-6 font-monospace"><?php echo $c->users_count; ?></span>
                        </td>
                        <td class="font-monospace fw-bold <?php echo $isExpired ? 'text-danger' : 'text-body'; ?>" style="font-size: 13px;">
                            <?php if($c->subscription_ends_at): ?>
                                <?php echo date('Y-m-d', strtotime($c->subscription_ends_at)); ?>
                                <?php if($isExpired): ?> <i class="fas fa-exclamation-triangle" title="الاشتراك منتهي"></i> <?php endif; ?>
                            <?php else: ?>
                                <span class="text-success"><i class="fas fa-infinity"></i> غير محدود</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?> px-2 py-1"><?php echo $statusLabel; ?></span></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <form action="<?php echo URLROOT; ?>/company/toggleStatus/<?php echo $c->id; ?>" method="POST" class="m-0 p-0">
                                    <input type="hidden" name="status" value="<?php echo $isSuspended ? 'active' : 'suspended'; ?>">
                                    <?php if($isSuspended): ?>
                                        <button type="submit" class="btn btn-sm btn-success" title="تفعيل الشركة"><i class="fas fa-play"></i> تفعيل</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-sm btn-danger" title="إيقاف الشركة"><i class="fas fa-ban"></i> إيقاف</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted p-5"><i class="fas fa-building fs-1 mb-3 opacity-50 d-block"></i> لا توجد شركات مسجلة في النظام.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>