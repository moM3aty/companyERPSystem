<?php
// Path: app/Modules/FixedAssets/Transfers/Application/TransferService.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Transfers\Application;

use App\Core\Database\TransactionManager;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;
use App\Modules\FixedAssets\Transfers\Infrastructure\AssetTransferRepository;
use App\Core\Tenant\TenantContext;

class TransferService
{
    protected AssetTransferRepository $transferRepo;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;
    protected TenantContext $tenant;

    public function __construct(
        AssetTransferRepository $transferRepo,
        TransactionManager $transaction,
        DatabaseManager $db,
        TenantContext $tenant
    ) {
        $this->transferRepo = $transferRepo;
        $this->transaction = $transaction;
        $this->db = $db;
        $this->tenant = $tenant;
    }

    public function executeTransfer(array $data, int $userId): int
    {
        $companyId = $this->tenant->requireTenant()->companyId;

        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            
            $assetId = (int) $data['asset_id'];
            $asset = $this->db->connection()->selectOne("SELECT branch_id, location_id, status FROM fixed_assets WHERE id = ? AND company_id = ? FOR UPDATE", [$assetId, $companyId]);

            if (!$asset || $asset['status'] !== 'active') {
                throw new BusinessException("Only active assets can be transferred.");
            }

            if ($asset['branch_id'] == $data['to_branch_id'] && $asset['location_id'] == ($data['to_location_id'] ?? null)) {
                throw new BusinessException("The asset is already in the specified destination.");
            }

            $transferData = [
                'company_id'       => $companyId,
                'asset_id'         => $assetId,
                'from_branch_id'   => $asset['branch_id'],
                'to_branch_id'     => $data['to_branch_id'],
                'from_location_id' => $asset['location_id'],
                'to_location_id'   => $data['to_location_id'] ?? null,
                'transfer_date'    => $data['transfer_date'],
                'notes'            => $data['notes'] ?? '',
                'created_by'       => $userId,
                'created_at'       => date('Y-m-d H:i:s')
            ];

            $transferId = $this->transferRepo->create($transferData);

            $this->db->connection()->update(
                "UPDATE fixed_assets SET branch_id = ?, location_id = ?, updated_at = ? WHERE id = ?",
                [$data['to_branch_id'], $data['to_location_id'] ?? null, date('Y-m-d H:i:s'), $assetId]
            );

            return $transferId;
        });
    }
}