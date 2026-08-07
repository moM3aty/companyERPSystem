<?php
// app/helpers/Layout.php
/**
 * كلاس مساعد لتوليد مكونات الواجهة المشتركة مثل القائمة الجانبية (Sidebar)
 * يتم استدعاؤه في جميع ملفات العرض (Views)
 */
class Layout {
    
    public static function renderSidebar($currentUrl = '') {
        // هيكل القائمة الجانبية وتقسيماتها محدث بالروابط الجديدة
        $menu = [
            'الرئيسية' => [
                ['url' => 'dashboard', 'icon' => 'fa-house-chimney', 'text' => 'لوحة القيادة']
            ],
            'المبيعات والمشتريات' => [
                ['url' => 'sale/index', 'icon' => 'fa-file-invoice-dollar', 'text' => 'المبيعات والفواتير'],
                ['url' => 'purchase/index', 'icon' => 'fa-cart-flatbed', 'text' => 'أوامر الشراء'],
                ['url' => 'customer/index', 'icon' => 'fa-users', 'text' => 'العملاء'],
                ['url' => 'supplier/index', 'icon' => 'fa-truck-field', 'text' => 'الموردين']
            ],
            'المخزون والأصول' => [
                ['url' => 'product/index', 'icon' => 'fa-boxes-stacked', 'text' => 'المنتجات والمستودع'],
                ['url' => 'asset/index', 'icon' => 'fa-building', 'text' => 'الأصول الثابتة']
            ],
            'المالية والمحاسبة' => [
                ['url' => 'accounting/index', 'icon' => 'fa-chart-pie', 'text' => 'المصروفات والأرباح'],
                ['url' => 'account/ledger', 'icon' => 'fa-book-open', 'text' => 'دفتر الأستاذ'],
                ['url' => 'account/trial-balance', 'icon' => 'fa-scale-unbalanced', 'text' => 'ميزان المراجعة'],
                ['url' => 'account/balance-sheet', 'icon' => 'fa-scale-balanced', 'text' => 'الميزانية العمومية'],
                ['url' => 'account/income-statement', 'icon' => 'fa-money-bill-trend-up', 'text' => 'قائمة الدخل']
            ],
            'الموارد البشرية' => [
                ['url' => 'employee/index', 'icon' => 'fa-user-tie', 'text' => 'شؤون الموظفين'],
                ['url' => 'attendance/index', 'icon' => 'fa-user-clock', 'text' => 'الحضور والانصراف'],
                ['url' => 'payroll/index', 'icon' => 'fa-money-check-dollar', 'text' => 'مسير الرواتب']
            ],
            'CRM والمشاريع' => [
                ['url' => 'opportunity/index', 'icon' => 'fa-bullseye', 'text' => 'الفرص البيعية'],
                ['url' => 'followup/index', 'icon' => 'fa-phone-volume', 'text' => 'المتابعات والمهام'],
                ['url' => 'contract/index', 'icon' => 'fa-file-signature', 'text' => 'إدارة العقود'],
                ['url' => 'project/index', 'icon' => 'fa-diagram-project', 'text' => 'المشاريع']
            ],
            'التقارير والإعدادات' => [
                ['url' => 'reports/sales', 'icon' => 'fa-chart-column', 'text' => 'تقارير المبيعات'],
                ['url' => 'audit/index', 'icon' => 'fa-clock-rotate-left', 'text' => 'سجل التدقيق'],
                ['url' => 'settings/index', 'icon' => 'fa-gear', 'text' => 'إعدادات النظام']
            ]
        ];

        // الحصول على الرابط الأساسي للمشروع
        $baseUrl = defined('URLROOT') ? URLROOT : '';

        $html = '<nav class="sidebar-nav">';
        
        foreach ($menu as $section => $items) {
            $html .= '<div class="nav-section-title">' . htmlspecialchars($section) . '</div>';
            
            foreach ($items as $item) {
                // التحقق مما إذا كان الرابط الحالي يطابق عنصر القائمة (لإضافة كلاس active)
                $isActive = ($currentUrl === $item['url'] || strpos($currentUrl, $item['url'] . '/') === 0) ? 'active' : '';
                
                $html .= '<a href="' . $baseUrl . '/' . $item['url'] . '" class="nav-link ' . $isActive . '">';
                $html .= '<i class="fas ' . $item['icon'] . '"></i>';
                $html .= '<span>' . htmlspecialchars($item['text']) . '</span>';
                $html .= '</a>';
            }
        }
        
        $html .= '</nav>';
        
        return $html;
    }
}