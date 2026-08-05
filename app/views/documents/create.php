<?php
// المسار: app/views/documents/create.php
?>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; max-width:800px; margin:0 auto;">
    
    <div style="padding:24px 30px; border-bottom:1px solid var(--border); background:#f8fafc;">
        <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-cloud-arrow-up" style="color:var(--primary);"></i> أرشفة وثيقة جديدة
        </h3>
        <p style="margin:4px 0 0; font-size:12px; color:var(--text-muted);">الامتدادات المسموحة: PDF, DOC, DOCX, XLS, XLSX, PNG, JPG, ZIP, RAR. (الحد الأقصى: 10MB)</p>
    </div>

    <!-- هام جداً إضافة enctype لدعم رفع الملفات -->
    <form action="<?php echo URL_ROOT; ?>/document/create" method="POST" enctype="multipart/form-data" id="uploadForm">
        <div style="padding:30px; display:grid; grid-template-columns:1fr; gap:20px;">
            
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">عنوان / وصف الوثيقة <span style="color:var(--danger);">*</span></label>
                <input type="text" name="title" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; font-size:15px; outline:none;" placeholder="مثال: عقد تأسيس الشركة، الهوية الوطنية، تقرير المبيعات السنوي...">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">الملف المراد رفعه <span style="color:var(--danger);">*</span></label>
                
                <div style="position:relative; width:100%;">
                    <input type="file" name="document_file" id="docFile" required style="position:absolute; width:100%; height:100%; opacity:0; cursor:pointer; z-index:2;">
                    <div style="border: 2px dashed var(--primary); background: var(--primary-light); border-radius: 8px; padding: 30px; text-align: center; color: var(--primary-dark);">
                        <i class="fas fa-file-arrow-up" style="font-size: 32px; margin-bottom: 10px;"></i>
                        <h4 style="margin: 0 0 5px; font-size: 15px;">اضغط هنا لاختيار الملف أو قم بسحبه وإفلاته</h4>
                        <span id="fileNameDisplay" style="font-size: 12px; font-family: monospace; color: var(--text-muted); font-weight: bold;">لم يتم اختيار ملف بعد</span>
                    </div>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">مستوى الوصول (الخصوصية) <span style="color:var(--danger);">*</span></label>
                <div style="display:flex; gap:15px; margin-top:5px;">
                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer; padding: 10px 15px; border: 1px solid var(--border); border-radius: 8px; background: #fff; flex: 1;">
                        <input type="radio" name="access_level" value="private" checked> 
                        <div>
                            <strong><i class="fas fa-lock"></i> خاص (Private)</strong><br>
                            <span style="font-size: 11px; color: var(--text-muted);">يمكن لك وللإدارة فقط رؤية وتنزيل الملف.</span>
                        </div>
                    </label>
                    
                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer; padding: 10px 15px; border: 1px solid var(--border); border-radius: 8px; background: #fff; flex: 1;">
                        <input type="radio" name="access_level" value="public"> 
                        <div>
                            <strong><i class="fas fa-globe"></i> عام (Public)</strong><br>
                            <span style="font-size: 11px; color: var(--text-muted);">يمكن لجميع موظفي النظام الوصول للملف.</span>
                        </div>
                    </label>
                </div>
            </div>

        </div>
        
        <div style="padding:20px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; gap:12px;">
            <button type="submit" id="submitBtn" style="padding:10px 24px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:8px;"><i class="fas fa-cloud-arrow-up"></i> رفع وأرشفة</button>
            <a href="<?php echo URL_ROOT; ?>/document/index" style="padding:10px 24px; background:transparent; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600;">إلغاء</a>
        </div>
    </form>
</div>

<script>
    // تحديث اسم الملف المختار في الواجهة
    const docInput = document.getElementById('docFile');
    const nameDisplay = document.getElementById('fileNameDisplay');
    
    docInput.addEventListener('change', function(e) {
        if(e.target.files.length > 0) {
            nameDisplay.textContent = 'الملف المختار: ' + e.target.files[0].name;
            nameDisplay.style.color = 'var(--primary-dark)';
        } else {
            nameDisplay.textContent = 'لم يتم اختيار ملف بعد';
            nameDisplay.style.color = 'var(--text-muted)';
        }
    });

    // إظهار حالة التحميل عند الضغط
    document.getElementById('uploadForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري الرفع...';
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.8';
    });
</script>