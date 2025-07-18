<?php
/**
 * GuideAI Multi-AI API Integration
 * Uses Claude for content generation and ChatGPT for conversational tone
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Load configuration (optional)
if (file_exists('../config.php')) {
    try {
        require_once '../config.php';
    } catch (Exception $e) {
        // Continue without config
    }
}

class GuideAIMultiAI {
    private $geminiApiKey;
    private $claudeApiKey;
    private $chatgptApiKey;
    private $openCageApiKey;
    private $language;
    private $resourceLinkingSystem;
    
    public function __construct() {
        $this->geminiApiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
        $this->claudeApiKey = defined('CLAUDE_API_KEY') ? CLAUDE_API_KEY : '';
        $this->chatgptApiKey = defined('CHATGPT_API_KEY') ? CHATGPT_API_KEY : '';
        $this->openCageApiKey = defined('OPENCAGE_API_KEY') ? OPENCAGE_API_KEY : '';
        $this->language = $_GET['lang'] ?? 'en';
        $this->resourceLinkingSystem = null; // Skip resource linking for stability
    }
    

    
    /**
     * Process chat with multi-AI approach
     */
    private function processChat($input) {
        $message = $input['message'] ?? '';
        $location = $input['location'] ?? null;
        $history = $input['history'] ?? [];
        
        if (empty($message)) {
            return $this->errorResponse('Message is required');
        }
        
        try {
            $response = $this->generateMultiAIResponse($message, $location, $history);
            return $this->successResponse($response);
        } catch (Exception $e) {
            return $this->errorResponse('Error processing message: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate response using Claude + ChatGPT approach
     */
    private function generateMultiAIResponse($message, $location = null, $history = []) {
        // Step 1: Generate core content with Claude
        $coreContent = null;
        try {
            $coreContent = $this->generateCoreContentWithClaude($message, $location, $history);
        } catch (Exception $e) {
            // If Claude fails, try Gemini as fallback
            try {
                $coreContent = $this->callGeminiAPI($message, $location, $history);
            } catch (Exception $geminiError) {
                // If both fail, throw the original error
                throw new Exception('AI services unavailable: ' . $e->getMessage());
            }
        }
        
        // Step 2: Make it conversational with ChatGPT
        $conversationalResponse = $this->makeConversationalWithChatGPT($coreContent, $message, $location);
        
        return $conversationalResponse;
    }
    
    /**
     * Enhanced Gemini API for links and locations
     */
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? '';
            
            switch ($action) {
                case 'chat':
                    return $this->processChat($input);
                case 'enhance':
                    return $this->enhanceWithLinksAndLocations($input);
                case 'getLocationResources':
                    return $this->getLocationResources($input);
                case 'getUserLocation':
                    return $this->getUserLocation($input);
                case 'test':
                    return $this->testConnection();
                default:
                    return $this->errorResponse('Invalid action');
            }
        } else {
            return $this->errorResponse('Method not allowed');
        }
    }
    
    /**
     * Enhance content with links and locations using Gemini
     */
    private function enhanceWithLinksAndLocations($input) {
        $message = $input['message'] ?? '';
        $context = $input['context'] ?? [];
        $coreContent = $input['core_content'] ?? '';
        $focus = $input['focus'] ?? 'links_and_locations';
        
        if (empty($message) || empty($coreContent)) {
            return $this->errorResponse('Message and core content are required');
        }
        
        try {
            $enhancement = $this->generateLinksAndLocations($message, $context, $coreContent);
            return $this->successResponse($enhancement);
        } catch (Exception $e) {
            return $this->errorResponse('Error enhancing content: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate links and locations using Gemini
     */
    private function generateLinksAndLocations($message, $context, $coreContent) {
        if (empty($this->geminiApiKey)) {
            throw new Exception('Gemini API key not configured');
        }
        
        // Try multiple Gemini models
        $models = [
            'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent',
            'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent'
        ];
        
        $url = $models[0]; // Use first model as default
        
        $prompt = $this->buildLinksAndLocationsPrompt($message, $context, $coreContent);
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 1000
            ]
        ];
        
        // Try each model until one works
        foreach ($models as $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '?key=' . $this->geminiApiKey);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for development
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable host verification for development
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                continue; // Try next model
            }
            
            if ($httpCode === 200) {
                $result = json_decode($response, true);
                
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $enhancementText = $result['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Parse the enhancement to extract links and locations
                    $enhancement = $this->parseEnhancement($enhancementText, $context);
                    
                    return $enhancement;
                }
            }
        }
        
        // If we get here, none of the models worked
        throw new Exception('All Gemini API models failed');
    }
    
    /**
     * Build prompt for links and locations enhancement
     */
    private function buildLinksAndLocationsPrompt($message, $context, $coreContent) {
        $prompt = "You are a specialized AI assistant that enhances special education content with relevant links and location-specific resources. Your task is to analyze the given content and provide additional resources.

CONTEXT:
- User Question: {$message}
- User Location: " . json_encode($context['location'] ?? 'unknown') . "
- Language: " . ($context['language'] ?? 'en') . "

CORE CONTENT:
{$coreContent}

TASK:
Analyze the content and provide:
1. Relevant online resources (websites, articles, guides)
2. Location-specific organizations and services
3. Contact information for local support
4. Additional helpful links

FORMAT YOUR RESPONSE AS JSON:
{
  \"links\": [
    {
      \"title\": \"Resource Title\",
      \"url\": \"https://example.com\",
      \"description\": \"Brief description\",
      \"category\": \"federal|state|local|crisis\"
    }
  ],
  \"locations\": [
    {
      \"name\": \"Organization Name\",
      \"address\": \"Full address\",
      \"phone\": \"Phone number\",
      \"website\": \"https://example.com\",
      \"services\": \"Description of services\"
    }
  ],
  \"summary\": \"Brief summary of additional resources provided\"
}

