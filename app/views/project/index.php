<?php
$flash = $data['flash'] ?? null;
$projects = $data['projects'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="toolbar">
        <div class="toolbar-right"><h3>المشاريع</h3></div>
        <div>
            <a href="<?php echo URL_ROOT; ?>/project/create" class="btn-add">
                <i class="fas fa-plus"></i> مشروع جديد
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
                        <th>العميل</th>
                        <th>المدير</th>
                        <th>الميزانية</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $proj) : ?>
                    <tr>
                        <td><?php echo $proj->code; ?></td>
                        <td><?php echo htmlspecialchars($proj->name); ?></td>
                        <td><?php echo htmlspecialchars($proj->customer_name ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($proj->manager_name ?? '—'); ?></td>
                        <td style="direction:ltr;"><?php echo number_format($proj->budget, 2); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $proj->status; ?>">
                                <?php echo ucfirst($proj->status); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo URL_ROOT; ?>/project/view/<?php echo $proj->id; ?>" class="act-btn btn-view"><i class="fas fa-eye"></i></a>
                            <a href="<?php echo URL_ROOT; ?>/project/edit/<?php echo $proj->id; ?>" class="act-btn btn-edit"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($projects)) : ?>
                    <tr><td colspan="7" class="empty-state">لا توجد مشاريع</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>