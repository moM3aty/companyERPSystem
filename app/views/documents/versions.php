<?php
// المسار: app/views/documents/versions.php
$document = $document ?? ($data['document'] ?? null);
$versions = $versions ?? ($data['versions'] ?? []);

function formatBytesV(float|int $bytes, int $precision = 2): string { 
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
        <h3 class="mb-0 text-dark"><i class="fas fa-code-branch text-warning"></i> التحكم في الإصدارات (Version Control)</h3>
        <p class="text-muted mt-1 font-monospace fs-6">الوثيقة: <?php echo htmlspecialchars($document->title); ?></p>
    </div>
</div>

<div class="form-grid" style="grid-template-columns: 1fr 2fr; align-items: start;">
    
    <!-- نموذج رفع إصدار جديد -->
    <div class="card mb-0">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-upload text-primary"></i> رفع إصدار مُحدث</h3>
        </div>
        <form action="<?php echo URLROOT; ?>/documentVersion/create/<?php echo $document->id; ?>" method="POST" enctype="multipart/form-data">
            <div class="card-body form-group gap-3">
                <div class="form-group">
                    <label class="form-label">رقم الإصدار (V) <span class="required">*</span></label>
                    <input type="text" name="version_number" class="form-control font-monospace" required placeholder="مثال: V1.2 أو 2024-Rev2">
                </div>
                <div class="form-group">
                    <label class="form-label">الملف المُحدث <span class="required">*</span></label>
                    <input type="file" name="document_file" class="form-control" required style="padding: 8px;">
                </div>
                <div class="alert alert-info mt-2 mb-0" style="padding:10px; font-size:12px;">
                    <i class="fas fa-info-circle"></i> سيتم الاحتفاظ بالنسخة القديمة في الأرشيف وتوفير النسخة الجديدة للعمل.
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning w-100 text-dark"><i class="fas fa-save"></i> حفظ الإصدار الجديد</button>
            </div>
        </form>
    </div>

    <!-- جدول سجل الإصدارات القديمة -->
    <div class="card mb-0 h-100">
        <div class="card-header d-flex justify-content-between">
            <h3 class="card-title"><i class="fas fa-history text-info"></i> سجل التعديلات والنسخ</h3>
            <a href="<?php echo URLROOT; ?>/document/index" class="btn btn-sm btn-secondary">إدارة الوثائق</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>الإصدار</th>
                            <th>الملف والحجم</th>
                            <th>الرافع (المُعدّل)</th>
                            <th>تاريخ الرفع</th>
                            <th class="text-center">تنزيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- عرض النسخة الأساسية كإصدار أول -->
                        <tr style="background:#f8fafc;">
                            <td class="font-monospace fw-bold text-success">النسخة الأصلية</td>
                            <td class="text-muted fs-6"><?php echo htmlspecialchars($document->file_name); ?> <br> <?php echo formatBytesV($document->file_size); ?></td>
                            <td class="text-dark fw-bold">الرافع الأساسي</td>
                            <td class="text-muted fs-6"><?php echo date('Y-m-d H:i', strtotime($document->created_at)); ?></td>
                            <td class="text-center">
                                <a href="<?php echo URLROOT; ?>/document/download/<?php echo $document->id; ?>" class="btn-icon view"><i class="fas fa-download"></i></a>
                            </td>
                        </tr>
                        <!-- الإصدارات اللاحقة -->
                        <?php foreach($versions as $v): ?>
                        <tr>
                            <td class="font-monospace fw-bold text-primary"><?php echo htmlspecialchars($v->version_number); ?></td>
                            <td class="text-muted fs-6"><?php echo htmlspecialchars($v->file_name); ?> <br> <?php echo formatBytesV($v->file_size); ?></td>
                            <td class="text-dark fw-bold"><i class="fas fa-user-pen text-muted"></i> <?php echo htmlspecialchars($v->uploader_name); ?></td>
                            <td class="text-muted fs-6"><?php echo date('Y-m-d H:i', strtotime($v->created_at)); ?></td>
                            <td class="text-center">
                                <a href="<?php echo URLROOT . $v->file_path; ?>" target="_blank" class="btn-icon view" title="تنزيل / عرض"><i class="fas fa-external-link-alt"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>