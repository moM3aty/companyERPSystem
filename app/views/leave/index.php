<?php $flash = $data['flash'] ?? null; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* أنماط مشابهة للملفات الأخرى (يمكنك استيراد نفس الـ CSS) */
        :root {
            --primary: #14b8a6;
            --primary-dark: #0d9488;
            --primary-light: #ccfbf1;
            --accent: #f59e0b;
            --accent-light: #fef3c7;
            --success: #22c55e;
            --success-light: #dcfce7;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --info: #06b6d4;
            --info-light: #cffafe;
            --sidebar-w: 272px;
            --topbar-h: 68px;
            --page-bg: #f1f5f9;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-body: #475569;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --radius: 14px;
            --radius-sm: 10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
        }
        /* ... نفس التنسيقات السابقة ... */
    </style>
</head>
<body>
    <!-- الـ Layout سيتولى الـ Sidebar والـ Topbar -->
    <div class="main-content">
        <div class="page-body">
            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <div class="toolbar">
                <div class="toolbar-right">
                    <h3>طلبات الإجازات</h3>
                </div>
                <div>
                    <a href="<?php echo URL_ROOT; ?>/leave/create" class="btn-add">
                        <i class="fas fa-plus"></i> طلب إجازة جديد
                    </a>
                </div>
            </div>

            <div class="table-card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الموظف</th>
                                <th>النوع</th>
                                <th>من</th>
                                <th>إلى</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['requests'] as $req) : ?>
                            <tr>
                                <td><?php echo $req->id; ?></td>
                                <td><?php echo htmlspecialchars($req->employee_name ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($req->leave_type_name ?? '—'); ?></td>
                                <td><?php echo $req->start_date; ?></td>
                                <td><?php echo $req->end_date; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $req->status; ?>">
                                        <?php echo ucfirst($req->status); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($data['is_admin'] && $req->status === 'pending') : ?>
                                        <form method="POST" action="<?php echo URL_ROOT; ?>/leave/approve/<?php echo $req->id; ?>" style="display:inline;">
                                            <button type="submit" class="act-btn btn-success" title="موافقة">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo URL_ROOT; ?>/leave/reject/<?php echo $req->id; ?>" style="display:inline;">
                                            <button type="submit" class="act-btn btn-danger" title="رفض">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($data['requests'])) : ?>
                            <tr><td colspan="7" class="empty-state">لا توجد طلبات إجازات</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>