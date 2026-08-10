<?php
// app/views/attendance/index.php
$records = $data['records'] ?? [];
$employees = $data['employees'] ?? [];
$selectedDate = $data['date'] ?? date('Y-m-d');
$canEdit = in_array(Session::getUserRole(), ['admin', 'manager', 'super_admin']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-fingerprint text-info"></i> سجل الحضور والانصراف</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">مراقبة تواجد الموظفين وساعات الدوام اليومية.</p>
    </div>
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

<div class="content-grid" style="grid-template-columns: 1fr 2fr;">
    
    <!-- نموذج تسجيل الحضور -->
    <?php if($canEdit): ?>
    <div class="card mb-0">
        <div class="card-header bg-light">
            <h3 class="card-title"><i class="fas fa-user-clock text-primary"></i> تسجيل حركة</h3>
        </div>
        <form action="<?php echo URLROOT; ?>/attendance/log" method="POST">
            <div class="card-body d-flex flex-column gap-3">
                <div class="form-group mb-0">
                    <label class="form-label">الموظف <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- اختر الموظف --</option>
                        <?php foreach($employees as $emp): ?>
                            <option value="<?php echo $emp->id ?? $emp['id']; ?>"><?php echo htmlspecialchars($emp->name ?? $emp['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group mb-0">
                    <label class="form-label">تاريخ الحركة <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($selectedDate); ?>" required>
                </div>
                
                <div class="form-grid mb-0" style="grid-template-columns: 1fr 1fr;">
                    <div>
                        <label class="form-label">حضور</label>
                        <input type="time" name="check_in" class="form-control" value="08:00">
                    </div>
                    <div>
                        <label class="form-label">انصراف</label>
                        <input type="time" name="check_out" class="form-control">
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-control fw-bold">
                        <option value="present">حاضر</option>
                        <option value="absent">غائب</option>
                        <option value="late">متأخر</option>
                        <option value="half_day">نصف يوم</option>
                    </select>
                </div>
                
                <div class="form-group mb-0">
                    <label class="form-label">ملاحظات</label>
                    <input type="text" name="notes" class="form-control" placeholder="سبب التأخير، الخ...">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> حفظ الحركة</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- جدول عرض الحضور -->
    <div class="card mb-0 h-100">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h3 class="card-title"><i class="fas fa-list text-success"></i> حركات يوم: <?php echo htmlspecialchars($selectedDate); ?></h3>
            <form action="<?php echo URLROOT; ?>/attendance/index" method="GET" class="d-flex gap-2">
                <input type="date" name="date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($selectedDate); ?>" onchange="this.form.submit()">
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-white">
                        <tr>
                            <th>الموظف</th>
                            <th class="text-center">الدخول</th>
                            <th class="text-center">الخروج</th>
                            <th class="text-center">الحالة</th>
                            <?php if($canEdit): ?><th class="text-center">حذف</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($records as $rec): 
                            $statusClass = match($rec->status) {
                                'present' => 'badge-success', 'absent' => 'badge-danger', 'late' => 'badge-warning', 'half_day' => 'badge-info', default => 'badge-secondary'
                            };
                            $statusLabel = match($rec->status) {
                                'present' => 'حاضر', 'absent' => 'غائب', 'late' => 'متأخر', 'half_day' => 'نصف يوم', default => $rec->status
                            };
                        ?>
                        <tr>
                            <td class="fw-bold text-dark"><i class="fas fa-user text-muted me-1"></i> <?php echo htmlspecialchars($rec->employee_name ?? 'مجهول'); ?></td>
                            <td class="text-center font-monospace fw-bold text-primary" style="direction:ltr;"><?php echo $rec->check_in ? date('H:i', strtotime($rec->check_in)) : '—'; ?></td>
                            <td class="text-center font-monospace fw-bold text-danger" style="direction:ltr;"><?php echo $rec->check_out ? date('H:i', strtotime($rec->check_out)) : '—'; ?></td>
                            <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                            <?php if($canEdit): ?>
                            <td class="text-center">
                                <form action="<?php echo URLROOT; ?>/attendance/delete/<?php echo $rec->id; ?>" method="POST" onsubmit="return confirm('تأكيد حذف السجل؟');">
                                    <button type="submit" class="btn-icon delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($records)): ?>
                        <tr><td colspan="5" class="text-center text-muted p-5"><i class="fas fa-bed fs-1 mb-3 opacity-50 d-block"></i> لا توجد حركات مسجلة لهذا اليوم.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>