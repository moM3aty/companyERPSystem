<?php
// app/views/sales_returns/create.php
$invoices = $data['invoices'] ?? [];
$products = $data['products'] ?? [];
$currentUrl = 'saleReturn/index';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل مرتجع | ERP Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* أنماط أساسية مبسطة للسرعة */
        body { font-family: 'Cairo', sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo'; }
        .btn { background: #ef4444; color: #fff; padding: 12px 24px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="margin-top:0; color:#0f172a;"><i class="fas fa-arrow-rotate-left" style="color:#ef4444;"></i> تسجيل فاتورة مرتجعات</h2>
        <p style="color:#64748b; font-size:14px;">حدد الفاتورة والأصناف التي سيتم إرجاعها لاستردادها للمخزون.</p>
        
        <form action="<?php echo URL_ROOT; ?>/saleReturn/create" method="POST">
            <div class="form-group">
                <label>الفاتورة الأصلية المستهدف إرجاعها *</label>
                <select name="invoice_id" class="form-control" required>
                    <option value="">-- اختر الفاتورة --</option>
                    <?php foreach($invoices as $inv): ?>
                        <option value="<?php echo $inv->id; ?>">رقم: <?php echo htmlspecialchars($inv->invoice_number); ?> - العميل: <?php echo htmlspecialchars($inv->customer_name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>سبب الإرجاع</label>
                <input type="text" name="reason" class="form-control" placeholder="مثال: عيب مصنعي، استبدال، خطأ بالطلب...">
            </div>

            <hr style="border:0; border-top:1px solid #e2e8f0; margin:30px 0;">
            <h4 style="margin-top:0;">الأصناف المرتجعة</h4>
            
            <table style="width:100%; text-align:right;" id="itemsTable">
                <tr>
                    <th>الصنف</th>
                    <th>الكمية المرتجعة</th>
                    <th>سعر الوحدة المُسترد</th>
                </tr>
                <tr>
                    <td>
                        <select name="product_id[]" class="form-control" required>
                            <option value="">-- الصنف --</option>
                            <?php foreach($products as $p): ?>
                                <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="number" name="quantity[]" class="form-control" value="1" min="1" required></td>
                    <td><input type="number" name="price[]" class="form-control" step="0.01" required placeholder="0.00"></td>
                </tr>
            </table>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn"><i class="fas fa-check"></i> اعتماد المرتجع</button>
                <a href="<?php echo URL_ROOT; ?>/saleReturn/index" style="margin-right:15px; color:#64748b; text-decoration:none; font-weight:bold;">إلغاء</a>
            </div>
        </form>
    </div>
</body>
</html>