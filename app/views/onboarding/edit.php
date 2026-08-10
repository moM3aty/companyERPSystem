<?php
// app/views/onboarding/edit.php
$o = $data['onboard'] ?? null;
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-success-light border-success">
        <h3 class="card-title text-success-dark mb-0"><i class="fas fa-list-check"></i> تحديث مهام الموظف: <?php echo htmlspecialchars($o->employee_name); ?></h3>
    </div>
    <form action="<?php echo URLROOT; ?>/onboarding/edit/<?php echo $o->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="contract_signed" <?php echo $o->contract_signed ? 'checked' : ''; ?>> توقيع العقد</label>
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="id_received" <?php echo $o->id_received ? 'checked' : ''; ?>> استلام الهوية</label>
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="bank_details" <?php echo $o->bank_details ? 'checked' : ''; ?>> بيانات البنك</label>
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="email_created" <?php echo $o->email_created ? 'checked' : ''; ?>> إنشاء الإيميل</label>
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="equipment_issued" <?php echo $o->equipment_issued ? 'checked' : ''; ?>> تسليم اللابتوب/المعدات</label>
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="access_card" <?php echo $o->access_card ? 'checked' : ''; ?>> بطاقة الدخول</label>
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="system_accounts" <?php echo $o->system_accounts ? 'checked' : ''; ?>> حسابات النظام</label>
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="orientation" <?php echo $o->orientation ? 'checked' : ''; ?>> جولة تعريفية</label>
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="safety_training" <?php echo $o->safety_training ? 'checked' : ''; ?>> تدريب السلامة</label>
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="manager_assigned" <?php echo $o->manager_assigned ? 'checked' : ''; ?>> تعيين المدير</label>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-success"><i class="fas fa-save"></i> حفظ التحديثات</button></div>
    </form>
</div>