<?php
$flash = $data['flash'] ?? null;
$project = $data['project'] ?? null;
$tasks = $data['tasks'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <h3>تفاصيل المشروع: <?php echo $project->name; ?></h3>
    <div class="info-bar">
        <div class="ib-item">الكود: <?php echo $project->code; ?></div>
        <div class="ib-item">العميل: <?php echo htmlspecialchars($project->customer_name ?? '—'); ?></div>
        <div class="ib-item">المدير: <?php echo htmlspecialchars($project->manager_name ?? '—'); ?></div>
        <div class="ib-item">الميزانية: <?php echo number_format($project->budget, 2); ?></div>
        <div class="ib-item">الحالة: <?php echo ucfirst($project->status); ?></div>
        <div class="ib-item">الفترة: <?php echo $project->start_date . ' إلى ' . $project->end_date; ?></div>
    </div>

    <div class="table-card">
        <div class="card-header">
            <h3>المهام</h3>
            <a href="<?php echo URL_ROOT; ?>/project/add-task/<?php echo $project->id; ?>" class="btn-add">+ إضافة مهمة</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>العنوان</th>
                        <th>المسؤول</th>
                        <th>تاريخ الاستحقاق</th>
                        <th>الأولوية</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task->title); ?></td>
                        <td><?php echo htmlspecialchars($task->assigned_to_name ?? '—'); ?></td>
                        <td><?php echo $task->due_date; ?></td>
                        <td><?php echo ucfirst($task->priority); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $task->status; ?>">
                                <?php echo ucfirst($task->status); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo URL_ROOT; ?>/project/edit-task/<?php echo $task->id; ?>" class="act-btn btn-edit"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($tasks)) : ?>
                    <tr><td colspan="6" class="empty-state">لا توجد مهام</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>