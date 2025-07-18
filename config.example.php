<?php
/**
 * GuideAI Configuration Template
 * 
 * Copy this file to config.php and add your API keys
 * 
 * IMPORTANT: Never commit your actual API keys to version control!
 * Add config.php to your .gitignore file.
 */

// Claude AI API Key (Anthropic)
// Get your key from: https://console.anthropic.com/
define('CLAUDE_API_KEY', 'your_claude_api_key_here');

// Google Gemini API Key
// Get your key from: https://makersuite.google.com/app/apikey
define('GEMINI_API_KEY', 'your_gemini_api_key_here');

// OpenAI API Key (Fallback)
// Get your key from: https://platform.openai.com/api-keys
define('CHATGPT_API_KEY', 'your_openai_api_key_here');

// OpenCage API Key (for location services)
// Get your key from: https://opencagedata.com/users/sign_up
define('OPENCAGE_API_KEY', 'your_opencage_api_key_here');

// Application Settings
define('APP_NAME', 'GuideAI');
define('APP_VERSION', '2.0.0');
define('APP_ENVIRONMENT', 'production'); // production, development, testing

// Debug Settings
define('DEBUG_MODE', false);
define('LOG_LEVEL', 'error'); // debug, info, warning, error

// Rate Limiting
define('RATE_LIMIT_REQUESTS', 100); // requests per hour
define('RATE_LIMIT_WINDOW', 3600); // seconds

// Cache Settings
define('CACHE_ENABLED', true);
define('CACHE_DURATION', 300); // seconds (5 minutes)

// Security Settings
define('ALLOWED_ORIGINS', ['*']); // Set to specific domains in production
define('API_TIMEOUT', 30); // seconds

// Feature Flags
define('ENABLE_PROGRESSIVE_DISPLAY', true);
define('ENABLE_VOICE_INPUT', true);
define('ENABLE_RESOURCE_LINKS', true);
define('ENABLE_LOCATION_SERVICES', true);

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Logging Configuration
if (!function_exists('debugLog')) {
    function debugLog($message, $context = []) {
        if (DEBUG_MODE) {
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[$timestamp] $message";
            if (!empty($context)) {
                $logMessage .= ' ' . json_encode($context);
            }
            error_log($logMessage . PHP_EOL, 3, __DIR__ . '/logs/debug.log');
        }
    }
}

// Security Headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    if (APP_ENVIRONMENT === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// CORS Configuration
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array('*', ALLOWED_ORIGINS) || in_array($origin, ALLOWED_ORIGINS)) {
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
                    return false; // Rate limit exceeded
                }
                $data['count']++;
            } else {
                $data = ['timestamp' => $now, 'count' => 1];
            }
        } else {
            $data = ['timestamp' => $now, 'count' => 1];
        }
        
        // Ensure cache directory exists
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
        
        // Ensure cache directory exists
        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        return file_put_contents($cacheFile, json_encode($data)) !== false;
    }
}

// Validation Functions
if (!function_exists('validateApiKey')) {
    function validateApiKey($key) {
        return !empty($key) && strlen($key) > 10;
    }
}

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
    if (!defined($key) || constant($key) === 'your_' . strtolower($key) . '_here') {
        $missingKeys[] = $key;
    }
}

if (!empty($missingKeys) && APP_ENVIRONMENT === 'production') {
    error_log('Missing required API keys: ' . implode(', ', $missingKeys));
}

// Load environment-specific configuration
$envConfigFile = __DIR__ . '/config.' . APP_ENVIRONMENT . '.php';
if (file_exists($envConfigFile)) {
    require_once $envConfigFile;
} 