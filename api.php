<?php
// Enhanced api.php - Complete AI System Integration with Resource Linking
// FIXED VERSION - Resolves character encoding and JSON issues

// Error handling and security headers
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(0);

// Set UTF-8 encoding
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Clean any output buffer
if (ob_get_level()) {
    ob_clean();
}

// Set proper headers with explicit UTF-8
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, Accept-Language');

// Debug logging function
function debugLog($message, $data = null) {
    $logMessage = "[" . date('Y-m-d H:i:s') . "] " . $message;
    if ($data !== null) {
        $logMessage .= " | Data: " . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    error_log($logMessage);
}

// Enhanced response function with better JSON handling
function sendResponse($data, $code = 200) {
    http_response_code($code);
    
    // Ensure we have the required structure
    if (!isset($data['success'])) {
        $data['success'] = ($code >= 200 && $code < 300);
    }
    
    if (!isset($data['timestamp'])) {
        $data['timestamp'] = date('c');
    }
    
    // Clean and validate all strings in the data
    $data = cleanDataForJson($data);
    
    // Use JSON_UNESCAPED_UNICODE to handle special characters properly
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE);
    
    if ($json === false) {
        $fallback = [
            'success' => false,
            'error' => 'Failed to encode JSON response',
            'json_error' => json_last_error_msg(),
            'timestamp' => date('c')
        ];
        echo json_encode($fallback, JSON_UNESCAPED_UNICODE);
    } else {
        echo $json;
    }
    exit;
}

// Function to clean data for JSON encoding
function cleanDataForJson($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = cleanDataForJson($value);
        }
    } elseif (is_string($data)) {
        // Ensure UTF-8 encoding and remove any invalid characters
        $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        // Remove any non-printable characters except newlines and tabs
        $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $data);
    }
    return $data;
}

// Load environment variables
function loadEnvironment() {
    $envPaths = [
        dirname(__DIR__) . '/../.env',
        __DIR__ . '/../.env',
        $_SERVER['DOCUMENT_ROOT'] . '/../.env'
    ];
    
    foreach ($envPaths as $envPath) {
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0 || empty(trim($line))) continue;
                
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value, " \t\n\r\0\x0B\"'");
                    
                    $_ENV[$name] = $value;
                    putenv("$name=$value");
                }
            }
            debugLog("Environment loaded from: $envPath");
            return true;
        }
    }
    
    debugLog("No .env file found, using system environment variables");
    return false;
}

// Safe environment variable getter
function getEnvVar($key, $default = null) {
    $value = $_ENV[$key] ?? getenv($key) ?? $default;
    
    // Don't log API keys, just their existence
    if (strpos($key, 'API_KEY') !== false) {
        debugLog("Environment variable $key: " . ($value ? 'SET' : 'NOT SET'));
    } else {
        debugLog("Environment variable $key: " . ($value ?: 'default'));
    }
    
    return $value;
}

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendResponse(['status' => 'ok', 'message' => 'CORS preflight successful']);
}

// Handle GET requests for status check
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $apiKey = getEnvVar('OPENAI_API_KEY');
    if (!$apiKey || $apiKey === 'your-openai-api-key') {
        if (defined('OPENAI_API_KEY')) {
            $apiKey = OPENAI_API_KEY;
        }
    }
    
    sendResponse([
        'success' => true,
        'status' => 'operational',
        'api_configured' => !empty($apiKey) && $apiKey !== 'your-openai-api-key',
        'timestamp' => date('c')
    ]);
}

// Only allow POST requests for chat
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(['success' => false, 'error' => 'Only POST method allowed'], 405);
}

// Load environment
loadEnvironment();

// Load configuration file for API keys
$config = null;
if (file_exists(__DIR__ . '/config.php')) {
    try {
        $config = include __DIR__ . '/config.php';
        debugLog("Configuration file loaded successfully");
    } catch (Exception $e) {
        debugLog("Configuration file error: " . $e->getMessage());
    }
} else {
    debugLog("Configuration file not found");
}

// Load API keys from secret files if not in environment
$secretFiles = [
    'OPENAI_API_KEY' => 'openai.secret.php',
    'GEMINI_API_KEY' => 'gemini.secret.php',
    'CLAUDE_API_KEY' => 'claude.secret.php'
];

foreach ($secretFiles as $envVar => $secretFile) {
    if (!getEnvVar($envVar) && file_exists(__DIR__ . '/' . $secretFile)) {
        try {
            require_once __DIR__ . '/' . $secretFile;
            $constantName = strtoupper($envVar);
            if (defined($constantName)) {
                $_ENV[$envVar] = constant($constantName);
                putenv($envVar . '=' . constant($constantName));
                debugLog("$envVar loaded from secret file");
            }
        } catch (Exception $e) {
            debugLog("Secret file error for $secretFile: " . $e->getMessage());
        }
    }
}

// Parse input with better error handling
$input = file_get_contents('php://input');
if (!$input) {
    sendResponse(['success' => false, 'error' => 'No input data received'], 400);
}

debugLog("Raw input received", substr($input, 0, 200) . "...");

// Clean input before JSON parsing
$input = mb_convert_encoding($input, 'UTF-8', 'UTF-8');
$data = json_decode($input, true);

if (!$data) {
    sendResponse([
        'success' => false, 
        'error' => 'Invalid JSON format', 
        'json_error' => json_last_error_msg(),
        'input_preview' => substr($input, 0, 100)
    ], 400);
}

debugLog("Parsed input data", $data);

// Check for action-based requests
$action = $data['action'] ?? '';
if ($action === 'test') {
    // Test connection endpoint
    $apiKey = getEnvVar('OPENAI_API_KEY');
    if (!$apiKey || $apiKey === 'your-openai-api-key') {
        if (defined('OPENAI_API_KEY')) {
            $apiKey = OPENAI_API_KEY;
        }
    }
    
    if (!$apiKey || $apiKey === 'your-openai-api-key') {
        sendResponse(['success' => false, 'error' => 'OpenAI API key not configured'], 400);
    }
    
    sendResponse([
        'success' => true,
        'data' => [
            'status' => 'connected',
            'message' => 'OpenAI API is working'
        ],
        'timestamp' => date('c')
    ]);
}

// Validate required fields for chat requests
$question = trim($data['question'] ?? '');
if (!$question) {
    sendResponse(['success' => false, 'error' => 'Question is required'], 400);
}

if (strlen($question) > 1000) {
    sendResponse(['success' => false, 'error' => 'Question too long (max 1000 characters)'], 400);
}

