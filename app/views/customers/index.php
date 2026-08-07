<?php
// المسار: app/views/customers/index.php
$customers = $customers ?? ($data['customers'] ?? []);
$totalReceivables = $totalReceivables ?? ($data['total_receivables'] ?? 0);
?>

<!-- أزرار الإجراءات العلوية -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="search-box">
        <input type="text" id="searchInput" class="form-control" placeholder="ابحث عن عميل..." style="width: 300px;">
    </div>
    <a href="<?php echo URLROOT; ?>/customer/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة عميل جديد
    </a>
</div>

<!-- كروت الإحصائيات -->
<div class="form-grid" style="margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--info-light); color: var(--info); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800;"><?php echo count($customers); ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">إجمالي العملاء</span>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--danger-light); color: var(--danger); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 800; color: var(--danger);" class="font-monospace"><?php echo number_format($totalReceivables, 2); ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">إجمالي الديون المستحقة</span>
            </div>
        </div>
    </div>
</div>

<!-- جدول العملاء -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> سجل العملاء</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table" id="customersTable">
                <thead>
                    <tr>
                        <th>رقم العميل</th>
                        <th>الاسم / الشركة</th>
                        <th>النوع</th>
                        <th>الهاتف</th>
                        <th>الرصيد المدين</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($customers)): foreach($customers as $c): ?>
                    <tr>
                        <td class="text-muted font-monospace">#<?php echo $c->id; ?></td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-dark);"><?php echo htmlspecialchars($c->name); ?></div>
                            <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($c->email ?? 'لا يوجد بريد'); ?></div>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $c->type === 'company' ? 'primary' : 'info'; ?>">
                                <i class="fas fa-<?php echo $c->type === 'company' ? 'building' : 'user'; ?>"></i> 
                                <?php echo $c->type === 'company' ? 'شركة' : 'فرد'; ?>
                            </span>
                        </td>
                        <td class="font-monospace"><?php echo htmlspecialchars($c->phone ?? '—'); ?></td>
                        <td>
                            <?php if ($c->balance > 0): ?>
                                <span class="badge badge-danger font-monospace"><?php echo number_format($c->balance, 2); ?></span>
                            <?php else: ?>
                                <span class="badge badge-success font-monospace">0.00</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo URLROOT; ?>/customer/edit/<?php echo $c->id; ?>" class="btn-icon edit" title="تعديل">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="<?php echo URLROOT; ?>/customer/delete/<?php echo $c->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف العميل؟');">
                                <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 40px;">لا يوجد عملاء مسجلين حالياً.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // كود بحث بسيط للجدول
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#customersTable tbody tr');
        
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>