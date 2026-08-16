<?php
// Path: app/Core/Http/Middleware/PermissionMiddleware.php

declare(strict_types=1);

namespace App\Core\Http\Middleware;

use Closure;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Authorization\Gate;
use App\Core\Exceptions\AuthorizationException;

/**
 * Enterprise Permission Middleware
 * يقوم بحماية الروابط بناءً على صلاحيات المستخدم المحددة عبر الـ Gate.
 * مثال للاستخدام في الـ Route: permission:sales,invoice,create
 */
class PermissionMiddleware implements MiddlewareInterface
{
    protected Gate $gate;
    
    protected string $module;
    protected string $resource;
    protected string $action;

    /**
     * PermissionMiddleware constructor.
     *
     * @param Gate $gate
     */
    public function __construct(Gate $gate)
    {
        $this->gate = $gate;
    }

    /**
     * إعداد البارامترات المطلوبة لفحص الصلاحية.
     *
     * @param string $module
     * @param string $resource
     * @param string $action
     * @return self
     */
    public function setParameters(string $module, string $resource, string $action): self
    {
        $this->module = $module;
        $this->resource = $resource;
        $this->action = $action;
        return $this;
    }

    /**
     * Process an incoming server request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws AuthorizationException
     */
    public function process(Request $request, Closure $next): Response
    {
        if (empty($this->module) || empty($this->resource) || empty($this->action)) {
            throw new \RuntimeException("PermissionMiddleware requires module, resource, and action parameters.");
        }

        // تفويض عملية الفحص لـ الـ Gate الذي سيقوم برمي استثناء AuthorizationException في حال الفشل
        $this->gate->authorize($this->module, $this->resource, $this->action);

        // المستخدم لديه الصلاحية، مرر الطلب للطبقة التالية
        return $next($request);
    }
}