<?php
// المسار: app/views/suppliers/index.php
$suppliers = $data['suppliers'] ?? [];
$totalPayables = $data['total_payables'] ?? 0;
$totalCount = $data['total_count'] ?? 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="search-box d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/supplier/index" method="GET" class="d-flex gap-2 align-items-center">
            <input type="text" name="search" value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>" class="form-control" placeholder="ابحث عن مورد..." style="width: 250px;">
            <select name="filter" class="form-control" style="width: auto;">
                <option value="all" <?php echo ($data['filter'] ?? '') === 'all' ? 'selected' : ''; ?>>الكل</option>
                <option value="company" <?php echo ($data['filter'] ?? '') === 'company' ? 'selected' : ''; ?>>شركات</option>
                <option value="individual" <?php echo ($data['filter'] ?? '') === 'individual' ? 'selected' : ''; ?>>أفراد</option>
            </select>
            <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <a href="<?php echo URLROOT; ?>/supplier/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة مورد جديد
    </a>
</div>

<div class="form-grid mb-4">
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--info-light); color: var(--info); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-truck-field"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800;"><?php echo $totalCount; ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">إجمالي الموردين</span>
            </div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--warning-light); color: var(--accent); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800; color: var(--accent);" class="font-monospace"><?php echo number_format($totalPayables, 2); ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">إجمالي الديون (الدائنون)</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> سجل الموردين المعتمدين</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>رقم المورد</th>
                        <th>اسم المورد / الشركة</th>
                        <th>النوع</th>
                        <th>الهاتف / التواصل</th>
                        <th>الرصيد الدائن</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($suppliers)): foreach($suppliers as $s): ?>
                    <tr>
                        <td class="text-muted font-monospace">#<?php echo $s->id; ?></td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-dark);"><?php echo htmlspecialchars($s->name); ?></div>
                            <div style="font-size: 12px; color: var(--text-muted);"><i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($s->contact_person ?? '—'); ?></div>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $s->type === 'company' ? 'primary' : 'info'; ?>">
                                <i class="fas fa-<?php echo $s->type === 'company' ? 'building' : 'user'; ?>"></i> 
                                <?php echo $s->type === 'company' ? 'شركة' : 'فرد'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="font-monospace text-right" style="direction: ltr; display: inline-block;"><?php echo htmlspecialchars($s->phone ?? '—'); ?></div>
                        </td>
                        <td>
                            <?php if ($s->balance > 0): ?>
                                <span class="badge badge-warning font-monospace text-dark"><?php echo number_format($s->balance, 2); ?></span>
                            <?php else: ?>
                                <span class="badge badge-success font-monospace">0.00</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo URLROOT; ?>/supplier/edit/<?php echo $s->id; ?>" class="btn-icon edit" title="تعديل">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="<?php echo URLROOT; ?>/supplier/delete/<?php echo $s->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف المورد؟');">
                                <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 40px;">لا يوجد موردين مطابقين للبحث.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>