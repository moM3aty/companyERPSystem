<?php
// app/controllers/RoleController.php

class RoleController extends Controller {

    public function __construct() {
        // حماية: الإدارة فقط (مالك النظام أو مدير الشركة) يمكنهم الاطلاع على مصفوفة الصلاحيات
        $this->requireAnyRole(['super_admin', 'admin']);
    }

    public function index(): void {
        $data = [
            'title' => 'دليل الصلاحيات والأدوار (Roles Matrix)',
            'breadcrumb' => [
                ['label' => 'الإدارة والدعم', 'url' => '#'],
                ['label' => 'دليل الصلاحيات', 'url' => 'role/index']
            ]
        ];

        ob_start();
        $this->view('roles/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
}