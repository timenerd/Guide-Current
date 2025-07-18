<?php
/**
 * GuideAI Production Configuration
 * 
 * IMPORTANT: This file should be copied to config.php and API keys should be set
 * via environment variables or secure configuration management.
 * 
 * NEVER commit actual API keys to version control!
 */

// Load environment variables if .env file exists
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

// API Keys - Use environment variables for security
define('CLAUDE_API_KEY', $_ENV['CLAUDE_API_KEY'] ?? getenv('CLAUDE_API_KEY') ?? '');
define('GEMINI_API_KEY', $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?? '');
define('OPENAI_API_KEY', $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY') ?? '');
define('OPENCAGE_API_KEY', $_ENV['OPENCAGE_API_KEY'] ?? getenv('OPENCAGE_API_KEY') ?? '');

// Application Settings
define('APP_NAME', 'GuideAI');
define('APP_VERSION', '2.0.0');
define('APP_ENVIRONMENT', 'production');

// Production Security Settings
define('DEBUG_MODE', false);
define('LOG_LEVEL', 'error');

// Rate Limiting (Production values)
define('RATE_LIMIT_REQUESTS', 50); // More conservative for production
define('RATE_LIMIT_WINDOW', 3600);

// Cache Settings
define('CACHE_ENABLED', true);
define('CACHE_DURATION', 600); // 10 minutes for production

// Security Settings
define('ALLOWED_ORIGINS', ['https://getguideai.com', 'https://www.getguideai.com']); // Restrict to your domain
define('API_TIMEOUT', 30);

// Feature Flags
define('ENABLE_PROGRESSIVE_DISPLAY', true);
define('ENABLE_VOICE_INPUT', true);
define('ENABLE_RESOURCE_LINKS', true);
define('ENABLE_LOCATION_SERVICES', true);

// Production Error Handling
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Security Headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.googletagmanager.com; style-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; img-src \'self\' data: https:; font-src \'self\' https://cdnjs.cloudflare.com; connect-src \'self\' https://api.anthropic.com https://api.openai.com https://generativelanguage.googleapis.com;');
}

// CORS Configuration (Production)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, ALLOWED_ORIGINS)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Rate Limiting Function
if (!function_exists('checkRateLimit')) {
    function checkRateLimit($identifier) {
        $cacheFile = __DIR__ . '/cache/rate_limit_' . md5($identifier) . '.json';
        $now = time();
        
        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if ($data && $now - $data['timestamp'] < RATE_LIMIT_WINDOW) {
                if ($data['count'] >= RATE_LIMIT_REQUESTS) {
                    return false;
                }
                $data['count']++;
            } else {
                $data = ['timestamp' => $now, 'count' => 1];
            }
        } else {
            $data = ['timestamp' => $now, 'count' => 1];
        }
        
        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        file_put_contents($cacheFile, json_encode($data));
        return true;
    }
}

// Cache Functions
if (!function_exists('getCache')) {
    function getCache($key) {
        if (!CACHE_ENABLED) return null;
        
        $cacheFile = __DIR__ . '/cache/' . md5($key) . '.json';
        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if ($data && time() - $data['timestamp'] < CACHE_DURATION) {
                return $data['value'];
            }
        }
        return null;
    }
}

if (!function_exists('setCache')) {
    function setCache($key, $value) {
        if (!CACHE_ENABLED) return false;
        
        $cacheFile = __DIR__ . '/cache/' . md5($key) . '.json';
        $data = [
            'timestamp' => time(),
            'value' => $value
        ];
        
        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        return file_put_contents($cacheFile, json_encode($data)) !== false;
    }
}

// Input Sanitization
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map('sanitizeInput', $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

// Initialize required directories
$directories = [
    __DIR__ . '/logs',
    __DIR__ . '/cache',
    __DIR__ . '/uploads'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Validate configuration
$requiredKeys = ['CLAUDE_API_KEY', 'GEMINI_API_KEY'];
$missingKeys = [];

foreach ($requiredKeys as $key) {
    if (!defined($key) || empty(constant($key))) {
        $missingKeys[] = $key;
    }
}

if (!empty($missingKeys) && APP_ENVIRONMENT === 'production') {
    error_log('Missing required API keys: ' . implode(', ', $missingKeys));
    http_response_code(500);
    die('Configuration error. Please check server logs.');
}
?> 