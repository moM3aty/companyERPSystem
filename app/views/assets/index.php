<?php
// المسار: app/views/assets/index.php
$assets = $data['assets'] ?? [];
?>

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-building" style="color:var(--primary);"></i> سجل الأصول الثابتة (Fixed Assets)
        </h3>
        <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">إدارة الأصول ومتابعة الإهلاك والقيمة الدفترية</p>
    </div>
    
    <div style="display:flex; gap:10px;">
        <a href="<?php echo URL_ROOT; ?>/asset/create" style="padding:10px 20px; background:var(--primary); color:#fff; border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; transition:0.2s; box-shadow:0 4px 10px rgba(20,184,166,0.2);">
            <i class="fas fa-plus"></i> تسجيل أصل جديد
        </a>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">رمز / اسم الأصل</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">التصنيف والموقع</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">تكلفة الشراء</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">القيمة الدفترية (مُهلك)</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">حالة الأصل</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; text-align:center;">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($assets)): foreach($assets as $asset): 
                    // تنسيق الأيقونات للتصنيفات
                    $catIcon = match($asset->category) {
                        'equipment' => '<i class="fas fa-microchip" style="color:var(--info);"></i>',
                        'vehicle' => '<i class="fas fa-car" style="color:var(--accent);"></i>',
                        'furniture' => '<i class="fas fa-chair" style="color:var(--purple);"></i>',
                        'real_estate' => '<i class="fas fa-building" style="color:var(--primary);"></i>',
                        default => '<i class="fas fa-box" style="color:var(--text-muted);"></i>'
                    };
                    $catLabel = match($asset->category) {
                        'equipment' => 'معدات وأجهزة', 'vehicle' => 'مركبات وسيارات', 'furniture' => 'أثاث وتجهيزات', 'real_estate' => 'عقارات ومباني', default => 'أخرى'
                    };

                    // تنسيق الحالات
                    $statusClass = match($asset->status) {
                        'active' => 'background:var(--success-light); color:var(--success); border:1px solid var(--success);',
                        'maintenance' => 'background:var(--accent-light); color:#b45309; border:1px solid var(--accent);',
                        'disposed' => 'background:var(--danger-light); color:var(--danger); border:1px solid var(--danger); text-decoration:line-through;',
                        'sold' => 'background:var(--page-bg); color:var(--text-muted); border:1px solid var(--border);',
                        default => 'background:var(--page-bg);'
                    };
                    $statusLabel = match($asset->status) {
                        'active' => 'نشط (قيد الاستخدام)', 'maintenance' => 'في الصيانة', 'disposed' => 'مُكهن (تالف)', 'sold' => 'تم البيع', default => $asset->status
                    };
                ?>
                <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;">
                    <td style="padding:14px 20px;">
                        <div style="font-weight:700; color:var(--text-dark); font-size:14px; margin-bottom:4px;"><?php echo htmlspecialchars($asset->name); ?></div>
                        <span style="background:var(--page-bg); padding:2px 6px; border-radius:4px; border:1px solid var(--border); font-family:monospace; font-size:11px; color:var(--text-muted);"><i class="fas fa-barcode"></i> <?php echo htmlspecialchars($asset->asset_tag); ?></span>
                    </td>
                    <td style="padding:14px 20px;">
                        <div style="font-size:13px; font-weight:600; color:var(--text-body); margin-bottom:4px;"><?php echo $catIcon . ' ' . $catLabel; ?></div>
                        <div style="font-size:11px; color:var(--text-muted);"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($asset->location ?? 'غير محدد الموقع'); ?></div>
                    </td>
                    <td style="padding:14px 20px;">
                        <div style="font-family:monospace; font-weight:800; font-size:14px; color:var(--text-dark); direction:ltr; text-align:right; margin-bottom:2px;"><?php echo number_format($asset->purchase_cost, 2); ?></div>
                        <div style="font-size:10px; color:var(--text-muted);"><i class="far fa-calendar"></i> شراء: <?php echo date('Y-m-d', strtotime($asset->purchase_date)); ?></div>
                    </td>
                    <td style="padding:14px 20px;">
                        <div style="font-family:monospace; font-weight:800; font-size:15px; color:var(--primary-dark); direction:ltr; text-align:right; margin-bottom:2px;" title="القيمة الحالية = التكلفة - مجمع الإهلاك">
                            <?php echo number_format($asset->book_value, 2); ?>
                        </div>
                        <div style="font-size:10px; color:var(--danger); direction:ltr; text-align:right;" title="مجمع الإهلاك">
                            -<?php echo number_format($asset->accumulated_depreciation, 2); ?>
                        </div>
                    </td>
                    <td style="padding:14px 20px;">
                        <span style="padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                    </td>
                    <td style="padding:14px 20px; text-align:center;">
                        <button title="تفاصيل (تحت التطوير)" style="border:none; background:var(--info-light); color:var(--info-dark); width:32px; height:32px; border-radius:6px; cursor:pointer;"><i class="fas fa-eye"></i></button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:60px 20px;">
                        <i class="fas fa-couch" style="font-size:40px; color:var(--border); margin-bottom:12px;"></i>
                        <h4 style="margin:0 0 6px; font-size:15px; color:var(--text-dark);">لا توجد أصول مسجلة</h4>
                        <p style="margin:0; font-size:13px; color:var(--text-muted);">قم بتسجيل أجهزة، مركبات، أو عقارات الشركة لتتبع قيمتها الدفترية.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>