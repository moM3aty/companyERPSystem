<?php
// Path: app/Modules/Accounting/Http/Resources/AccountResource.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Resources;

class AccountResource
{
    public static function toArray(array|object $account): array
    {
        $data = is_object($account) ? (array) $account : $account;

        return [
            'id' => $data['id'] ?? null,
            'code' => $data['account_code'] ?? null,
            'name' => $data['account_name'] ?? null,
            'type' => $data['account_type'] ?? null,
            'normal_balance' => $data['normal_balance'] ?? null,
            'is_control' => (bool) ($data['is_control_account'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];
    }

    public static function collection(array $accounts): array
    {
        return array_map(fn($acc) => self::toArray($acc), $accounts);
    }
}