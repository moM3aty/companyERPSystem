<?php
// app/views/journal/index.php
$entries = $data['entries'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-book-journal-whills text-primary"></i> دفتر قيود اليومية</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع وعرض وتعديل الحركات المحاسبية اليومية المزدوجة.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/journal/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> إنشاء قيد يومية يدوي
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
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>رقم القيد</th>
                        <th>التاريخ</th>
                        <th>البيان العام</th>
                        <th>المصدر (المرجع)</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $entry) : ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($entry->entry_number); ?></td>
                        <td class="text-muted fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($entry->entry_date)); ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($entry->description); ?></div>
                            <div class="text-muted" style="font-size:11px;">بواسطة: <?php echo htmlspecialchars($entry->creator_name ?? 'النظام'); ?></div>
                        </td>
                        <td>
                            <?php if(!empty($entry->reference_type)): ?>
                                <span class="badge badge-info"><i class="fas fa-link"></i> <?php echo htmlspecialchars($entry->reference_type); ?></span>
                            <?php else: ?>
                                <span class="badge badge-secondary"><i class="fas fa-user-pen"></i> إدخال يدوي</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/journal/show/<?php echo $entry->id; ?>" class="btn-icon view" title="عرض القيد"><i class="fas fa-eye"></i></a>
                                
                                <!-- زر التعديل الجديد -->
                                <a href="<?php echo URLROOT; ?>/journal/edit/<?php echo $entry->id; ?>" class="btn-icon edit" title="تعديل القيد"><i class="fas fa-pen"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($entries)) : ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted p-5">
                            <i class="fas fa-book-open fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد قيود يومية مسجلة في النظام.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>