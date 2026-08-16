<?php
// Path: app/Core/Console/ConsoleKernel.php

declare(strict_types=1);

namespace App\Core\Console;

use App\Core\Bootstrap\Application;
use App\Core\Bootstrap\Container;
use Throwable;

/**
 * Enterprise Console Kernel
 * نواة تشغيل سطر الأوامر (CLI). تدير تنفيذ المهام المجدولة، وطابور العمليات (Queues)، 
 * وأوامر الـ Migrations بمعزل عن دورة حياة الـ HTTP.
 */
class ConsoleKernel
{
    protected Application $app;
    
    /**
     * خريطة الأوامر المتاحة.
     *
     * @var array
     */
    protected array $commands = [
        'migrate'       => \App\Core\Console\Commands\MigrateCommand::class,
        'queue:work'    => \App\Core\Console\Commands\QueueWorkCommand::class,
        'schedule:run'  => \App\Core\Console\Commands\ScheduleRunCommand::class,
    ];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * معالجة الطلب الوارد من سطر الأوامر.
     *
     * @param array $argv مصفوفة المدخلات من CLI
     * @return int رمز الخروج (0 = نجاح، 1 = فشل)
     */
    public function handle(array $argv): int
    {
        try {
            // 1. تشغيل التطبيق (تهيئة الـ Container والـ Config)
            $this->app->boot();

            // 2. تحليل الأمر
            $commandName = $argv[1] ?? null;

            if (!$commandName) {
                $this->printHelp();
                return 0;
            }

            if (!isset($this->commands[$commandName])) {
                $this->error("Command not found: {$commandName}");
                $this->printHelp();
                return 1;
            }

            // 3. جلب الـ Command وتسليمه للـ Container لحقن المتطلبات (DI)
            $commandClass = $this->commands[$commandName];
            $commandInstance = $this->app->make($commandClass);

            if (!method_exists($commandInstance, 'execute')) {
                $this->error("The command [{$commandClass}] must have an 'execute' method.");
                return 1;
            }

            // تمرير المتغيرات الإضافية (Arguments)
            $args = array_slice($argv, 2);

            // 4. التنفيذ
            return (int) $commandInstance->execute($args);

        } catch (Throwable $e) {
            $this->error("FATAL ERROR: " . $e->getMessage());
            echo $e->getTraceAsString() . PHP_EOL;
            return 1;
        }
    }

    protected function printHelp(): void
    {
        echo "\n\033[34mERP Pro System - Enterprise Console\033[0m\n\n";
        echo "Usage:\n";
        echo "  php bin/console [command]\n\n";
        echo "Available commands:\n";
        
        foreach (array_keys($this->commands) as $cmd) {
            echo "  \033[32m{$cmd}\033[0m\n";
        }
        echo "\n";
    }

    protected function error(string $message): void
    {
        echo "\033[31m{$message}\033[0m\n";
    }
}