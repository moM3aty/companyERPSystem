<?php
// المسار: app/views/documents/index.php
$documents = $documents ?? ($data['documents'] ?? []);

function formatBytes(float|int $bytes, int $precision = 2): string { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow]; 
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-folder-tree text-primary"></i> نظام إدارة الوثائق (DMS)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">أرشفة، إدارة، ومشاركة الملفات والمستندات بأمان.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/document/create" class="btn btn-primary">
        <i class="fas fa-cloud-arrow-up"></i> رفع وثيقة
    </a>
</div>

<?php 
    $flash = Session::getFlash();
    if ($flash): 
?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">النوع</th>
                        <th>عنوان الوثيقة</th>
                        <th>الحجم</th>
                        <th class="text-center">مستوى الوصول</th>
                        <th>الرافع والتاريخ</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($documents)): foreach($documents as $doc): 
                        $ext = strtolower($doc->file_type);
                        $icon = 'fa-file text-muted';
                        if (in_array($ext, ['pdf'])) $icon = 'fa-file-pdf text-danger';
                        elseif (in_array($ext, ['doc', 'docx'])) $icon = 'fa-file-word text-info';
                        elseif (in_array($ext, ['xls', 'xlsx'])) $icon = 'fa-file-excel text-success';
                        elseif (in_array($ext, ['png', 'jpg', 'jpeg'])) $icon = 'fa-file-image text-purple';
                        elseif (in_array($ext, ['zip', 'rar'])) $icon = 'fa-file-zipper text-warning';

                        $accessClass = $doc->access_level === 'public' ? 'badge-success' : 'badge-secondary';
                        $accessLabel = $doc->access_level === 'public' ? '<i class="fas fa-globe"></i> عام' : '<i class="fas fa-lock"></i> خاص';
                    ?>
                    <tr>
                        <td class="text-center"><i class="fas <?php echo $icon; ?> fs-3"></i></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($doc->title); ?></div>
                            <div class="text-muted font-monospace" style="font-size:11px; direction:ltr; text-align:right;"><?php echo htmlspecialchars($doc->file_name); ?></div>
                        </td>
                        <td class="font-monospace fw-bold text-dark" style="direction:ltr; text-align:right;"><?php echo formatBytes($doc->file_size); ?></td>
                        <td class="text-center"><span class="badge <?php echo $accessClass; ?>"><?php echo $accessLabel; ?></span></td>
                        <td class="text-muted fs-6">
                            <div class="fw-bold text-body"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($doc->uploader_name ?? $doc->uploaded_by_name ?? 'غير معروف'); ?></div>
                            <div style="font-size:11px;"><i class="far fa-clock"></i> <?php echo date('Y-m-d H:i', strtotime($doc->created_at)); ?></div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <!-- زر إصدارات الملف الذي طلبته -->
                                <a href="<?php echo URLROOT; ?>/documentVersion/index/<?php echo $doc->id; ?>" class="btn-icon text-warning" style="background:var(--accent-light); border-color:var(--accent);" title="إصدارات الملف"><i class="fas fa-code-branch"></i></a>
                                
                                <a href="<?php echo URLROOT; ?>/document/download/<?php echo $doc->id; ?>" class="btn-icon view" title="تنزيل"><i class="fas fa-download"></i></a>
                                
                                <?php if (Session::getUserRole() === 'admin' || Session::getUserId() === $doc->uploaded_by): ?>
                                <form action="<?php echo URLROOT; ?>/document/delete/<?php echo $doc->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الحذف نهائياً؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding: 40px;">لا توجد وثائق مؤرشفة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>