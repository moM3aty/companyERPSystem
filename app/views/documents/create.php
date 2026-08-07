<?php
// المسار: app/views/documents/create.php
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cloud-arrow-up text-primary"></i> أرشفة وثيقة جديدة</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/document/create" method="POST" enctype="multipart/form-data" id="uploadForm">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">عنوان / وصف الوثيقة <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="مثال: عقد تأسيس الشركة، الهوية الوطنية...">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">الملف المراد رفعه <span class="required">*</span></label>
                    <div style="position:relative; width:100%;">
                        <input type="file" name="document_file" id="docFile" required style="position:absolute; width:100%; height:100%; opacity:0; cursor:pointer; z-index:2;">
                        <div style="border: 2px dashed var(--primary); background: var(--primary-light); border-radius: var(--radius-sm); padding: 30px; text-align: center; color: var(--primary-dark);">
                            <i class="fas fa-file-arrow-up" style="font-size: 32px; margin-bottom: 10px;"></i>
                            <h4 style="margin: 0 0 5px; font-size: 15px;">اضغط هنا لاختيار الملف أو قم بسحبه وإفلاته</h4>
                            <span id="fileNameDisplay" class="font-monospace text-muted fw-bold fs-6">لم يتم اختيار ملف بعد</span>
                        </div>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">مستوى الوصول (الخصوصية) <span class="required">*</span></label>
                    <div class="d-flex gap-3 mt-2">
                        <label class="d-flex align-items-center gap-2 p-2 border rounded cursor-pointer w-100 bg-light">
                            <input type="radio" name="access_level" value="private" checked> 
                            <div>
                                <strong class="text-dark"><i class="fas fa-lock"></i> خاص (Private)</strong><br>
                                <span class="text-muted" style="font-size:11px;">يمكن لك وللإدارة فقط رؤية الملف.</span>
                            </div>
                        </label>
                        <label class="d-flex align-items-center gap-2 p-2 border rounded cursor-pointer w-100 bg-light">
                            <input type="radio" name="access_level" value="public"> 
                            <div>
                                <strong class="text-dark"><i class="fas fa-globe"></i> عام (Public)</strong><br>
                                <span class="text-muted" style="font-size:11px;">يمكن لجميع موظفي النظام الوصول إليه.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-upload"></i> رفع وأرشفة</button>
            <a href="<?php echo URLROOT; ?>/document/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('docFile').addEventListener('change', function(e) {
        const display = document.getElementById('fileNameDisplay');
        if(e.target.files.length > 0) {
            display.textContent = 'الملف المختار: ' + e.target.files[0].name;
            display.className = 'font-monospace text-primary fw-bold fs-6';
        } else {
            display.textContent = 'لم يتم اختيار ملف بعد';
            display.className = 'font-monospace text-muted fw-bold fs-6';
        }
    });

    document.getElementById('uploadForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري الرفع...';
        btn.style.pointerEvents = 'none';
    });
</script>