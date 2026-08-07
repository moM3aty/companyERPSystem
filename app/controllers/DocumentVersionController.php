<?php
// المسار: app/controllers/DocumentVersionController.php

class DocumentVersionController extends Controller {
    private DocumentVersion $versionModel;
    private Document $docModel;

    public function __construct() {
        $this->requireAuth();
        $this->versionModel = $this->model('DocumentVersion');
        $this->docModel = $this->model('Document');
    }

    public function index(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('document/index');
        $docId = (int)$id;
        
        $document = $this->docModel->getDocumentById($docId);
        if (!$document) $this->redirect('document/index');

        $versions = $this->versionModel->getVersionsByDocument($docId);

        $data = [
            'title' => 'التحكم في إصدارات الملف (Version Control)',
            'document' => $document,
            'versions' => $versions,
            'breadcrumb' => [
                ['label' => 'إدارة الوثائق', 'url' => 'document/index'],
                ['label' => 'سجل الإصدارات', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('documents/versions', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(string $docId = ''): void {
        $this->requireAnyRole(['admin', 'manager', 'editor']);
        if ($this->isPost() && !empty($docId) && is_numeric($docId)) {
            $versionNumber = trim($_POST['version_number'] ?? '');
            
            $uploadDir = dirname(APP_ROOT) . '/public/uploads/documents/versions/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['document_file']['tmp_name'];
                $fileName = $_FILES['document_file']['name'];
                $fileSize = $_FILES['document_file']['size'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $newFileName = uniqid('VER_') . '_' . time() . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $data = [
                        'document_id' => (int)$docId,
                        'version_number' => $versionNumber,
                        'file_name' => $fileName, // الاسم الأصلي
                        'file_path' => '/uploads/documents/versions/' . $newFileName, // المسار الجديد
                        'file_size' => $fileSize
                    ];

                    if ($this->versionModel->addVersion($data)) {
                        $this->setFlash('success', 'تم حفظ الإصدار الجديد للوثيقة بنجاح.');
                    } else {
                        $this->setFlash('error', 'فشل في حفظ بيانات الإصدار.');
                    }
                } else {
                    $this->setFlash('error', 'فشل في نقل ورفع الملف.');
                }
            } else {
                $this->setFlash('error', 'يرجى اختيار ملف صالح לلرفع.');
            }
        }
        $this->redirect('documentVersion/index/' . $docId);
    }
}