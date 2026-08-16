<?php
// Path: app/Core/Contracts/TranslatorInterface.php

declare(strict_types=1);

namespace App\Core\Contracts;

/**
 * Enterprise Translator Interface
 * عقد موحد لنظام الترجمة لضمان استبدال محرك الترجمة مستقبلاً بدون تكسير الـ Controllers.
 */
interface TranslatorInterface
{
    /**
     * ترجمة مفتاح نصي إلى اللغة الحالية.
     *
     * @param string $key مفتاح الترجمة (مثال: 'messages.welcome')
     * @param array $replace مصفوفة المتغيرات للاستبدال (مثال: ['name' => 'Ahmed'])
     * @param string|null $locale رمز اللغة المراد الترجمة إليها (تجاهله يستخدم اللغة الافتراضية)
     * @return string
     */
    public function translate(string $key, array $replace = [], ?string $locale = null): string;

    /**
     * تغيير اللغة النشطة حالياً في النظام.
     *
     * @param string $locale
     * @return void
     */
    public function setLocale(string $locale): void;

    /**
     * جلب رمز اللغة النشطة حالياً.
     *
     * @return string
     */
    public function getLocale(): string;
}