// Extract context with UTF-8 safety
$language = isset($data['language']) ? mb_convert_encoding($data['language'], 'UTF-8', 'UTF-8') : 'en';
$userLocation = isset($data['user_location']) ? mb_convert_encoding($data['user_location'], 'UTF-8', 'UTF-8') : 'Bend, Oregon, US';
$urgency = $data['urgency'] ?? 'normal';
$familyContext = $data['family_context'] ?? true;

debugLog("Processing request", [
    'question_length' => strlen($question),
    'language' => $language,
    'location' => $userLocation,
    'urgency' => $urgency
]);

// Load Parsedown from vendor if available (for markdown processing)
$parsedownAvailable = false;

// First priority: Real Parsedown from vendor (Composer)
if (file_exists(__DIR__ . '/vendor/erusev/parsedown/Parsedown.php')) {
    try {
        require_once __DIR__ . '/vendor/erusev/parsedown/Parsedown.php';
        $parsedownAvailable = class_exists('Parsedown');
        debugLog("Parsedown loaded successfully from vendor", ['version' => Parsedown::version ?? 'unknown']);
    } catch (Exception $e) {
        debugLog("Vendor Parsedown failed to load", ['error' => $e->getMessage()]);
        $parsedownAvailable = false;
    }
}

// Second priority: Autoload via Composer (if vendor/autoload.php exists)
if (!$parsedownAvailable && file_exists(__DIR__ . '/vendor/autoload.php')) {
    try {
        require_once __DIR__ . '/vendor/autoload.php';
        $parsedownAvailable = class_exists('Parsedown');
        debugLog("Parsedown loaded via Composer autoload", ['available' => $parsedownAvailable]);
    } catch (Exception $e) {
        debugLog("Composer autoload failed", ['error' => $e->getMessage()]);
        $parsedownAvailable = false;
    }
}

// Last resort: Custom Parsedown (basic implementation)
if (!$parsedownAvailable && file_exists(__DIR__ . '/Parsedown.php')) {
    try {
        require_once __DIR__ . '/Parsedown.php';
        $parsedownAvailable = class_exists('Parsedown');
        debugLog("Parsedown loaded from custom implementation", ['available' => $parsedownAvailable]);
    } catch (Exception $e) {
        debugLog("Custom Parsedown failed to load", ['error' => $e->getMessage()]);
        $parsedownAvailable = false;
    }
}

if (!$parsedownAvailable) {
    debugLog("No Parsedown implementation available - using basic text formatting");
}

