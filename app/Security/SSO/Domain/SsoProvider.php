<?php
// Path: app/Security/SSO/Domain/SsoProvider.php

declare(strict_types=1);

namespace App\Security\SSO\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: SSO Provider
 * يمثل مزود هوية خارجي للشركة (مثال: Microsoft Entra ID، Okta، Google Workspace).
 * يسمح بتفعيل الـ Single Sign-On للموظفين.
 */
class SsoProvider extends Entity
{
    protected array $casts = [
        'id'                       => 'integer',
        'company_id'               => 'integer',
        'provider_name'            => 'string', // 'azure_ad', 'okta', 'google'
        'protocol'                 => 'string', // 'saml2', 'oauth2', 'oidc'
        'metadata_url'             => 'string',
        'entity_id'                => 'string',
        'sso_url'                  => 'string',
        'x509_certificate'         => 'string',
        'client_id'                => 'string', // For OAuth/OIDC
        'client_secret'            => 'string', // For OAuth/OIDC
        'auto_provision_users'     => 'boolean', // إنشاء حساب تلقائي للموظف إذا لم يكن موجوداً (JIT Provisioning)
        'is_active'                => 'boolean',
    ];
}