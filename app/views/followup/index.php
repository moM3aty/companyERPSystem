<?php
$flash = $data['flash'] ?? null;
$followups = $data['followups'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="toolbar">
        <div class="toolbar-right"><h3>المتابعات</h3></div>
        <div>
            <a href="<?php echo URL_ROOT; ?>/followup/create" class="btn-add">
                <i class="fas fa-plus"></i> متابعة جديدة
            </a>
        </div>
    </div>

    <div class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>الموضوع</th>
                        <th>النوع</th>
                        <th>العميل</th>
                        <th>الفرصة</th>
                        <th>التاريخ المحدد</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($followups as $fu) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fu->subject); ?></td>
                        <td><?php echo ucfirst($fu->type); ?></td>
                        <td><?php echo htmlspecialchars($fu->customer_name ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($fu->opportunity_title ?? '—'); ?></td>
                        <td><?php echo $fu->scheduled_at; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $fu->status; ?>">
                                <?php echo ucfirst($fu->status); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo URL_ROOT; ?>/followup/edit/<?php echo $fu->id; ?>" class="act-btn btn-edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="<?php echo URL_ROOT; ?>/followup/delete/<?php echo $fu->id; ?>" style="display:inline;">
                                <button type="submit" class="act-btn btn-del"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($followups)) : ?>
                    <tr><td colspan="7" class="empty-state">لا توجد متابعات</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>