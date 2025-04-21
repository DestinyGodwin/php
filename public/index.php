<?php

// Auto-loading using Composer
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../routes/api.php';

// Initialize configuration
\App\Config\Config::init();

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Parse JSON request body for POST, PUT, PATCH methods
$requestData = [];
if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
    $input = file_get_contents('php://input');
    $requestData = json_decode($input, true) ?? [];
    
    // Also merge any form data
    if ($method === 'POST' && !empty($_POST)) {
        $requestData = array_merge($requestData, $_POST);
    }
}

// Add query parameters to request data
$requestData = array_merge($requestData, $_GET);

// Route the request
$response = routeApi($method, $uri, $requestData);

// Set HTTP status code
http_response_code($response['status']);

// Set response headers
foreach ($response['headers'] as $name => $value) {
    header("$name: $value");
}

// Output response body
echo $response['body'];