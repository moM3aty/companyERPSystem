<?php
$flash = $data['flash'] ?? null;
$customers = $data['customers'] ?? [];
$opportunities = $data['opportunities'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="form-card">
        <form action="<?php echo URL_ROOT; ?>/followup/create" method="POST">
            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <label>الموضوع</label>
                        <input type="text" name="subject" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>النوع</label>
                        <select name="type" class="form-input">
                            <option value="call">مكالمة</option>
                            <option value="meeting">اجتماع</option>
                            <option value="email">بريد إلكتروني</option>
                            <option value="task">مهمة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>العميل (اختياري)</label>
                        <select name="customer_id" class="form-input">
                            <option value="">-- بدون عميل --</option>
                            <?php foreach ($customers as $c) : ?>
                                <option value="<?php echo $c->id; ?>"><?php echo $c->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الفرصة (اختياري)</label>
                        <select name="opportunity_id" class="form-input">
                            <option value="">-- بدون فرصة --</option>
                            <?php foreach ($opportunities as $opp) : ?>
                                <option value="<?php echo $opp->id; ?>"><?php echo $opp->title; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>التاريخ والوقت المحدد</label>
                        <input type="datetime-local" name="scheduled_at" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>الوصف</label>
                        <textarea name="description" class="form-input" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> حفظ</button>
                <a href="<?php echo URL_ROOT; ?>/followup/index" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>