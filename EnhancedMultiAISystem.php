<?php
// EnhancedMultiAISystem.php

require_once __DIR__ . '/MultiAISystem.php';
require_once __DIR__ . '/ResourceLinkingSystem.php';

// Include Parsedown from vendor if it exists
if (file_exists(__DIR__ . '/vendor/erusev/parsedown/Parsedown.php')) {
    require_once __DIR__ . '/vendor/erusev/parsedown/Parsedown.php';
} elseif (file_exists(__DIR__ . '/Parsedown.php')) {
    require_once __DIR__ . '/Parsedown.php';
}

require_once __DIR__ . '/OpenAIClient.php';
require_once __DIR__ . '/ClaudeClient.php';

// Only include GoogleGeminiClient if it exists
if (file_exists(__DIR__ . '/GoogleGeminiClient.php')) {
    require_once __DIR__ . '/GoogleGeminiClient.php';
}

class EnhancedMultiAISystem extends MultiAISystem {
    private $resource_linker;
    private $parsedown;
    private $samplePrompts;
    private $availableServices = [];
    
    public function __construct($config) {
        // Call parent constructor first
        parent::__construct($config);
        
        // Validate required configuration
        $this->validateConfiguration($config);
        
        // Initialize API clients with error handling
        try {
            $this->initializeAPIClients($config);
            
            // Initialize resource linking system if available
            if (class_exists('ResourceLinkingSystem')) {
                $this->resource_linker = new ResourceLinkingSystem();
            } else {
                $this->resource_linker = null;
                error_log('ResourceLinkingSystem class not found - continuing without resource linking');
            }
            
            // Initialize Parsedown for Markdown processing
            if (class_exists('Parsedown')) {
                $this->parsedown = new Parsedown();
                $this->parsedown->setSafeMode(true);
            }
            
            // Initialize sample prompts from language manager if available
            $this->samplePrompts = $this->initializeSamplePrompts();
            
        } catch (Exception $e) {
            error_log('Error initializing Enhanced AI system: ' . $e->getMessage());
            throw new Exception('Failed to initialize Enhanced AI system: ' . $e->getMessage());
        }
    }
    
    /**
     * Validate required configuration fields
     */
    private function validateConfiguration(&$config) {
        $required = ['defaults'];
        $missing = [];
        
        foreach ($required as $field) {
            if (!isset($config[$field])) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            throw new Exception('Missing required configuration fields: ' . implode(', ', $missing));
        }
        
        // Validate and set defaults
        if (!isset($config['defaults']['max_tokens'])) {
            $config['defaults']['max_tokens'] = 1000;
        }
        if (!isset($config['defaults']['temperature'])) {
            $config['defaults']['temperature'] = 0.7;
        }
    }
    
    /**
     * Initialize available API clients
     */
    private function initializeAPIClients($config) {
        // Initialize OpenAI (required)
        if (isset($config['openai_api_key']) && !empty($config['openai_api_key'])) {
            try {
                $this->apis['openai'] = new OpenAIClient($config['openai_api_key'], $config['models']['openai_model'] ?? 'gpt-3.5-turbo');
                $this->availableServices[] = 'openai';
            } catch (Exception $e) {
                error_log('Failed to initialize OpenAI client: ' . $e->getMessage());
            }
        }
        
        // Initialize Claude (optional)
        if (isset($config['claude_api_key']) && !empty($config['claude_api_key']) && class_exists('ClaudeClient')) {
            try {
                $this->apis['claude'] = new ClaudeClient($config['claude_api_key'], $config['models']['claude_model'] ?? 'claude-3-sonnet-20240229');
                $this->availableServices[] = 'claude';
            } catch (Exception $e) {
                error_log('Failed to initialize Claude client: ' . $e->getMessage());
            }
        }
        
        // Initialize Gemini (optional, only if class exists)
        if (class_exists('GoogleGeminiClient') && isset($config['gemini_api_key']) && !empty($config['gemini_api_key'])) {
            try {
                $this->apis['gemini'] = new GoogleGeminiClient($config['gemini_api_key'], $config['models']['gemini_model'] ?? 'gemini-pro');
                $this->availableServices[] = 'gemini';
            } catch (Exception $e) {
                error_log('Failed to initialize Gemini client: ' . $e->getMessage());
            }
        }
        
        // Ensure we have at least one working API
        if (empty($this->availableServices)) {
            throw new Exception('No AI services could be initialized. Please check your API keys and configuration.');
        }
        
        error_log('Enhanced Multi-AI System initialized with services: ' . implode(', ', $this->availableServices));
    }
    
