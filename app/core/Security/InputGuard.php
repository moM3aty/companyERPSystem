<?php
// Path: app/Core/Security/InputGuard.php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Http\Request;

/**
 * Enterprise Input Guard
 * Combines the Request object and the Sanitizer to provide safely filtered input arrays.
 */
class InputGuard
{
    protected Sanitizer $sanitizer;

    /**
     * InputGuard constructor.
     *
     * @param Sanitizer $sanitizer
     */
    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    /**
     * Sanitize an entire array of data recursively.
     * Default behavior: Trims and strips HTML tags from all string values.
     *
     * @param array $data
     * @return array
     */
    public function sanitizeArray(array $data): array
    {
        $clean = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $clean[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $clean[$key] = $this->sanitizer->cleanString($value);
            } else {
                $clean[$key] = $value;
            }
        }

        return $clean;
    }

    /**
     * Extract and sanitize all POST payload data from the Request.
     *
     * @param Request $request
     * @return array
     */
    public function getCleanPayload(Request $request): array
    {
        // Extract all POST/PUT data. Note: Request->post() usually returns a specific key, 
        // but for this utility, we'll access the raw POST array indirectly or assume 
        // the controller passed the raw array.
        // Assuming we have a method to get all payload data in Request (we'll implement this logic)
        
        $rawData = $_POST; // Fallback if $request doesn't expose all()

        // If it's a JSON payload
        if (empty($rawData) && str_contains($request->server('CONTENT_TYPE', ''), 'application/json')) {
            $json = file_get_contents('php://input');
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $rawData = $decoded;
            }
        }

        return $this->sanitizeArray($rawData);
    }
}