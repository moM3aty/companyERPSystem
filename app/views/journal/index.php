<?php
// app/views/journal/index.php
$entries = $data['entries'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-book-journal-whills text-primary"></i> سجل القيود اليومية</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">جميع الحركات المالية مسجلة بنظام القيد المزدوج.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/journal/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة قيد مزدوج
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم القيد</th>
                        <th>التاريخ</th>
                        <th>البيان (الوصف)</th>
                        <th>المنشئ</th>
                        <th class="text-center">عرض</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($entries)): foreach($entries as $entry): ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($entry->entry_number); ?></td>
                        <td class="text-muted fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($entry->entry_date)); ?></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($entry->description); ?></td>
                        <td class="text-muted fs-6"><i class="fas fa-user-pen"></i> <?php echo htmlspecialchars($entry->creator_name ?? 'النظام'); ?></td>
                        <td class="text-center">
                            <a href="<?php echo URLROOT; ?>/journal/show/<?php echo $entry->id; ?>" class="btn-icon view" title="عرض القيد">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding: 40px;">لا توجد قيود يومية مسجلة بعد.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>