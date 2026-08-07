<?php
// app/views/assets/index.php
$assets = $assets ?? ($data['assets'] ?? []);
$stats = $data['stats'] ?? ['total_cost' => 0, 'total_book_value' => 0, 'active_count' => 0];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-building text-primary"></i> سجل الأصول الثابتة والإهلاك</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع الممتلكات والإهلاك السنوي (Book Value) والقيم الدفترية.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> طباعة التقرير</button>
        <a href="<?php echo URLROOT; ?>/fixedAsset/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة أصل جديد
        </a>
    </div>
</div>

<!-- بطاقات الإحصائيات للأصول -->
<div class="form-grid mb-4" style="grid-template-columns: repeat(3, 1fr);">
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--info-light); color: var(--info); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-cubes"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800;"><?php echo $stats['active_count']; ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">الأصول النشطة حالياً</span>
            </div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--success-light); color: var(--success); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 22px; font-weight: 800; color: var(--success);" class="font-monospace text-right" style="direction:ltr;"><?php echo number_format($stats['total_cost'], 2); ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">التكلفة الإجمالية للأصول (ر.س)</span>
            </div>
        </div>
    </div>
    <div class="card mb-0" style="background: linear-gradient(135deg, var(--text-dark), #1e293b); color: #fff; border:none;">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); color: var(--primary-light); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 22px; font-weight: 800; color: var(--primary-light);" class="font-monospace text-right" style="direction:ltr;"><?php echo number_format($stats['total_book_value'], 2); ?></h4>
                <span style="font-size: 12px; color: #cbd5e1; font-weight: 700;">القيمة الدفترية الحالية (بعد الإهلاك)</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>الأصل / الكود</th>
                        <th>التصنيف والموقع</th>
                        <th class="text-left">تكلفة الشراء</th>
                        <th class="text-left">القيمة الدفترية الحالية</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center d-print-none">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($assets)): foreach($assets as $a): 
                        $statusClass = match($a->status) {
                            'active' => 'badge-success',
                            'maintenance' => 'badge-warning',
                            'disposed' => 'badge-danger',
                            'sold' => 'badge-secondary',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($a->status) {
                            'active' => 'مستخدم', 'maintenance' => 'صيانة', 'disposed' => 'مُتلف', 'sold' => 'مباع', default => $a->status
                        };
                        $catIcon = match($a->category) {
                            'equipment' => 'fa-microchip text-info', 'vehicle' => 'fa-car text-accent', 'furniture' => 'fa-chair text-purple', 'real_estate' => 'fa-building text-primary', default => 'fa-box text-muted'
                        };
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($a->name); ?></div>
                            <span class="badge badge-secondary font-monospace mt-1"><i class="fas fa-barcode"></i> <?php echo htmlspecialchars($a->asset_tag); ?></span>
                        </td>
                        <td>
                            <div class="text-dark fw-bold mb-1"><i class="fas <?php echo $catIcon; ?> me-1"></i> <?php echo ucfirst($a->category); ?></div>
                            <div class="text-muted fs-6"><i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($a->location ?? '—'); ?></div>
                        </td>
                        <td>
                            <div class="font-monospace fw-bold text-dark text-left" style="direction:ltr;"><?php echo number_format($a->purchase_cost, 2); ?></div>
                            <div class="text-muted fs-6 mt-1 text-left"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($a->purchase_date)); ?></div>
                        </td>
                        <td>
                            <div class="font-monospace fw-bold text-primary fs-5 text-left" style="direction:ltr;"><?php echo number_format($a->book_value, 2); ?></div>
                            <div class="font-monospace text-danger fs-6 mt-1 text-left">-<?php echo number_format($a->accumulated_depreciation, 2); ?> (مُهلك)</div>
                        </td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td class="text-center d-print-none">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/fixedAsset/edit/<?php echo $a->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/fixedAsset/delete/<?php echo $a->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف الأصل؟ سيؤثر على القوائم إذا كان مسجلاً في الحسابات.');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding: 60px;"><i class="fas fa-building fa-2x mb-3 d-block opacity-50"></i> لا توجد أصول مسجلة بعد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>