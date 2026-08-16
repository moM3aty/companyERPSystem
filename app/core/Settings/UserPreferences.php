<?php
// Path: app/Core/Settings/UserPreferences.php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Auth\AuthManager;
use App\Core\Exceptions\AuthorizationException;

/**
 * Enterprise User Preferences
 * واجهة لإدارة تفضيلات المستخدم الشخصية (الـ Theme المظلم/الفاتح، حجم الخط، إعدادات الإشعارات).
 * لا تتدخل في إعدادات الشركة لضمان الخصوصية.
 */
class UserPreferences
{
    protected SettingsManager $manager;
    protected AuthManager $auth;
    protected const SCOPE = Setting::SCOPE_USER;

    /**
     * UserPreferences constructor.
     *
     * @param SettingsManager $manager
     * @param AuthManager $auth
     */
    public function __construct(SettingsManager $manager, AuthManager $auth)
    {
        $this->manager = $manager;
        $this->auth = $auth;
    }


    /**
     * جلب ID المستخدم الحالي بصلاحية آمنة.
     *
     * @return int
     * @throws AuthorizationException
     */
    protected function getUserId(): int
    {
        $user = $this->auth->user();

        if (!$user) {
            throw new AuthorizationException("Cannot access user preferences. User is not authenticated.");
        }

        return $user->id;
    }

    /**
     * جلب تفضيل شخصي.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->manager->get(self::SCOPE, $this->getUserId(), $key, $default);
    }

    /**
     * حفظ تفضيل شخصي.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return void
     */
    public function set(string $key, mixed $value, string $type = 'string'): void
    {
        $this->manager->set(self::SCOPE, $this->getUserId(), $key, $value, $type);
    }

    /**
     * جلب كل التفضيلات كـ Array لتمريرها للـ Frontend عند تسجيل الدخول.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->manager->loadScope(self::SCOPE, $this->getUserId());
    }
}