<?php
// المسار: app/views/documents/index.php
$documents = $data['documents'] ?? [];

// دالة مساعدة لتحويل حجم الملف إلى صيغة مقروءة
function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow]; 
}
?>

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-folder-tree" style="color:var(--primary);"></i> نظام إدارة الوثائق (DMS)
        </h3>
        <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">أرشفة، إدارة، ومشاركة الملفات والمستندات بأمان</p>
    </div>
    
    <div style="display:flex; gap:10px;">
        <a href="<?php echo URL_ROOT; ?>/document/create" style="padding:10px 20px; background:var(--primary); color:#fff; border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; transition:0.2s; box-shadow:0 4px 10px rgba(20,184,166,0.2);">
            <i class="fas fa-cloud-arrow-up"></i> رفع وثيقة
        </a>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; width: 40px;">النوع</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">عنوان الوثيقة</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">الحجم</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">مستوى الوصول</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">الرافع والتاريخ</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; text-align:center;">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($documents)): foreach($documents as $doc): 
                    // تحديد الأيقونة واللون بناءً على نوع الملف
                    $ext = strtolower($doc->file_type);
                    $icon = 'fa-file';
                    $color = 'var(--text-muted)';
                    
                    if (in_array($ext, ['pdf'])) { $icon = 'fa-file-pdf'; $color = '#ef4444'; }
                    elseif (in_array($ext, ['doc', 'docx'])) { $icon = 'fa-file-word'; $color = '#2563eb'; }
                    elseif (in_array($ext, ['xls', 'xlsx'])) { $icon = 'fa-file-excel'; $color = '#16a34a'; }
                    elseif (in_array($ext, ['png', 'jpg', 'jpeg'])) { $icon = 'fa-file-image'; $color = '#8b5cf6'; }
                    elseif (in_array($ext, ['zip', 'rar'])) { $icon = 'fa-file-zipper'; $color = '#f59e0b'; }

                    $accessClass = $doc->access_level === 'public' ? 'badge-public' : 'badge-private';
                    $accessLabel = $doc->access_level === 'public' ? '<i class="fas fa-globe"></i> عام' : '<i class="fas fa-lock"></i> خاص';
                ?>
                <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;">
                    <td style="padding:14px 20px; text-align:center;">
                        <i class="fas <?php echo $icon; ?>" style="font-size:24px; color:<?php echo $color; ?>;"></i>
                    </td>
                    <td style="padding:14px 20px;">
                        <div style="font-weight:700; color:var(--text-dark); font-size:14px; margin-bottom:4px;"><?php echo htmlspecialchars($doc->title); ?></div>
                        <div style="font-size:11px; color:var(--text-muted); font-family:monospace; direction:ltr; text-align:right;"><?php echo htmlspecialchars($doc->file_name); ?></div>
                    </td>
                    <td style="padding:14px 20px; font-family:monospace; font-size:13px; color:var(--text-body); font-weight:600; direction:ltr; text-align:right;">
                        <?php echo formatBytes($doc->file_size); ?>
                    </td>
                    <td style="padding:14px 20px;">
                        <span class="badge <?php echo $accessClass; ?>"><?php echo $accessLabel; ?></span>
                    </td>
                    <td style="padding:14px 20px;">
                        <div style="font-size:13px; font-weight:600; color:var(--text-body); margin-bottom:2px;"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($doc->uploaded_by_name); ?></div>
                        <div style="font-size:11px; color:var(--text-muted);"><i class="far fa-clock"></i> <?php echo date('Y-m-d H:i', strtotime($doc->created_at)); ?></div>
                    </td>
                    <td style="padding:14px 20px; text-align:center;">
                        <div style="display:flex; justify-content:center; gap:8px;">
                            <a href="<?php echo URL_ROOT; ?>/document/download/<?php echo $doc->id; ?>" class="act-btn" style="color:var(--primary); background:var(--primary-light); border-color:var(--primary-light);" title="تنزيل الملف"><i class="fas fa-download"></i></a>
                            
                            <?php if (Session::getUserRole() === 'admin' || Session::getUserId() === $doc->uploaded_by): ?>
                            <form action="<?php echo URL_ROOT; ?>/document/delete/<?php echo $doc->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الوثيقة نهائياً؟');">
                                <button type="submit" class="act-btn" style="color:var(--danger); background:var(--danger-light); border-color:var(--danger-light);" title="حذف"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:60px 20px;">
                        <i class="fas fa-folder-open" style="font-size:40px; color:var(--border); margin-bottom:12px;"></i>
                        <h4 style="margin:0 0 6px; font-size:15px; color:var(--text-dark);">لا توجد وثائق مؤرشفة</h4>
                        <p style="margin:0; font-size:13px; color:var(--text-muted);">ابدأ برفع الملفات والمستندات الهامة للحفاظ عليها وتنظيمها.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; }
    .badge-public { background: var(--success-light); color: var(--success); border: 1px solid var(--success); }
    .badge-private { background: var(--page-bg); color: var(--text-muted); border: 1px solid var(--border); }
    .act-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid transparent; text-decoration: none; cursor: pointer; transition: 0.2s; }
    .act-btn:hover { filter: brightness(0.9); }
</style>