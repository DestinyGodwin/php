<?php

use App\Controllers\VirtualAccountController;
use App\Controllers\VeluxController;

/**
 * Define API routes
 * 
 * @param string $method HTTP method
 * @param string $path URL path
 * @param array $requestData Request data
 * @return array|null Response if route matches, null otherwise
 */
function routeApi($method, $path, $requestData)
{
    // Route definitions
    $routes = [
        // Virtual Account Routes
        'POST /api/virtual-accounts' => function($data) {
            $controller = new VirtualAccountController();
            return $controller->create($data);
        },
        'GET /api/test' => function($data) {
            return [
                'status' => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode([
                    'success' => true,
                    'message' => 'API is working!'
                ])
            ];
        },

        // VeluxSwap Routes
        'GET /api/rates' => function($data) {
            $controller = new VeluxController();
            return [
                'status' => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($controller->rates())
            ];
        },

        'GET /api/transactions' => function($data) {
            $controller = new VeluxController();
            return [
                'status' => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($controller->transactions())
            ];
        },

        'POST /api/sell-crypto' => function($data) {
            $controller = new VeluxController();
            return [
                'status' => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($controller->sellCrypto($data))
            ];
        },

        'POST /api/sell-giftcard' => function($data) {
            $controller = new VeluxController();
            return [
                'status' => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($controller->sellGiftCard($data))
            ];
        },

        'GET /api/giftcard-transactions' => function($data) {
            $controller = new VeluxController();
            return [
                'status' => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($controller->giftCardTransactions())
            ];
        },
    ];

    $routeKey = "$method $path";
    if (isset($routes[$routeKey])) {
        $handler = $routes[$routeKey];
        return $handler($requestData);
    }

    return [
        'status' => 404,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode([
            'success' => false,
            'message' => 'Route not found'
        ])
    ];
}
