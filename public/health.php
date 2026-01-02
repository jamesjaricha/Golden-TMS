<?php
/**
 * Golden TMS - Production Health Check Endpoint
 *
 * This file provides a simple health check endpoint for monitoring services.
 * Access via: https://your-domain.com/health.php
 *
 * Returns JSON with system status.
 */

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$health = [
    'status' => 'ok',
    'timestamp' => date('c'),
    'checks' => []
];

// Check 1: PHP Version
$health['checks']['php'] = [
    'status' => version_compare(PHP_VERSION, '8.2.0', '>=') ? 'ok' : 'error',
    'version' => PHP_VERSION,
    'required' => '8.2.0'
];

// Check 2: Required Extensions
$requiredExtensions = ['pdo', 'mbstring', 'openssl', 'json', 'curl', 'dom', 'xml'];
$missingExtensions = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}
$health['checks']['extensions'] = [
    'status' => empty($missingExtensions) ? 'ok' : 'error',
    'missing' => $missingExtensions
];

// Check 3: Storage Writable
$storagePath = __DIR__ . '/../storage';
$health['checks']['storage'] = [
    'status' => is_writable($storagePath) ? 'ok' : 'error',
    'writable' => is_writable($storagePath)
];

// Check 4: Bootstrap Cache Writable
$cachePath = __DIR__ . '/../bootstrap/cache';
$health['checks']['cache'] = [
    'status' => is_writable($cachePath) ? 'ok' : 'error',
    'writable' => is_writable($cachePath)
];

// Check 5: Environment File Exists
$envPath = __DIR__ . '/../.env';
$health['checks']['env'] = [
    'status' => file_exists($envPath) ? 'ok' : 'error',
    'exists' => file_exists($envPath)
];

// Check 6: Database Connection (via Laravel)
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    // Test database connection
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    $health['checks']['database'] = [
        'status' => 'ok',
        'connected' => true
    ];
} catch (Exception $e) {
    $health['checks']['database'] = [
        'status' => 'error',
        'connected' => false,
        'error' => $e->getMessage()
    ];
}

// Determine overall status
foreach ($health['checks'] as $check) {
    if ($check['status'] !== 'ok') {
        $health['status'] = 'error';
        break;
    }
}

// Set HTTP status code
http_response_code($health['status'] === 'ok' ? 200 : 503);

echo json_encode($health, JSON_PRETTY_PRINT);