try {
    
    // Generate AI response
    $aiResponse = generateAIResponse($question, $language, $userLocation, $urgency, $familyContext);
    debugLog("AI response generated", ['length' => strlen($aiResponse), 'success' => !empty($aiResponse)]);
    
    if (!$aiResponse) {
        throw new Exception('No response generated from AI system');
    }
    
    // Ensure UTF-8 encoding of AI response
    $aiResponse = mb_convert_encoding($aiResponse, 'UTF-8', 'UTF-8');
    
    // Process with Parsedown if available, otherwise use basic formatting
    $processedResponse = $aiResponse;
    if ($parsedownAvailable) {
        try {
            $parsedown = new Parsedown();
            $parsedown->setSafeMode(true);
            
            // Check if content looks like markdown - more comprehensive detection
            $hasMarkdownHeaders = preg_match('/^#{1,6}\s/m', $aiResponse);
            $hasMarkdownLists = preg_match('/^[-*]\s/m', $aiResponse);
            $hasMarkdownBold = preg_match('/\*\*.*?\*\*/', $aiResponse);
            $hasMarkdownItalic = preg_match('/\*[^*].*?\*/', $aiResponse);
            $isMarkdownContent = $hasMarkdownHeaders || $hasMarkdownLists || $hasMarkdownBold || $hasMarkdownItalic;
            
            debugLog("Markdown detection", [
                'has_headers' => $hasMarkdownHeaders,
                'has_lists' => $hasMarkdownLists, 
                'has_bold' => $hasMarkdownBold,
                'has_italic' => $hasMarkdownItalic,
                'is_markdown' => $isMarkdownContent,
                'sample_content' => substr($aiResponse, 0, 200)
            ]);
            
            if ($isMarkdownContent) {
                $processedResponse = $parsedown->text($aiResponse);
                debugLog("Parsedown processing completed", ['original_length' => strlen($aiResponse), 'processed_length' => strlen($processedResponse)]);
                
                // Check if headers were actually converted
                $headerConversionCheck = preg_match('/<h[1-6]>/', $processedResponse);
                debugLog("Header conversion check", ['headers_found_in_output' => $headerConversionCheck]);
                
            } else {
                // If no markdown detected, try basic header conversion anyway
                $processedResponse = $aiResponse;
                
                // Convert headers - more robust patterns
                $processedResponse = preg_replace('/^###\s+(.+)$/m', '<h3>$1</h3>', $processedResponse);
                $processedResponse = preg_replace('/^##\s+(.+)$/m', '<h2>$1</h2>', $processedResponse);
                $processedResponse = preg_replace('/^#\s+(.+)$/m', '<h1>$1</h1>', $processedResponse);
                
                // Convert other markdown elements
                $processedResponse = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $processedResponse);
                $processedResponse = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $processedResponse);
                
                // Convert line breaks and escape HTML
                $processedResponse = nl2br(htmlspecialchars($processedResponse, ENT_QUOTES, 'UTF-8'));
                debugLog("Basic HTML formatting applied with header conversion");
            }
            
        } catch (Exception $e) {
            debugLog("Parsedown processing failed", ['error' => $e->getMessage()]);
            $processedResponse = nl2br(htmlspecialchars($aiResponse, ENT_QUOTES, 'UTF-8'));
            debugLog("Fallback formatting applied");
        }
    } else {
        // Fallback: Enhanced basic formatting with UTF-8 safety
        debugLog("Using enhanced basic formatting (no Parsedown)");
        
        // Apply basic markdown-like formatting
        $processedResponse = $aiResponse;
        
        // Convert headers - more robust patterns
        $originalResponse = $processedResponse;
        $processedResponse = preg_replace('/^###\s+(.+)$/m', '<h3>$1</h3>', $processedResponse);
        $processedResponse = preg_replace('/^##\s+(.+)$/m', '<h2>$1</h2>', $processedResponse);
        $processedResponse = preg_replace('/^#\s+(.+)$/m', '<h1>$1</h1>', $processedResponse);
        
        // Debug header conversion
        $headerCount = preg_match_all('/^#{1,6}\s/m', $originalResponse, $matches);
        debugLog("Header conversion debug", [
            'original_headers_found' => $headerCount,
            'sample_headers' => array_slice($matches[0] ?? [], 0, 3),
            'conversion_applied' => $processedResponse !== $originalResponse
        ]);
        
        // Convert bold and italic
        $processedResponse = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $processedResponse);
        $processedResponse = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $processedResponse);
        
        // Convert lists
        $processedResponse = preg_replace('/^- (.*$)/m', '<li>$1</li>', $processedResponse);
        $processedResponse = preg_replace('/^\* (.*$)/m', '<li>$1</li>', $processedResponse);
        
        // Wrap consecutive list items in <ul> tags
        $processedResponse = preg_replace('/(<li>.*?<\/li>)(\s*<li>.*?<\/li>)*/s', '<ul>$0</ul>', $processedResponse);
        
        // Convert line breaks
        $processedResponse = nl2br($processedResponse);
        
        debugLog("Enhanced basic formatting applied");
    }
    
    // Final cleanup: ensure any remaining ### patterns are converted
    if (strpos($processedResponse, '###') !== false) {
        $processedResponse = preg_replace('/###\s+(.+?)(?:\n|$)/m', '<h3>$1</h3>', $processedResponse);
        debugLog("Final cleanup: converted remaining ### patterns");
    }
    
    // Additional cleanup for any remaining markdown patterns
    $processedResponse = preg_replace('/^##\s+(.+?)(?:\n|$)/m', '<h2>$1</h2>', $processedResponse);
    $processedResponse = preg_replace('/^#\s+(.+?)(?:\n|$)/m', '<h1>$1</h1>', $processedResponse);
    
    // Convert any remaining markdown formatting
    $processedResponse = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $processedResponse);
    $processedResponse = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $processedResponse);
    
    // Convert any remaining list items
    $processedResponse = preg_replace('/^- (.*$)/m', '<li>$1</li>', $processedResponse);
    $processedResponse = preg_replace('/^\* (.*$)/m', '<li>$1</li>', $processedResponse);
    
    // Wrap any consecutive list items in <ul> tags
    $processedResponse = preg_replace('/(<li>.*?<\/li>)(\s*<li>.*?<\/li>)*/s', '<ul>$0</ul>', $processedResponse);
    
    // Comprehensive final markdown cleanup - ensure all markdown is converted
    $originalProcessed = $processedResponse;
    
    // Convert any remaining headers (more aggressive patterns)
    $processedResponse = preg_replace('/^###\s*(.+?)(?:\n|$)/m', '<h3>$1</h3>', $processedResponse);
    $processedResponse = preg_replace('/^##\s*(.+?)(?:\n|$)/m', '<h2>$1</h2>', $processedResponse);
    $processedResponse = preg_replace('/^#\s*(.+?)(?:\n|$)/m', '<h1>$1</h1>', $processedResponse);
    
    // Convert any remaining inline formatting
    $processedResponse = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $processedResponse);
    $processedResponse = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $processedResponse);
    
    // Convert any remaining list items
    $processedResponse = preg_replace('/^- (.*$)/m', '<li>$1</li>', $processedResponse);
    $processedResponse = preg_replace('/^\* (.*$)/m', '<li>$1</li>', $processedResponse);
    
    // Wrap any remaining consecutive list items
    $processedResponse = preg_replace('/(<li>.*?<\/li>)(\s*<li>.*?<\/li>)*/s', '<ul>$0</ul>', $processedResponse);
    
    // Log if any changes were made
    if ($processedResponse !== $originalProcessed) {
        debugLog("Comprehensive markdown cleanup applied", [
            'changes_made' => true,
            'original_length' => strlen($originalProcessed),
            'final_length' => strlen($processedResponse)
        ]);
    }
    
    // Get related readings
    $relatedReadings = [];
    try {
        $relatedReadings = getRelatedReadings($question, $language);
        debugLog("Related readings generated", ['count' => count($relatedReadings)]);
    } catch (Exception $e) {
        debugLog("Related readings failed", ['error' => $e->getMessage()]);
        $relatedReadings = [];
    }
    
    // Generate Gemini resources
    $geminiResources = [];
    try {
        $geminiResources = generateGeminiResources($question, $userLocation, $language);
        debugLog("Gemini resources generated", ['count' => count($geminiResources)]);
    } catch (Exception $e) {
        debugLog("Gemini resources failed", ['error' => $e->getMessage()]);
        $geminiResources = [];
    }
    
    // Check for emergency content
    $isEmergency = isEmergencyQuestion($question);
    debugLog("Emergency check", ['is_emergency' => $isEmergency]);
    
    // Enhance response with resources if available
    $enhancedResponse = $processedResponse;
    
    // Initialize Resource Linking System for enhanced resource detection
    $resourceLinkingSystem = null;
    try {
        if (file_exists(__DIR__ . '/ResourceLinkingSystem.php')) {
            require_once __DIR__ . '/ResourceLinkingSystem.php';
            $resourceLinkingSystem = new ResourceLinkingSystem();
            debugLog("Resource Linking System initialized");
        }
    } catch (Exception $e) {
        debugLog("Resource Linking System failed to initialize", ['error' => $e->getMessage()]);
    }
    
    // Use Resource Linking System to enhance the response with relevant resources
    if ($resourceLinkingSystem) {
        try {
            $enhancedResponse = $resourceLinkingSystem->enhanceResponseWithLinks($enhancedResponse, $userLocation);
            debugLog("Resource linking applied to response");
            
            // Also link phone numbers in the response
            $enhancedResponse = $resourceLinkingSystem->linkPhoneNumbersInHtml($enhancedResponse);
            debugLog("Phone number linking applied to response");
            
            // Final markdown cleanup after resource linking
            if (strpos($enhancedResponse, '###') !== false) {
                $enhancedResponse = preg_replace('/###\s+(.+?)(?:\n|$)/m', '<h3>$1</h3>', $enhancedResponse);
                debugLog("Post-resource-linking markdown cleanup applied");
            }
        } catch (Exception $e) {
            debugLog("Resource linking failed", ['error' => $e->getMessage()]);
            // Fallback to original response
            $enhancedResponse = $processedResponse;
        }
    }
    
    // Add Gemini resources if available and not already included by Resource Linking System
    if (!empty($geminiResources) && $resourceLinkingSystem === null) {
        $enhancedResponse .= buildResourceSection($geminiResources);
        debugLog("Fallback resources added to response");
    }
    
    // Build response with UTF-8 safety
    $result = [
        'success' => true,
        'result' => [
            'mega_response' => $enhancedResponse,
            'parsedown_enabled' => $parsedownAvailable,
            'ai_used' => 'openai',
            'ai_used_for_resources' => !empty($geminiResources) ? 'gemini+openai' : 'fallback',
            'related_readings' => $relatedReadings,
            'gemini_resources' => $geminiResources,
            'user_location_detected' => $userLocation,
            'language' => $language,
            'urgency_level' => $urgency,
            'resource_linking_enabled' => $resourceLinkingSystem !== null
        ],
        'debug_info' => [
            'processing_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
            'memory_usage' => memory_get_peak_usage(true),
            'parsedown_available' => $parsedownAvailable,
            'utf8_validation' => 'passed',
            'gemini_resources_count' => count($geminiResources),
            'resources_source' => !empty($geminiResources) ? 'ai_generated' : 'fallback',
            'resource_linking_system' => $resourceLinkingSystem !== null ? 'enabled' : 'disabled'
        ],
        'timestamp' => date('c')
    ];
    
    // Add emergency resources if needed
    if ($isEmergency) {
        $result['result']['emergency_resources'] = getEmergencyResources($userLocation);
        debugLog("Emergency resources added");
    }
    
    debugLog("Final response prepared", [
        'success' => true, 
        'response_length' => strlen($processedResponse),
        'contains_headers' => preg_match('/<h[1-6]>/', $processedResponse),
        'contains_markdown' => strpos($processedResponse, '###') !== false || strpos($processedResponse, '##') !== false || strpos($processedResponse, '#') !== false
    ]);
    sendResponse($result);
    
} catch (Exception $e) {
    debugLog("Error in API processing", ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    
    // Generate fallback response
    $fallbackResponse = generateFallbackResponse($question, $language, $userLocation);
    
    sendResponse([
        'success' => false,
        'error' => 'AI system temporarily unavailable',
        'fallback_guidance' => $fallbackResponse,
        'debug_error' => $e->getMessage(),
        'timestamp' => date('c')
    ], 503);
}

/**
 * Generate AI response using OpenAI API
 */
function generateAIResponse($question, $language, $userLocation, $urgency, $familyContext) {
    // Try multiple sources for API key
    $apiKey = getEnvVar('OPENAI_API_KEY');
    
    // If not found in environment, try from config constants
    if (!$apiKey || $apiKey === 'your-openai-api-key') {
        if (defined('OPENAI_API_KEY')) {
            $apiKey = OPENAI_API_KEY;
        }
    }
    
    // If still not found, try loading from secret file directly
    if (!$apiKey || $apiKey === 'your-openai-api-key') {
        if (file_exists(__DIR__ . '/openai.secret.php')) {
            try {
                require_once __DIR__ . '/openai.secret.php';
                if (defined('OPENAI_API_KEY')) {
                    $apiKey = OPENAI_API_KEY;
                }
            } catch (Exception $e) {
                debugLog("Failed to load API key from secret file: " . $e->getMessage());
            }
        }
    }
    
    if (!$apiKey || $apiKey === 'your-openai-api-key') {
        debugLog("OpenAI API key not configured");
        throw new Exception('OpenAI API key not configured');
    }
    
    // Build system prompt based on language and context
    $systemPrompt = buildSystemPrompt($language, $userLocation, $familyContext);
    
    // Adjust question based on urgency
    if ($urgency === 'emergency') {
        $question = "URGENT: " . $question . " (Please prioritize immediate resources and emergency contacts)";
    } elseif ($urgency === 'urgent') {
        $question = "URGENT: " . $question . " (Please provide timely guidance)";
    }
    
    $postData = [
        'model' => 'gpt-4',
        'messages' => [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ],
            [
                'role' => 'user',
                'content' => $question
            ]
        ],
        'temperature' => 0.7,
        'max_tokens' => 2000,
        'presence_penalty' => 0.1,
        'frequency_penalty' => 0.1
    ];
    
    debugLog("OpenAI request prepared", ['model' => $postData['model'], 'messages_count' => count($postData['messages'])]);
    
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    
    // Set up SSL with CA certificate bundle if available
    $curlOptions = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($postData, JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2
    ];
    
    // Add CA certificate bundle if available
    $caCertPath = __DIR__ . '/cacert.pem';
    if (file_exists($caCertPath)) {
        $curlOptions[CURLOPT_CAINFO] = $caCertPath;
    }
    
    curl_setopt_array($ch, $curlOptions);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    debugLog("OpenAI API response", ['http_code' => $httpCode, 'curl_error' => $curlError]);
    
    if ($curlError) {
        throw new Exception("OpenAI API connection failed: $curlError");
    }
    
    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMessage = $errorData['error']['message'] ?? "HTTP $httpCode error";
        throw new Exception("OpenAI API error: $errorMessage");
    }
    
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON response from OpenAI: ' . json_last_error_msg());
    }
    
    if (!isset($data['choices'][0]['message']['content'])) {
        throw new Exception('No content in OpenAI response');
    }
    
    $aiResponse = trim($data['choices'][0]['message']['content']);
    if (empty($aiResponse)) {
        throw new Exception('Empty response from OpenAI');
    }
    
    debugLog("OpenAI response successful", ['response_length' => strlen($aiResponse)]);
    return $aiResponse;
}

