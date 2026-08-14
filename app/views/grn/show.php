<?php
// app/views/grn/show.php
$grn = $data['grn'] ?? null;
$items = $data['items'] ?? [];

$statusBadge = match($grn->status) { 'Received' => 'badge-success', 'Verified' => 'badge-primary', 'Invoiced' => 'badge-dark', default => 'badge-secondary' };
?>

<div class="card" style="max-width: 900px; margin: 0 auto; border: none; box-shadow: var(--shadow-md);">
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <div class="d-flex align-items-center gap-3">
            <h3 class="card-title text-dark mb-0"><i class="fas fa-dolly text-success"></i> مذكرة استلام بضاعة (GRN)</h3>
            <span class="badge <?php echo $statusBadge; ?> fs-6"><?php echo $grn->status; ?></span>
        </div>
        <div class="d-flex gap-2">
            <?php if($grn->status === 'Received' && Session::hasRole('admin')): ?>
                <!-- 🟢 ربط بالمطابقة الثلاثية (تحويل لفاتورة مورد) 🟢 -->
                <a href="#" onclick="alert('سيتم توجيهك قريباً لإدخال فاتورة المورد (Supplier Invoice) للمطابقة 3-Way Match.')" class="btn btn-primary btn-sm"><i class="fas fa-file-invoice-dollar"></i> إدخال فاتورة المورد</a>
            <?php endif; ?>
            <button class="btn btn-dark btn-sm" onclick="window.print()"><i class="fas fa-print"></i> طباعة (PDF)</button>
            <a href="<?php echo URLROOT; ?>/grn/index" class="btn btn-secondary btn-sm">رجوع</a>
        </div>
    </div>

    <div class="card-body p-5 bg-white">
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <h1 style="font-size: 24px; font-weight: 900; color: var(--text-dark); margin-bottom: 5px;">مذكرة استلام مخزون</h1>
                <div class="text-muted font-monospace fs-5">GOODS RECEIVED NOTE</div>
            </div>
            <div class="text-left" style="direction: ltr; text-align: left;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--success-dark); margin-bottom: 5px;">#<?php echo htmlspecialchars($grn->grn_number); ?></h2>
                <div class="text-muted fs-6 font-monospace">Date: <?php echo date('Y-m-d', strtotime($grn->delivery_date)); ?></div>
            </div>
        </div>

        <div class="row mb-5" style="display: flex; gap: 20px;">
            <div style="flex: 1; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
                <div class="bg-light p-2 fw-bold text-dark border-bottom text-center">التوريد (Supplier & Delivery)</div>
                <div class="p-3">
                    <h5 class="fw-bold text-primary mb-2"><i class="fas fa-truck"></i> <?php echo htmlspecialchars($grn->supplier_name ?? '—'); ?></h5>
                    <?php if($grn->delivery_note): ?><div class="text-muted fs-6 font-monospace mb-1">بوليصة توصيل: <?php echo htmlspecialchars($grn->delivery_note); ?></div><?php endif; ?>
                    <?php if($grn->po_number): ?><div class="text-muted fs-6 font-monospace text-info fw-bold">أمر الشراء: <?php echo htmlspecialchars($grn->po_number); ?></div><?php endif; ?>
                </div>
            </div>
            
            <div style="flex: 1; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
                <div class="bg-light p-2 fw-bold text-dark border-bottom text-center">الاستلام (Receiving Info)</div>
                <div class="p-3">
                    <h5 class="fw-bold text-success mb-2"><i class="fas fa-warehouse"></i> <?php echo htmlspecialchars($grn->warehouse_name ?? '—'); ?></h5>
                    <div class="text-muted fs-6">أمين المستودع: <?php echo htmlspecialchars($grn->receiver_name ?? '—'); ?></div>
                    <div class="text-muted fs-6 font-monospace mt-1">تاريخ ووقت النظام: <?php echo $grn->created_at; ?></div>
                </div>
            </div>
        </div>

        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-boxes"></i> تفاصيل الكميات المستلمة (تمت إضافتها للمخزون)</h6>
        <table class="table" style="border: 1px solid var(--border-color); width: 100%;">
            <thead style="background: var(--slate-100);">
                <tr>
                    <th style="padding: 12px; color: var(--slate-700);">المنتج (SKU)</th>
                    <th class="text-center" style="padding: 12px; color: var(--slate-700);">المطلوب بـ PO</th>
                    <th class="text-center" style="padding: 12px; color: var(--primary-dark);">المستلم الفعلي</th>
                    <th class="text-center" style="padding: 12px; color: var(--danger);">التالف/مرفوض</th>
                    <th class="text-center" style="padding: 12px; background: var(--success-light); color: var(--success-dark);">الكمية المقبولة</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 15px;">
                        <strong class="text-dark"><?php echo htmlspecialchars($item->product_name); ?></strong>
                        <div class="text-muted font-monospace" style="font-size: 11px;">SKU: <?php echo htmlspecialchars($item->sku); ?></div>
                        <?php if($item->batch_number || $item->expiry_date): ?>
                            <div class="text-info font-monospace mt-1" style="font-size: 11px;">Lot: <?php echo $item->batch_number;?> | Exp: <?php echo $item->expiry_date; ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center font-monospace text-muted align-middle" style="padding: 15px;"><?php echo $item->ordered_qty; ?></td>
                    <td class="text-center font-monospace fw-bold text-primary align-middle" style="padding: 15px;"><?php echo $item->received_qty; ?></td>
                    <td class="text-center font-monospace fw-bold text-danger align-middle" style="padding: 15px;"><?php echo $item->damaged_qty; ?></td>
                    <td class="text-center font-monospace fw-black text-success align-middle fs-5" style="padding: 15px; background: #f0fdf4;"><?php echo $item->accepted_qty; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if($grn->notes): ?>
            <div class="mt-4 p-3 bg-light rounded text-muted" style="font-size: 13px; border-right: 4px solid var(--warning);">
                <strong>ملاحظات المستودع:</strong><br>
                <?php echo nl2br(htmlspecialchars($grn->notes)); ?>
            </div>
        <?php endif; ?>

        <!-- Approval Signatures (Print Only) -->
        <div class="mt-5 pt-5 d-none d-print-block" style="page-break-inside: avoid;">
            <div class="row" style="display: flex; justify-content: space-around; text-align: center;">
                <div style="flex: 1;"><div class="fw-bold text-dark mb-4">أمين المستودع (المستلم)</div><div style="border-bottom: 1px dashed var(--slate-400); width: 60%; margin: 0 auto 10px;"></div></div>
                <div style="flex: 1;"><div class="fw-bold text-dark mb-4">مندوب التوصيل (المورد)</div><div style="border-bottom: 1px dashed var(--slate-400); width: 60%; margin: 0 auto 10px;"></div></div>
            </div>
        </div>

    </div>
</div>

<style>
    @media print { body { background: #fff !important; } .d-print-none, .sidebar, .topbar { display: none !important; } .main-content { margin: 0 !important; } .card { box-shadow: none !important; border: none !important; max-width: 100% !important; margin: 0 !important;} .card-body { padding: 0 !important; } .table th, .table td { border: 1px solid #000 !important; } }
</style>