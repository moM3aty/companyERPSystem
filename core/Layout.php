<?php
// core/Layout.php

/**
 * نظام القوالب الرئيسي - يعتمد على الـ 15 وحدة المكونة لنظام ERP Pro
 */
class Layout
{

    /**
     * عرض الصفحة الكاملة مع الهيكل (Layout)
     */
    public static function render(string $viewContent, array $pageData = []): void
    {
        Session::checkTimeout();

        // توليد الـ HTML للقائمة الجانبية بناءً على الرابط الحالي
        $currentUrl = $_GET['url'] ?? 'dashboard';
        $sidebarHtml = self::renderSidebar($currentUrl);

        $layoutData = [
            'page_title'   => $pageData['title'] ?? 'لوحة التحكم',
            'breadcrumb'  => $pageData['breadcrumb'] ?? [],
            'user_name'  => Session::getUserName(),
            'user_role'  => Session::getUserRole(),
            'user_initials' => Session::getInitials(),
            'app_version' => defined('APP_VERSION') ? APP_VERSION : '2.0.0',
            'flash'      => Session::getFlash(),
            'sidebarHtml' => $sidebarHtml,
            'viewContent' => $viewContent
        ];

        $data = array_merge($layoutData, $pageData);
        extract($data);

        require_once APP_ROOT . '/app/views/layouts/main.php';
    }