    /**
     * Enhanced IEP Assistant with improved error handling and response processing
     */
    public function iepAssistant($question, $context = []) {
        try {
            // Initialize response structure compatible with frontend
            $response = [
                'mega_response' => null,
                'synthesis_successful' => false,
                'synthesis_error_message' => null,
                'processed_initial_responses' => [],
                'initial_responses_raw' => [],
                'related_readings' => [],
                'errors' => [],
                'services_used' => [],
                'debug_info' => []
            ];

            // Validate input
            if (empty($question)) {
                throw new Exception('Question cannot be empty');
            }

            // Add debug info
            $response['debug_info']['available_services'] = $this->availableServices;
            $response['debug_info']['timestamp'] = date('c');

            // Get initial responses from all AIs
            try {
                $initialResponses = $this->getInitialResponses($question, $context);
                $response['initial_responses_raw'] = $initialResponses;
                $response['services_used'] = array_keys($initialResponses);
            } catch (Exception $e) {
                error_log("Error getting initial responses: " . $e->getMessage());
                $response['errors']['initial_responses'] = $e->getMessage();
                return $response;
            }
            
            // Process initial responses
            foreach ($initialResponses as $ai => $result) {
                if (isset($result['error'])) {
                    $response['errors'][$ai] = $result['error'];
                    error_log("Error from {$ai}: " . $result['error']);
                } else {
                    $response['processed_initial_responses'][$ai] = [
                        'response' => $result['response']
                    ];
                }
            }

            // Get and process the best response
            try {
                $bestResponse = $this->getBestResponse($response['processed_initial_responses']);
                if ($bestResponse) {
                    // Process the response with markdown and resource enhancement
                    $processedResponse = $this->processResponse($bestResponse, $context);
                    $response['mega_response'] = $processedResponse;
                    $response['synthesis_successful'] = true;
                } else {
                    $response['synthesis_error_message'] = 'No valid responses received from any AI service';
                }
            } catch (Exception $e) {
                error_log("Error processing best response: " . $e->getMessage());
                $response['synthesis_error_message'] = $e->getMessage();
            }

            // Get related readings
            try {
                $response['related_readings'] = $this->getRelatedReadings($question);
            } catch (Exception $e) {
                error_log("Error getting related readings: " . $e->getMessage());
                $response['errors']['related_readings'] = $e->getMessage();
            }

            return $response;

        } catch (Exception $e) {
            error_log('Enhanced IEP Assistant error: ' . $e->getMessage());
            return [
                'mega_response' => null,
                'synthesis_successful' => false,
                'synthesis_error_message' => 'System error: ' . $e->getMessage(),
                'processed_initial_responses' => [],
                'initial_responses_raw' => [],
                'related_readings' => [],
                'errors' => ['system' => $e->getMessage()],
                'services_used' => [],
                'debug_info' => ['error_details' => $e->getMessage(), 'timestamp' => date('c')]
            ];
        }
    }

    /**
     * Get initial responses from all available APIs
     */
    private function getInitialResponses($question, $context) {
        $responses = [];
        $errors = [];
        
        // Check if we have any available APIs
        if (empty($this->apis)) {
            throw new Exception('No AI APIs are configured. Please check your API keys.');
        }
        
        // Try each available API
        foreach ($this->apis as $ai => $client) {
            try {
                // Check if client has required methods
                if (!method_exists($client, 'hasValidKey') || !method_exists($client, 'getCompletion')) {
                    $errors[$ai] = "Client {$ai} missing required methods";
                    continue;
                }
                
                if (!$client->hasValidKey()) {
                    $errors[$ai] = "API key not configured for {$ai}";
                    continue;
                }
                
                $response = $client->getCompletion($question, [
                    'max_tokens' => $this->config['defaults']['max_tokens'],
                    'temperature' => $this->config['defaults']['temperature']
                ]);
                
                if (!empty($response)) {
                    $responses[$ai] = [
                        'response' => $response,
                        'timestamp' => date('c')
                    ];
                } else {
                    $errors[$ai] = "Empty response from {$ai}";
                }
            } catch (Exception $e) {
                error_log("Error from {$ai}: " . $e->getMessage());
                $errors[$ai] = $e->getMessage();
            }
        }
        
        // If we have no successful responses, throw an exception
        if (empty($responses)) {
            $errorMessage = 'No successful responses from any AI service. Errors: ' . implode(', ', $errors);
            error_log($errorMessage);
            throw new Exception($errorMessage);
        }
        
        return $responses;
    }

