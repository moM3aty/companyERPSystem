<?php
// app/views/warehouse/index.php
$warehouses = $data['warehouses'] ?? [];

$totalWarehouses = count($warehouses);
$mainWarehouses = 0;
$subWarehouses = 0;

foreach ($warehouses as $wh) {
    if ($wh->is_main) {
        $mainWarehouses++;
    } else {
        $subWarehouses++;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-warehouse text-primary"></i> المستودعات</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة فروع ومستودعات الشركة والمخزون الخاص بها.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/warehouse/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة مستودع جديد
    </a>
</div>

<?php 
    $flash = $data['flash'] ?? Session::getFlash();
    if ($flash): 
?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<!-- إحصائيات سريعة -->
<div class="form-grid mb-4" style="grid-template-columns: repeat(3, 1fr);">
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--info-light); color: var(--info); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-cubes"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800;" class="font-monospace text-dark"><?php echo $totalWarehouses; ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">إجمالي المستودعات</span>
            </div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--success-light); color: var(--success); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-building-circle-check"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800;" class="font-monospace text-success"><?php echo $mainWarehouses; ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">مستودع رئيسي</span>
            </div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--warning-light); color: var(--warning); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-code-branch"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800;" class="font-monospace text-warning"><?php echo $subWarehouses; ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">مستودع فرعي</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-list"></i> قائمة المستودعات</h3>
        <div style="position: relative;">
            <input type="text" id="searchInput" class="form-control" placeholder="ابحث عن مستودع..." style="width: 250px; padding-right: 35px;">
            <i class="fas fa-search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>الكود</th>
                        <th>اسم المستودع</th>
                        <th class="text-center">النوع</th>
                        <th>تاريخ الإضافة</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($warehouses as $wh) : ?>
                    <tr class="search-row" data-search="<?php echo htmlspecialchars($wh->name . ' ' . $wh->code); ?>">
                        <td class="text-muted font-monospace fw-bold"><?php echo $wh->id; ?></td>
                        <td><span class="badge badge-secondary font-monospace"><?php echo htmlspecialchars($wh->code); ?></span></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($wh->name); ?></div>
                            <div class="text-muted" style="font-size: 12px;"><i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($wh->address ?? 'لا يوجد عنوان مسجل'); ?></div>
                        </td>
                        <td class="text-center">
                            <?php if($wh->is_main): ?>
                                <span class="badge badge-success"><i class="fas fa-star text-warning"></i> رئيسي</span>
                            <?php else: ?>
                                <span class="badge badge-info">فرعي</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted fs-6"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($wh->created_at)); ?></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/warehouse/edit/<?php echo $wh->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <form method="POST" action="<?php echo URLROOT; ?>/warehouse/delete/<?php echo $wh->id; ?>" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    <button type="submit" class="btn-icon delete" title="حذف" <?php echo $wh->is_main ? 'disabled style="opacity:0.3;cursor:not-allowed;"' : ''; ?>><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($warehouses)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-warehouse fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد مستودعات مسجلة. قم بإنشاء مستودع لبدء العمل.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('.search-row');
    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            rows.forEach(row => {
                const text = (row.getAttribute('data-search') || '').toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        });
    }
</script> 