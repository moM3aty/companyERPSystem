<?php
// Path: app/Core/Api/ApiVersion.php

declare(strict_types=1);

namespace App\Core\Api;

use App\Core\Http\Request;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise API Version Manager
 * يدير إصدارات الـ API ويتحقق مما إذا كان التطبيق (Frontend/Mobile) يستخدم إصداراً مدعوماً.
 */
class ApiVersion
{
    public const CURRENT_VERSION = 'v2';
    
    protected array $supportedVersions = ['v1', 'v2'];

    /**
     * التحقق من إصدار الـ API المطلوب عبر ترويسات الطلب.
     *
     * @param Request $request
     * @return string
     * @throws BusinessException
     */
    public function resolveAndValidate(Request $request): string
    {
        // الممارسة الأمثل هي إرسال الإصدار في ترويسة Accept
        // مثال: Accept: application/vnd.erp.v2+json
        $acceptHeader = $request->server('HTTP_ACCEPT', '');
        
        preg_match('/vnd\.erp\.(v[0-9]+)\+json/', $acceptHeader, $matches);
        
        $requestedVersion = $matches[1] ?? self::CURRENT_VERSION;

        if (!in_array($requestedVersion, $this->supportedVersions, true)) {
            throw new BusinessException("API Version [{$requestedVersion}] is no longer supported. Please upgrade your client.", 426); // 426 Upgrade Required
        }

        return $requestedVersion;
    }
}