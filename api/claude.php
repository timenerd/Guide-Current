<?php
/**
 * GuideAI Claude API Integration
 * Handles response processing using Claude AI
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Handle GET requests for testing
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $api = new GuideAIClaudeAPI();
    $api->handleGetRequest();
    exit;
}

// Debug logging function
function debugLog($message, $data = null) {
    $logMessage = "[" . date('Y-m-d H:i:s') . "] " . $message;
    if ($data !== null) {
        $logMessage .= " | Data: " . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    error_log($logMessage . PHP_EOL, 3, __DIR__ . '/../logs/claude_debug.log');
}

// Load configuration
if (file_exists('../config.php')) {
    try {
        require_once '../config.php';
        debugLog("Configuration loaded successfully");
    } catch (Exception $e) {
        debugLog("Configuration error: " . $e->getMessage());
    }
} else {
    debugLog("Configuration file not found");
}

class GuideAIClaudeAPI {
    private $claudeApiKey;
    private $language;
    private $systemPrompt;
    
    public function __construct() {
        $this->claudeApiKey = defined('CLAUDE_API_KEY') ? CLAUDE_API_KEY : '';
        $this->language = $_GET['lang'] ?? 'en';
        $this->systemPrompt = $this->buildSystemPrompt();
        
        debugLog("Claude API initialized", [
            'api_key_set' => !empty($this->claudeApiKey),
            'api_key_length' => strlen($this->claudeApiKey),
            'language' => $this->language
        ]);
    }
    
    /**
     * Main API endpoint
     */
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        debugLog("Request method: " . $method);
        
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? '';
            
            debugLog("Request received", [
                'action' => $action,
                'input_keys' => array_keys($input),
                'has_message' => isset($input['message'])
            ]);
            
            switch ($action) {
                case 'chat':
                    return $this->processChat($input);
                case 'test':
                    return $this->testConnection();
                default:
                    debugLog("Invalid action: " . $action);
                    return $this->errorResponse('Invalid action');
            }
        } else {
            debugLog("Method not allowed: " . $method);
            return $this->errorResponse('Method not allowed');
        }
    }
    
    /**
     * Handle GET requests for testing
     */
    public function handleGetRequest() {
        debugLog("GET request received for testing");
        
        $status = [
            'success' => true,
            'api_configured' => !empty($this->claudeApiKey),
            'api_key_length' => strlen($this->claudeApiKey),
            'language' => $this->language,
            'timestamp' => date('c')
        ];
        
        if (empty($this->claudeApiKey)) {
            $status['error'] = 'Claude API key not configured';
            $status['success'] = false;
        }
        
        debugLog("GET response", $status);
        echo json_encode($status);
    }
    
    /**
     * Process chat with Claude
     */
    private function processChat($input) {
        $message = $input['message'] ?? '';
        $context = $input['context'] ?? [];
        $preferences = $input['preferences'] ?? [];
        
        debugLog("Processing chat", [
            'message_length' => strlen($message),
            'has_context' => !empty($context),
            'has_preferences' => !empty($preferences)
        ]);
        
        if (empty($message)) {
            debugLog("Empty message received");
            return $this->errorResponse('Message is required');
        }
        
        try {
            $response = $this->generateClaudeResponse($message, $context, $preferences);
            debugLog("Claude response generated successfully");
            return $this->successResponse($response);
        } catch (Exception $e) {
            debugLog("Error processing message: " . $e->getMessage());
            return $this->errorResponse('Error processing message: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate response using Claude
     */
    private function generateClaudeResponse($message, $context, $preferences) {
        if (empty($this->claudeApiKey)) {
            debugLog("Claude API key not configured");
            throw new Exception('Claude API key not configured');
        }
        
        $url = 'https://api.anthropic.com/v1/messages';
        
        // Build enhanced prompt with context
        $prompt = $this->buildEnhancedPrompt($message, $context, $preferences);
        
        $data = [
            'model' => 'claude-3-5-sonnet-20241022',
            'max_tokens' => 2000,
            'temperature' => 0.7,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ];
        
        debugLog("Making Claude API request", [
            'url' => $url,
            'model' => $data['model'],
            'prompt_length' => strlen($prompt)
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $this->claudeApiKey,
            'anthropic-version: 2023-06-01'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for development
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable host verification for development
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        debugLog("Claude API response", [
            'http_code' => $httpCode,
            'curl_error' => $curlError,
            'response_length' => strlen($response)
        ]);
        
        if ($curlError) {
            debugLog("Curl error: " . $curlError);
            throw new Exception('Claude API curl error: ' . $curlError);
        }
        
        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMessage = 'Claude API error: ' . $httpCode;
            if ($errorData && isset($errorData['error']['message'])) {
                $errorMessage .= ' - ' . $errorData['error']['message'];
            }
            debugLog("Claude API error response", [
                'http_code' => $httpCode,
                'error_data' => $errorData
            ]);
            throw new Exception($errorMessage);
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['content'][0]['text'])) {
            $content = $result['content'][0]['text'];
            
            // Apply user preferences to the response
            $processedContent = $this->applyUserPreferences($content, $preferences);
            
            debugLog("Claude response processed successfully", [
                'content_length' => strlen($processedContent),
                'tokens_used' => $result['usage']['input_tokens'] + $result['usage']['output_tokens']
            ]);
            
            return [
                'content' => $processedContent,
                'raw_content' => $content,
                'model_used' => 'claude-3-5-sonnet',
                'tokens_used' => $result['usage']['input_tokens'] + $result['usage']['output_tokens'],
                'processing_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
            ];
        } else {
            debugLog("Invalid Claude response structure", [
                'result_keys' => array_keys($result),
                'has_content' => isset($result['content']),
                'content_structure' => $result['content'] ?? 'not_set'
            ]);
            throw new Exception('Invalid response from Claude API');
        }
    }
    
    /**
     * Build system prompt for Claude
     */
    private function buildSystemPrompt() {
        $basePrompt = "You are GuideAI, a compassionate and knowledgeable assistant specializing in special education support for families. Your role is to provide accurate, helpful, and empathetic guidance to parents navigating their child's special education journey.

Key Responsibilities:
1. Provide accurate information about special education laws, rights, and processes
2. Offer practical advice for IEP meetings, evaluations, and accommodations
3. Connect families with relevant resources and support organizations
4. Maintain a warm, understanding, and non-judgmental tone
5. Prioritize the child's best interests and family well-being

Guidelines:
- Always acknowledge the emotional challenges families face
- Provide specific, actionable advice when possible
- Include relevant legal rights and protections
- Suggest appropriate resources and contacts
- Use clear, accessible language
- Be encouraging and supportive
- When uncertain, suggest consulting with professionals

Format your responses with:
- Clear headings using ## and ###
- Bullet points for lists
- Bold text for important points
- Links to resources when relevant
- Contact information when appropriate

Remember: You're supporting families during what can be a challenging and emotional time. Be patient, understanding, and always prioritize their well-being.";

        if ($this->language === 'es') {
            $basePrompt .= "\n\nIMPORTANTE: Responde en español. Usa un tono cálido y comprensivo. Incluye recursos específicos para familias hispanohablantes cuando sea apropiado.";
        }
        
        return $basePrompt;
    }
    
    /**
     * Build enhanced prompt with context
     */
    private function buildEnhancedPrompt($message, $context, $preferences) {
        $prompt = $this->systemPrompt . "\n\n";
        
        // Add context information
        if (!empty($context)) {
            $prompt .= "Context Information:\n";
            
            if (isset($context['location']) && $context['location']) {
                $prompt .= "- User Location: " . json_encode($context['location']) . "\n";
            }
            
            if (isset($context['language'])) {
                $prompt .= "- Language: " . $context['language'] . "\n";
            }
            
            if (isset($context['chatHistory']) && !empty($context['chatHistory'])) {
                $prompt .= "- Recent Conversation History:\n";
                foreach (array_slice($context['chatHistory'], -3) as $historyItem) {
                    $role = $historyItem['role'] === 'user' ? 'Parent' : 'GuideAI';
                    $prompt .= "  $role: " . substr($historyItem['content'], 0, 200) . "\n";
                }
            }
            
            $prompt .= "\n";
        }
        
        // Add user preferences
        if (!empty($preferences)) {
            $prompt .= "User Preferences:\n";
            if (isset($preferences['responseStyle'])) {
                $prompt .= "- Response Style: " . $preferences['responseStyle'] . "\n";
            }
            if (isset($preferences['language'])) {
                $prompt .= "- Language: " . $preferences['language'] . "\n";
            }
            $prompt .= "\n";
        }
        
        // Add the user's question
        $prompt .= "Parent's Question: " . $message . "\n\n";
        $prompt .= "Please provide a comprehensive, helpful response that addresses their question while being mindful of their context and preferences.";
        
        return $prompt;
    }
    
    /**
     * Apply user preferences to response
     */
    private function applyUserPreferences($content, $preferences) {
        $processedContent = $content;
        
        // Apply response style
        if (isset($preferences['responseStyle'])) {
            switch ($preferences['responseStyle']) {
                case 'simple':
                    $processedContent = $this->simplifyContent($processedContent);
                    break;
                case 'formal':
                    $processedContent = $this->makeFormal($processedContent);
                    break;
                case 'conversational':
                default:
                    $processedContent = $this->makeConversational($processedContent);
                    break;
            }
        }
        
        // Apply accessibility considerations
        if (isset($preferences['largeFonts']) && $preferences['largeFonts']) {
            // Add larger text indicators
            $processedContent = str_replace('<h4>', '<h4 style="font-size: 1.3em;">', $processedContent);
            $processedContent = str_replace('<h5>', '<h5 style="font-size: 1.2em;">', $processedContent);
        }
        
        if (isset($preferences['highContrast']) && $preferences['highContrast']) {
            // Add high contrast indicators
            $processedContent = str_replace('<strong>', '<strong style="color: #000; background-color: #fff;">', $processedContent);
        }
        
        return $processedContent;
    }
    
    /**
     * Simplify content for accessibility
     */
    private function simplifyContent($content) {
        return preg_replace([
            '/## .*?\n/',
            '/\*\*(.*?)\*\*/',
            '/\*(.*?)\*/',
            '/`(.*?)`/',
            '/\[([^\]]+)\]\([^)]+\)/',
            '/\n\n/'
        ], [
            '',
            '$1',
            '$1',
            '$1',
            '$1',
            '\n'
        ], trim($content));
    }
    
    /**
     * Make content more formal
     */
    private function makeFormal($content) {
        return preg_replace([
            '/\bI\'m\b/',
            '/\bI\'ll\b/',
            '/\bI\'ve\b/',
            '/\bdon\'t\b/',
            '/\bcan\'t\b/',
            '/\bwon\'t\b/',
            '/\bIt\'s\b/',
            '/\bThat\'s\b/'
        ], [
            'I am',
            'I will',
            'I have',
            'do not',
            'cannot',
            'will not',
            'It is',
            'That is'
        ], $content);
    }
    
    /**
     * Make content more conversational
     */
    private function makeConversational($content) {
        return preg_replace([
            '/\bIt is\b/',
            '/\bThat is\b/',
            '/\bI am\b/',
            '/\bdo not\b/',
            '/\bcannot\b/',
            '/\bwill not\b/'
        ], [
            "It's",
            "That's",
            "I'm",
            "don't",
            "can't",
            "won't"
        ], $content);
    }
    
    /**
     * Test connection to Claude API
     */
    private function testConnection() {
        if (empty($this->claudeApiKey)) {
            return $this->errorResponse('Claude API key not configured');
        }
        
        try {
            $url = 'https://api.anthropic.com/v1/messages';
            $data = [
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 10,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Hello'
                    ]
                ]
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'x-api-key: ' . $this->claudeApiKey,
                'anthropic-version: 2023-06-01'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for development
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable host verification for development
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                return $this->successResponse(['status' => 'connected', 'message' => 'Claude API is working']);
            } else {
                return $this->errorResponse('Claude API test failed: HTTP ' . $httpCode);
            }
        } catch (Exception $e) {
            return $this->errorResponse('Claude API test failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Send success response
     */
    private function successResponse($data) {
        echo json_encode([
            'success' => true,
            'data' => $data,
            'timestamp' => date('c')
        ]);
    }
    
    /**
     * Send error response
     */
    private function errorResponse($message) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $message,
            'timestamp' => date('c')
        ]);
    }
}

// Initialize and handle request
$api = new GuideAIClaudeAPI();
$api->handleRequest(); 