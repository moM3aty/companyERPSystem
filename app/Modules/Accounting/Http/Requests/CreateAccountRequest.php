<?php
// Path: app/Modules/Accounting/Http/Requests/CreateAccountRequest.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Requests;

use App\Core\Http\Request;
use App\Modules\Accounting\Application\DTOs\CreateAccountDTO;
use InvalidArgumentException;

class CreateAccountRequest
{
    public static function validateAndCreateDTO(Request $request, int $companyId): CreateAccountDTO
    {
        $code = $request->input('account_code');
        $name = $request->input('account_name');
        $type = $request->input('account_type');
        $normalBalance = $request->input('normal_balance');
        $parentId = $request->input('parent_id') ? (int) $request->input('parent_id') : null;
        $isControl = (bool) $request->input('is_control_account', false);
        $isActive = (bool) $request->input('is_active', true);

        if (!$code || !$name || !$type || !$normalBalance) {
            throw new InvalidArgumentException("Missing required fields for Account creation.");
        }

        return new CreateAccountDTO(
            companyId: $companyId,
            accountCode: $code,
            accountName: $name,
            accountType: $type,
            normalBalance: $normalBalance,
            parentId: $parentId,
            isControlAccount: $isControl,
            isActive: $isActive
        );
    }
}