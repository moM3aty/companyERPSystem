<?php
// app/views/settings/index.php
$pageTitle = $data['title'] ?? 'إعدادات النظام';
$settings = $data['settings'] ?? [];
$user = $data['user'] ?? null;
$sysInfo = $data['system_info'] ?? [];
$sysStats = $data['system_stats'] ?? [];
?>

<style>
    .settings-container {
        font-family: 'Cairo', sans-serif;
        color: #334155;
    }
    
    .settings-header {
        margin-bottom: 24px;
    }
    .settings-header h3 {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
    }
    .settings-header p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 14px;
    }

    /* تصميم التبويبات (Tabs) */
    .settings-tabs { 
        display: flex; 
        gap: 8px; 
        background: #ffffff; 
        border-radius: 12px; 
        border: 1px solid #e2e8f0; 
        padding: 8px; 
        margin-bottom: 24px; 
        overflow-x: auto; 
        box-shadow: 0 1px 3px rgba(0,0,0,0.05); 
    }
    .tab-btn { 
        display: inline-flex; 
        align-items: center; 
        gap: 8px; 
        padding: 12px 24px; 
        border-radius: 8px; 
        border: none; 
        background: transparent; 
        font-family: inherit; 
        font-size: 14px; 
        font-weight: 600; 
        color: #64748b; 
        cursor: pointer; 
        transition: all 0.2s ease; 
        white-space: nowrap; 
    }
    .tab-btn:hover { 
        color: #0f172a; 
        background: #f8fafc; 
    }
    .tab-btn.active { 
        background: #0ea5e9; 
        color: #ffffff; 
        box-shadow: 0 4px 10px rgba(14, 165, 233, 0.2); 
    }
    
    .tab-panel { 
        display: none; 
        animation: fadeIn 0.4s ease both; 
    }
    .tab-panel.active { 
        display: block; 
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .settings-card { 
        background: #ffffff; 
        border-radius: 12px; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
        overflow: hidden; 
        margin-bottom: 24px; 
    }
    .settings-card-header { 
        padding: 24px 32px; 
        border-bottom: 1px solid #e2e8f0; 
        display: flex; 
        align-items: center; 
        gap: 16px; 
        background: #f8fafc; 
    }
    .sch-icon { 
        width: 48px; 
        height: 48px; 
        border-radius: 12px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 20px; 
        flex-shrink: 0; 
    }
    .settings-card-header h3 { 
        font-size: 18px; 
        font-weight: 700; 
        color: #0f172a; 
        margin: 0 0 4px 0; 
    }
    .settings-card-header p { 
        font-size: 13px; 
        color: #64748b; 
        margin: 0; 
    }
    .settings-card-body { 
        padding: 32px; 
    }
    .settings-grid { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 24px; 
    }
    
    .s-group { 
        display: flex; 
        flex-direction: column; 
    }
    .s-group.full { 
        grid-column: 1 / -1; 
    }
    .s-label { 
        font-size: 14px; 
        font-weight: 600; 
        color: #334155; 
        margin-bottom: 8px; 
        display: flex; 
        align-items: center; 
        gap: 6px; 
    }
    .s-input { 
        padding: 12px 16px; 
        border: 1.5px solid #cbd5e1; 
        border-radius: 8px; 
        font-family: inherit; 
        font-size: 15px; 
        background: #ffffff; 
        color: #0f172a; 
        outline: none; 
        transition: all 0.2s ease; 
        width: 100%;
        box-sizing: border-box;
    }
    .s-input:focus { 
        border-color: #0ea5e9; 
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15); 
    }
    .s-input:disabled {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }
    select.s-input {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: left 12px center;
        background-size: 16px;
        padding-left: 40px; /* Adjust padding for icon */
    }

    .s-actions { 
        padding: 20px 32px; 
        background: #f8fafc; 
        border-top: 1px solid #e2e8f0; 
        display: flex; 
        justify-content: flex-end; 
    }
    .btn-save {
        background-color: #0ea5e9;
        color: #ffffff;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-family: inherit;
        font-size: 15px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s ease;
    }
    .btn-save:hover {
        background-color: #0284c7;
    }
    .btn-danger-action {
        background-color: #ef4444;
        color: #ffffff;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-family: inherit;
        font-size: 15px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s ease;
    }
    .btn-danger-action:hover {
        background-color: #dc2626;
    }

    .profile-header { 
        display: flex; 
        align-items: center; 
        gap: 24px; 
        padding: 32px; 
        border-bottom: 1px solid #e2e8f0; 
        background: linear-gradient(to right, #f8fafc, #ffffff); 
    }
    .profile-avatar { 
        width: 100px; 
        height: 100px; 
        border-radius: 20px; 
        background: linear-gradient(135deg, #0ea5e9, #8b5cf6); 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 36px; 
        font-weight: 800; 
        color: #ffffff; 
        box-shadow: 0 10px 25px rgba(14, 165, 233, 0.3); 
    }
    
    .sys-info-grid { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 16px; 
    }
    .sys-info-item { 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        padding: 16px 20px; 
        background: #f8fafc; 
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }
    
    .sys-stats-grid { 
        display: grid; 
        grid-template-columns: repeat(4, 1fr); 
        gap: 16px; 
        margin-top: 32px; 
    }
    .sys-stat-card { 
        background: #ffffff; 
        border: 1px solid #e2e8f0; 
        border-radius: 12px; 
        padding: 24px 20px; 
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .sys-stat-card i {
        font-size: 28px;
        margin-bottom: 12px;
        color: #0ea5e9;
    }
    .sys-stat-card .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }
    
    /* Toggle Switch (2FA) */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 24px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider { background-color: #10b981; }
    input:checked + .slider:before { transform: translateX(26px); }
    .toggle-label-wrapper { display: flex; align-items: center; gap: 12px; }

    @media (max-width: 768px) { 
        .settings-grid, .sys-info-grid { grid-template-columns: 1fr; } 
        .sys-stats-grid { grid-template-columns: 1fr 1fr; } 
        .profile-header { flex-direction: column; text-align: center; }
    }
</style>

<div class="settings-container">
    <div class="settings-header d-flex justify-content-between align-items-center">
        <div>
            <h3><i class="fas fa-gear text-primary me-2"></i> <?php echo $pageTitle; ?></h3>
            <p>تخصيص خيارات النظام، بيانات المؤسسة، وإعدادات الأمان.</p>
        </div>
    </div>

    <!-- شريط التبويبات -->
    <div class="settings-tabs">
        <button class="tab-btn active" data-tab="company"><i class="fas fa-building"></i> بيانات المؤسسة</button>
        <button class="tab-btn" data-tab="profile"><i class="fas fa-user-gear"></i> الملف الشخصي</button>
        <button class="tab-btn" data-tab="security"><i class="fas fa-shield-halved"></i> الأمان والمرور</button>
        <button class="tab-btn" data-tab="system"><i class="fas fa-server"></i> حالة النظام</button>
    </div>

    <!-- تبويب 1: إعدادات الشركة -->
    <div class="tab-panel active" id="panel-company">
        <form action="<?php echo URLROOT; ?>/settings/index" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="form_action" value="save_company">
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="sch-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fas fa-building"></i></div>
                    <div>
                        <h3>بيانات المؤسسة والشعار</h3>
                        <p>تُستخدم هذه البيانات والشعار في طباعة الفواتير والتقارير الرسمية.</p>
                    </div>
                </div>
                <div class="settings-card-body">
                    <div class="settings-grid">
                        <div class="s-group full border-bottom pb-4 mb-2">
                            <label class="s-label">شعار الشركة (Logo)</label>
                            <div class="d-flex align-items-center gap-3">
                                <?php if(!empty($settings['company_logo'])): ?>
                                    <img src="<?php echo URLROOT . $settings['company_logo']; ?>" alt="Logo" style="width:60px; height:60px; border-radius:8px; object-fit:contain; border:1px solid var(--border-color);">
                                <?php else: ?>
                                    <div style="width:60px; height:60px; border-radius:8px; background:var(--page-bg); display:flex; align-items:center; justify-content:center; color:var(--text-muted);"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                                <input type="file" name="company_logo" class="s-input" accept="image/*" style="flex:1;">
                            </div>
                        </div>

                        <div class="s-group">
                            <label class="s-label">الاسم التجاري <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="company_name" class="s-input" value="<?php echo htmlspecialchars($settings['company_name'] ?? ''); ?>" required placeholder="اسم الشركة الرسمي">
                        </div>
                        <div class="s-group">
                            <label class="s-label">البريد الإلكتروني الرسمي</label>
                            <input type="email" name="company_email" class="s-input" value="<?php echo htmlspecialchars($settings['company_email'] ?? ''); ?>" style="direction:ltr;text-align:right;" placeholder="info@company.com">
                        </div>
                        <div class="s-group">
                            <label class="s-label">رقم هاتف التواصل</label>
                            <input type="text" name="company_phone" class="s-input" value="<?php echo htmlspecialchars($settings['company_phone'] ?? ''); ?>" style="direction:ltr;text-align:right;" placeholder="+966 50 000 0000">
                        </div>
                        
                        <!-- الحقل الجديد للرقم الضريبي -->
                        <div class="s-group full border-top pt-4 mt-2">
                            <label class="s-label"><i class="fas fa-file-invoice text-success me-1"></i> الرقم الضريبي (VAT Number) لمتطلبات الفاتورة الإلكترونية</label>
                            <input type="text" name="vat_number" class="s-input font-monospace fw-bold" value="<?php echo htmlspecialchars($settings['vat_number'] ?? ''); ?>" style="direction:ltr;text-align:right;" placeholder="أدخل الرقم الضريبي المكون من 15 خانة">
                            <div style="font-size:12px; color:#64748b; margin-top:6px;"><i class="fas fa-info-circle"></i> هذا الرقم سيتم استخدامه في توليد رمز الـ QR Code الخاص بالفواتير المعتمدة.</div>
                        </div>

                        <div class="s-group">
                            <label class="s-label">العملة الافتراضية <span style="color:#ef4444;">*</span></label>
                            <select name="currency" class="s-input">
                                <?php $curr = $settings['currency'] ?? 'ر.س'; ?>
                                <option value="ر.س" <?php echo $curr === 'ر.س' ? 'selected' : ''; ?>>ريال سعودي (ر.س)</option>
                                <option value="ج.م" <?php echo $curr === 'ج.م' ? 'selected' : ''; ?>>جنيه مصري (ج.م)</option>
                                <option value="د.إ" <?php echo $curr === 'د.إ' ? 'selected' : ''; ?>>درهم إماراتي (د.إ)</option>
                                <option value="$" <?php echo $curr === '$' ? 'selected' : ''; ?>>دولار أمريكي ($)</option>
                            </select>
                        </div>
                        <div class="s-group full border-top pt-4 mt-2" style="border-top: 1px dashed #cbd5e1;">
                            <label class="s-label">نسبة ضريبة القيمة المضافة (VAT %) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="tax_rate" class="s-input font-monospace fw-bold" value="<?php echo htmlspecialchars($settings['tax_rate'] ?? '15'); ?>" min="0" max="100" step="0.1" style="direction:ltr;text-align:right; font-size: 18px; color: #0ea5e9;" required>
                            <div style="font-size:12px; color:#64748b; margin-top:6px;"><i class="fas fa-info-circle"></i> يتم تطبيق هذه النسبة بشكل افتراضي عند إنشاء فواتير المبيعات وعروض الأسعار.</div>
                        </div>
                    </div>
                </div>
                <div class="s-actions">
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> حفظ إعدادات المؤسسة</button>
                </div>
            </div>
        </form>
    </div>

    <!-- تبويب 2: الملف الشخصي -->
    <div class="tab-panel" id="panel-profile">
        <form action="<?php echo URLROOT; ?>/settings/index" method="POST">
            <input type="hidden" name="form_action" value="save_profile">
            <div class="settings-card">
                <div class="profile-header">
                    <div class="profile-avatar"><?php echo mb_substr($user->name ?? 'م', 0, 2); ?></div>
                    <div>
                        <h3 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;"><?php echo htmlspecialchars($user->name ?? ''); ?></h3>
                        <p style="color: #64748b; margin: 0 0 12px 0;"><i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($user->email ?? ''); ?></p>
                        <span style="background: #e0f2fe; color: #0284c7; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;"><i class="fas fa-shield-halved me-1"></i> <?php echo htmlspecialchars($user->role ?? 'admin'); ?></span>
                    </div>
                </div>
                <div class="settings-card-body">
                    <div class="settings-grid">
                        <div class="s-group">
                            <label class="s-label">الاسم الكامل <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="profile_name" class="s-input" value="<?php echo htmlspecialchars($user->name ?? ''); ?>" required>
                        </div>
                        <div class="s-group">
                            <label class="s-label">البريد الإلكتروني (يستخدم لتسجيل الدخول) <span style="color:#ef4444;">*</span></label>
                            <input type="email" name="profile_email" class="s-input" value="<?php echo htmlspecialchars($user->email ?? ''); ?>" required style="direction:ltr;text-align:right;">
                        </div>
                        <div class="s-group">
                            <label class="s-label">رقم الجوال الشخصي</label>
                            <input type="text" name="profile_phone" class="s-input" value="<?php echo htmlspecialchars($user->phone ?? ''); ?>" style="direction:ltr;text-align:right;">
                        </div>
                        <div class="s-group">
                            <label class="s-label">مستوى الصلاحية (مُقفل)</label>
                            <input type="text" class="s-input" value="<?php echo htmlspecialchars($user->role ?? ''); ?>" disabled>
                        </div>
                    </div>
                </div>
                <div class="s-actions">
                    <button type="submit" class="btn-save" style="background-color: #10b981;"><i class="fas fa-user-check"></i> تحديث بياناتي</button>
                </div>
            </div>
        </form>
    </div>

    <!-- تبويب 3: الأمان وكلمة المرور -->
    <div class="tab-panel" id="panel-security">
        <form action="<?php echo URLROOT; ?>/settings/index" method="POST" id="passwordForm">
            <input type="hidden" name="form_action" value="change_password">
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="sch-icon" style="background:#fee2e2; color:#ef4444;"><i class="fas fa-key"></i></div>
                    <div>
                        <h3>إعدادات الأمان وتغيير كلمة المرور</h3>
                        <p>يُنصح بتغيير كلمة المرور بشكل دوري لضمان حماية حسابك.</p>
                    </div>
                </div>
                <div class="settings-card-body">
                    <div class="settings-grid">
                        <div class="s-group full mb-2">
                            <label class="s-label">كلمة المرور الحالية <span style="color:#ef4444;">*</span></label>
                            <input type="password" name="current_password" class="s-input" required placeholder="أدخل كلمة المرور الحالية للتحقق">
                        </div>
                        <div class="s-group">
                            <label class="s-label">كلمة المرور الجديدة <span style="color:#ef4444;">*</span></label>
                            <input type="password" name="new_password" class="s-input" id="newPass" required placeholder="6 أحرف أو أرقام على الأقل" oninput="checkMatch()">
                        </div>
                        <div class="s-group">
                            <label class="s-label">تأكيد كلمة المرور الجديدة <span style="color:#ef4444;">*</span></label>
                            <input type="password" name="confirm_password" class="s-input" id="confirmPass" required placeholder="أعد كتابة كلمة المرور" oninput="checkMatch()">
                            <div id="matchLabel" style="font-size:12px; margin-top:6px; font-weight:600;"></div>
                        </div>
                        
                        <!-- إضافة خيار 2FA -->
                        <div class="s-group full mt-4 pt-4" style="border-top: 1px dashed #cbd5e1;">
                            <label class="s-label" style="font-size: 16px;"><i class="fas fa-mobile-screen text-primary me-2"></i> المصادقة الثنائية (2FA) - <span class="badge badge-info ms-2" style="font-size:10px;">اختياري</span></label>
                            <div class="toggle-label-wrapper mt-3 p-4 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <label class="toggle-switch">
                                    <input type="checkbox" name="enable_2fa" value="1" onchange="alert('تنبيه: سيتم تفعيل طبقة الحماية الإضافية. سنرسل رمز تحقق (OTP) إلى بريدك الإلكتروني في كل مرة تحاول فيها تسجيل الدخول.');">
                                    <span class="slider"></span>
                                </label>
                                <div>
                                    <div style="font-weight: 700; color: #0f172a; margin-bottom:4px;">تفعيل التحقق بخطوتين</div>
                                    <div style="font-size:13px; color:#64748b;">يزيد من أمان حسابك بطلب رمز مؤقت (OTP) يرسل لبريدك الإلكتروني عند تسجيل الدخول.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="s-actions">
                    <button type="submit" class="btn-danger-action" id="btnUpdatePass"><i class="fas fa-shield-check"></i> حفظ التغييرات الأمنية</button>
                </div>
            </div>
        </form>
    </div>

    <!-- تبويب 4: معلومات النظام -->
    <div class="tab-panel" id="panel-system">
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="sch-icon" style="background:#e0e7ff; color:#4f46e5;"><i class="fas fa-server"></i></div>
                <div>
                    <h3>معلومات وإحصائيات الخادم (Server Info)</h3>
                    <p>تفاصيل تقنية مفيدة للدعم الفني وتحليل الأداء.</p>
                </div>
            </div>
            <div class="settings-card-body">
                <div class="sys-info-grid">
                    <div class="sys-info-item">
                        <span style="color:#64748b; font-weight:600; font-size:14px;"><i class="fas fa-code-branch me-2"></i> إصدار النظام (ERP)</span>
                        <span class="font-monospace" style="color:#0f172a; font-weight:700;">v<?php echo htmlspecialchars($sysInfo['app_version'] ?? '2.0.0'); ?></span>
                    </div>
                    <div class="sys-info-item">
                        <span style="color:#64748b; font-weight:600; font-size:14px;"><i class="fab fa-php me-2"></i> إصدار خادم PHP</span>
                        <span class="font-monospace" style="color:#0f172a; font-weight:700;"><?php echo htmlspecialchars($sysInfo['php_version'] ?? ''); ?></span>
                    </div>
                    <div class="sys-info-item">
                        <span style="color:#64748b; font-weight:600; font-size:14px;"><i class="fas fa-database me-2"></i> قاعدة البيانات</span>
                        <span class="font-monospace" style="color:#0f172a; font-weight:700;"><?php echo htmlspecialchars($sysInfo['db_name'] ?? ''); ?></span>
                    </div>
                    <div class="sys-info-item">
                        <span style="color:#64748b; font-weight:600; font-size:14px;"><i class="fas fa-clock me-2"></i> المنطقة الزمنية (Timezone)</span>
                        <span class="font-monospace" style="color:#0f172a; font-weight:700;"><?php echo htmlspecialchars($sysInfo['timezone'] ?? ''); ?></span>
                    </div>
                    <div class="sys-info-item">
                        <span style="color:#64748b; font-weight:600; font-size:14px;"><i class="fas fa-upload me-2"></i> حد الرفع (Max Upload)</span>
                        <span class="font-monospace text-primary fw-bold"><?php echo htmlspecialchars($sysInfo['max_upload'] ?? ''); ?></span>
                    </div>
                    <div class="sys-info-item">
                        <span style="color:#64748b; font-weight:600; font-size:14px;"><i class="fas fa-memory me-2"></i> حد الذاكرة (Memory Limit)</span>
                        <span class="font-monospace text-primary fw-bold"><?php echo htmlspecialchars($sysInfo['memory_limit'] ?? ''); ?></span>
                    </div>
                </div>

                <h4 style="margin: 32px 0 16px; font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">إحصائيات سريعة للبيانات</h4>
                
                <div class="sys-stats-grid">
                    <div class="sys-stat-card">
                        <i class="fas fa-users-gear" style="color: #8b5cf6;"></i>
                        <div class="stat-value font-monospace"><?php echo number_format($sysStats['employees'] ?? 0); ?></div>
                        <div style="font-size:12px; color:#64748b; font-weight:600;">موظفين مسجلين</div>
                    </div>
                    <div class="sys-stat-card">
                        <i class="fas fa-boxes-stacked" style="color: #f59e0b;"></i>
                        <div class="stat-value font-monospace"><?php echo number_format($sysStats['products'] ?? 0); ?></div>
                        <div style="font-size:12px; color:#64748b; font-weight:600;">أصناف بالمخزون</div>
                    </div>
                    <div class="sys-stat-card">
                        <i class="fas fa-file-invoice-dollar" style="color: #10b981;"></i>
                        <div class="stat-value font-monospace"><?php echo number_format($sysStats['invoices'] ?? 0); ?></div>
                        <div style="font-size:12px; color:#64748b; font-weight:600;">فواتير مصدرة</div>
                    </div>
                    <div class="sys-stat-card">
                        <i class="fas fa-money-bill-transfer" style="color: #ef4444;"></i>
                        <div class="stat-value font-monospace"><?php echo number_format($sysStats['expenses'] ?? 0); ?></div>
                        <div style="font-size:12px; color:#64748b; font-weight:600;">عمليات صرف</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // نظام التبويبات
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // إزالة الكلاس النشط من جميع الأزرار واللوحات
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            
            // إضافة الكلاس النشط للزر واللوحة المحددة
            this.classList.add('active');
            document.getElementById('panel-' + this.dataset.tab).classList.add('active');
        });
    });

    // التحقق من تطابق كلمات المرور
    function checkMatch() {
        const pass1 = document.getElementById('newPass').value;
        const pass2 = document.getElementById('confirmPass').value;
        const label = document.getElementById('matchLabel');
        const btn = document.getElementById('btnUpdatePass');
        
        if (pass1 === '' && pass2 === '') {
            label.innerHTML = '';
            btn.disabled = false;
            return;
        }

        if (pass1 === pass2) {
            label.innerHTML = '<i class="fas fa-check-circle"></i> الكلمات متطابقة';
            label.style.color = '#10b981';
            btn.disabled = false;
        } else {
            label.innerHTML = '<i class="fas fa-times-circle"></i> الكلمات غير متطابقة!';
            label.style.color = '#ef4444';
            btn.disabled = true;
        }
    }
</script>