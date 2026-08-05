<?php
$flash = $data['flash'] ?? null;
$assets = $data['assets'] ?? [];
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
        /* أنماط مشابهة لباقي الصفحات */
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
        /* ... نفس التنسيقات ... */
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .badge { padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; }
        .badge-success { background: var(--success-light); color: #15803d; }
        .badge-warning { background: var(--accent-light); color: #b45309; }
        .badge-danger { background: var(--danger-light); color: #dc2626; }
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
                <h3>قائمة الأصول الثابتة</h3>
                <div>
                    <a href="<?php echo URL_ROOT; ?>/asset/create" class="btn-add">
                        <i class="fas fa-plus"></i> إضافة أصل جديد
                    </a>
                    <a href="<?php echo URL_ROOT; ?>/asset/calculateDepreciation" class="btn-add" style="background:var(--accent);">
                        <i class="fas fa-calculator"></i> حساب الإهلاك
                    </a>
                </div>
            </div>

            <div class="table-card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>كود الأصل</th>
                                <th>الاسم</th>
                                <th>التصنيف</th>
                                <th>سعر الشراء</th>
                                <th>القيمة الحالية</th>
                                <th>المخصص لـ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assets as $asset) : ?>
                            <tr>
                                <td><?php echo $asset->id; ?></td>
                                <td><strong><?php echo $asset->asset_code; ?></strong></td>
                                <td><?php echo htmlspecialchars($asset->name); ?></td>
                                <td><?php echo htmlspecialchars($asset->category ?? '—'); ?></td>
                                <td><?php echo number_format($asset->purchase_price, 2); ?> ر.س</td>
                                <td>
                                    <?php
                                    $value = $asset->current_value;
                                    $color = $value > 0 ? 'success' : 'danger';
                                    ?>
                                    <span class="badge badge-<?php echo $color; ?>">
                                        <?php echo number_format($value, 2); ?> ر.س
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($asset->assigned_to_name ?? '—'); ?></td>
                                <td>
                                    <a href="<?php echo URL_ROOT; ?>/asset/edit/<?php echo $asset->id; ?>" class="act-btn btn-edit"><i class="fas fa-pen"></i></a>
                                    <form method="POST" action="<?php echo URL_ROOT; ?>/asset/delete/<?php echo $asset->id; ?>" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الأصل؟')">
                                        <button type="submit" class="act-btn btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($assets)) : ?>
                            <tr><td colspan="8" style="text-align:center;padding:40px;">لا توجد أصول مسجلة</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>