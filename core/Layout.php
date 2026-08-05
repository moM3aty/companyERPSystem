<?php
// core/Layout.php

/**
 * نظام القوالب الرئيسي - يتضمن الـ Sidebar والـ Topbar
 * يُستخدم بدلاً من تكرار الكود في كل صفحة
 */
class Layout {
    
    /**
     * عرض الصفحة الكاملة مع الهيكل
     */
    public static function render(string $viewContent, array $pageData = []): void {
        // التحقق من انتهاء الجلسة
        Session::checkTimeout();
        
        // بيانات الصفحة الافتراضية
        $layoutData = [
            'page_title'   => $pageData['title'] ?? 'لوحة التحكم',
            'breadcrumb'  => $pageData['breadcrumb'] ?? [],
            'user_name'  => Session::getUserName(),
            'user_role'  => Session::getUserRole(),
            'user_initials'=> Session::getInitials(),
            'current_url' => $_GET['url'] ?? 'dashboard',
            'app_version'=> APP_VERSION,
        ];
        
        // دمج بيانات الصفحة فوق بيانات الهيكل
        $data = array_merge($layoutData, $pageData);
        
        // استدعاء ملف الهيكل
        require_once APP_ROOT . '/app/views/layouts/main.php';
    }
    
    /**
     * الحصول على قائمة عناصر القائمة الجانبي
     */
 public static function getSidebarItems(): array {
    return [
        [
            'section' => 'الرئيسية',
            'items' => [
                ['label' => 'لوحة التحكم', 'icon' => 'fa-gauge-high', 'url' => 'dashboard'],
                ['label' => 'الموظفين', 'icon' => 'fa-users', 'url' => 'employee/index'],
                ['label' => 'المخزون', 'icon' => 'fa-boxes-stacked', 'url' => 'product/index'],
                ['label' => 'المبيعات', 'icon' => 'fa-file-invoice-dollar', 'url' => 'sale/index'],
            ]
        ],
        [
            'section' => 'الموارد البشرية',
            'items' => [
                ['label' => 'الإجازات', 'icon' => 'fa-calendar-check', 'url' => 'leave/index'],
                ['label' => 'الحضور', 'icon' => 'fa-clock', 'url' => 'attendance/index'],
            ]
        ],
        [
            'section' => 'المشتريات والمخزون',
            'items' => [
                ['label' => 'أوامر الشراء', 'icon' => 'fa-cart-plus', 'url' => 'purchase/index'],
                ['label' => 'المستودعات', 'icon' => 'fa-warehouse', 'url' => 'warehouse/index'],
                ['label' => 'نقل المخزون', 'icon' => 'fa-arrows-left-right', 'url' => 'warehouse/transfers'],
            ]
        ],
        [
            'section' => 'المحاسبة',
            'items' => [
                ['label' => 'دفتر الأستاذ', 'icon' => 'fa-book', 'url' => 'account/ledger'],
                ['label' => 'الميزانية العمومية', 'icon' => 'fa-scale-balanced', 'url' => 'account/balance-sheet'],
                ['label' => 'قائمة الدخل', 'icon' => 'fa-chart-simple', 'url' => 'account/income-statement'],
            ]
        ],
        [
            'section' => 'CRM والمشاريع',
            'items' => [
                ['label' => 'الفرص', 'icon' => 'fa-bullseye', 'url' => 'opportunity/index'],
                ['label' => 'المتابعات', 'icon' => 'fa-phone', 'url' => 'followup/index'],
                ['label' => 'المشاريع', 'icon' => 'fa-diagram-project', 'url' => 'project/index'],
            ]
        ],
        [
            'section' => 'الأصول الثابتة',
            'items' => [
                ['label' => 'الأصول', 'icon' => 'fa-building', 'url' => 'asset/index'],
            ]
        ],
        [
            'section' => 'النظام',
            'items' => [
                ['label' => 'الإعدادات', 'icon' => 'fa-gear', 'url' => 'settings/index'],
                ['label' => 'سجل التدقيق', 'icon' => 'fa-clipboard-list', 'url' => 'audit/index'],
            ]
        ]
    ];
}
    
