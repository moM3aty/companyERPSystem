<?php
// تعريف المتغيرات من مصفوفة البيانات
$users = $data['users'] ?? [];
?>
<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:var(--text-dark); margin:0;">
            <i class="fas fa-users-gear" style="color:var(--primary); margin-left:8px;"></i> إدارة النظام والمستخدمين
        </h2>
    </div>
    <div>
        <a href="<?php echo URL_ROOT; ?>/user/create" style="display:inline-flex; align-items:center; gap:8px; padding:10px 20px; background:var(--primary); color:#fff; border-radius:8px; text-decoration:none; font-weight:600; font-size:13px;">
            <i class="fas fa-user-plus"></i> مستخدم جديد
        </a>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:14px 20px; font-size:12px; color:var(--text-muted);">#</th>
                    <th style="padding:14px 20px; font-size:12px; color:var(--text-muted);">المستخدم</th>
                    <th style="padding:14px 20px; font-size:12px; color:var(--text-muted);">الصلاحية (الدور)</th>
                    <th style="padding:14px 20px; font-size:12px; color:var(--text-muted);">الهاتف</th>
                    <th style="padding:14px 20px; font-size:12px; color:var(--text-muted);">تاريخ الانضمام</th>
                    <th style="padding:14px 20px; font-size:12px; color:var(--text-muted); text-align:center;">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): 
                    $roleBadge = match($user->role) {
                        'admin' => '<span style="background:var(--danger-light); color:var(--danger); padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700;"><i class="fas fa-crown"></i> مدير عام</span>',
                        'editor' => '<span style="background:var(--success-light); color:var(--success); padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700;"><i class="fas fa-pen"></i> محرر</span>',
                        default => '<span style="background:var(--info-light); color:var(--info); padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700;"><i class="fas fa-eye"></i> عارض</span>',
                    };
                ?>
                <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;">
                    <td style="padding:14px 20px; color:var(--text-muted); font-size:13px;"><?php echo $user->id; ?></td>
                    <td style="padding:14px 20px;">
                        <div style="font-weight:700; color:var(--text-dark);"><?php echo htmlspecialchars($user->name); ?></div>
                        <div style="font-size:12px; color:var(--text-muted); direction:ltr; text-align:right;"><?php echo htmlspecialchars($user->email); ?></div>
                    </td>
                    <td style="padding:14px 20px;"><?php echo $roleBadge; ?></td>
                    <td style="padding:14px 20px; direction:ltr; text-align:right; font-size:13px;"><?php echo htmlspecialchars($user->phone ?? '—'); ?></td>
                    <td style="padding:14px 20px; font-size:12px; color:var(--text-muted);"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($user->created_at)); ?></td>
                    <td style="padding:14px 20px; text-align:center;">
                        <?php if($user->id !== Session::getUserId()): ?>
                            <form action="<?php echo URL_ROOT; ?>/user/delete/<?php echo $user->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف هذا المستخدم نهائياً؟');">
                                <button type="submit" style="background:transparent; border:1px solid var(--danger-light); color:var(--danger); width:32px; height:32px; border-radius:6px; cursor:pointer;" title="حذف الحساب"><i class="fas fa-trash"></i></button>
                            </form>
                        <?php else: ?>
                            <span style="font-size:11px; color:var(--success); font-weight:600;">(حسابك الحالي)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($users)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">لا يوجد مستخدمين لعرضهم</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>