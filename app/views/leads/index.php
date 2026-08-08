<?php
// app/views/leads/index.php
$groupedLeads = $data['groupedLeads'] ?? [];
$totalLeads = $data['totalLeads'] ?? 0;

$columns = [
    'new' => ['title' => 'عملاء جدد', 'icon' => 'fa-inbox', 'color' => 'var(--info)'],
    'contacted' => ['title' => 'تم التواصل', 'icon' => 'fa-phone-volume', 'color' => 'var(--primary)'],
    'qualified' => ['title' => 'مؤهل (مهتم)', 'icon' => 'fa-star', 'color' => 'var(--success)'],
    'lost' => ['title' => 'مفقود / غير مهتم', 'icon' => 'fa-times-circle', 'color' => 'var(--danger)']
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-users text-primary"></i> العملاء المحتملين (Leads Pipeline)</h3>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="bg-white border rounded px-3 py-2 text-dark shadow-sm">
            <span class="text-muted fs-6 fw-bold me-2"><i class="fas fa-users"></i> إجمالي العملاء:</span>
            <span class="font-monospace fs-5 fw-bold text-info" style="direction:ltr;"><?php echo $totalLeads; ?></span>
        </div>
        <a href="<?php echo URLROOT; ?>/lead/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة عميل
        </a>
    </div>
</div>

<!-- رسالة AJAX -->
<div id="toastNotification" class="alert alert-success" style="display: none; position: fixed; bottom: 20px; left: 20px; z-index: 9999; box-shadow: var(--shadow-md);">
    <i class="fas fa-check-circle"></i> تم تحديث حالة العميل بنجاح!
</div>

<!-- إشعارات Flash العادية -->
<?php 
    $flash = Session::getFlash();
    if ($flash) : 
?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>


<!-- لوحة الكانبان (Kanban Board) -->
<div class="kanban-board">
    <?php foreach ($columns as $stageKey => $colDef): ?>
        <div class="kanban-col" data-stage="<?php echo $stageKey; ?>" style="border-top: 4px solid <?php echo $colDef['color']; ?>;">
            <div class="k-header">
                <div>
                    <div class="fw-bold text-dark" style="font-size: 15px;">
                        <i class="fas <?php echo $colDef['icon']; ?>" style="color: <?php echo $colDef['color']; ?>; margin-left: 5px;"></i>
                        <?php echo $colDef['title']; ?>
                    </div>
                </div>
                <span class="badge badge-secondary fs-6" style="background: <?php echo $colDef['color']; ?>22; color: <?php echo $colDef['color']; ?>;" id="count-<?php echo $stageKey; ?>"><?php echo count($groupedLeads[$stageKey] ?? []); ?></span>
            </div>
            
            <div class="k-cards" id="cards-<?php echo $stageKey; ?>">
                <?php if(!empty($groupedLeads[$stageKey])): foreach ($groupedLeads[$stageKey] as $lead): ?>
                    <div class="k-card" draggable="true" data-id="<?php echo $lead->id; ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="k-title fw-bold text-dark" style="font-size: 14px;">
                                <a href="<?php echo URLROOT; ?>/lead/edit/<?php echo $lead->id; ?>" class="text-dark text-decoration-none hover-primary"><?php echo htmlspecialchars($lead->name); ?></a>
                            </div>
                            
                            <!-- مجموعة الأزرار للإجراءات (تعديل وحذف) -->
                            <div class="d-flex gap-1">
                                <a href="<?php echo URLROOT; ?>/lead/edit/<?php echo $lead->id; ?>" class="btn-icon view" style="width:24px; height:24px; font-size:11px;" title="تعديل"><i class="fas fa-pen text-primary"></i></a>
                                
                                <!-- زر الحذف المضاف حديثاً -->
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/lead/delete/<?php echo $lead->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف العميل المحتمل؟\n(لن يمكن الحذف إذا كانت هناك متابعات مرتبطة به)');">
                                    <button type="submit" class="btn-icon delete" style="width:24px; height:24px; font-size:11px; padding:0; border:none; background:transparent;" title="حذف العميل">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="k-company text-muted mb-2" style="font-size: 12px;">
                            <i class="fas fa-building"></i> <?php echo htmlspecialchars($lead->company ?? 'لا توجد شركة'); ?>
                        </div>
                        
                        <div class="k-company text-muted mb-2 font-monospace" style="font-size: 12px; direction: ltr; text-align: right;">
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($lead->phone ?? '—'); ?>
                        </div>
                        
                        <div class="k-meta d-flex justify-content-between align-items-center pt-2" style="border-top: 1px dashed var(--border-color);">
                            <span class="text-muted font-monospace" style="font-size: 11px;"><i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($lead->source ?? 'organic'); ?></span>
                            <div class="k-avatar" title="المسؤول: <?php echo htmlspecialchars($lead->assigned_name ?? 'غير معين'); ?>" style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary-light); color: var(--primary-dark); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">
                                <?php echo mb_substr($lead->assigned_name ?? '؟', 0, 2); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
    .hover-primary:hover { color: var(--primary) !important; text-decoration: underline !important; }
</style>

<script>
    const cards = document.querySelectorAll('.k-card');
    const columns = document.querySelectorAll('.kanban-col');

    cards.forEach(card => {
        card.addEventListener('dragstart', () => {
            card.classList.add('dragging');
            card.dataset.sourceStage = card.closest('.kanban-col').dataset.stage;
        });

        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
        });
    });

    columns.forEach(col => {
        const cardsContainer = col.querySelector('.k-cards');
        
        col.addEventListener('dragover', e => {
            e.preventDefault(); 
            cardsContainer.classList.add('drag-over');
        });

        col.addEventListener('dragleave', e => {
            cardsContainer.classList.remove('drag-over');
        });

        col.addEventListener('drop', e => {
            e.preventDefault();
            cardsContainer.classList.remove('drag-over');
            
            const draggingCard = document.querySelector('.dragging');
            if(draggingCard) {
                cardsContainer.appendChild(draggingCard);
                
                const newStage = col.dataset.stage;
                const oldStage = draggingCard.dataset.sourceStage;
                
                if(newStage !== oldStage) {
                    updateLeadStatusAjax(draggingCard.dataset.id, newStage, oldStage);
                    draggingCard.dataset.sourceStage = newStage; 
                }
            }
        });
    });

    function updateLeadStatusAjax(leadId, newStage, oldStage) {
        // تحديث العدادات محلياً
        const newCountEl = document.getElementById('count-' + newStage);
        const oldCountEl = document.getElementById('count-' + oldStage);
        newCountEl.textContent = parseInt(newCountEl.textContent) + 1;
        oldCountEl.textContent = parseInt(oldCountEl.textContent) - 1;

        // إرسال طلب السيرفر (لاحظ تغيير الرابط من opportunity إلى lead)
        fetch('<?php echo URLROOT; ?>/lead/updateStatusAjax', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${leadId}&status=${newStage}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const toast = document.getElementById('toastNotification');
                toast.style.display = 'block';
                setTimeout(() => { toast.style.display = 'none'; }, 3000);
            } else {
                alert('حدث خطأ أثناء التحديث.');
                location.reload(); 
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('انقطع الاتصال.');
            location.reload();
        });
    }
</script>