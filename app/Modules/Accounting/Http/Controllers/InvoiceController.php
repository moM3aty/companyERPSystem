<?php
// Path: app/Modules/Accounting/Http/Controllers/InvoiceController.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Controllers;

use App\Core\Http\Request;

class InvoiceController
{
    public function index(Request $request): void
    {
        // For accounting perspective, we show AR invoices (Sales)
        // Usually fetched via an Integration or Cross-Module repository
        require BASE_PATH . '/resources/views/accounting/invoices/index.php';
    }
}