Focus on providing accurate, helpful, and location-relevant resources for special education support.";

        return $prompt;
    }
    
    /**
     * Parse enhancement response to extract structured data
     */
    private function parseEnhancement($enhancementText, $context) {
        // Try to extract JSON from the response
        $jsonMatch = [];
        if (preg_match('/\{.*\}/s', $enhancementText, $jsonMatch)) {
            try {
                $parsed = json_decode($jsonMatch[0], true);
                if ($parsed && is_array($parsed)) {
                    return $parsed;
                }
            } catch (Exception $e) {
                // Continue with fallback parsing
            }
        }
        
        // Fallback: parse text to extract links and locations
        return $this->fallbackParseEnhancement($enhancementText, $context);
    }
    
    /**
     * Fallback parsing for enhancement text
     */
    private function fallbackParseEnhancement($text, $context) {
        $links = [];
        $locations = [];
        
        // Extract links
        preg_match_all('/\[([^\]]+)\]\(([^)]+)\)/', $text, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $links[] = [
                'title' => $match[1],
                'url' => $match[2],
                'description' => 'Resource link',
                'category' => 'general'
            ];
        }
        
        // Extract phone numbers as potential locations
        preg_match_all('/(\d{3}-\d{3}-\d{4})/', $text, $phoneMatches);
        foreach ($phoneMatches[1] as $phone) {
            $locations[] = [
                'name' => 'Contact Number',
                'phone' => $phone,
                'services' => 'Phone support'
            ];
        }
        
        return [
            'links' => $links,
            'locations' => $locations,
            'summary' => 'Enhanced with additional resources'
        ];
    }
    
    /**
     * Test connection to Gemini API
     */
    private function testConnection() {
        if (empty($this->geminiApiKey)) {
            return $this->errorResponse('Gemini API key not configured');
        }
        
        try {
            // Use the correct Gemini API endpoint - try multiple models
            $models = [
                'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent',
                'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro:generateContent',
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent'
            ];
            
            $data = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => 'Hello'
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 10
                ]
            ];
            
            // Try each model until one works
            foreach ($models as $url) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url . '?key=' . $this->geminiApiKey);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for development
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable host verification for development
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);
                
                if ($curlError) {
                    continue; // Try next model
                }
                
                if ($httpCode === 200) {
                    return $this->successResponse(['status' => 'connected', 'message' => 'Gemini API is working with ' . basename($url)]);
                }
            }
            
            // If we get here, none of the models worked
            return $this->errorResponse('Gemini API test failed: All models returned errors');
        } catch (Exception $e) {
            return $this->errorResponse('Gemini API test failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate core content using Claude
     */
    private function generateCoreContentWithClaude($message, $location = null, $history = []) {
        if (empty($this->claudeApiKey)) {
            // Fallback to Gemini or local response
            return $this->fallbackToOtherAI($message, $location, $history);
        }
        
        $url = 'https://api.anthropic.com/v1/messages';
        
        // Build context for Claude
        $context = $this->buildClaudeContext($location, $history);
        $prompt = $this->buildClaudePrompt($message, $context);
        
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
        
        if ($curlError) {
            throw new Exception('Claude API curl error: ' . $curlError);
        }
        
        if ($httpCode !== 200) {
            // Try to get more detailed error information
            $errorData = json_decode($response, true);
            $errorMessage = 'Claude API error: ' . $httpCode;
            if ($errorData && isset($errorData['error']['message'])) {
                $errorMessage .= ' - ' . $errorData['error']['message'];
            }
            throw new Exception($errorMessage);
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['content'][0]['text'])) {
            return $result['content'][0]['text'];
        } else {
            throw new Exception('Invalid response from Claude API');
        }
    }
    
    /**
     * Make response conversational using ChatGPT
     */
    private function makeConversationalWithChatGPT($coreContent, $originalMessage, $location = null) {
        if (empty($this->chatgptApiKey)) {
            // Return core content as-is if ChatGPT is not available
            return $coreContent;
        }
        
        $url = 'https://api.openai.com/v1/chat/completions';
        
        $prompt = $this->buildChatGPTPrompt($coreContent, $originalMessage, $location);
        
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant that makes special education content more conversational and engaging. Keep the same information but make it warmer and more approachable.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 2000,
            'temperature' => 0.8
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->chatgptApiKey
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for development
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable host verification for development
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            // Return core content if ChatGPT fails
            return $coreContent;
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['choices'][0]['message']['content'])) {
            return $result['choices'][0]['message']['content'];
        } else {
            return $coreContent;
        }
    }
    
    /**
     * Fallback to other AI models or local response
     */
    private function fallbackToOtherAI($message, $location = null, $history = []) {
        // Try Gemini first
        if (!empty($this->geminiApiKey)) {
            try {
                return $this->callGeminiAPI($message, $location, $history);
            } catch (Exception $e) {
                // Continue to next fallback
            }
        }
        // No canned/local response: return error
        throw new Exception('All AI services are currently unavailable. Please try again later.');
    }
    
    /**
     * Build context for Claude
     */
    private function buildClaudeContext($location = null, $history = []) {
        $context = "You are GuideAI, a compassionate AI assistant specializing in special education and IEP support. ";
        $context .= "You help families navigate the complex world of special education with expertise and empathy. ";
        $context .= "Provide comprehensive, accurate information that is both informative and supportive. ";
        $context .= "Focus on practical advice and actionable steps. ";
        
        if ($location) {
            $context .= "User location: {$location['city']}, {$location['state']}, {$location['country']}. ";
            $context .= "Include location-specific information when relevant. ";
        }
        
        if (!empty($history)) {
            $context .= "Previous conversation context: ";
            foreach (array_slice($history, -5) as $entry) {
                $context .= "{$entry['role']}: {$entry['content']} ";
            }
        }
        
        return $context;
    }
    
    /**
     * Build prompt for Claude
     */
    private function buildClaudePrompt($message, $context) {
        $prompt = $context . "\n\n";
        $prompt .= "User question: " . $message . "\n\n";
        $prompt .= "Please provide a comprehensive, informative response that includes:\n";
        $prompt .= "1. Direct answer to the user's question\n";
        $prompt .= "2. Detailed explanations and background information\n";
        $prompt .= "3. Practical steps and actionable advice\n";
        $prompt .= "4. Relevant legal information and rights\n";
        $prompt .= "5. Resources and organizations that can help\n";
        $prompt .= "6. If location-specific information is available, include it\n\n";
        $prompt .= "Format your response in HTML with proper headings, lists, and emphasis. ";
        $prompt .= "Be thorough and educational while maintaining a supportive tone.";
        
        return $prompt;
    }
    
    /**
     * Build prompt for ChatGPT to make content conversational
     */
    private function buildChatGPTPrompt($coreContent, $originalMessage, $location = null) {
        $prompt = "I have generated comprehensive content about special education, but I need to make it more conversational and engaging. ";
        $prompt .= "Here's the original user question: \"{$originalMessage}\"\n\n";
        
        if ($location) {
            $prompt .= "User location: {$location['city']}, {$location['state']}\n\n";
        }
        
        $prompt .= "Here's the core content:\n{$coreContent}\n\n";
        $prompt .= "Please make this content more conversational and engaging while:\n";
        $prompt .= "1. Keeping all the important information\n";
        $prompt .= "2. Making it warmer and more approachable\n";
        $prompt .= "3. Adding encouraging and supportive language\n";
        $prompt .= "4. Maintaining the same structure and formatting\n";
        $prompt .= "5. Making it feel like a caring conversation with a knowledgeable friend\n\n";
        $prompt .= "Return the enhanced content in HTML format.";
        
        return $prompt;
    }
    
    /**
     * Call Gemini API (fallback method)
     */
    private function callGeminiAPI($message, $location = null, $history = []) {
        if (empty($this->geminiApiKey)) {
            throw new Exception('Gemini API key not available');
        }
        
        // Try multiple Gemini models
        $models = [
            'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent',
            'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent'
        ];
        
        $context = $this->buildContext($location, $history);
        $prompt = $this->buildPrompt($message, $context);
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ]
        ];
        
        // Try each model until one works
        foreach ($models as $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '?key=' . $this->geminiApiKey);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                continue; // Try next model
            }
            
            if ($httpCode === 200) {
                $result = json_decode($response, true);
                
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    return $result['candidates'][0]['content']['parts'][0]['text'];
                }
            }
        }
        
        // If we get here, none of the models worked
        throw new Exception('All Gemini API models failed');
    }
    
    /**
     * Build context for Gemini (fallback)
     */
    private function buildContext($location = null, $history = []) {
        $context = "You are GuideAI, a compassionate AI assistant specializing in special education and IEP support. ";
        $context .= "You help families navigate the complex world of special education with empathy and expertise. ";
        $context .= "Always respond in a warm, supportive tone and provide practical, actionable advice. ";
        $context .= "If the user's language preference is Spanish, respond in Spanish. ";
        
        if ($location) {
            $context .= "User location: {$location['city']}, {$location['state']}, {$location['country']}. ";
            $context .= "Provide location-specific resources and information when relevant. ";
        }
        
        if (!empty($history)) {
            $context .= "Previous conversation context: ";
            foreach (array_slice($history, -5) as $entry) {
                $context .= "{$entry['role']}: {$entry['content']} ";
            }
        }
        
        return $context;
    }
    
    /**
     * Build the complete prompt for Gemini (fallback)
     */
    private function buildPrompt($message, $context) {
        $prompt = $context . "\n\n";
        $prompt .= "User question: " . $message . "\n\n";
        $prompt .= "Please provide a comprehensive, helpful response that includes:\n";
        $prompt .= "1. Direct answer to the user's question\n";
        $prompt .= "2. Practical steps or actionable advice\n";
        $prompt .= "3. Relevant resources or organizations\n";
        $prompt .= "4. Encouragement and support\n";
        $prompt .= "5. If location-specific information is available, include it\n\n";
        $prompt .= "Format your response in HTML with proper headings, lists, and emphasis.";
        
        return $prompt;
    }
    
    /**
     * Get location-based resources
     */
    private function getLocationResources($input) {
        $location = $input['location'] ?? null;
        
        if (!$location) {
            return $this->errorResponse('Location is required');
        }
        
        try {
            $resources = $this->fetchLocationResources($location);
            return $this->successResponse($resources);
        } catch (Exception $e) {
            return $this->errorResponse('Error fetching resources: ' . $e->getMessage());
        }
    }
    
    /**
     * Fetch resources based on location
     */
    private function fetchLocationResources($location) {
        $city = $location['city'] ?? '';
        $state = $location['state'] ?? '';
        $country = $location['country'] ?? '';
        
        // Get comprehensive location-specific resources
        $comprehensiveResources = $this->getComprehensiveLocationResources($location);
        
        // Check for Oregon-specific resources and grants
        $oregonGrantResources = [];
        if (strtolower($state) === 'oregon') {
            $oregonGrantResources = $this->resourceLinkingSystem->getOregonGrantResources();
        }
        
        $result = [
            'federal' => [
                'title' => 'Federal Resources',
                'resources' => $comprehensiveResources['federal']
            ],
            'state' => [
                'title' => "State Resources - {$state}",
                'resources' => $comprehensiveResources['state']
            ],
            'local' => [
                'title' => "Local Resources - {$city}, {$state}",
                'resources' => $comprehensiveResources['local']
            ],
            'advocacy' => [
                'title' => 'Advocacy Resources',
                'resources' => $comprehensiveResources['advocacy']
            ],
            'legal' => [
                'title' => 'Legal Resources',
                'resources' => $comprehensiveResources['legal']
            ]
        ];
        
        // Add Oregon grant resources if available
        if (!empty($oregonGrantResources)) {
            $result['oregon_grant_resources'] = $oregonGrantResources;
        }
        
        return $result;
    }
    
    /**
     * Get comprehensive location-specific resources
     */
    private function getComprehensiveLocationResources($location) {
        $state = $location['state'] ?? '';
        $city = $location['city'] ?? '';
        
        return [
            'federal' => [
                [
                    'name' => 'IDEA - Individuals with Disabilities Education Act',
                    'description' => 'Federal law ensuring special education services',
                    'url' => 'https://sites.ed.gov/idea/',
                    'phone' => '202-245-7459'
                ],
                [
                    'name' => 'Office of Special Education Programs (OSEP)',
                    'description' => 'Federal oversight of special education',
                    'url' => 'https://www2.ed.gov/about/offices/list/osers/osep/index.html',
                    'phone' => '202-245-7459'
                ],
                [
                    'name' => 'Section 504 of the Rehabilitation Act',
                    'description' => 'Civil rights law prohibiting disability discrimination',
                    'url' => 'https://www.hhs.gov/civil-rights/for-individuals/disability/section-504-rehabilitation-act-of-1973/index.html',
                    'phone' => '800-368-1019'
                ],
                [
                    'name' => 'Americans with Disabilities Act (ADA)',
                    'description' => 'Civil rights law for people with disabilities',
                    'url' => 'https://www.ada.gov/',
                    'phone' => '800-514-0301'
                ]
            ],
            'state' => $this->getStateResources($state),
            'local' => $this->getLocalResources($city, $state),
            'advocacy' => [
                [
                    'name' => 'Parent Training and Information Centers',
                    'description' => 'Free training and information for parents',
                    'url' => 'https://www.parentcenterhub.org/find-your-center/',
                    'phone' => '888-248-0822'
                ],
                [
                    'name' => 'Center for Parent Information and Resources',
                    'description' => 'Federal parent training and information center',
                    'url' => 'https://www.parentcenterhub.org/',
                    'phone' => '888-248-0822'
                ]
            ],
            'legal' => [
                [
                    'name' => 'Due Process Hearing Rights',
                    'description' => 'Procedural safeguards and hearing rights',
                    'url' => 'https://sites.ed.gov/idea/regs/b/e/300.507',
                    'phone' => '202-245-7459'
                ],
                [
                    'name' => 'Independent Educational Evaluation',
                    'description' => 'Right to request outside evaluation',
                    'url' => 'https://sites.ed.gov/idea/regs/b/e/300.502',
                    'phone' => '202-245-7459'
                ]
            ]
        ];
    }
    
    /**
     * Get state-specific resources
     */
    private function getStateResources($state) {
        // Comprehensive state-specific data
        $stateResources = [
            'Oregon' => [
                [
                    'name' => 'Oregon Department of Education - Office of Enhancing Student Opportunities',
                    'description' => 'Oregon state special education oversight and services',
                    'url' => 'https://www.oregon.gov/ode/students-and-family/SpecialEducation/Pages/default.aspx',
                    'phone' => '503-947-5600'
                ],
                [
                    'name' => 'Disability Rights Oregon',
                    'description' => 'Legal advocacy and protection for Oregonians with disabilities',
                    'url' => 'https://droregon.org/',
                    'phone' => '503-243-2081'
                ],
                [
                    'name' => 'Oregon Parent Training and Information Center (OPTIC)',
                    'description' => 'Parent training, information, and advocacy support',
                    'url' => 'https://www.orptc.org/',
                    'phone' => '888-988-9315'
                ],
                [
                    'name' => 'Oregon Developmental Disabilities Coalition',
                    'description' => 'Advocacy for people with developmental disabilities and their families',
                    'url' => 'https://www.orddcoalition.org/',
                    'phone' => '503-581-8156'
                ],
                [
                    'name' => 'Autism Society of Oregon',
                    'description' => 'Support, advocacy, and resources for autism community',
                    'url' => 'https://autismsocietyoforegon.org/',
                    'phone' => '503-636-1676'
                ],
                [
                    'name' => 'Oregon Early Intervention/Early Childhood Special Education',
                    'description' => 'Services for children birth to age 5 with disabilities',
                    'url' => 'https://www.oregon.gov/ode/students-and-family/SpecialEducation/EarlyIntervention/Pages/default.aspx',
                    'phone' => '503-947-5600'
                ]
            ],
            'California' => [
                [
                    'name' => 'California Department of Education - Special Education',
                    'description' => 'State special education information and resources',
                    'url' => 'https://www.cde.ca.gov/sp/se/',
                    'phone' => '916-445-4602'
                ],
                [
                    'name' => 'Disability Rights California',
                    'description' => 'Legal advocacy and protection for Californians with disabilities',
                    'url' => 'https://www.disabilityrightsca.org/',
                    'phone' => '800-776-5746'
                ]
            ],
            'Texas' => [
                [
                    'name' => 'Texas Education Agency - Special Education',
                    'description' => 'Texas special education resources and guidance',
                    'url' => 'https://tea.texas.gov/academics/special-student-populations/special-education',
                    'phone' => '512-463-9414'
                ],
                [
                    'name' => 'Disability Rights Texas',
                    'description' => 'Legal advocacy and protection for Texans with disabilities',
                    'url' => 'https://www.disabilityrightstx.org/',
                    'phone' => '800-252-9108'
                ]
            ],
            'New York' => [
                [
                    'name' => 'New York State Education Department - Special Education',
                    'description' => 'New York special education resources and guidance',
                    'url' => 'https://www.nysed.gov/special-education',
                    'phone' => '518-474-3852'
                ],
                [
                    'name' => 'Disability Rights New York',
                    'description' => 'Legal advocacy and protection for New Yorkers with disabilities',
                    'url' => 'https://www.drny.org/',
                    'phone' => '800-993-8982'
                ]
            ],
            'Florida' => [
                [
                    'name' => 'Florida Department of Education - Exceptional Student Education',
                    'description' => 'Florida special education resources and guidance',
                    'url' => 'https://www.fldoe.org/academics/exceptional-student-edu/',
                    'phone' => '850-245-0475'
                ],
                [
                    'name' => 'Disability Rights Florida',
                    'description' => 'Legal advocacy and protection for Floridians with disabilities',
                    'url' => 'https://disabilityrightsflorida.org/',
                    'phone' => '800-342-0823'
                ]
            ]
        ];
        
        return $stateResources[$state] ?? [
            [
                'name' => "{$state} Department of Education",
                'description' => 'Contact your state education department for local resources',
                'url' => 'https://www.ed.gov/state-contacts',
                'phone' => 'Check state website'
            ],
            [
                'name' => "Disability Rights {$state}",
                'description' => 'Legal advocacy and protection for people with disabilities',
                'url' => 'https://www.ndrn.org/ndrn-member-agencies.html',
                'phone' => 'Check state website'
            ]
        ];
    }
    
    /**
     * Get local resources
     */
    private function getLocalResources($city, $state) {
        // Enhanced local resources with more specific information
        $localResources = [
            [
                'name' => 'Local School District Special Education Office',
                'description' => 'Contact your school district for local services and IEP support',
                'url' => 'https://www.greatschools.org/',
                'phone' => 'Check district website'
            ],
            [
                'name' => 'Local Parent Support Groups',
                'description' => 'Connect with other parents in your area for support and information',
                'url' => 'https://www.parentcenterhub.org/find-your-center/',
                'phone' => 'Search local directories'
            ],
            [
                'name' => 'Local Disability Services',
                'description' => 'County or city disability services and support programs',
                'url' => 'https://www.211.org/',
                'phone' => 'Dial 211 for local services'
            ],
            [
                'name' => 'Local Special Education Advocates',
                'description' => 'Professional advocates who can help with IEP meetings and disputes',
                'url' => 'https://www.copaa.org/',
                'phone' => 'Check local directories'
            ]
        ];
        
        // Add state-specific local resources
        if (strtolower($state) === 'oregon') {
            $oregonLocalResources = [
                [
                    'name' => 'Oregon Family Support Network',
                    'description' => 'Statewide network of family support groups',
                    'url' => 'https://www.ofsn.org/',
                    'phone' => '503-786-6082'
                ],
                [
                    'name' => 'Oregon Special Education Mediation',
                    'description' => 'Free mediation services for special education disputes',
                    'url' => 'https://www.oregon.gov/ode/students-and-family/SpecialEducation/Pages/mediation.aspx',
                    'phone' => '503-947-5600'
                ]
            ];
            $localResources = array_merge($oregonLocalResources, $localResources);
        }
        
        return $localResources;
    }
    
    /**
     * Get user location from coordinates
     */
    private function getUserLocation($input) {
        $lat = $input['lat'] ?? null;
        $lng = $input['lng'] ?? null;
        
        if (!$lat || !$lng) {
            return $this->errorResponse('Latitude and longitude are required');
        }
        
        try {
            $location = $this->reverseGeocode($lat, $lng);
            return $this->successResponse($location);
        } catch (Exception $e) {
            return $this->errorResponse('Error getting location: ' . $e->getMessage());
        }
    }
    
    /**
     * Reverse geocoding using OpenCage API
     */
    private function reverseGeocode($lat, $lng) {
        if (empty($this->openCageApiKey)) {
            // Fallback to basic location info
            return [
                'city' => 'Unknown',
                'state' => 'Unknown',
                'country' => 'Unknown',
                'coordinates' => ['lat' => $lat, 'lng' => $lng]
            ];
        }
        
        $url = "https://api.opencagedata.com/geocode/v1/json?q={$lat}+{$lng}&key={$this->openCageApiKey}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for development
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable host verification for development
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Geocoding API error: ' . $httpCode);
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['results'][0]['components'])) {
            $components = $result['results'][0]['components'];
            return [
                'city' => $components['city'] ?? $components['town'] ?? 'Unknown',
                'state' => $components['state'] ?? 'Unknown',
                'country' => $components['country'] ?? 'Unknown',
                'coordinates' => ['lat' => $lat, 'lng' => $lng]
            ];
        } else {
            throw new Exception('Invalid geocoding response');
        }
    }
    
    /**
     * Success response helper
     */
    private function successResponse($data) {
        return json_encode([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Error response helper
     */
    private function errorResponse($message) {
        return json_encode([
            'success' => false,
            'error' => $message
        ]);
    }
}

// Initialize and handle request with error handling
try {
    $api = new GuideAIMultiAI();
    echo $api->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error: ' . $e->getMessage()
    ]);
}
?> 