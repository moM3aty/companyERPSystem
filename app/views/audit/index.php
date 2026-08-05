<?php
$flash = $data['flash'] ?? null;
$logs = $data['logs'] ?? [];
$users = $data['users'] ?? [];
$actions = $data['actions'] ?? [];
$tables = $data['tables'] ?? [];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* أنماط مشابهة */
        .filter-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; background: var(--card-bg); padding: 16px; border-radius: var(--radius); border: 1px solid var(--border); }
        .filter-bar select, .filter-bar input { padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius-sm); }
        .filter-bar .btn-filter { padding: 8px 16px; background: var(--primary); color: #fff; border: none; border-radius: var(--radius-sm); cursor: pointer; }
        .json-preview { background: #f8fafc; padding: 10px; border-radius: 4px; font-size: 12px; max-height: 100px; overflow: auto; }
        .badge-info { background: var(--info-light); color: #0e7490; }
        .badge-success { background: var(--success-light); color: #15803d; }
        .badge-warning { background: var(--accent-light); color: #b45309; }
        .badge-danger { background: var(--danger-light); color: #dc2626; }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="page-body">
            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
            <?php endif; ?>

            <div class="toolbar">
                <h3>سجل التدقيق</h3>
                <button onclick="document.getElementById('cleanForm').style.display='block'" class="btn-add" style="background:var(--danger);">
                    <i class="fas fa-broom"></i> تنظيف السجل
                </button>
            </div>

            <!-- نموذج الفلترة -->
            <div class="filter-bar">
                <form method="GET" action="<?php echo URL_ROOT; ?>/audit/index" style="display:flex;gap:10px;flex-wrap:wrap;width:100%;">
                    <select name="user">
                        <option value="">جميع المستخدمين</option>
                        <?php foreach ($users as $u) : ?>
                            <option value="<?php echo $u->user_id; ?>" <?php echo ($data['filter_user'] == $u->user_id) ? 'selected' : ''; ?>>
                                <?php echo $u->name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="action">
                        <option value="">جميع الإجراءات</option>
                        <?php foreach ($actions as $act) : ?>
                            <option value="<?php echo $act->action; ?>" <?php echo ($data['filter_action'] == $act->action) ? 'selected' : ''; ?>>
                                <?php echo $act->action; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="table">
                        <option value="">جميع الجداول</option>
                        <?php foreach ($tables as $tbl) : ?>
                            <option value="<?php echo $tbl->table_name; ?>" <?php echo ($data['filter_table'] == $tbl->table_name) ? 'selected' : ''; ?>>
                                <?php echo $tbl->table_name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-filter">تصفية</button>
                    <a href="<?php echo URL_ROOT; ?>/audit/index" class="btn-cancel">إلغاء الفلترة</a>
                </form>
            </div>

            <!-- تنظيف السجل -->
            <div id="cleanForm" style="display:none;background:var(--card-bg);padding:16px;border-radius:var(--radius);margin-bottom:20px;">
                <form method="POST" action="<?php echo URL_ROOT; ?>/audit/clean">
                    <label>حذف السجلات الأقدم من (أيام):</label>
                    <input type="number" name="days" value="30" min="7" style="width:80px;padding:8px;">
                    <button type="submit" class="btn-danger" onclick="return confirm('هل أنت متأكد من حذف السجلات القديمة؟')">تأكيد الحذف</button>
                    <button type="button" onclick="this.form.style.display='none'" class="btn-cancel">إلغاء</button>
                </form>
            </div>

            <div class="table-card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>المستخدم</th>
                                <th>الإجراء</th>
                                <th>الجدول</th>
                                <th>المعرف</th>
                                <th>البيانات القديمة</th>
                                <th>البيانات الجديدة</th>
                                <th>التاريخ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log) : ?>
                            <tr>
                                <td><?php echo $log->id; ?></td>
                                <td><?php echo htmlspecialchars($log->user_name ?? '—'); ?></td>
                                <td><span class="badge badge-<?php echo $log->action === 'delete' ? 'danger' : ($log->action === 'update' ? 'warning' : 'info'); ?>"><?php echo $log->action; ?></span></td>
                                <td><?php echo $log->table_name; ?></td>
                                <td><?php echo $log->record_id ?? '—'; ?></td>
                                <td><?php echo $log->old_data ? '<span class="json-preview">' . substr($log->old_data, 0, 50) . '...</span>' : '—'; ?></td>
                                <td><?php echo $log->new_data ? '<span class="json-preview">' . substr($log->new_data, 0, 50) . '...</span>' : '—'; ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($log->created_at)); ?></td>
                                <td>
                                    <a href="<?php echo URL_ROOT; ?>/audit/view/<?php echo $log->id; ?>" class="act-btn btn-view" title="تفاصيل"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($logs)) : ?>
                            <tr><td colspan="9" style="text-align:center;padding:40px;">لا توجد سجلات</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>