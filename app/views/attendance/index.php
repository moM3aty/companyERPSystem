<?php
$flash = $data['flash'] ?? null;
$records = $data['records'] ?? [];
$month = $data['month'] ?? date('m');
$year = $data['year'] ?? date('Y');
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
        /* أنماط مشابهة للملفات الأخرى (اختصارًا) */
        :root {
            --primary: #14b8a6; --primary-dark: #0d9488; --primary-light: #ccfbf1;
            --accent: #f59e0b; --accent-light: #fef3c7;
            --success: #22c55e; --success-light: #dcfce7;
            --danger: #ef4444; --danger-light: #fee2e2;
            --info: #06b6d4; --info-light: #cffafe;
            --sidebar-w: 272px; --topbar-h: 68px;
            --page-bg: #f1f5f9; --card-bg: #ffffff;
            --text-dark: #0f172a; --text-body: #475569; --text-muted: #94a3b8;
            --border: #e2e8f0; --radius: 14px; --radius-sm: 10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06); --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
        }
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; background: var(--page-bg); color: var(--text-body); min-height: 100vh; }
        /* ... باقي التنسيقات مشابهة لما في الملفات الأخرى ... يمكنك استيرادها من main.css */
    </style>
</head>
<body>
    <!-- سيتم تضمين الـ Layout الرئيسي -->
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
                    <h3>سجل الحضور</h3>
                    <form method="GET" style="display:inline;">
                        <select name="month" class="form-input" style="width:120px;">
                            <?php for ($m = 1; $m <= 12; $m++) : ?>
                                <option value="<?php echo str_pad($m,2,'0',STR_PAD_LEFT); ?>" <?php echo $month == $m ? 'selected' : ''; ?>>
                                    <?php echo date('F', mktime(0,0,0,$m,1)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <select name="year" class="form-input" style="width:100px;">
                            <?php for ($y = date('Y')-2; $y <= date('Y'); $y++) : ?>
                                <option value="<?php echo $y; ?>" <?php echo $year == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="btn-add">عرض</button>
                    </form>
                </div>
                <div>
                    <a href="<?php echo URL_ROOT; ?>/attendance/create" class="btn-add">
                        <i class="fas fa-plus"></i> تسجيل حضور
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
                                <th>التاريخ</th>
                                <th>وقت الدخول</th>
                                <th>وقت الخروج</th>
                                <th>الحالة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $rec) : ?>
                            <tr>
                                <td><?php echo $rec->id; ?></td>
                                <td><?php echo htmlspecialchars($rec->employee_name); ?></td>
                                <td><?php echo $rec->date; ?></td>
                                <td><?php echo $rec->check_in ? date('h:i A', strtotime($rec->check_in)) : '—'; ?></td>
                                <td><?php echo $rec->check_out ? date('h:i A', strtotime($rec->check_out)) : '—'; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $rec->status; ?>">
                                        <?php echo ucfirst($rec->status); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($rec->notes ?? '—'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($records)) : ?>
                            <tr><td colspan="7" class="empty-state">لا توجد سجلات حضور لهذا الشهر</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>