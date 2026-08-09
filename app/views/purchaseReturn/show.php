<?php
// app/views/purchaseReturn/show.php
$return = $data['return'] ?? null;
$items = $data['items'] ?? [];

if (!$return) {
    echo "<div class='alert alert-danger m-4'>المرتجع غير متاح.</div>";
    return;
}

$db = Database::getInstance();
$cid = Session::get('company_id') ?: 1;
$db->query("SELECT setting_key, setting_value FROM settings WHERE company_id = :cid OR company_id IS NULL");
$db->bind(':cid', $cid);
$sysSettings = $db->resultSet();

$companyName = 'اسم الشركة';
$vatNumber = 'غير مسجل';

foreach ($sysSettings as $s) {
    if ($s->setting_key === 'company_name' && !empty($s->setting_value)) $companyName = $s->setting_value;
    if (in_array($s->setting_key, ['tax_number', 'vat_number']) && !empty($s->setting_value)) $vatNumber = $s->setting_value;
}
?>

<div class="card" style="max-width: 900px; margin: 0 auto; border: none; box-shadow: var(--shadow-md);">
    
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <div class="d-flex align-items-center gap-3">
            <h3 class="card-title text-dark mb-0"><i class="fas fa-boxes-packing text-danger"></i> إشعار مرتجع مشتريات (Purchase Return)</h3>
            <?php 
                $statusBadge = match($return->status) {
                    'draft' => 'badge-secondary', 'approved' => 'badge-success', 'cancelled' => 'badge-danger', default => 'badge-secondary'
                };
                $statusLabel = match($return->status) {
                    'draft' => 'مسودة', 'approved' => 'معتمد', 'cancelled' => 'ملغي', default => $return->status
                };
            ?>
            <span class="badge <?php echo $statusBadge; ?> fs-6"><?php echo $statusLabel; ?></span>
        </div>
        
        <div class="d-flex gap-2">
            <button class="btn btn-dark btn-sm" onclick="window.print()"><i class="fas fa-print"></i> طباعة الإشعار</button>
            <a href="<?php echo URLROOT; ?>/purchaseReturn/index" class="btn btn-secondary btn-sm">رجوع</a>
        </div>
    </div>

    <div class="card-body p-5 bg-white" style="border-radius: 0 0 var(--radius-md) var(--radius-md);">
        
        <!-- الترويسة -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 900; color: var(--danger-dark); margin-bottom: 5px;">مرتجع مشتريات</h1>
                <div class="text-muted font-monospace fs-5">PURCHASE RETURN</div>
            </div>
            
            <div class="text-left" style="direction: ltr; text-align: left;">
                <h2 style="font-size: 24px; font-weight: 900; color: var(--text-dark); margin-bottom: 5px;"><?php echo htmlspecialchars($companyName); ?></h2>
                <div class="text-muted fs-6">
                    الرقم الضريبي: <span class="font-monospace text-dark fw-bold"><?php echo htmlspecialchars($vatNumber); ?></span>
                </div>
            </div>
        </div>

        <div class="row mb-5" style="display: flex; justify-content: space-between;">
            <div style="width: 48%;">
                <div class="text-muted fs-6 fw-bold text-uppercase mb-2">إرجاع إلى المورد:</div>
                <div class="fs-4 fw-bold text-dark mb-1"><i class="fas fa-truck text-muted"></i> <?php echo htmlspecialchars($return->supplier_name ?? 'مورد غير مسجل'); ?></div>
            </div>
            <div style="width: 48%; text-align: left; background: #fff1f2; padding: 15px; border-radius: 8px; border: 1px dashed #fca5a5;">
                <table style="width: 100%; font-size: 14px;">
                    <tr>
                        <td class="text-danger fw-bold pb-2">رقم المرتجع:</td>
                        <td class="font-monospace fw-bold text-danger text-left pb-2" style="direction:ltr;"><?php echo htmlspecialchars($return->return_number); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-bold pb-2">تاريخ الارتجاع:</td>
                        <td class="font-monospace text-dark text-left pb-2" style="direction:ltr;"><?php echo date('Y-m-d', strtotime($return->return_date)); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="table" style="border: 1px solid var(--border-color); width: 100%;">
            <thead style="background: var(--danger-light);">
                <tr>
                    <th style="padding: 12px; color: var(--danger-dark);">المنتج المرتجع</th>
                    <th class="text-center" style="padding: 12px; color: var(--danger-dark);">الكمية</th>
                    <th style="padding: 12px; color: var(--danger-dark); text-align:left;">تكلفة الوحدة</th>
                    <th style="padding: 12px; color: var(--danger-dark); text-align:left;">الإجمالي الفرعي</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 15px;">
                        <strong class="text-dark"><?php echo htmlspecialchars($item->product_name ?? 'منتج غير معروف'); ?></strong>
                        <div class="text-muted font-monospace" style="font-size: 11px;">SKU: <?php echo htmlspecialchars($item->sku ?? '—'); ?></div>
                    </td>
                    <td class="text-center font-monospace fw-bold" style="padding: 15px;"><?php echo $item->quantity; ?></td>
                    <td class="font-monospace" style="padding: 15px; direction:ltr; text-align:left;"><?php echo number_format($item->unit_cost, 2); ?></td>
                    <td class="font-monospace fw-bold text-dark" style="padding: 15px; direction:ltr; text-align:left;"><?php echo number_format($item->subtotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="display: flex; justify-content: space-between; margin-top: 30px;">
            <div style="width: 50%;">
                <?php if(!empty($return->notes)): ?>
                    <h6 class="fw-bold text-dark mb-2">أسباب الارتجاع والملاحظات:</h6>
                    <p class="text-muted" style="font-size: 13px; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($return->notes)); ?></p>
                <?php endif; ?>
            </div>
            <div style="width: 350px;">
                <table style="width: 100%; font-size: 15px;">
                    <tr>
                        <td class="fw-bold py-3" style="font-size: 18px; color: var(--text-dark);">إجمالي قيمة المرتجع:</td>
                        <td class="font-monospace fw-bold text-left py-3" style="font-size: 22px; color: var(--danger); direction:ltr;">
                            <?php echo number_format($return->total_amount, 2); ?> <span style="font-size: 12px; color: var(--text-muted);">ر.س</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mt-5 pt-5 text-center d-flex justify-content-around px-5" style="border-top: 1px solid var(--border-color);">
            <div>
                <p class="fw-bold text-dark mb-4">أمين المستودع</p>
                <div style="width: 200px; border-bottom: 1px dashed var(--slate-400); margin: auto;"></div>
            </div>
            <div>
                <p class="fw-bold text-dark mb-4">توقيع المستلم (المورد)</p>
                <div style="width: 200px; border-bottom: 1px dashed var(--slate-400); margin: auto;"></div>
            </div>
        </div>

    </div>
</div>

<style>
    @media print {
        body { background: #fff !important; }
        .d-print-none, .sidebar, .topbar { display: none !important; }
        .main-content { margin: 0 !important; }
        .card { box-shadow: none !important; border: none !important; max-width: 100% !important; margin: 0 !important;}
        .card-body { padding: 0 !important; }
    }
</style>