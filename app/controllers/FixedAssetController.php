<?php
// app/controllers/FixedAssetController.php

class FixedAssetController extends Controller {
    
    private FixedAsset $assetModel;

    public function __construct() {
        $this->requireAuth();
        $this->assetModel = $this->model('FixedAsset');
    }

    public function index(): void {
        $assets = $this->assetModel->getAllAssets();
        
        $totalCost = 0;
        $totalBookValue = 0;
        $activeAssetsCount = 0;
        
        foreach ($assets as $asset) {
            if ($asset->status === 'active') {
                $totalCost += $asset->purchase_cost;
                $totalBookValue += $asset->book_value;
                $activeAssetsCount++;
            }
        }

        $data = [
            'title' => 'سجل الأصول الثابتة', 
            'assets' => $assets,
            'stats' => [
                'total_cost' => $totalCost,
                'total_book_value' => $totalBookValue,
                'active_count' => $activeAssetsCount
            ],
            'breadcrumb' => [
                ['label' => 'المشاريع والأصول', 'url' => '#'],
                ['label' => 'الأصول الثابتة', 'url' => 'fixedAsset/index']
            ]
        ];
        
        ob_start();
        $this->view('assets/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'asset_tag'         => trim($_POST['asset_tag'] ?? ''),
                'name'              => trim($_POST['name'] ?? ''),
                'category'          => trim($_POST['category'] ?? 'other'),
                'purchase_date'     => trim($_POST['purchase_date'] ?? date('Y-m-d')),
                'purchase_cost'     => (float)($_POST['purchase_cost'] ?? 0.0),
                'salvage_value'     => (float)($_POST['salvage_value'] ?? 0.0),
                'useful_life_years' => (int)($_POST['useful_life_years'] ?? 1),
                'location'          => trim($_POST['location'] ?? ''),
                'status'            => trim($_POST['status'] ?? 'active'),
                'notes'             => trim($_POST['notes'] ?? ''),
                'created_by'        => Session::getUserId()
            ];

            if (empty($data['name']) || $data['purchase_cost'] <= 0 || $data['useful_life_years'] < 1) {
                $this->setFlash('error', 'يجب إدخال بيانات صحيحة (الاسم، تكلفة الشراء، العمر الإنتاجي).');
                $this->redirect('fixedAsset/create');
            }

            if ($this->assetModel->createAsset($data)) {
                $this->setFlash('success', 'تم حفظ الأصل بنجاح وتم توليد قيد الإثبات المحاسبي.');
                $this->redirect('fixedAsset/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ الأصل، تأكد من عدم تكرار الرقم التسلسلي (Asset Tag).');
                $this->redirect('fixedAsset/create');
            }
        } else {
            $data = [
                'title' => 'إضافة أصل جديد', 
                'breadcrumb' => [
                    ['label' => 'الأصول الثابتة', 'url' => 'fixedAsset/index'], 
                    ['label' => 'إضافة', 'url' => '#']
                ]
            ];
            ob_start();
            $this->view('assets/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = ''): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);
        if (empty($id) || !is_numeric($id)) $this->redirect('fixedAsset/index');
        
        $assetId = (int)$id;
        $asset = $this->assetModel->getAssetById($assetId);
        
        if (!$asset) {
            $this->setFlash('error', 'الأصل غير موجود.');
            $this->redirect('fixedAsset/index');
        }
        
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'asset_tag'         => trim($_POST['asset_tag'] ?? ''),
                'name'              => trim($_POST['name'] ?? ''),
                'category'          => trim($_POST['category'] ?? 'other'),
                'purchase_date'     => trim($_POST['purchase_date'] ?? date('Y-m-d')),
                'purchase_cost'     => (float)($_POST['purchase_cost'] ?? 0.0),
                'salvage_value'     => (float)($_POST['salvage_value'] ?? 0.0),
                'useful_life_years' => (int)($_POST['useful_life_years'] ?? 1),
                'location'          => trim($_POST['location'] ?? ''),
                'status'            => trim($_POST['status'] ?? 'active'),
                'notes'             => trim($_POST['notes'] ?? '')
            ];
            
            if ($this->assetModel->updateAsset($assetId, $data)) {
                ActivityLog::logAction('UPDATE', 'FixedAssets', $assetId, "تم تعديل بيانات الأصل الثابت: {$data['name']}");
                $this->setFlash('success', 'تم تعديل الأصل بنجاح.');
                $this->redirect('fixedAsset/index');
            } else {
                $this->setFlash('error', 'حدث خطأ، تأكد من عدم تكرار كود الأصل.');
                $this->redirect('fixedAsset/edit/' . $assetId);
            }
        } else {
            $data = [
                'title' => 'تعديل أصل', 
                'asset' => $asset, 
                'breadcrumb' => [
                    ['label' => 'الأصول', 'url' => 'fixedAsset/index'], 
                    ['label' => 'تعديل', 'url' => '#']
                ]
            ];
            ob_start();
            $this->view('assets/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->assetModel->deleteAsset((int)$id)) {
                ActivityLog::logAction('DELETE', 'FixedAssets', (int)$id, "تم حذف أصل ثابت من النظام");
                $this->setFlash('success', 'تم حذف الأصل.');
            } else {
                $this->setFlash('error', 'فشل في حذف الأصل.');
            }
        }
        $this->redirect('fixedAsset/index');
    }
}