/**
 * Build system prompt based on context
 */
function buildSystemPrompt($language, $userLocation, $familyContext) {
    $basePrompt = "You are GuideAI, a compassionate AI assistant specializing in helping families with disabled children navigate special education systems, IEP processes, and disability rights. ";
    
    if ($language === 'es') {
        // Use properly encoded Spanish text
        $basePrompt = "Eres GuideAI, un asistente de IA compasivo que se especializa en ayudar a familias con ninos con discapacidades a navegar sistemas de educacion especial, procesos de IEP y derechos de discapacidad. ";
        $basePrompt .= "IMPORTANTE: Responde SIEMPRE en espanol claro y comprensible. ";
        $basePrompt .= "Proporciona informacion practica y empatica, adaptada a la comunidad hispanohablante en Estados Unidos. ";
        $basePrompt .= "Explica terminos tecnicos en ingles cuando sea necesario (ej: 'IEP (Programa Educativo Individualizado)'). ";
    } else {
        $basePrompt .= "Provide warm, empathetic, and practical guidance. Use clear, jargon-free language that parents can understand. ";
        $basePrompt .= "Focus on actionable advice and emotional support. ";
    }
    
    // Add location context
    if (strpos($userLocation, 'Oregon') !== false) {
        if ($language === 'es') {
            $basePrompt .= "El usuario esta en Oregon - incluye recursos especificos de Oregon cuando sea relevante. ";
        } else {
            $basePrompt .= "The user is in Oregon - include Oregon-specific resources when relevant. ";
        }
    }
    
    // Add family context
    if ($familyContext) {
        if ($language === 'es') {
            $basePrompt .= "Recuerda que estas hablando con padres que pueden estar estresados o abrumados. Se paciente, alentador y practico. ";
        } else {
            $basePrompt .= "Remember you're speaking with parents who may be stressed or overwhelmed. Be patient, encouraging, and practical. ";
        }
    }
    
    // Add enhanced formatting instructions
    if ($language === 'es') {
        $basePrompt .= "IMPORTANTE - Formato de respuesta:\n";
        $basePrompt .= "1. Usa ## para títulos principales (ej: '## ¿Qué es un IEP?')\n";
        $basePrompt .= "2. Usa ### para subtítulos (ej: '### Pasos a seguir')\n";
        $basePrompt .= "3. Usa - para listas con viñetas\n";
        $basePrompt .= "4. Usa **texto** para negritas importantes\n";
        $basePrompt .= "5. Menciona recursos específicos como IDEA, OSEP, Section 504, ADA, Early Intervention\n";
        $basePrompt .= "6. Sé específico sobre recursos federales, estatales y locales de Oregon\n";
        $basePrompt .= "7. Proporciona pasos claros y accionables\n";
        $basePrompt .= "8. Incluye información de contacto cuando sea relevante\n";
        $basePrompt .= "9. Menciona organizaciones como Disability Rights Oregon, OPTIC, Autism Society of Oregon\n";
    } else {
        $basePrompt .= "IMPORTANT - Response Format:\n";
        $basePrompt .= "1. Use ## for main headings (e.g., '## What is an IEP?')\n";
        $basePrompt .= "2. Use ### for subheadings (e.g., '### Steps to Take')\n";
        $basePrompt .= "3. Use - for bulleted lists\n";
        $basePrompt .= "4. Use **text** for important bold text\n";
        $basePrompt .= "5. Mention specific resources like IDEA, OSEP, Section 504, ADA, Early Intervention\n";
        $basePrompt .= "6. Be specific about federal, state, and local Oregon resources\n";
        $basePrompt .= "7. Provide clear, actionable steps\n";
        $basePrompt .= "8. Include contact information when relevant\n";
        $basePrompt .= "9. Mention organizations like Disability Rights Oregon, OPTIC, Autism Society of Oregon\n";
    }
    
    return $basePrompt;
}

