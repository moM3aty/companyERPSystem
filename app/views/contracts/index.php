<?php
// المسار: app/views/contracts/index.php
$contracts = $data['contracts'] ?? [];

$activeContracts = 0;
$expiringContracts = 0;
$expiredContracts = 0;

$today = new DateTime();

foreach ($contracts as $c) {
    if ($c->status !== 'active') {
        continue;
    }
    
    $endDate = new DateTime($c->end_date);
    $interval = $today->diff($endDate);
    
    if ($endDate < $today) {
        $expiredContracts++;
        $c->display_status = 'expired';
    } elseif ($interval->days <= 30 && $interval->invert === 0) { // تنتهي خلال 30 يوم
        $expiringContracts++;
        $c->display_status = 'expiring';
    } else {
        $activeContracts++;
        $c->display_status = 'active';
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-contract text-primary"></i> سجل العقود والمواثيق</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">توثيق ومتابعة عقود العملاء والموردين وتلقي تنبيهات قبل الانتهاء.</p>
    </div>
    <div class="d-flex gap-2">
        <div class="search-box position-relative me-2">
            <input type="text" id="searchInput" class="form-control" placeholder="ابحث باسم العقد أو الطرف المعني..." autocomplete="off" style="padding-right: 35px; width: 280px;">
            <i class="fas fa-search position-absolute text-muted" style="top: 50%; transform: translateY(-50%); right: 12px;"></i>
        </div>
        <a href="<?php echo URLROOT; ?>/contract/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> إبرام عقد جديد
        </a>
    </div>
</div>

<!-- بطاقات المتابعة والتنبيهات -->
<div class="form-grid mb-4" style="grid-template-columns: repeat(3, 1fr);">
    <div class="card mb-0 border-0" style="background: linear-gradient(135deg, var(--success-light), #f0fdf4); box-shadow: var(--shadow-sm);">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--success); color: #fff; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-file-signature"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800; color: var(--success);"><?php echo $activeContracts; ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">عقود نشطة وسارية</span>
            </div>
        </div>
    </div>
    <div class="card mb-0 border-0" style="background: linear-gradient(135deg, var(--warning-light), #fffbeb); box-shadow: var(--shadow-sm);">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--warning); color: #fff; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800; color: var(--warning);"><?php echo $expiringContracts; ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">عقود تنتهي قريباً (تحتاج تجديد)</span>
            </div>
        </div>
    </div>
    <div class="card mb-0 border-0" style="background: linear-gradient(135deg, var(--danger-light), #fef2f2); box-shadow: var(--shadow-sm);">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--danger); color: #fff; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-calendar-times"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800; color: var(--danger);"><?php echo $expiredContracts; ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">عقود منتهية الصلاحية</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0" id="contractsTable">
                <thead class="bg-light">
                    <tr>
                        <th>رقم العقد</th>
                        <th>موضوع العقد والطرف الثاني</th>
                        <th>تاريخ البدء</th>
                        <th>تاريخ الانتهاء</th>
                        <th class="text-left">قيمة العقد</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($contracts)): foreach ($contracts as $c) : 
                        $statusClass = match($c->display_status ?? $c->status) {
                            'active' => 'badge-success',
                            'expiring' => 'badge-warning',
                            'expired' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($c->display_status ?? $c->status) {
                            'active' => '<i class="fas fa-check"></i> ساري المفعول',
                            'expiring' => '<i class="fas fa-exclamation-triangle"></i> ينتهي قريباً',
                            'expired' => '<i class="fas fa-times"></i> منتهي الصلاحية',
                            'draft' => '<i class="fas fa-pen"></i> مسودة',
                            default => $c->status
                        };
                        $partyIcon = $c->party_type === 'customer' ? 'fa-user-tie text-info' : 'fa-truck-field text-accent';
                        $partyLabel = $c->party_type === 'customer' ? 'عميل' : 'مورد';
                    ?>
                    <tr class="search-row" style="<?php echo ($c->display_status === 'expired') ? 'background: #fef2f2;' : (($c->display_status === 'expiring') ? 'background: #fffbeb;' : ''); ?>">
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($c->contract_number); ?></td>
                        <td>
                            <div class="fw-bold text-dark search-target"><?php echo htmlspecialchars($c->title); ?></div>
                            <div class="text-muted search-target" style="font-size:12px; margin-top:2px;">
                                <i class="fas <?php echo $partyIcon; ?>"></i> <?php echo $partyLabel . ': ' . htmlspecialchars($c->party_name ?? 'غير محدد'); ?>
                            </div>
                        </td>
                        <td class="text-muted fs-6"><i class="far fa-calendar text-success"></i> <?php echo $c->start_date; ?></td>
                        <td class="text-muted fs-6 font-monospace fw-bold <?php echo ($c->display_status === 'expired' || $c->display_status === 'expiring') ? 'text-danger' : ''; ?>">
                            <i class="far fa-calendar-times <?php echo ($c->display_status === 'expired') ? 'text-danger' : ''; ?>"></i> <?php echo $c->end_date; ?>
                        </td>
                        <td class="font-monospace fw-bold text-primary" style="direction:ltr; text-align:right;"><?php echo number_format($c->value, 2); ?></td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?> px-2 py-1"><?php echo $statusLabel; ?></span></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="#" class="btn-icon view" title="عرض" onclick="alert('واجهة العرض تحت التطوير')"><i class="fas fa-eye"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form method="POST" action="<?php echo URLROOT; ?>/contract/delete/<?php echo $c->id; ?>" style="display:inline;" onsubmit="return confirm('تأكيد حذف العقد نهائياً؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف العقد"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="text-center p-5 text-muted">
                                <i class="fas fa-file-signature fs-1 mb-3 d-block opacity-50"></i>
                                <h4>لا توجد عقود مسجلة</h4>
                                <p>قم بإضافة عقود العملاء والموردين للحصول على تنبيهات التجديد الآلية.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // البحث السريع
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('.search-row');

    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            const q = this.value.trim().toLowerCase();
            rows.forEach(row => {
                const textElements = row.querySelectorAll('.search-target');
                let found = false;
                textElements.forEach(el => {
                    if (el.textContent.toLowerCase().includes(q)) found = true;
                });
                row.style.display = found ? '' : 'none';
            });
        });
    }
</script>