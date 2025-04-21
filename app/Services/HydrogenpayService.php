<?php

namespace App\Services;

use App\Config\Config;
use App\Models\VirtualAccount;

class HydrogenPayService
{
    private $apiUrl;
    private $authKey;

    public function __construct()
    {
        $config = Config::getHydrogenPayConfig();
        $this->apiUrl = $config['api_url'];
        $this->authKey = $config['auth_key'];
    }

    /**
     * Create a virtual account using Hydrogen Pay API
     * 
     * @param VirtualAccount $virtualAccount
     * @return array Response with status and data
     */
    public function createVirtualAccount(VirtualAccount $virtualAccount)
    {
        // Validate the virtual account data
        $validationErrors = $virtualAccount->validate();
        if (!empty($validationErrors)) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validationErrors
            ];
        }

        // Prepare the API request
        $requestData = $virtualAccount->toArray();
        
        // Remove empty fields
        $requestData = array_filter($requestData, function($value) {
            return $value !== null && $value !== '';
        });

        // Make API request to Hydrogen Pay
        $response = $this->makeApiRequest($this->apiUrl, $requestData);
        
        // Process the response
        if ($response['success']) {
            if (isset($response['data']['data'])) {
                $virtualAccount->setResponseData($response['data']['data']);
                return [
                    'success' => true,
                    'data' => $virtualAccount->getResponseData()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid response format from Hydrogen Pay API',
                    'response' => $response['data']
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to create virtual account',
                'response' => $response['data'] ?? null
            ];
        }
    }

    /**
     * Make an API request to Hydrogen Pay
     * 
     * @param string $url API endpoint
     * @param array $data Request data
     * @return array Response with status and data
     */
    private function makeApiRequest($url, $data)
    {
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->authKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'message' => 'cURL Error: ' . $error,
                'data' => null
            ];
        }

        $decodedResponse = json_decode($response, true);
        
        if (!$decodedResponse) {
            return [
                'success' => false,
                'message' => 'Failed to decode API response',
                'data' => $response
            ];
        }

        // Check if the response indicates success based on Hydrogen Pay's status codes
        $isSuccess = isset($decodedResponse['statusCode']) && $decodedResponse['statusCode'] === '90000';
        
        return [
            'success' => $isSuccess,
            'message' => $decodedResponse['message'] ?? ($isSuccess ? 'Success' : 'API request failed'),
            'data' => $decodedResponse
        ];
    }
}