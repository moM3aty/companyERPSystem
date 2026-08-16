<?php
// Path: app/Core/Monitoring/HealthCheck.php

declare(strict_types=1);

namespace App\Core\Monitoring;

use App\Core\Database\DatabaseManager;
use App\Core\Cache\CacheManager;

/**
 * Enterprise Health Check Service
 * يفحص استقرار وحالة مكونات النظام الأساسية (الداتابيز، الكاش، التخزين).
 * ضروري لعمليات الـ Load Balancing والمراقبة (مثل AWS Target Groups أو Kubernetes Liveness Probes).
 */
class HealthCheck
{
    protected DatabaseManager $db;
    protected CacheManager $cache;

    public function __construct(DatabaseManager $db, CacheManager $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
    }

    /**
     * إجراء الفحص الشامل وإرجاع مصفوفة التفاصيل.
     *
     * @return array
     */
    public function run(): array
    {
        $status = [
            'status' => 'healthy',
            'components' => []
        ];

        // 1. Database Check
        try {
            $this->db->connection()->selectOne("SELECT 1");
            $status['components']['database'] = 'ok';
        } catch (\Throwable $e) {
            $status['components']['database'] = 'failed';
            $status['status'] = 'unhealthy';
        }

        // 2. Cache Check
        try {
            $this->cache->set('health_ping', 'ok', 10);
            $status['components']['cache'] = $this->cache->get('health_ping') === 'ok' ? 'ok' : 'failed';
        } catch (\Throwable $e) {
            $status['components']['cache'] = 'failed';
            $status['status'] = 'unhealthy';
        }

        // 3. Storage Check (Directory Writable)
        $storagePath = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'storage';
        if (is_writable($storagePath)) {
            $status['components']['storage'] = 'ok';
        } else {
            $status['components']['storage'] = 'failed';
            $status['status'] = 'unhealthy';
        }

        $status['timestamp'] = date('Y-m-d\TH:i:s\Z');
        
        return $status;
    }
}