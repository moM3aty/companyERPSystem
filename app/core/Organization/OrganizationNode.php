<?php
// Path: app/Core/Organization/OrganizationNode.php

declare(strict_types=1);

namespace App\Core\Organization;

use App\Core\Models\Entity;

/**
 * Enterprise Organization Node Entity
 * يمثل أي عنصر في شجرة الهيكل التنظيمي (سواء كان قطاع، قسم، أو فريق).
 * يعتمد على هيكلة (Adjacency List) باستخدام parent_id.
 */
class OrganizationNode extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'parent_id' => 'integer',
        'manager_id' => 'integer', // مدير هذه العقدة
        'cost_center_id' => 'integer', // مركز التكلفة المرتبط بها
        'name' => 'string',
        'type' => 'string', // 'division', 'department', 'team'
        'level' => 'integer', // عمق العقدة في الشجرة (Root = 0)
        'is_active' => 'boolean',
    ];

    /**
     * التحقق مما إذا كانت هذه العقدة هي الجذر الرئيسي (Root).
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->getAttribute('parent_id') === null;
    }
}