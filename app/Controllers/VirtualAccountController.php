<?php

namespace App\Controllers;

use App\Models\VirtualAccount;
use App\Services\HydrogenPayService;

class VirtualAccountController
{
    private $hydrogenPayService;

    public function __construct()
    {
        $this->hydrogenPayService = new HydrogenPayService();
    }

    /**
     * Create a new virtual account
     * 
     * @param array $requestData Request data
     * @return array Response with status and data
     */
    public function create($requestData)
    {
        // Validate request data
        if (empty($requestData)) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'No data provided'
            ], 400);
        }

        // Create and populate the virtual account model
        $virtualAccount = new VirtualAccount($requestData);
        
        // Create the virtual account through the service
        $result = $this->hydrogenPayService->createVirtualAccount($virtualAccount);
        
        if ($result['success']) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Virtual account created successfully',
                'data' => $result['data']
            ], 201);
        } else {
            $statusCode = isset($result['response']['statusCode']) ? 400 : 500;
            return $this->jsonResponse([
                'success' => false,
                'message' => $result['message'],
                'errors' => $result['errors'] ?? [],
                'response' => $result['response'] ?? null
            ], $statusCode);
        }
    }

    /**
     * Format JSON response with appropriate headers
     * 
     * @param array $data Response data
     * @param int $statusCode HTTP status code
     * @return array Formatted response
     */
    private function jsonResponse($data, $statusCode = 200)
    {
        return [
            'status' => $statusCode,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($data)
        ];
    }
}