<?php
// app/views/opportunity/index.php
$groupedOpps = $data['groupedOpps'] ?? [];
$pipelineValue = $data['pipelineValue'] ?? 0;

$columns = [
    'qualification' => ['title' => 'التأهيل', 'icon' => 'fa-filter', 'color' => 'var(--info)'],
    'proposal'      => ['title' => 'تقديم العرض', 'icon' => 'fa-file-signature', 'color' => 'var(--primary)'],
    'negotiation'   => ['title' => 'التفاوض', 'icon' => 'fa-comments-dollar', 'color' => 'var(--accent)'],
    'closed_won'    => ['title' => 'تم الفوز', 'icon' => 'fa-trophy', 'color' => 'var(--success)'],
    'closed_lost'   => ['title' => 'الخسارة', 'icon' => 'fa-xmark-circle', 'color' => 'var(--danger)']
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-bullseye text-primary"></i> مسار الفرص البيعية (Pipeline)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">اسحب وأفلت الفرص لتحديث مراحل المبيعات.</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="bg-white border rounded px-3 py-2 text-dark shadow-sm">
            <span class="text-muted fs-6 fw-bold me-2">قيمة الفرص النشطة:</span>
            <span class="font-monospace fs-5 fw-bold text-success" style="direction:ltr;"><?php echo number_format($pipelineValue, 2); ?> ر.س</span>
        </div>
        <a href="<?php echo URLROOT; ?>/opportunity/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة فرصة
        </a>
    </div>
</div>

<!-- رسالة AJAX -->
<div id="toastNotification" class="alert alert-success" style="display: none; position: fixed; bottom: 20px; left: 20px; z-index: 9999; box-shadow: var(--shadow-md);">
    <i class="fas fa-check-circle"></i> تم تحديث مرحلة الفرصة بنجاح!
</div>

<!-- لوحة الكانبان (Kanban Board) -->
<div class="kanban-board">
    <?php foreach ($columns as $stageKey => $colDef): ?>
        <?php
            // حساب القيمة الإجمالية للفرص في هذا العمود
            $colValue = 0;
            foreach ($groupedOpps[$stageKey] as $opp) {
                $colValue += $opp->estimated_value;
            }
        ?>
        <div class="kanban-col" data-stage="<?php echo $stageKey; ?>" style="border-top: 4px solid <?php echo $colDef['color']; ?>;">
            <div class="k-header">
                <div>
                    <div class="fw-bold text-dark" style="font-size: 15px;">
                        <i class="fas <?php echo $colDef['icon']; ?>" style="color: <?php echo $colDef['color']; ?>; margin-left: 5px;"></i>
                        <?php echo $colDef['title']; ?>
                    </div>
                    <div class="font-monospace fw-bold mt-1" style="font-size: 12px; color: <?php echo $colDef['color']; ?>;" id="val-<?php echo $stageKey; ?>">
                        <?php echo number_format($colValue, 0); ?> ر.س
                    </div>
                </div>
                <span class="badge badge-secondary fs-6" id="count-<?php echo $stageKey; ?>"><?php echo count($groupedOpps[$stageKey]); ?></span>
            </div>
            
            <div class="k-cards" id="cards-<?php echo $stageKey; ?>">
                <?php foreach ($groupedOpps[$stageKey] as $opp): ?>
                    <div class="k-card" draggable="true" data-id="<?php echo $opp->id; ?>" data-value="<?php echo $opp->estimated_value; ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="k-title fw-bold text-dark" style="font-size: 14px;"><a href="<?php echo URLROOT; ?>/opportunity/edit/<?php echo $opp->id; ?>" class="text-dark"><?php echo htmlspecialchars($opp->title); ?></a></div>
                        </div>
                             <div class="d-flex flex-column align-items-end gap-1">
                                <span class="font-monospace fw-bold text-success fs-6 mb-1" style="direction:ltr;"><?php echo number_format($opp->estimated_value, 0); ?></span>
                                <div class="d-flex gap-1">
                                    <a href="<?php echo URLROOT; ?>/opportunity/edit/<?php echo $opp->id; ?>" class="btn-icon view" style="width:24px; height:24px; font-size:11px;" title="تعديل"><i class="fas fa-pen text-primary"></i></a>
                                    
                                    <?php if(Session::hasRole('admin')): ?>
                                    <form action="<?php echo URLROOT; ?>/opportunity/delete/<?php echo $opp->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف هذه الفرصة البيعية نهائياً؟');">
                                        <button type="submit" class="btn-icon delete" style="width:24px; height:24px; font-size:11px; padding:0; border:none; background:transparent;" title="حذف">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <div class="k-company text-muted mb-2" style="font-size: 12px;">
                            <i class="fas fa-building"></i> <?php echo htmlspecialchars($opp->customer_name ?? 'بدون عميل'); ?>
                        </div>
                        
                        <div class="k-meta d-flex justify-content-between align-items-center pt-2" style="border-top: 1px dashed var(--border);">
                            <span class="text-muted" style="font-size: 11px;"><i class="far fa-calendar-check text-info"></i> <?php echo $opp->expected_close_date ? date('M d', strtotime($opp->expected_close_date)) : 'غير محدد'; ?></span>
                            <div class="k-avatar" title="المسؤول: <?php echo htmlspecialchars($opp->assigned_name ?? 'غير معين'); ?>" style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary-light); color: var(--primary-dark); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">
                                <?php echo mb_substr($opp->assigned_name ?? '؟', 0, 2); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
    .kanban-board {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        padding-bottom: 20px;
        min-height: 65vh;
        align-items: flex-start;
    }
    
    .kanban-col {
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: 12px;
        width: 300px;
        min-width: 300px;
        display: flex;
        flex-direction: column;
        max-height: 100%;
    }
    
    .k-header {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        background: #fff;
        border-radius: 12px 12px 0 0;
    }
    
    .k-cards {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
        overflow-y: auto;
        min-height: 150px;
        transition: background 0.2s;
    }
    
    .k-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 16px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        cursor: grab;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .k-card:active {
        cursor: grabbing;
    }
    
    .k-card:hover {
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        transform: translateY(-2px);
        border-color: var(--primary-light);
    }
    
    .k-card.dragging {
        opacity: 0.5;
        transform: scale(0.95);
    }
    
    .drag-over {
        background: #e2e8f0 !important;
        border-radius: 8px;
    }
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
                    updateOpportunityStageAjax(draggingCard.dataset.id, newStage, oldStage, draggingCard.dataset.value);
                    draggingCard.dataset.sourceStage = newStage; 
                }
            }
        });
    });

    function updateOpportunityStageAjax(oppId, newStage, oldStage, valStr) {
        // تحديث العدادات محلياً
        const newCountEl = document.getElementById('count-' + newStage);
        const oldCountEl = document.getElementById('count-' + oldStage);
        newCountEl.textContent = parseInt(newCountEl.textContent) + 1;
        oldCountEl.textContent = parseInt(oldCountEl.textContent) - 1;

        // إرسال طلب السيرفر
        fetch('<?php echo URLROOT; ?>/opportunity/updateStageAjax', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${oppId}&stage=${newStage}`
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
        });
    }
</script>