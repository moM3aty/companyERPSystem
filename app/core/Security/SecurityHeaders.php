<?php
// Path: app/Core/Security/SecurityHeaders.php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Http\Middleware\MiddlewareInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use Closure;

/**
 * Enterprise Security Headers Middleware
 * يضيف ترويسات الأمان القياسية لكل استجابة لمنع هجمات (Clickjacking, MIME-Sniffing, XSS).
 */
class SecurityHeaders implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // يمنع تضمين النظام داخل iframe في مواقع أخرى (يحمي من Clickjacking)
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');

        // يمنع المتصفح من تخمين نوع الملف (يحمي من MIME-Sniffing)
        $response->setHeader('X-Content-Type-Options', 'nosniff');

        // يجبر المتصفح على استخدام HTTPS فقط لمدة سنة كاملة
        $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // سياسة المحتوى (CSP) الأساسية (يمكن تخصيصها لاحقاً حسب حاجة الواجهة الأمامية)
        $response->setHeader('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
        
        // يمنع إرسال مسار الصفحة الحالية للمواقع الخارجية في الـ Referer
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}