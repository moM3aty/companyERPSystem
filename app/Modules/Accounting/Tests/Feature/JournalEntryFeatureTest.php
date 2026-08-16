<?php
// Path: app/Modules/Accounting/Tests/Feature/JournalEntryFeatureTest.php

declare(strict_types=1);

namespace App\Modules\Accounting\Tests\Feature;

use App\Core\Http\Request;
use App\Modules\Accounting\Http\Controllers\JournalEntryController;
use App\Modules\Accounting\Application\Services\JournalEntryService;
use App\Modules\Accounting\Infrastructure\Persistence\Repositories\JournalEntryRepository;
use PHPUnit\Framework\TestCase;

/**
 * Enterprise Feature Test: Journal Entry API
 * نختبر هنا الـ Controller وتعامله مع الـ Request لضمان أن المسار (Endpoint) يعمل من البداية للنهاية.
 */
class JournalEntryFeatureTest extends TestCase
{
    private JournalEntryController $controller;

    protected function setUp(): void
    {
        // In a real Laravel/Symfony app, we'd use the App container.
        // Here we mock the underlying services to test the Controller's feature isolation.
        $mockService = $this->createMock(JournalEntryService::class);
        $mockService->method('createAndPostEntry')->willReturn(101); // Returns simulated Entry ID

        $mockRepo = $this->createMock(JournalEntryRepository::class);

        $this->controller = new JournalEntryController($mockService, $mockRepo);
    }

    public function test_api_can_create_journal_entry_successfully(): void
    {
        // 1. Simulate an incoming POST request with balanced JSON payload
        $request = new Request([
            'entry_date' => '2026-08-16',
            'memo' => 'Test API Entry',
            'reference_type' => 'Manual',
            'lines' => [
                ['account_id' => 1010, 'debit' => 5000, 'credit' => 0],
                ['account_id' => 4000, 'debit' => 0, 'credit' => 5000],
            ],
            'action' => 'draft'
        ]);

        // Force request to expect JSON
        $request->headers->set('Accept', 'application/json');

        // 2. Call the Controller
        $response = $this->controller->store($request);

        // 3. Assertions
        $this->assertEquals(201, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals(101, $responseData['id']);
    }

    public function test_api_rejects_unbalanced_journal_entry(): void
    {
        // 1. Simulate a request with an Unbalanced payload (Missing 100 on credit)
        $request = new Request([
            'entry_date' => '2026-08-16',
            'memo' => 'Bad API Entry',
            'lines' => [
                ['account_id' => 1010, 'debit' => 5000, 'credit' => 0],
                ['account_id' => 4000, 'debit' => 0, 'credit' => 4900], 
            ]
        ]);
        $request->headers->set('Accept', 'application/json');

        // Note: The validation exception is normally thrown by the Request/DTO layer.
        // We expect the controller to catch it and return a 400 JSON response.
        $response = $this->controller->store($request);

        // Assertions
        $this->assertEquals(400, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('error', $responseData);
    }
}