/**
 * Generate fallback response when AI is unavailable
 */
function generateFallbackResponse($question, $language, $userLocation) {
    if ($language === 'es') {
        // Use properly encoded Spanish characters
        $response = "## Sistema Temporalmente No Disponible\n\n";
        $response .= "Lo sentimos, pero nuestro sistema de IA esta temporalmente no disponible. ";
        $response .= "Mientras tanto, aqui tienes algunos recursos que pueden ayudarte:\n\n";
        $response .= "### Recursos de Emergencia:\n";
        $response .= "- **Linea Nacional de Prevencion del Suicidio:** 988\n";
        $response .= "- **Linea de Crisis por Texto:** Envia 'HELLO' al 741741\n\n";
        $response .= "### Recursos de Educacion Especial:\n";
        $response .= "- **IDEA - Ley de Educacion para Personas con Discapacidades:** https://sites.ed.gov/idea/\n";
        $response .= "- **Centro de Informacion para Padres:** https://parentcenterhub.org\n";
        $response .= "- **Wrightslaw:** https://wrightslaw.com\n\n";
        
        if (strpos($userLocation, 'Oregon') !== false) {
            $response .= "### Recursos de Oregon:\n";
            $response .= "- **Derechos de Discapacidad de Oregon:** 503-243-2081\n";
            $response .= "- **Departamento de Educacion de Oregon:** https://oregon.gov/ode\n\n";
        }
        
        $response .= "Por favor, intenta de nuevo en unos minutos.";
    } else {
        $response = "## System Temporarily Unavailable\n\n";
        $response .= "We're sorry, but our AI system is temporarily unavailable. ";
        $response .= "In the meantime, here are some resources that may help:\n\n";
        $response .= "### Emergency Resources:\n";
        $response .= "- **National Suicide Prevention Lifeline:** 988\n";
        $response .= "- **Crisis Text Line:** Text 'HELLO' to 741741\n\n";
        $response .= "### Special Education Resources:\n";
        $response .= "- **IDEA - Individuals with Disabilities Education Act:** https://sites.ed.gov/idea/\n";
        $response .= "- **Center for Parent Information:** https://parentcenterhub.org\n";
        $response .= "- **Wrightslaw:** https://wrightslaw.com\n\n";
        
        if (strpos($userLocation, 'Oregon') !== false) {
            $response .= "### Oregon Resources:\n";
            $response .= "- **Disability Rights Oregon:** 503-243-2081\n";
            $response .= "- **Oregon Department of Education:** https://oregon.gov/ode\n\n";
        }
        
        $response .= "Please try again in a few minutes.";
    }
    
    return $response;
}

/**
 * Get related reading suggestions
 */
