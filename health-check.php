<?php
/**
 * GuideAI Production Health Check
 * 
 * This script checks the health of your GuideAI installation
 * Run this periodically to ensure everything is working correctly
 */

// Prevent direct access in production
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['health'])) {
    http_response_code(403);
    die('Access denied');
}

header('Content-Type: application/json');

$health = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

// 1. Check PHP version
$health['checks']['php_version'] = [
    'status' => version_compare(PHP_VERSION, '8.0.0', '>=') ? 'pass' : 'fail',
    'current' => PHP_VERSION,
    'required' => '8.0.0+'
];

// 2. Check required PHP extensions
$required_extensions = ['mbstring', 'xml', 'ctype', 'iconv', 'intl', 'json'];
$health['checks']['extensions'] = [];
foreach ($required_extensions as $ext) {
    $health['checks']['extensions'][$ext] = [
        'status' => extension_loaded($ext) ? 'pass' : 'fail',
        'loaded' => extension_loaded($ext)
    ];
}

// 3. Check directory permissions
$directories = [
    'logs' => __DIR__ . '/logs',
    'cache' => __DIR__ . '/cache',
    'uploads' => __DIR__ . '/uploads'
];

$health['checks']['directories'] = [];
foreach ($directories as $name => $path) {
    $health['checks']['directories'][$name] = [
        'status' => is_dir($path) && is_writable($path) ? 'pass' : 'fail',
        'exists' => is_dir($path),
        'writable' => is_writable($path)
    ];
}

// 4. Check configuration
$health['checks']['configuration'] = [
    'config_exists' => [
        'status' => file_exists(__DIR__ . '/config.php') ? 'pass' : 'fail',
        'exists' => file_exists(__DIR__ . '/config.php')
    ]
];

// 5. Check API connectivity (if config exists)
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
    
    // Test Claude API
    if (defined('CLAUDE_API_KEY') && !empty(CLAUDE_API_KEY)) {
        $claude_test = testClaudeAPI();
        $health['checks']['api']['claude'] = $claude_test;
    }
    
    // Test Gemini API
    if (defined('GEMINI_API_KEY') && !empty(GEMINI_API_KEY)) {
        $gemini_test = testGeminiAPI();
        $health['checks']['api']['gemini'] = $gemini_test;
    }
}

// 6. Check disk space
$disk_free = disk_free_space(__DIR__);
$disk_total = disk_total_space(__DIR__);
$disk_used_percent = (($disk_total - $disk_free) / $disk_total) * 100;

$health['checks']['disk_space'] = [
    'status' => $disk_used_percent < 90 ? 'pass' : 'warn',
    'free_gb' => round($disk_free / 1024 / 1024 / 1024, 2),
    'total_gb' => round($disk_total / 1024 / 1024 / 1024, 2),
    'used_percent' => round($disk_used_percent, 2)
];

// 7. Check memory usage
$memory_limit = ini_get('memory_limit');
$memory_usage = memory_get_usage(true);
$memory_peak = memory_get_peak_usage(true);

$health['checks']['memory'] = [
    'status' => 'pass',
    'limit' => $memory_limit,
    'current_mb' => round($memory_usage / 1024 / 1024, 2),
    'peak_mb' => round($memory_peak / 1024 / 1024, 2)
];

// 8. Check error logs
$error_log = __DIR__ . '/logs/error.log';
$health['checks']['error_log'] = [
    'status' => file_exists($error_log) ? 'pass' : 'warn',
    'exists' => file_exists($error_log),
    'size_kb' => file_exists($error_log) ? round(filesize($error_log) / 1024, 2) : 0
];

// Determine overall status
$failed_checks = 0;
$warning_checks = 0;

foreach ($health['checks'] as $category => $checks) {
    if (is_array($checks)) {
        foreach ($checks as $check) {
            if (isset($check['status'])) {
                if ($check['status'] === 'fail') $failed_checks++;
                if ($check['status'] === 'warn') $warning_checks++;
            }
        }
    }
}

if ($failed_checks > 0) {
    $health['status'] = 'unhealthy';
} elseif ($warning_checks > 0) {
    $health['status'] = 'warning';
}

$health['summary'] = [
    'total_checks' => count($health['checks']),
    'failed' => $failed_checks,
    'warnings' => $warning_checks,
    'passed' => count($health['checks']) - $failed_checks - $warning_checks
];

// Return health status
echo json_encode($health, JSON_PRETTY_PRINT);

/**
 * Test Claude API connectivity
 */
function testClaudeAPI() {
    if (!defined('CLAUDE_API_KEY') || empty(CLAUDE_API_KEY)) {
        return ['status' => 'fail', 'error' => 'API key not configured'];
    }
    
    $url = 'https://api.anthropic.com/v1/messages';
    $data = [
        'model' => 'claude-3-sonnet-20240229',
        'max_tokens' => 10,
        'messages' => [
            ['role' => 'user', 'content' => 'Hello']
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-api-key: ' . CLAUDE_API_KEY,
        'anthropic-version: 2023-06-01'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['status' => 'fail', 'error' => 'CURL error: ' . $error];
    }
    
    if ($http_code === 200) {
        return ['status' => 'pass', 'response_code' => $http_code];
    } else {
        return ['status' => 'fail', 'error' => 'HTTP ' . $http_code, 'response' => $response];
    }
}

/**
 * Test Gemini API connectivity
 */
function testGeminiAPI() {
    if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY)) {
        return ['status' => 'fail', 'error' => 'API key not configured'];
    }
    
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . GEMINI_API_KEY;
    $data = [
        'contents' => [
            ['parts' => [['text' => 'Hello']]]
        ],
        'generationConfig' => [
            'maxOutputTokens' => 10
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['status' => 'fail', 'error' => 'CURL error: ' . $error];
    }
    
    if ($http_code === 200) {
        return ['status' => 'pass', 'response_code' => $http_code];
    } else {
        return ['status' => 'fail', 'error' => 'HTTP ' . $http_code, 'response' => $response];
    }
}
?> 