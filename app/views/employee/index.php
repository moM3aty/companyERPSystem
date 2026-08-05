<?php
// تعريف المتغيرات من مصفوفة البيانات
$employees = $data['employees'] ?? [];
?>
<!-- المسار: app/views/employee/index.php -->

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-users" style="color:var(--primary);"></i> سجل الموظفين
        </h3>
        <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">إدارة قاعدة بيانات القوى العاملة (HR)</p>
    </div>
    
    <div style="display:flex; gap:10px;">
        <div class="search-box" style="position:relative;">
            <input type="text" placeholder="ابحث باسم الموظف أو المسمى..." style="padding:10px 16px 10px 36px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; font-size:13px; outline:none; min-width:250px;">
            <i class="fas fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:12px;"></i>
        </div>
        <a href="<?php echo URL_ROOT; ?>/employee/create" style="padding:10px 20px; background:var(--primary); color:#fff; border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; transition:0.2s; box-shadow:0 4px 10px rgba(20,184,166,0.2);">
            <i class="fas fa-user-plus"></i> موظف جديد
        </a>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">#</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">الموظف / التواصل</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">المسمى الوظيفي والقسم</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">الراتب الأساسي</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">تاريخ التسجيل</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; text-align:center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($employees)): foreach($employees as $emp): ?>
                <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;">
                    <td style="padding:14px 20px; color:var(--text-muted); font-size:12px; font-weight:600;"><?php echo $emp->id; ?></td>
                    
                    <td style="padding:14px 20px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:40px; height:40px; border-radius:10px; background:var(--primary-light); color:var(--primary-dark); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:14px; flex-shrink:0;">
                                <?php echo mb_substr($emp->name, 0, 2); ?>
                            </div>
                            <div>
                                <div style="font-weight:700; color:var(--text-dark); font-size:14px; margin-bottom:2px;"><?php echo htmlspecialchars($emp->name); ?></div>
                                <div style="font-size:12px; color:var(--text-muted); direction:ltr; text-align:right;"><i class="fas fa-envelope" style="font-size:10px;"></i> <?php echo htmlspecialchars($emp->email); ?></div>
                            </div>
                        </div>
                    </td>
                    
                    <td style="padding:14px 20px;">
                        <div style="font-size:13px; font-weight:600; color:var(--text-body); margin-bottom:2px;"><?php echo htmlspecialchars($emp->position); ?></div>
                        <span style="display:inline-flex; align-items:center; gap:4px; padding:2px 8px; background:var(--page-bg); border:1px solid var(--border); border-radius:6px; font-size:10px; color:var(--text-muted); font-weight:700;">
                            <i class="fas fa-building"></i> <?php echo htmlspecialchars($emp->department_name ?? 'غير محدد'); ?>
                        </span>
                    </td>
                    
                    <td style="padding:14px 20px;">
                        <span style="font-weight:700; color:var(--success); font-variant-numeric:tabular-nums; direction:ltr; display:inline-block;">
                            <?php echo number_format($emp->salary, 2); ?> <span style="font-size:10px; color:var(--text-muted);">ر.س</span>
                        </span>
                    </td>
                    
                    <td style="padding:14px 20px;">
                        <div style="font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:6px;">
                            <i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($emp->created_at)); ?>
                        </div>
                    </td>
                    
                    <td style="padding:14px 20px; text-align:center;">
                        <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                            <button title="تعديل (غير متاح حالياً)" style="width:32px; height:32px; border-radius:8px; border:1px solid var(--border); background:transparent; color:var(--accent); cursor:pointer;"><i class="fas fa-pen"></i></button>
                            
                            <?php if (Session::hasRole('admin')): ?>
                            <form method="POST" action="<?php echo URL_ROOT; ?>/employee/delete/<?php echo $emp->id; ?>" onsubmit="return confirm('هل أنت متأكد من حذف بيانات هذا الموظف نهائياً؟');" style="display:inline;">
                                <button type="submit" title="حذف الموظف" style="width:32px; height:32px; border-radius:8px; border:1px solid var(--danger-light); background:transparent; color:var(--danger); cursor:pointer;"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:60px 20px;">
                        <i class="fas fa-users-slash" style="font-size:40px; color:var(--border); margin-bottom:12px;"></i>
                        <h4 style="margin:0 0 6px; font-size:15px; color:var(--text-dark);">لا يوجد موظفين مسجلين</h4>
                        <p style="margin:0; font-size:13px; color:var(--text-muted);">قم بإضافة الموظفين لبدء إدارة رواتبهم وحضورهم.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>