    /**
     * Get the best available response based on priority
     */
    private function getBestResponse($responses) {
        if (empty($responses)) {
            return null;
        }

        // Prioritize responses based on quality and availability
        $priority = ['claude', 'openai', 'gemini'];
        foreach ($priority as $ai) {
            if (isset($responses[$ai]['response']) && !empty($responses[$ai]['response'])) {
                return $responses[$ai]['response'];
            }
        }

        // If no prioritized response is available, return the first non-empty response
        foreach ($responses as $response) {
            if (isset($response['response']) && !empty($response['response'])) {
                return $response['response'];
            }
        }

        return null;
    }

    /**
     * Get related sample prompts based on user query
     */
    public function getRelatedReadings($user_query) {
        $relatedReadings = [];
        if (empty(trim($user_query))) {
            return $relatedReadings;
        }

        $user_query_lower = strtolower($user_query);
        $user_query_words = preg_split('/\s+/', $user_query_lower);

        $scores = [];

        foreach ($this->samplePrompts as $sample) {
            $prompt_lower = strtolower($sample['prompt']);
            $prompt_keywords = array_map('strtolower', $sample['keywords']);
            $score = 0;

            // Check for direct keyword matches
            foreach ($prompt_keywords as $keyword) {
                if (strpos($user_query_lower, $keyword) !== false) {
                    $score += 2; // Higher score for keyword match
                }
            }

            // Check for common words with user query
            foreach ($user_query_words as $user_word) {
                if (strlen($user_word) > 3 && strpos($prompt_lower, $user_word) !== false) {
                    $score++;
                }
            }
            
            if ($score > 0) {
                $scores[] = ['prompt' => $sample['prompt'], 'score' => $score, 'id' => $sample['id']];
            }
        }

        // Sort by score descending
        usort($scores, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Get top 3 relevant prompts
        $top_matches = array_slice($scores, 0, 3);
        
        foreach($top_matches as $match){
            $relatedReadings[] = ['id' => $match['id'], 'prompt' => $match['prompt']];
        }

        return $relatedReadings;
    }

    /**
     * Process response with markdown and comprehensive resource links
     */
    private function processResponse($response, $context) {
        if (empty($response)) {
            return '';
        }

        try {
            // Convert markdown to HTML if Parsedown is available
            if ($this->parsedown) {
                $html_content = $this->parsedown->text($response);
            } else {
                // Fallback: simple nl2br conversion with HTML escaping
                $html_content = nl2br(htmlspecialchars($response, ENT_QUOTES, 'UTF-8'));
            }
            
            // Only apply resource linking if ResourceLinkingSystem is available
            if ($this->resource_linker) {
                // Link phone numbers
                $html_content = $this->resource_linker->linkPhoneNumbersInHtml($html_content);
                
                // Enhance with comprehensive resource links in correct order
                $html_content = $this->enhanceWithComprehensiveResources(
                    $html_content,
                    $context['user_location_string'] ?? null,
                    $context
                );
            }

            return $html_content;
            
        } catch (Exception $e) {
            error_log('Error processing response: ' . $e->getMessage());
            // Return the original response if processing fails
            return htmlspecialchars($response, ENT_QUOTES, 'UTF-8');
        }
    }

    /**
     * Enhanced resource linking with comprehensive coverage and proper ordering
     */
    private function enhanceWithComprehensiveResources($html_content, $user_location = null, $context = []) {
        $plain_text_content = strtolower(strip_tags($html_content));
        $found_resources = [];
        $used_urls = []; // Track used URLs to prevent duplicates

        // 1. LOCAL RESOURCES (City/Municipal level)
        $local_resources = $this->getLocalResources($plain_text_content, $user_location, $used_urls);
        if (!empty($local_resources)) {
            $found_resources['local'] = $local_resources;
        }

        // 2. COUNTY RESOURCES
        $county_resources = $this->getCountyResources($plain_text_content, $user_location, $used_urls);
        if (!empty($county_resources)) {
            $found_resources['county'] = $county_resources;
        }

        // 3. STATE RESOURCES
        $state_resources = $this->getStateResources($plain_text_content, $user_location, $used_urls);
        if (!empty($state_resources)) {
            $found_resources['state'] = $state_resources;
        }

        // 4. FEDERAL RESOURCES
        $federal_resources = $this->getFederalResources($plain_text_content, $used_urls);
        if (!empty($federal_resources)) {
            $found_resources['federal'] = $federal_resources;
        }

        // 5. ADVOCACY & LEGAL RESOURCES
        $advocacy_resources = $this->getAdvocacyResources($plain_text_content, $used_urls);
        if (!empty($advocacy_resources)) {
            $found_resources['advocacy'] = $advocacy_resources;
        }

        $legal_resources = $this->getLegalResources($plain_text_content, $used_urls);
        if (!empty($legal_resources)) {
            $found_resources['legal'] = $legal_resources;
        }

        // Append comprehensive resource section if resources were found
        if (!empty($found_resources)) {
            $html_content .= $this->buildComprehensiveResourceSection($found_resources, $user_location);
        }

        return $html_content;
    }

    /**
     * Get local (city/municipal) resources
     */
    private function getLocalResources($content, $user_location, &$used_urls) {
        $resources = [];
        
        // Detect local resources based on content keywords
        $local_keywords = ['local', 'city', 'municipal', 'community', 'neighborhood'];
        $has_local_content = false;
        
        foreach ($local_keywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                $has_local_content = true;
                break;
            }
        }

        if ($has_local_content && $user_location) {
            // Add local parent support groups
            $local_support = $this->getLocalParentSupportGroups($user_location);
            foreach ($local_support as $resource) {
                if (!in_array($resource['url'], $used_urls)) {
                    $resources[] = $resource;
                    $used_urls[] = $resource['url'];
                }
            }
        }

        return $resources;
    }

