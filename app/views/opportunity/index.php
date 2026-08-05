<?php
$flash = $data['flash'] ?? null;
$opportunities = $data['opportunities'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="toolbar">
        <div class="toolbar-right"><h3>فرص البيع</h3></div>
        <div>
            <a href="<?php echo URL_ROOT; ?>/opportunity/create" class="btn-add">
                <i class="fas fa-plus"></i> إضافة فرصة
            </a>
        </div>
    </div>

    <div class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>العنوان</th>
                        <th>العميل</th>
                        <th>المرحلة</th>
                        <th>القيمة</th>
                        <th>الاحتمال</th>
                        <th>المسؤول</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($opportunities as $opp) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($opp->title); ?></td>
                        <td><?php echo htmlspecialchars($opp->customer_name); ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $opp->stage)); ?></td>
                        <td style="direction:ltr;"><?php echo number_format($opp->estimated_value, 2); ?></td>
                        <td><?php echo $opp->probability; ?>%</td>
                        <td><?php echo htmlspecialchars($opp->assigned_name ?? '—'); ?></td>
                        <td>
                            <a href="<?php echo URL_ROOT; ?>/opportunity/edit/<?php echo $opp->id; ?>" class="act-btn btn-edit"><i class="fas fa-edit"></i></a>
                            <a href="<?php echo URL_ROOT; ?>/opportunity/view/<?php echo $opp->id; ?>" class="act-btn btn-view"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($opportunities)) : ?>
                    <tr><td colspan="7" class="empty-state">لا توجد فرص</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>