<?php
// Path: app/Core/Localization/Translator.php

declare(strict_types=1);

namespace App\Core\Localization;

/**
 * Enterprise Translator Engine
 * محرك الترجمة الفعلي الذي يأخذ مفتاح النص (Key) ويقوم بتبديل المتغيرات (Placeholders)
 * لإنتاج النص النهائي باللغة المطلوبة.
 */
class Translator
{
    protected TranslationManager $manager;
    protected Locale $currentLocale;

    /**
     * Translator constructor.
     *
     * @param TranslationManager $manager
     * @param string $defaultLocale
     */
    public function __construct(TranslationManager $manager, string $defaultLocale = 'en')
    {
        $this->manager = $manager;
        $this->currentLocale = new Locale($defaultLocale);
    }

    /**
     * تغيير لغة الواجهة الحالية.
     *
     * @param string $locale
     * @return self
     */
    public function setLocale(string $locale): self
    {
        $this->currentLocale = new Locale($locale);
        return $this;
    }

    /**
     * جلب اللغة الحالية.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->currentLocale->code;
    }

    /**
     * ترجمة مفتاح نصي مع استبدال المتغيرات إن وجدت.
     *
     * @param string $key مفتاح الترجمة (مثال: 'messages.welcome')
     * @param array $replacements المتغيرات (مثال: ['name' => 'Ahmed'])
     * @param string|null $locale لغة معينة (إذا لم تمرر سيستخدم اللغة الحالية)
     * @return string
     */
    public function translate(string $key, array $replacements = [], ?string $locale = null): string
    {
        $targetLocale = $locale ? new Locale($locale) : $this->currentLocale;
        
        $line = $this->manager->getLine($targetLocale->code, $key);

        if ($line === null) {
            // كإجراء احتياطي في الأنظمة الكبرى، إذا لم نجد الترجمة نرجع المفتاح نفسه ليلاحظه المطور
            return $key;
        }

        if (empty($replacements)) {
            return $line;
        }

        return $this->makeReplacements($line, $replacements);
    }

    /**
     * استبدال المتغيرات داخل النص المترجم.
     *
     * @param string $line
     * @param array $replacements
     * @return string
     */
    protected function makeReplacements(string $line, array $replacements): string
    {
        foreach ($replacements as $key => $value) {
            // يدعم الصيغتين: :name أو {name}
            $line = str_replace(
                [':' . $key, '{' . $key . '}'],
                [(string) $value, (string) $value],
                $line
            );
        }

        return $line;
    }
}