    /**
     * Get county-level resources
     */
    private function getCountyResources($content, $user_location, &$used_urls) {
        $resources = [];
        
        if ($user_location && $this->resource_linker) {
            // Use existing resource linker to get county resources
            $county_links = $this->resource_linker->detectAndLinkStateResources($content, $user_location);
            
            if (isset($county_links['county_resources'])) {
                foreach ($county_links['county_resources'] as $resource) {
                    if (!in_array($resource['url'], $used_urls)) {
                        $resources[] = $resource;
                        $used_urls[] = $resource['url'];
                    }
                }
            }
        }

        return $resources;
    }

    /**
     * Get state-level resources
     */
    private function getStateResources($content, $user_location, &$used_urls) {
        $resources = [];
        
        if ($user_location && $this->resource_linker) {
            // Use existing resource linker to get state resources
            $state_links = $this->resource_linker->detectAndLinkStateResources($content, $user_location);
            
            // Filter out county resources and get only state-level
            foreach ($state_links as $category => $category_resources) {
                if ($category !== 'county_resources' && is_array($category_resources)) {
                    foreach ($category_resources as $resource) {
                        if (!in_array($resource['url'], $used_urls)) {
                            $resources[] = $resource;
                            $used_urls[] = $resource['url'];
                        }
                    }
                }
            }
        }

        return $resources;
    }

    /**
     * Get federal resources
     */
    private function getFederalResources($content, &$used_urls) {
        $resources = [];
        
        if ($this->resource_linker) {
            $federal_links = $this->resource_linker->detectAndLinkFederalResources($content);
            
            foreach ($federal_links as $resource) {
                if (!in_array($resource['url'], $used_urls)) {
                    $resources[] = $resource;
                    $used_urls[] = $resource['url'];
                }
            }
        }

        return $resources;
    }

    /**
     * Get advocacy resources
     */
    private function getAdvocacyResources($content, &$used_urls) {
        $resources = [];
        
        if ($this->resource_linker) {
            $advocacy_links = $this->resource_linker->detectAndLinkAdvocacyResources($content);
            
            foreach ($advocacy_links as $resource) {
                if (!in_array($resource['url'], $used_urls)) {
                    $resources[] = $resource;
                    $used_urls[] = $resource['url'];
                }
            }
        }

        return $resources;
    }

