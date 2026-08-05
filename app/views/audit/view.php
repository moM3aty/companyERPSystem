<?php
$flash = $data['flash'] ?? null;
$log = $data['log'] ?? null;
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3>تفاصيل السجل #<?php echo $log->id; ?></h3>
            <a href="<?php echo URL_ROOT; ?>/audit/index" class="btn-cancel">رجوع</a>
        </div>
        <div class="card-body">
            <table class="details-table">
                <tr><th>المستخدم</th><td><?php echo htmlspecialchars($log->user_name ?? '—'); ?></td></tr>
                <tr><th>الإجراء</th><td><?php echo $log->action; ?></td></tr>
                <tr><th>الجدول</th><td><?php echo $log->table_name; ?></td></tr>
                <tr><th>معرف السجل</th><td><?php echo $log->record_id ?? '—'; ?></td></tr>
                <tr><th>عنوان IP</th><td><?php echo $log->ip_address ?? '—'; ?></td></tr>
                <tr><th>المتصفح</th><td><?php echo htmlspecialchars($log->user_agent ?? '—'); ?></td></tr>
                <tr><th>التاريخ</th><td><?php echo date('Y-m-d H:i:s', strtotime($log->created_at)); ?></td></tr>
                <tr>
                    <th>البيانات القديمة</th>
                    <td>
                        <?php if ($log->old_data) : ?>
                            <pre style="background:#f8fafc;padding:10px;border-radius:4px;"><?php print_r($log->old_data); ?></pre>
                        <?php else : ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>البيانات الجديدة</th>
                    <td>
                        <?php if ($log->new_data) : ?>
                            <pre style="background:#f8fafc;padding:10px;border-radius:4px;"><?php print_r($log->new_data); ?></pre>
                        <?php else : ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>