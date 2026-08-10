<?php
// app/views/employee/edit.php
$employee = $data['employee'] ?? null;
?>

<div class="card" style="max-width: 1000px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-pen text-accent"></i> تعديل بيانات الموظف (Edit Employee)</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/employee/edit/<?php echo $employee->id; ?>" method="POST">
        <div class="card-body">
            
            <!-- 1. Basic Information -->
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-4 text-primary"><i class="fas fa-address-card"></i> المعلومات الأساسية (Basic Information)</h5>
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">الرقم الوظيفي</label>
                    <input type="text" class="form-control font-monospace bg-light" value="<?php echo htmlspecialchars($employee->employee_number); ?>" readonly disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم الكامل (Full Name) <span class="required">*</span></label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($employee->full_name); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم بالعربية (Name in Arabic)</label>
                    <input type="text" name="name_ar" class="form-control" value="<?php echo htmlspecialchars($employee->name_ar ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم بالإنجليزية (Name in English)</label>
                    <input type="text" name="name_en" class="form-control" style="direction:ltr; text-align:left;" value="<?php echo htmlspecialchars($employee->name_en ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">الجنس (Gender)</label>
                    <select name="gender" class="form-control">
                        <option value="Male" <?php echo $employee->gender == 'Male' ? 'selected' : ''; ?>>ذكر (Male)</option>
                        <option value="Female" <?php echo $employee->gender == 'Female' ? 'selected' : ''; ?>>أنثى (Female)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الميلاد (Date of Birth)</label>
                    <input type="date" name="dob" class="form-control" value="<?php echo $employee->dob ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">الجنسية (Nationality)</label>
                    <input type="text" name="nationality" class="form-control" value="<?php echo htmlspecialchars($employee->nationality ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">الحالة الاجتماعية (Marital Status)</label>
                    <select name="marital_status" class="form-control">
                        <option value="Single" <?php echo $employee->marital_status == 'Single' ? 'selected' : ''; ?>>أعزب (Single)</option>
                        <option value="Married" <?php echo $employee->marital_status == 'Married' ? 'selected' : ''; ?>>متزوج (Married)</option>
                        <option value="Divorced" <?php echo $employee->marital_status == 'Divorced' ? 'selected' : ''; ?>>مطلق (Divorced)</option>
                        <option value="Widowed" <?php echo $employee->marital_status == 'Widowed' ? 'selected' : ''; ?>>أرمل (Widowed)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">فصيلة الدم (Blood Group)</label>
                    <input type="text" name="blood_group" class="form-control font-monospace" value="<?php echo htmlspecialchars($employee->blood_group ?? ''); ?>">
                </div>
            </div>

            <!-- Contact Information -->
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-4 text-info"><i class="fas fa-phone"></i> بيانات التواصل والطوارئ (Contact Info)</h5>
            <div class="form-grid mb-4 bg-light p-3 rounded border">
                <div class="form-group mb-0">
                    <label class="form-label">الجوال الشخصي (Personal Mobile)</label>
                    <input type="text" name="personal_mobile" class="form-control font-monospace" style="direction:ltr;" value="<?php echo htmlspecialchars($employee->personal_mobile ?? ''); ?>">
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">البريد الإلكتروني (Personal Email)</label>
                    <input type="email" name="personal_email" class="form-control font-monospace" style="direction:ltr;" value="<?php echo htmlspecialchars($employee->personal_email ?? ''); ?>">
                </div>
                <div class="form-group mb-0 full-width mt-2 border-top pt-3">
                    <strong class="text-danger d-block mb-2"><i class="fas fa-truck-medical"></i> جهة اتصال للطوارئ (Emergency Contact):</strong>
                    <div class="d-flex gap-2">
                        <input type="text" name="emergency_contact" class="form-control" placeholder="اسم جهة الطوارئ" value="<?php echo htmlspecialchars($employee->emergency_contact ?? ''); ?>">
                        <input type="text" name="emergency_relation" class="form-control" placeholder="صلة القرابة" value="<?php echo htmlspecialchars($employee->emergency_relation ?? ''); ?>">
                        <input type="text" name="emergency_phone" class="form-control font-monospace" placeholder="رقم الجوال" style="direction:ltr;" value="<?php echo htmlspecialchars($employee->emergency_phone ?? ''); ?>">
                    </div>
                </div>
            </div>

            <!-- 2. Employment Information -->
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-4 text-success"><i class="fas fa-briefcase"></i> البيانات الوظيفية (Employment Info)</h5>
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">المسمى الوظيفي العام (Job Title)</label>
                    <input type="text" name="job_title" class="form-control" value="<?php echo htmlspecialchars($employee->job_title ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">المنصب (Position)</label>
                    <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($employee->position ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">فئة الموظف (Employee Category)</label>
                    <input type="text" name="employee_category" class="form-control" value="<?php echo htmlspecialchars($employee->employee_category ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">نوع الدوام (Employment Type)</label>
                    <select name="employment_type" class="form-control fw-bold">
                        <option value="Full-time" <?php echo $employee->employment_type == 'Full-time' ? 'selected' : ''; ?>>دوام كامل</option>
                        <option value="Part-time" <?php echo $employee->employment_type == 'Part-time' ? 'selected' : ''; ?>>دوام جزئي</option>
                        <option value="Temporary" <?php echo $employee->employment_type == 'Temporary' ? 'selected' : ''; ?>>مؤقت</option>
                        <option value="Contract" <?php echo $employee->employment_type == 'Contract' ? 'selected' : ''; ?>>عقد</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الالتحاق (Date of Joining)</label>
                    <input type="date" name="date_of_joining" class="form-control" value="<?php echo $employee->date_of_joining ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">بداية فترة التجربة (Probation Start)</label>
                    <input type="date" name="probation_start_date" class="form-control" value="<?php echo $employee->probation_start_date ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label text-warning">نهاية فترة التجربة (Probation End)</label>
                    <input type="date" name="probation_end_date" class="form-control" value="<?php echo $employee->probation_end_date ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">حالة الموظف (Employment Status)</label>
                    <select name="employment_status" class="form-control fw-bold text-primary">
                        <option value="Active" <?php echo $employee->employment_status == 'Active' ? 'selected' : ''; ?>>نشط (Active)</option>
                        <option value="Exit Process" <?php echo $employee->employment_status == 'Exit Process' ? 'selected' : ''; ?>>في فترة إخلاء الطرف</option>
                        <option value="Terminated" <?php echo $employee->employment_status == 'Terminated' ? 'selected' : ''; ?>>منهي خدماته (Terminated)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">موقع العمل (Work Location)</label>
                    <input type="text" name="work_location" class="form-control" value="<?php echo htmlspecialchars($employee->work_location ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">مركز التكلفة (Cost Center)</label>
                    <input type="text" name="cost_center" class="form-control" value="<?php echo htmlspecialchars($employee->cost_center ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">المشروع/التكليف (Project)</label>
                    <input type="text" name="project_assignment" class="form-control" value="<?php echo htmlspecialchars($employee->project_assignment ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">درجة الموظف (Employee Grade)</label>
                    <input type="text" name="employee_grade" class="form-control font-monospace" value="<?php echo htmlspecialchars($employee->employee_grade ?? ''); ?>">
                </div>
            </div>

            <!-- 3. Bank & Payroll Information -->
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-4 text-warning"><i class="fas fa-university"></i> البيانات المالية والبنكية (Bank & Payroll)</h5>
            <div class="form-grid mb-4 bg-light p-3 rounded border">
                <div class="form-group">
                    <label class="form-label">اسم البنك (Bank Name)</label>
                    <input type="text" name="bank_name" class="form-control" value="<?php echo htmlspecialchars($employee->bank_name ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">اسم صاحب الحساب (Account Holder)</label>
                    <input type="text" name="account_holder" class="form-control" value="<?php echo htmlspecialchars($employee->account_holder ?? ''); ?>">
                </div>
                <div class="form-group full-width">
                    <label class="form-label">رقم الآيبان (IBAN)</label>
                    <input type="text" name="iban" class="form-control font-monospace fw-bold" style="direction:ltr;" value="<?php echo htmlspecialchars($employee->iban ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">طريقة دفع الراتب (Payment Method)</label>
                    <select name="salary_payment_method" class="form-control">
                        <option value="Bank Transfer" <?php echo $employee->salary_payment_method == 'Bank Transfer' ? 'selected' : ''; ?>>تحويل بنكي</option>
                        <option value="Cash" <?php echo $employee->salary_payment_method == 'Cash' ? 'selected' : ''; ?>>نقدي</option>
                        <option value="Cheque" <?php echo $employee->salary_payment_method == 'Cheque' ? 'selected' : ''; ?>>شيك</option>
                    </select>
                </div>
                
                <div class="form-group full-width mt-3 border-top pt-3 border-secondary">
                    <strong class="text-success d-block mb-3"><i class="fas fa-money-bill-wave"></i> تفاصيل الراتب والبدلات (Salary & Allowances):</strong>
                    <div class="d-flex gap-2">
                        <div class="flex-1">
                            <label class="form-label text-muted" style="font-size: 11px;">الراتب الأساسي</label>
                            <input type="number" step="0.01" name="basic_salary" class="form-control font-monospace text-success fw-bold fs-5" value="<?php echo $employee->basic_salary; ?>" style="direction:ltr;">
                        </div>
                        <div class="flex-1">
                            <label class="form-label text-muted" style="font-size: 11px;">بدل السكن</label>
                            <input type="number" step="0.01" name="housing_allowance" class="form-control font-monospace text-primary fw-bold fs-5" value="<?php echo $employee->housing_allowance; ?>" style="direction:ltr;">
                        </div>
                        <div class="flex-1">
                            <label class="form-label text-muted" style="font-size: 11px;">بدل المواصلات</label>
                            <input type="number" step="0.01" name="transport_allowance" class="form-control font-monospace text-info fw-bold fs-5" value="<?php echo $employee->transport_allowance; ?>" style="direction:ltr;">
                        </div>
                        <div class="flex-1">
                            <label class="form-label text-muted" style="font-size: 11px;">بدلات أخرى</label>
                            <input type="number" step="0.01" name="other_allowances" class="form-control font-monospace fw-bold fs-5" value="<?php echo $employee->other_allowances; ?>" style="direction:ltr;">
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/employee/show/<?php echo $employee->id; ?>" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>