    /**
     * Get legal resources
     */
    private function getLegalResources($content, &$used_urls) {
        $resources = [];
        
        if ($this->resource_linker) {
            $legal_links = $this->resource_linker->detectAndLinkLegalResources($content);
            
            foreach ($legal_links as $resource) {
                if (!in_array($resource['url'], $used_urls)) {
                    $resources[] = $resource;
                    $used_urls[] = $resource['url'];
                }
            }
        }

        return $resources;
    }

    /**
     * Get local parent support groups
     */
    private function getLocalParentSupportGroups($location) {
        // This would be populated with actual local support groups
        // For now, return a sample structure
        return [
            [
                'name' => 'Local Parent Support Network',
                'url' => '#',
                'description' => 'Community-based parent support and advocacy group',
                'phone' => '(555) 123-4567',
                'type' => 'Support Group'
            ]
        ];
    }

    /**
     * Build comprehensive resource section with beautiful formatting
     */
    private function buildComprehensiveResourceSection($found_resources, $user_location = null) {
        if (empty($found_resources)) {
            return '';
        }

        $html = '<div class="comprehensive-resource-section mt-4">';
        $html .= '<div class="resource-header">';
        $html .= '<h4 class="resource-title"><i class="fas fa-compass me-2"></i>Comprehensive Resource Guide</h4>';
        $html .= '<p class="resource-subtitle">Resources organized by jurisdiction level for easy navigation</p>';
        $html .= '</div>';

        // Define the order and styling for each resource level
        $resource_levels = [
            'local' => [
                'title' => 'Local Resources',
                'icon' => 'fas fa-home',
                'color' => 'primary',
                'description' => 'City and community-level services'
            ],
            'county' => [
                'title' => 'County Resources',
                'icon' => 'fas fa-map-marker-alt',
                'color' => 'success',
                'description' => 'County-level programs and services'
            ],
            'state' => [
                'title' => 'State Resources',
                'icon' => 'fas fa-flag',
                'color' => 'warning',
                'description' => 'State-level agencies and programs'
            ],
            'federal' => [
                'title' => 'Federal Resources',
                'icon' => 'fas fa-landmark',
                'color' => 'danger',
                'description' => 'Federal laws, agencies, and programs'
            ],
            'advocacy' => [
                'title' => 'Advocacy Organizations',
                'icon' => 'fas fa-bullhorn',
                'color' => 'info',
                'description' => 'Advocacy and support organizations'
            ],
            'legal' => [
                'title' => 'Legal Resources',
                'icon' => 'fas fa-gavel',
                'color' => 'dark',
                'description' => 'Legal information and assistance'
            ]
        ];

        $has_resources = false;

        foreach ($resource_levels as $level => $level_info) {
            if (isset($found_resources[$level]) && !empty($found_resources[$level])) {
                $has_resources = true;
                $resources = $found_resources[$level];
                
                $html .= '<div class="resource-level mb-4">';
                $html .= '<div class="resource-level-header">';
                $html .= '<h5 class="resource-level-title">';
                $html .= '<i class="' . $level_info['icon'] . ' me-2 text-' . $level_info['color'] . '"></i>';
                $html .= htmlspecialchars($level_info['title']);
                $html .= '</h5>';
                $html .= '<p class="resource-level-description text-muted small">' . htmlspecialchars($level_info['description']) . '</p>';
                $html .= '</div>';
                
                $html .= '<div class="resource-cards">';
                foreach ($resources as $resource) {
                    if (!is_array($resource) || empty($resource['name'])) continue;
                    
                    $html .= '<div class="resource-card">';
                    $html .= '<div class="resource-card-header">';
                    
                    if (isset($resource['url'])) {
                        $html .= '<a href="' . htmlspecialchars($resource['url']) . '" target="_blank" rel="noopener noreferrer" class="resource-link">';
                        $html .= '<i class="fas fa-external-link-alt me-2"></i>';
                        $html .= '<strong>' . htmlspecialchars($resource['name']) . '</strong>';
                        $html .= '</a>';
                    } else {
                        $html .= '<i class="fas fa-info-circle me-2"></i>';
                        $html .= '<strong>' . htmlspecialchars($resource['name']) . '</strong>';
                    }
                    
                    $html .= '</div>';
                    
                    if (isset($resource['description']) && !empty($resource['description'])) {
                        $html .= '<p class="resource-description">' . htmlspecialchars($resource['description']) . '</p>';
                    }
                    
                    $html .= '<div class="resource-details">';
                    if (isset($resource['phone']) && !empty($resource['phone'])) {
                        $phone_digits = preg_replace('/[^0-9+]/', '', $resource['phone']);
                        $html .= '<div class="resource-phone">';
                        $html .= '<i class="fas fa-phone-alt me-1"></i>';
                        $html .= '<a href="tel:' . $phone_digits . '">' . htmlspecialchars($resource['phone']) . '</a>';
                        $html .= '</div>';
                    }
                    
                    if (isset($resource['type']) && !empty($resource['type'])) {
                        $html .= '<div class="resource-type">';
                        $html .= '<span class="badge bg-light text-dark">' . htmlspecialchars($resource['type']) . '</span>';
                        $html .= '</div>';
                    }
                    
                    if (isset($resource['eligibility']) && !empty($resource['eligibility'])) {
                        $html .= '<div class="resource-eligibility">';
                        $html .= '<small class="text-muted"><i class="fas fa-user-check me-1"></i>' . htmlspecialchars($resource['eligibility']) . '</small>';
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                    $html .= '</div>';
                }
                $html .= '</div>';
                $html .= '</div>';
            }
        }

        if (!$has_resources) {
            return '';
        }

        // Add footer with disclaimers and additional information
        $html .= '<div class="resource-footer mt-4">';
        $html .= '<div class="alert alert-info">';
        $html .= '<h6><i class="fas fa-info-circle me-2"></i>Important Information</h6>';
        $html .= '<ul class="mb-0">';
        $html .= '<li>Resources are provided for informational purposes only</li>';
        $html .= '<li>Please verify current information and eligibility requirements</li>';
        $html .= '<li>Contact organizations directly for the most up-to-date information</li>';
        $html .= '<li>This list is not exhaustive - additional resources may be available</li>';
        $html .= '</ul>';
        $html .= '</div>';
        
        if ($user_location) {
            $html .= '<p class="text-muted small">Resources tailored for: <strong>' . htmlspecialchars($user_location) . '</strong></p>';
        }
        
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
    
    /**
     * Initialize sample prompts from language manager
     */
    private function initializeSamplePrompts() {
        // Try to use language manager if available
        if (class_exists('LanguageManager')) {
            global $language_manager;
            if (isset($language_manager) && method_exists($language_manager, 'getSamplePrompts')) {
                return $language_manager->getSamplePrompts();
            }
        }
        
        // Fallback to hardcoded English prompts
        return [
            ["id" => "prep_steps", "prompt" => "Outline steps to prepare for an IEP meeting", "keywords" => ["prepare", "iep meeting", "steps", "checklist"]],
            ["id" => "adhd_accommodations", "prompt" => "Explain common accommodations for a student with ADHD", "keywords" => ["adhd", "accommodations", "student", "attention deficit"]],
            ["id" => "sample_agenda", "prompt" => "Generate a sample agenda for my child's IEP meeting", "keywords" => ["agenda", "iep meeting", "sample", "child's"]],
            ["id" => "key_questions", "prompt" => "List key questions to ask teachers during an IEP discussion", "keywords" => ["questions", "teachers", "iep discussion", "ask"]],
            ["id" => "jargon_translation", "prompt" => "Translate IEP jargon into parent-friendly language", "keywords" => ["translate", "jargon", "parent-friendly", "language", "terms"]],
            ["id" => "idea_rights", "prompt" => "Summarize my child's rights under IDEA", "keywords" => ["rights", "idea", "summarize", "child's", "federal law"]],
            ["id" => "virtual_learning", "prompt" => "Suggest strategies for supporting virtual learning accommodations", "keywords" => ["virtual learning", "strategies", "supporting", "accommodations", "online"]],
            ["id" => "collaboration_tips", "prompt" => "Provide tips for collaborating effectively with school staff", "keywords" => ["tips", "collaborating", "school staff", "teamwork", "communication"]]
        ];
    }
    
    /**
     * Get list of available AI services
     */
    public function getAvailableServices() {
        return $this->availableServices;
    }
    
    /**
     * Check if a specific service is available
     */
    public function isServiceAvailable($service) {
        return in_array($service, $this->availableServices);
    }
}
?>