function getRelatedReadings($question, $language) {
    $readings = [];
    
    if ($language === 'es') {
        // Use properly encoded Spanish characters
        $readings = [
            ['prompt' => '?Como me preparo para una reunion de IEP?'],
            ['prompt' => '?Que adaptaciones ayudan con TDAH?'],
            ['prompt' => '?Cuales son mis derechos bajo IDEA?'],
            ['prompt' => '?Como solicito una evaluacion de educacion especial?'],
            ['prompt' => 'Mi escuela no esta siguiendo el IEP de mi hijo. ?Que puedo hacer?'],
            ['prompt' => '?Como encuentro defensores de educacion especial locales?']
        ];
    } else {
        $readings = [
            ['prompt' => 'How do I prepare for an IEP meeting?'],
            ['prompt' => 'What accommodations help with ADHD?'],
            ['prompt' => 'What are my rights under IDEA?'],
            ['prompt' => 'How do I request a special education evaluation?'],
            ['prompt' => 'My school isn\'t following my child\'s IEP. What can I do?'],
            ['prompt' => 'How do I find local special education advocates?']
        ];
    }
    
    // Filter based on question content
    $questionLower = mb_strtolower($question, 'UTF-8');
    $filtered = [];
    
    foreach ($readings as $reading) {
        $promptLower = mb_strtolower($reading['prompt'], 'UTF-8');
        
        // Simple relevance scoring
        $relevance = 0;
        $questionWords = explode(' ', $questionLower);
        
        foreach ($questionWords as $word) {
            if (mb_strlen($word, 'UTF-8') > 3 && mb_strpos($promptLower, $word, 0, 'UTF-8') !== false) {
                $relevance++;
            }
        }
        
        if ($relevance > 0) {
            $reading['relevance'] = $relevance;
            $filtered[] = $reading;
        }
    }
    
    // Sort by relevance and return top 4
    usort($filtered, function($a, $b) {
        return ($b['relevance'] ?? 0) - ($a['relevance'] ?? 0);
    });
    
    $topReadings = array_slice($filtered, 0, 4);
    
    // If no relevant matches, return some default suggestions
    if (empty($topReadings)) {
        $topReadings = array_slice($readings, 0, 3);
    }
    
    // Remove relevance scores from output
    foreach ($topReadings as &$reading) {
        unset($reading['relevance']);
    }
    
    return $topReadings;
}

/**
 * Check if question indicates emergency
 */
function isEmergencyQuestion($question) {
    // Use properly encoded Spanish characters
    $emergencyKeywords = [
        // English
        'emergency', 'crisis', 'urgent', 'discrimination', 'abuse', 'violation', 
        'threatening', 'harmful', 'dangerous', 'immediate help', 'rights violation',
        // Spanish - properly encoded
        'emergencia', 'crisis', 'urgente', 'discriminacion', 'abuso', 'violacion',
        'amenazante', 'danino', 'peligroso', 'ayuda inmediata', 'violacion de derechos'
    ];
    
    $questionLower = mb_strtolower($question, 'UTF-8');
    
    foreach ($emergencyKeywords as $keyword) {
        if (mb_strpos($questionLower, $keyword, 0, 'UTF-8') !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Get emergency resources based on location
 */
function getEmergencyResources($userLocation = null) {
    $resources = [
        'crisis' => [
            'name' => 'National Suicide Prevention Lifeline',
            'phone' => '988',
            'description' => '24/7 crisis support and suicide prevention',
            'text' => 'Text "HELLO" to 741741 for crisis text support'
        ],
        'disability_rights' => [
            'name' => 'National Disability Rights Network',
            'phone' => '202-408-9514',
            'description' => 'Legal advocacy for disability discrimination'
        ],
        'parent_support' => [
            'name' => 'National Parent Helpline',
            'phone' => '1-855-427-2736',
            'description' => 'Emotional support for parents and caregivers'
        ]
    ];
    
    // Add Oregon-specific resources if user is in Oregon
    if ($userLocation && strpos($userLocation, 'Oregon') !== false) {
        $resources['oregon_disability'] = [
            'name' => 'Disability Rights Oregon',
            'phone' => '503-243-2081',
            'description' => 'Oregon-specific disability rights advocacy and legal support'
        ];
        
        $resources['oregon_parent'] = [
            'name' => 'Oregon Parent Training and Information Center',
            'phone' => '888-988-9315',
            'description' => 'Oregon parent support and advocacy training'
        ];
    }
    
    return $resources;
}

/**
 * Generate comprehensive resources using multiple AI services (Gemini + OpenAI)
 */
function generateGeminiResources($question, $userLocation = null, $language = 'en') {
    $resources = [];
    $aiServicesUsed = [];
    
    // Try to get resources from both services for comprehensive coverage
    $combineServices = true; // Set to false to use fallback approach instead
    
    if ($combineServices) {
        // Try both services and combine results
        $geminiResources = [];
        $openaiResources = [];
        
        // Get Gemini resources
        try {
            $geminiApiKey = getEnvVar('GEMINI_API_KEY');
            if (!$geminiApiKey && defined('GEMINI_API_KEY')) {
                $geminiApiKey = GEMINI_API_KEY;
            }
            
            if ($geminiApiKey && $geminiApiKey !== 'your-gemini-api-key') {
                $geminiResources = callGeminiForResources($question, $userLocation, $language, $geminiApiKey);
                if (!empty($geminiResources)) {
                    $aiServicesUsed[] = 'gemini';
                    debugLog("Gemini resources generated", ['count' => count($geminiResources)]);
                }
            }
        } catch (Exception $e) {
            debugLog("Gemini API failed: " . $e->getMessage());
        }
        
        // Get OpenAI resources
        try {
            $openaiApiKey = getEnvVar('OPENAI_API_KEY');
            if (!$openaiApiKey && defined('OPENAI_API_KEY')) {
                $openaiApiKey = OPENAI_API_KEY;
            }
            
            if ($openaiApiKey && $openaiApiKey !== 'your-openai-api-key') {
                $openaiResources = callOpenAIForResources($question, $userLocation, $language, $openaiApiKey);
                if (!empty($openaiResources)) {
                    $aiServicesUsed[] = 'openai';
                    debugLog("OpenAI resources generated", ['count' => count($openaiResources)]);
                }
            }
        } catch (Exception $e) {
            debugLog("OpenAI resource generation failed: " . $e->getMessage());
        }
        
        // Combine and deduplicate resources
        if (!empty($geminiResources) || !empty($openaiResources)) {
            $resources = combineAndDeduplicateResources($geminiResources, $openaiResources);
            debugLog("Combined resources from AI services", [
                'services_used' => $aiServicesUsed,
                'total_resources' => count($resources),
                'gemini_count' => count($geminiResources),
                'openai_count' => count($openaiResources)
            ]);
            return $resources;
        }
    } else {
        // Fallback approach: try one service at a time
        // Try Gemini first
        try {
            $geminiApiKey = getEnvVar('GEMINI_API_KEY');
            if (!$geminiApiKey && defined('GEMINI_API_KEY')) {
                $geminiApiKey = GEMINI_API_KEY;
            }
            
            if ($geminiApiKey && $geminiApiKey !== 'your-gemini-api-key') {
                $resources = callGeminiForResources($question, $userLocation, $language, $geminiApiKey);
                if (!empty($resources)) {
                    debugLog("Gemini resources generated successfully", ['count' => count($resources)]);
                    return $resources;
                }
            }
        } catch (Exception $e) {
            debugLog("Gemini API failed: " . $e->getMessage());
        }
        
        // Try OpenAI if Gemini fails or is unavailable
        try {
            $openaiApiKey = getEnvVar('OPENAI_API_KEY');
            if (!$openaiApiKey && defined('OPENAI_API_KEY')) {
                $openaiApiKey = OPENAI_API_KEY;
            }
            
            if ($openaiApiKey && $openaiApiKey !== 'your-openai-api-key') {
                $resources = callOpenAIForResources($question, $userLocation, $language, $openaiApiKey);
                if (!empty($resources)) {
                    debugLog("OpenAI resources generated successfully", ['count' => count($resources)]);
                    return $resources;
                }
            }
        } catch (Exception $e) {
            debugLog("OpenAI resource generation failed: " . $e->getMessage());
        }
    }
    
    // Fall back to curated resources
    debugLog("Using fallback resources due to API unavailability");
    return getFallbackResources($question, $userLocation, $language);
}

/**
 * Call Gemini API for resources
 */
function callGeminiForResources($question, $userLocation, $language, $geminiApiKey) {
    // Build location context
    $locationContext = '';
    if ($userLocation) {
        $locationContext = " The user is located in {$userLocation['city']}, {$userLocation['state']}, {$userLocation['country']}. Please provide location-specific resources when possible.";
    }
    
    // Build Gemini prompt for resource generation
    $prompt = "Based on this special education question: '{$question}'{$locationContext}
    
Please generate a list of helpful resources including:
1. Federal resources (IDEA, ED.gov, etc.)
2. State-specific resources (if location provided)
3. Advocacy organizations
4. Parent support groups
5. Legal resources
6. Educational materials

Format as JSON with this structure:
{
  \"resources\": [
    {
      \"title\": \"Resource Name\",
      \"url\": \"https://example.com\",
      \"description\": \"Brief description\",
      \"type\": \"federal|state|advocacy|support|legal|educational\",
      \"relevance\": \"How this helps with the question\"
    }
  ]
}

Respond only with valid JSON, no additional text.";

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.3,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 1024,
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $geminiApiKey);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        throw new Exception("Curl error: " . $curlError);
    }
    
    if ($httpCode !== 200) {
        throw new Exception("HTTP error: " . $httpCode);
    }
    
    $result = json_decode($response, true);
    
    if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception("Invalid response format");
    }
    
    $geminiText = $result['candidates'][0]['content']['parts'][0]['text'];
    
    // Parse the JSON response
    $resourceData = json_decode($geminiText, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON parse error: " . json_last_error_msg());
    }
    
    return $resourceData['resources'] ?? [];
}