    /**
     * قائمة الـ 15 وحدة (Modules)
     */
    public static function getSidebarItems(): array
    {
        return [
            [
                'section' => '1. النظام الأساسي',
                'items' => [
                    ['label' => 'لوحة التحكم', 'icon' => 'fa-gauge-high', 'url' => 'dashboard'],
                    ['label' => 'المستخدمين والصلاحيات', 'icon' => 'fa-users-gear', 'url' => 'users/index'],
                ]
            ],
            [
                'section' => '2. الموارد البشرية HR',
                'items' => [
                    ['label' => 'إدارة الموظفين', 'icon' => 'fa-users', 'url' => 'employee/index'],
                    ['label' => 'مسير الرواتب', 'icon' => 'fa-money-check-dollar', 'url' => 'payroll/index'],
                    ['label' => 'الحضور والانصراف', 'icon' => 'fa-fingerprint', 'url' => 'attendance/index'],
                    ['label' => 'طلبات الإجازات', 'icon' => 'fa-calendar-check', 'url' => 'leave/index', 'badge' => '2'],
                    ['label' => 'الجزاءات والسلف', 'icon' => 'fa-gavel', 'url' => 'sanction/index'],
                ]
            ],
            [
                'section' => '3. المالية والمحاسبة',
                'items' => [
                    ['label' => 'دليل الحسابات', 'icon' => 'fa-sitemap', 'url' => 'account/tree'],
                    ['label' => 'القيود اليومية', 'icon' => 'fa-book-journal-whills', 'url' => 'journal/index'],
                    ['label' => 'المصروفات والإيرادات', 'icon' => 'fa-wallet', 'url' => 'expense/index'],
                    ['label' => 'التقارير المالية', 'icon' => 'fa-file-invoice-dollar', 'url' => 'report/financial'],
                ]
            ],
            [
                'section' => '4. المبيعات Sales',
                'items' => [
                    ['label' => 'قاعدة العملاء', 'icon' => 'fa-address-book', 'url' => 'customer/index'],
                    ['label' => 'عروض الأسعار', 'icon' => 'fa-file-signature', 'url' => 'quote/index'],
                    ['label' => 'فواتير المبيعات', 'icon' => 'fa-receipt', 'url' => 'sale/index'],
                ]
            ],
            [
                'section' => '5. المشتريات والموردين',
                'items' => [
                    ['label' => 'إدارة الموردين', 'icon' => 'fa-truck-field', 'url' => 'supplier/index'],
                    ['label' => 'أوامر الشراء (PO)', 'icon' => 'fa-cart-shopping', 'url' => 'purchase/index'],
                    ['label' => 'استلام البضائع', 'icon' => 'fa-box-open', 'url' => 'purchase/receiving'],
                ]
            ],
            [
                'section' => '6. المخازن Inventory',
                'items' => [
                    ['label' => 'دليل الأصناف', 'icon' => 'fa-cubes', 'url' => 'product/index'],
                    ['label' => 'المستودعات ونقل المخزون', 'icon' => 'fa-warehouse', 'url' => 'warehouse/index'],
                    ['label' => 'الجرد والتسويات', 'icon' => 'fa-clipboard-check', 'url' => 'stocktake/index'],
                ]
            ],
            [
                'section' => '7. علاقات العملاء CRM',
                'items' => [
                    ['label' => 'الفرص البيعية', 'icon' => 'fa-bullseye', 'url' => 'opportunity/index'],
                    ['label' => 'المتابعات والاجتماعات', 'icon' => 'fa-phone-volume', 'url' => 'followup/index'],
                ]
            ],
            [
                'section' => '8. المشاريع Projects',
                'items' => [
                    ['label' => 'إدارة المشاريع', 'icon' => 'fa-diagram-project', 'url' => 'project/index'],
                    ['label' => 'المهام (Tasks)', 'icon' => 'fa-list-check', 'url' => 'task/index'],
                ]
            ],
            [
                'section' => '9. خدمة العملاء والدعم',
                'items' => [
                    ['label' => 'التذاكر (Help Desk)', 'icon' => 'fa-headset', 'url' => 'ticket/index'],
                ]
            ],
            [
                'section' => '10. الأصول والعقود',
                'items' => [
                    ['label' => 'الأصول الثابتة', 'icon' => 'fa-building', 'url' => 'asset/index'],
                    ['label' => 'إدارة العقود (Contracts)', 'icon' => 'fa-file-contract', 'url' => 'contract/index'],
                    ['label' => 'الوثائق والأرشفة (DMS)', 'icon' => 'fa-folder-tree', 'url' => 'dms/index'],
                ]
            ],
            [
                'section' => 'الإعدادات والتقارير',
                'items' => [
                    ['label' => 'التقارير التحليلية', 'icon' => 'fa-chart-pie', 'url' => 'report/index'],
                    ['label' => 'الإشعارات (SMS/Email)', 'icon' => 'fa-bell', 'url' => 'notification/index'],
                    ['label' => 'إعدادات النظام', 'icon' => 'fa-gears', 'url' => 'settings/index'],
                ]
            ]
        ];
    }

    /**
     * توليد الـ Sidebar HTML
     */
    public static function renderSidebar(string $currentUrl): string
    {
        $items = self::getSidebarItems();
        $html = '<nav class="sidebar-nav">';

        foreach ($items as $group) {
            $html .= sprintf('<div class="nav-section-title">%s</div>', Helpers::e($group['section']));

            foreach ($group['items'] as $item) {
                // التحقق ما إذا كان الرابط الحالي يطابق أو يندرج تحت هذا الرابط
                $urlPath = explode('/', $item['url'])[0];
                $currentPath = explode('/', $currentUrl)[0];

                $isActive = ($urlPath === $currentPath) ? ' active' : '';
                $badge = !empty($item['badge']) ? sprintf('<span class="nav-badge">%s</span>', $item['badge']) : '';

                $html .= sprintf(
                    '<div class="nav-item"><a href="%s" class="nav-link%s"><i class="fas %s"></i><span>%s</span>%s</a></div>',
                    self::url($item['url']),
                    $isActive,
                    $item['icon'],
                    Helpers::e($item['label']),
                    $badge
                );
            }
        }
        $html .= '</nav>';
        return $html;
    }

    public static function url(string $path): string
    {
        return URL_ROOT . '/' . ltrim($path, '/');
    }
}
