<?php
// Path: app/Core/Events/EventListener.php

declare(strict_types=1);

namespace App\Core\Events;

/**
 * Enterprise Event Listener Interface
 * العقد الذي يجب أن يلتزم به أي كلاس وظيفته الاستماع لحدث معين وتنفيذ إجراء استجابةً له.
 */
interface EventListener
{
    /**
     * معالجة الحدث عند وقوعه.
     *
     * @param Event $event
     * @return void
     */
    public function handle(Event $event): void;
}