/**
 * Call OpenAI API for resources
 */
function callOpenAIForResources($question, $userLocation, $language, $openaiApiKey) {
    // Build location context
    $locationContext = '';
    if ($userLocation) {
        $locationContext = " The user is located in {$userLocation['city']}, {$userLocation['state']}, {$userLocation['country']}. Please provide location-specific resources when possible.";
    }

    // Build OpenAI prompt for resource generation
    $prompt = "Based on this special education question: '{$question}'{$locationContext}
    
Please generate a list of helpful resources including:
1. Federal resources (IDEA, ED.gov, etc.)
2. State-specific resources (if location provided)
3. Advocacy organizations
4. Parent support groups
5. Legal resources
6. Educational materials

Format as JSON with this structure:
{
  \"resources\": [
    {
      \"title\": \"Resource Name\",
      \"url\": \"https://example.com\",
      \"description\": \"Brief description\",
      \"type\": \"federal|state|advocacy|support|legal|educational\",
      \"relevance\": \"How this helps with the question\"
    }
  ]
}

Respond only with valid JSON, no additional text.";

    $data = [
        'model' => 'gpt-4',
        'messages' => [
            [
                'role' => 'system',
                'content' => $prompt
            ]
        ],
        'temperature' => 0.3,
        'max_tokens' => 1000,
        'presence_penalty' => 0.1,
        'frequency_penalty' => 0.1
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openaiApiKey
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception("Curl error: " . $curlError);
    }

    if ($httpCode !== 200) {
        throw new Exception("HTTP error: " . $httpCode);
    }

    $result = json_decode($response, true);

    if (!isset($result['choices'][0]['message']['content'])) {
        throw new Exception("Invalid response format");
    }

    $openaiText = $result['choices'][0]['message']['content'];

    // Parse the JSON response
    $resourceData = json_decode($openaiText, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON parse error: " . json_last_error_msg());
    }

    return $resourceData['resources'] ?? [];
}

/**
 * Combine and deduplicate resources from two AI services.
 * This function takes two arrays of resources and returns a single array
 * with unique resources based on title and URL, with intelligent scoring.
 */
