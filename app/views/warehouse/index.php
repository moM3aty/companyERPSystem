<?php
$flash = $data['flash'] ?? null;
$warehouses = $data['warehouses'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="toolbar">
        <div class="toolbar-right"><h3>المستودعات</h3></div>
        <div>
            <a href="<?php echo URL_ROOT; ?>/warehouse/create" class="btn-add">
                <i class="fas fa-plus"></i> إضافة مستودع
            </a>
        </div>
    </div>

    <div class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>الكود</th>
                        <th>الاسم</th>
                        <th>العنوان</th>
                        <th>رئيسي</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($warehouses as $wh) : ?>
                    <tr>
                        <td><?php echo $wh->code; ?></td>
                        <td><?php echo htmlspecialchars($wh->name); ?></td>
                        <td><?php echo htmlspecialchars($wh->address ?? '—'); ?></td>
                        <td><?php echo $wh->is_main ? 'نعم' : 'لا'; ?></td>
                        <td>
                            <a href="<?php echo URL_ROOT; ?>/warehouse/edit/<?php echo $wh->id; ?>" class="act-btn btn-edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="<?php echo URL_ROOT; ?>/warehouse/delete/<?php echo $wh->id; ?>" style="display:inline;">
                                <button type="submit" class="act-btn btn-del" onclick="return confirm('هل أنت متأكد؟')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($warehouses)) : ?>
                    <tr><td colspan="5" class="empty-state">لا توجد مستودعات</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>