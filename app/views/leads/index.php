<?php
// app/views/leads/index.php
$groupedLeads = $data['groupedLeads'] ?? ['new'=>[], 'contacted'=>[], 'qualified'=>[], 'lost'=>[]];
$totalLeads = $data['totalLeads'] ?? 0;

$columns = [
    'new' => ['title' => 'عملاء جدد', 'icon' => 'fa-inbox', 'color' => 'var(--info)'],
    'contacted' => ['title' => 'تم التواصل', 'icon' => 'fa-phone-volume', 'color' => 'var(--primary)'],
    'qualified' => ['title' => 'مؤهل (مهتم)', 'icon' => 'fa-star', 'color' => 'var(--success)'],
    'lost' => ['title' => 'مفقود / غير مهتم', 'icon' => 'fa-xmark-circle', 'color' => 'var(--danger)']
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-filter text-primary"></i> مسار العملاء المحتملين (Pipeline)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">قم بسحب وإفلات بطاقة العميل لتحديث حالته ومساره البيعي.</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="badge badge-secondary fs-6"><i class="fas fa-users"></i> إجمالي العملاء: <?php echo $totalLeads; ?></span>
        <a href="<?php echo URLROOT; ?>/lead/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة عميل
        </a>
    </div>
</div>

<!-- حاوية التنبيهات المنبثقة للـ AJAX -->
<div id="toastNotification" class="alert alert-success" style="display: none; position: fixed; bottom: 20px; left: 20px; z-index: 9999; box-shadow: var(--shadow-md);">
    <i class="fas fa-check-circle"></i> تم تحديث مسار العميل بنجاح!
</div>

<!-- لوحة الكانبان -->
<div class="kanban-board">
    <?php foreach ($columns as $statusKey => $colDef): ?>
        <div class="kanban-col" data-status="<?php echo $statusKey; ?>" style="border-top: 4px solid <?php echo $colDef['color']; ?>;">
            <div class="k-header">
                <div class="fw-bold text-dark" style="font-size: 15px;">
                    <i class="fas <?php echo $colDef['icon']; ?>" style="color: <?php echo $colDef['color']; ?>; margin-left: 5px;"></i>
                    <?php echo $colDef['title']; ?>
                </div>
                <span class="badge badge-secondary" id="count-<?php echo $statusKey; ?>"><?php echo count($groupedLeads[$statusKey]); ?></span>
            </div>
            
            <div class="k-cards" id="cards-<?php echo $statusKey; ?>">
                <?php foreach ($groupedLeads[$statusKey] as $lead): ?>
                    <div class="k-card" draggable="true" data-id="<?php echo $lead->id; ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="k-title fw-bold text-dark" style="font-size: 14px;"><?php echo htmlspecialchars($lead->name); ?></div>
                            <div class="dropdown">
                                <a href="<?php echo URLROOT; ?>/lead/edit/<?php echo $lead->id; ?>" class="text-muted hover-primary" title="تعديل"><i class="fas fa-pen" style="font-size:12px;"></i></a>
                            </div>
                        </div>
                        
                        <div class="k-company text-muted" style="font-size: 12px; margin-bottom: 10px;">
                            <i class="fas fa-building"></i> <?php echo htmlspecialchars($lead->company ?? 'بدون شركة'); ?>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2 mb-3" style="font-size: 12px;">
                            <span class="badge" style="background:var(--page-bg); color:var(--text-body); border:1px solid var(--border);"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($lead->phone ?? '—'); ?></span>
                        </div>
                        
                        <div class="k-meta d-flex justify-content-between align-items-center" style="border-top: 1px dashed var(--border); padding-top: 10px;">
                            <span class="text-muted" style="font-size: 11px;"><i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($lead->source); ?></span>
                            <div class="k-avatar" title="المسؤول: <?php echo htmlspecialchars($lead->assigned_name ?? 'غير معين'); ?>" style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary-light); color: var(--primary-dark); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">
                                <?php echo mb_substr($lead->assigned_name ?? '؟', 0, 1); ?>
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
        align-items: center;
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
    
    .hover-primary:hover {
        color: var(--primary) !important;
    }
</style>

<script>
    // تفعيل السحب والإفلات باستخدام Vanilla JavaScript
    const cards = document.querySelectorAll('.k-card');
    const columns = document.querySelectorAll('.kanban-col');

    cards.forEach(card => {
        card.addEventListener('dragstart', () => {
            card.classList.add('dragging');
            // تخزين الحالة الأصلية للتحقق لاحقاً
            card.dataset.sourceStatus = card.closest('.kanban-col').dataset.status;
        });

        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
        });
    });

    columns.forEach(col => {
        const cardsContainer = col.querySelector('.k-cards');
        
        col.addEventListener('dragover', e => {
            e.preventDefault(); // ضروري للسماح بالإفلات
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
                
                const newStatus = col.dataset.status;
                const oldStatus = draggingCard.dataset.sourceStatus;
                
                // تحديث العدادات محلياً
                if(newStatus !== oldStatus) {
                    updateLeadStatusAjax(draggingCard.dataset.id, newStatus, oldStatus);
                    draggingCard.dataset.sourceStatus = newStatus; // تحديث الحالة الأصلية
                }
            }
        });
    });

    function updateLeadStatusAjax(leadId, newStatus, oldStatus) {
        // تحديث العدادات في الهيدر
        const newCountEl = document.getElementById('count-' + newStatus);
        const oldCountEl = document.getElementById('count-' + oldStatus);
        
        newCountEl.textContent = parseInt(newCountEl.textContent) + 1;
        oldCountEl.textContent = parseInt(oldCountEl.textContent) - 1;

        // إرسال طلب AJAX للخادم
        fetch('<?php echo URLROOT; ?>/lead/updateStatusAjax', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${leadId}&status=${newStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // إظهار إشعار نجاح
                const toast = document.getElementById('toastNotification');
                toast.style.display = 'block';
                setTimeout(() => { toast.style.display = 'none'; }, 3000);
            } else {
                alert('حدث خطأ أثناء تحديث حالة العميل بقاعدة البيانات.');
                location.reload(); // إعادة تحميل الصفحة لإصلاح الخطأ
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('انقطع الاتصال بالخادم.');
        });
    }
</script>