function combineAndDeduplicateResources($geminiResources, $openaiResources) {
    $combinedResources = [];
    $seenTitles = [];
    $seenUrls = [];
    $seenDomains = [];

    // Helper function to extract domain from URL
    $getDomain = function($url) {
        $parsed = parse_url($url);
        return isset($parsed['host']) ? strtolower($parsed['host']) : '';
    };

    // Helper function to calculate resource quality score
    $calculateScore = function($resource, $source) {
        $score = 0;
        $title = strtolower($resource['title'] ?? '');
        $description = strtolower($resource['description'] ?? '');
        $url = strtolower($resource['url'] ?? '');

        // Higher score for official/government sources
        if (strpos($url, '.gov') !== false || strpos($url, '.edu') !== false) {
            $score += 20;
        }

        // Score for well-known educational resources
        $knownSites = ['understood.org', 'wrightslaw.com', 'parentcenterhub.org', 'copaa.org'];
        foreach ($knownSites as $site) {
            if (strpos($url, $site) !== false) {
                $score += 15;
                break;
            }
        }

        // Score for specific keywords that indicate quality
        $qualityKeywords = ['idea', 'iep', '504', 'special education', 'advocacy', 'parent guide'];
        foreach ($qualityKeywords as $keyword) {
            if (strpos($title, $keyword) !== false || strpos($description, $keyword) !== false) {
                $score += 5;
            }
        }

        // Slight preference for Gemini resources (they tend to be more accurate)
        if ($source === 'gemini') {
            $score += 2;
        }

        return $score;
    };

    // Process Gemini resources first (higher priority)
    foreach ($geminiResources as $resource) {
        $title = $resource['title'] ?? '';
        $url = $resource['url'] ?? '';
        $domain = $getDomain($url);
        $titleKey = strtolower(trim($title));
        $urlKey = strtolower(trim($url));

        if (!isset($seenTitles[$titleKey]) && !isset($seenUrls[$urlKey]) && 
            (!isset($seenDomains[$domain]) || $domain === '')) {
            
            $resource['quality_score'] = $calculateScore($resource, 'gemini');
            $resource['source'] = 'gemini';
            $combinedResources[] = $resource;
            
            $seenTitles[$titleKey] = true;
            $seenUrls[$urlKey] = true;
            if ($domain) $seenDomains[$domain] = true;
        }
    }

    // Process OpenAI resources
    foreach ($openaiResources as $resource) {
        $title = $resource['title'] ?? '';
        $url = $resource['url'] ?? '';
        $domain = $getDomain($url);
        $titleKey = strtolower(trim($title));
        $urlKey = strtolower(trim($url));

        if (!isset($seenTitles[$titleKey]) && !isset($seenUrls[$urlKey]) && 
            (!isset($seenDomains[$domain]) || $domain === '')) {
            
            $resource['quality_score'] = $calculateScore($resource, 'openai');
            $resource['source'] = 'openai';
            $combinedResources[] = $resource;
            
            $seenTitles[$titleKey] = true;
            $seenUrls[$urlKey] = true;
            if ($domain) $seenDomains[$domain] = true;
        }
    }

    // Sort by quality score (highest first)
    usort($combinedResources, function($a, $b) {
        return ($b['quality_score'] ?? 0) <=> ($a['quality_score'] ?? 0);
    });

    // Return top 6 resources for variety without overwhelming
    $topResources = array_slice($combinedResources, 0, 6);
    
    // Clean up - remove quality_score and source from final output
    foreach ($topResources as &$resource) {
        unset($resource['quality_score']);
        unset($resource['source']);
    }
    
    return $topResources;
}

/**
 * Get fallback resources when Gemini is unavailable
 */
function getFallbackResources($question, $userLocation = null, $language = 'en') {
    $resources = [];
    
    // Always include these core federal resources
    $resources[] = [
        'title' => 'IDEA - Individuals with Disabilities Education Act',
        'url' => 'https://sites.ed.gov/idea/',
        'description' => 'Official federal website for special education law and resources',
        'type' => 'federal',
        'relevance' => 'Primary source for special education rights and procedures'
    ];
    
    $resources[] = [
        'title' => 'Center for Parent Information and Resources',
        'url' => 'https://www.parentcenterhub.org/',
        'description' => 'National hub for parent training and information centers',
        'type' => 'support',
        'relevance' => 'Parent training, support, and information on special education topics'
    ];
    
    $resources[] = [
        'title' => 'Council of Parent Attorneys and Advocates (COPAA)',
        'url' => 'https://www.copaa.org/',
        'description' => 'National organization for special education attorneys and advocates',
        'type' => 'legal',
        'relevance' => 'Legal resources and advocacy support for special education matters'
    ];
    
    $resources[] = [
        'title' => 'Understood.org',
        'url' => 'https://www.understood.org/',
        'description' => 'Comprehensive resource for learning and attention issues',
        'type' => 'educational',
        'relevance' => 'Practical guidance and tools for parents and educators'
    ];
    
    // Add IEP-specific resources if question mentions IEP
    if (stripos($question, 'iep') !== false || stripos($question, 'individualized education') !== false) {
        $resources[] = [
            'title' => 'IEP Guide - Understanding Your Rights',
            'url' => 'https://www.understood.org/en/school-learning/special-services/ieps/understanding-individualized-education-programs',
            'description' => 'Complete guide to IEP process and parent rights',
            'type' => 'educational',
            'relevance' => 'Step-by-step guidance for IEP meetings and processes'
        ];
    }
    
    // Add 504 Plan resources if mentioned
    if (stripos($question, '504') !== false) {
        $resources[] = [
            'title' => 'Section 504 Student and Parent Guide',
            'url' => 'https://www2.ed.gov/about/offices/list/ocr/504faq.html',
            'description' => 'Official guide to Section 504 accommodations',
            'type' => 'federal',
            'relevance' => 'Understanding 504 plans and accommodations'
        ];
    }
    
    // Add state-specific resources if location is provided
    if ($userLocation && isset($userLocation['state'])) {
        $state = $userLocation['state'];
        $resources[] = [
            'title' => $state . ' Parent Training and Information Center',
            'url' => 'https://www.parentcenterhub.org/find-your-center/',
            'description' => 'State-specific parent training and information resources',
            'type' => 'state',
            'relevance' => 'Local support and state-specific special education information'
        ];
    }
    
    return $resources;
}

/**
 * Build HTML resource section
 */
function buildResourceSection($resources) {
    if (empty($resources)) {
        return '';
    }
    
    $html = "\n\n<h3>📚 Related Resources</h3>\n";
    
    // Group resources by type
    $groupedResources = [];
    foreach ($resources as $resource) {
        $type = $resource['type'] ?? 'general';
        if (!isset($groupedResources[$type])) {
            $groupedResources[$type] = [];
        }
        $groupedResources[$type][] = $resource;
    }
    
    // Type labels
    $typeLabels = [
        'federal' => '🏛️ Federal Resources',
        'state' => '🗺️ State Resources', 
        'advocacy' => '🤝 Advocacy Organizations',
        'support' => '👥 Parent Support Groups',
        'legal' => '⚖️ Legal Resources',
        'educational' => '📖 Educational Materials',
        'general' => '🔗 Additional Resources'
    ];
    
    foreach ($groupedResources as $type => $typeResources) {
        $label = $typeLabels[$type] ?? '🔗 Resources';
        $html .= "\n<h4>{$label}</h4>\n<ul>\n";
        
        foreach ($typeResources as $resource) {
            $title = htmlspecialchars($resource['title'] ?? 'Resource');
            $url = htmlspecialchars($resource['url'] ?? '#');
            $description = htmlspecialchars($resource['description'] ?? '');
            $relevance = htmlspecialchars($resource['relevance'] ?? '');
            
            $html .= "<li><strong><a href=\"{$url}\" target=\"_blank\">{$title}</a></strong>";
            if ($description) {
                $html .= " - {$description}";
            }
            if ($relevance) {
                $html .= " <em>({$relevance})</em>";
            }
            $html .= "</li>\n";
        }
        
        $html .= "</ul>\n";
    }
    
    return $html;
}

?>