<?php
$flash = $data['flash'] ?? null;
$entries = $data['entries'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="toolbar">
        <div class="toolbar-right">
            <h3>دفتر الأستاذ</h3>
        </div>
        <div>
            <a href="<?php echo URL_ROOT; ?>/account/create-journal" class="btn-add">
                <i class="fas fa-plus"></i> قيد يومي جديد
            </a>
        </div>
    </div>

    <div class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>رقم القيد</th>
                        <th>التاريخ</th>
                        <th>البيان</th>
                        <th>المرجع</th>
                        <th>المنشئ</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $entry) : ?>
                    <tr>
                        <td><?php echo $entry->entry_number; ?></td>
                        <td><?php echo $entry->entry_date; ?></td>
                        <td><?php echo htmlspecialchars($entry->description); ?></td>
                        <td><?php echo $entry->reference_type ? $entry->reference_type . '#' . $entry->reference_id : '—'; ?></td>
                        <td><?php echo htmlspecialchars($entry->created_by_name); ?></td>
                        <td>
                            <a href="<?php echo URL_ROOT; ?>/account/view-journal/<?php echo $entry->id; ?>" class="act-btn btn-view">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($entries)) : ?>
                    <tr><td colspan="6" class="empty-state">لا توجد قيود يومية</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>