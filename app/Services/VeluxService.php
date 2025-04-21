<?php

namespace App\Services;

use App\Config\Config;

class VeluxService
{
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $this->baseUrl = Config::getBaseUrl();
        $this->apiKey = Config::getVeluxApiKey();
    }

    private function request($method, $endpoint, $data = [])
    {
        $ch = curl_init();

        $url = $this->baseUrl . $endpoint;

        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $this->apiKey,
            'Content-Type: application/json'
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function getRates()
    {
        return $this->request('GET', '/rates');
    }

    public function getTransactions()
    {
        return $this->request('GET', '/transaction');
    }

    public function createSellCrypto($payload)
    {
        return $this->request('POST', '/transaction/create/sell', $payload);
    }

    public function createGiftCard($payload)
    {
        return $this->request('POST', '/giftcard', $payload);
    }

    public function getGiftCardTransactions()
    {
        return $this->request('GET', '/giftcard');
    }
}