    /**
     * توليد الـ Sidebar HTML
     */
    public static function renderSidebar(string $currentUrl): string {
        $items = self::getSidebarItems();
        $html = '<nav class="sidebar-nav">';
        
        $currentSection = '';
        
        foreach ($items as $group) {
            // عنوان القسم
            $html .= sprintf(
                '<div class="nav-section-title">%s</div>',
                Helpers::e($group['section'])
            );
            
            // عناصر القسم
            foreach ($group['items'] as $item) {
                $isActive = $currentUrl === $item['url'] ? ' active' : '';
                $badge = '';
                
                // عرض شارة للعناصر النشط
                if ($item['badge'] !== null) {
                    $badge = sprintf(
                        '<span class="nav-badge">%s</span>',
                        $item['badge']
                    );
                }
                
                $html .= sprintf(
                    '<div class="nav-item">' .
                    '<a href="%s" class="nav-link%s">' .
                    '<i class="%s"></i>' .
                    '<span>%s</span>' .
                    '%s' .
                    '</a>' .
                    '</div>',
                    URL_ROOT . '/' . $item['url'],
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
    
    /**
     * توليد الـ Topbar HTML
     */
    public static function renderTopbar(string $pageTitle, array $breadcrumb = [], array $actions = []): string {
        // مسار البحث
        $searchHtml = '
            <div class="topbar-left">
                <div class="search-box">
                    <input type="text" id="globalSearch" placeholder="بحث سريع في النظام..." autocomplete="off">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        ';
        
        // أزرار الإجراءات
        $actionsHtml = '';
        if (!empty($actions)) {
            $actionsHtml = '<div class="topbar-right">';
            foreach ($actions as $action) {
                $actionsHtml .= sprintf(
                    '<button type="button" class="topbar-btn" title="%s" onclick="%s">' .
                    '<i class="%s"></i>' .
                    '</button>',
                    $action['title'] ?? '',
                    $action['onclick'] ?? '',
                    $action['icon'] ?? 'fa-circle'
                );
            }
            $actionsHtml .= '</div>';
        }
        
        // فتات مسار التنقل
        $breadcrumbHtml = '';
        if (!empty($breadcrumb)) {
            $breadcrumbHtml = '<div class="breadcrumb">';
            $count = count($breadcrumb);
            foreach ($breadcrumb as $i => $crumb) {
                if ($i < $count - 1) {
                    $breadcrumbHtml .= sprintf(
                        '<a href="%s">%s</a><i class="fas fa-chevron-left" style="font-size:9px;"></i>',
                        $crumb['url'],
                        Helpers::e($crumb['label'])
                    );
                } else {
                    $breadcrumbHtml .= '<span>' . Helpers::e($crumb['label']) . '</span>';
                }
            }
            $breadcrumbHtml .= '</div>';
        }
        
        return '
            <header class="topbar">
                <div style="display:flex;align-items:center;gap:16px;">
                    <button class="topbar-btn mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <div class="page-title">' . Helpers::e($pageTitle) . '</div>
                        ' . $breadcrumbHtml . '
                    </div>
                </div>
                ' . $searchHtml . $actionsHtml . '
            </header>
        ';
    }
    
    /**
     * توليد رسالة Flash HTML
     */
    public static function renderFlash(): string {
        $flash = Session::getFlash();
        
        if ($flash === null) {
            return '';
        }
        
        $icon = match($flash['type']) {
            'success' => 'fa-circle-check',
            'error'   => 'fa-circle-xmark',
            'warning' => 'fa-triangle-exclamation',
            default   => 'fa-info-circle',
        };
        
        return sprintf(
            '<div class="flash-msg flash-%s slideDown">' .
            '<i class="fas %s"></i>' .
            '<span>%s</span>' .
            '</div>',
            $flash['type'],
            $icon,
            Helpers::e($flash['message'])
        );
    }
    
    /**
     * توليد شريط التنقل (URL_ROOT/...)
     */
    public static function url(string $path): string {
        return URL_ROOT . '/' . ltrim($path, '/');
    }
    
    /**
     * توليد حقل CSRF المخفي
     */
    public static function csrfField(): string {
        return Security::csrfField();
    }
    
    /**
     * جلب اسم التصنيف بالمعرف
     */
    public static function getCategoryName(int $id): string {
        if ($id <= 0) return '—';
        
        $db = Database::getInstance();
        $db->query("SELECT name FROM categories WHERE id = :id LIMIT 1");
        $db->bind(':id', $id, PDO::PARAM_INT);
        $result = $db->single();
        
        return $result ? $result->name : '—';
    }
    
    /**
     * جلب اسم القسم بالمعرف
     */
    public static function getDepartmentName(int $id): string {
        if ($id <= 0) return '—';
        
        $db = Database::getInstance();
        $db->query("SELECT name FROM departments WHERE id = :id LIMIT 1");
        $db->bind(':id', $id, PDO::PARAM_INT);
        $result = $db->single();
        
        return $result ? $result->name : '—';
    }
    
}