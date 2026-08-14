<?php
// app/views/customer/index.php
$customers = $data['customers'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-users text-primary"></i> دليل العملاء (Accounts Receivable)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة العملاء، الحدود الائتمانية، وأرصدة المديونيات.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-print"></i> طباعة الدليل</button>
        <a href="<?php echo URLROOT; ?>/customer/create" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة عميل</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th>الكود</th>
                    <th>اسم العميل / الشركة</th>
                    <th>مسؤول التواصل</th>
                    <th class="text-left">الرصيد المستحق (مدين)</th>
                    <th class="text-left">الحد الائتماني</th>
                    <th class="text-center d-print-none">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $c): 
                    $balClass = $c->current_balance > 0 ? 'text-success fw-bold' : 'text-muted';
                    $limitExceeded = $c->current_balance > $c->credit_limit && $c->credit_limit > 0;
                ?>
                <tr <?php echo $limitExceeded ? 'style="background-color: #fff1f2;"' : ''; ?>>
                    <td class="font-monospace text-muted"><?php echo htmlspecialchars($c->customer_number); ?></td>
                    <td>
                        <div class="fw-bold text-dark"><i class="fas fa-user-circle text-primary me-1"></i> <?php echo htmlspecialchars($c->name); ?></div>
                        <?php if($c->company_name): ?><div class="text-muted mt-1" style="font-size:11px;"><i class="fas fa-building"></i> <?php echo htmlspecialchars($c->company_name); ?></div><?php endif; ?>
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($c->contact_person ?? '—'); ?></div>
                        <div class="text-muted font-monospace mt-1" style="font-size:11px;"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($c->phone ?? '—'); ?></div>
                    </td>
                    <td class="text-left font-monospace fs-5 <?php echo $balClass; ?>" style="direction:ltr;">
                        <?php echo number_format($c->current_balance, 2); ?>
                    </td>
                    <td class="text-left font-monospace text-muted" style="direction:ltr;">
                        <?php echo $c->credit_limit > 0 ? number_format($c->credit_limit, 2) : 'بدون حد'; ?>
                        <?php if($limitExceeded): ?><br><span class="badge badge-danger mt-1" style="font-size:10px;">تجاوز الحد!</span><?php endif; ?>
                    </td>
                    <td class="text-center d-print-none">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <a href="<?php echo URLROOT; ?>/customer/show/<?php echo $c->id; ?>" class="btn-icon view text-primary" style="border-color:var(--primary);" title="الملف الشامل"><i class="fas fa-id-card"></i></a>
                            <a href="<?php echo URLROOT; ?>/customer/edit/<?php echo $c->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                            <?php if(Session::hasRole('admin')): ?>
                            <form action="<?php echo URLROOT; ?>/customer/delete/<?php echo $c->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد مسح العميل؟');">
                                <button type="submit" class="btn-icon delete"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; if(empty($customers)): ?>
                    <tr><td colspan="6" class="text-center text-muted p-5">لا يوجد عملاء مسجلين.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>