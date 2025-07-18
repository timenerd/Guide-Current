<?php
// MultiAISystem.php - Base class for multi-AI system

class MultiAISystem {
    protected $config;
    protected $apis = [];
    
    public function __construct($config = []) {
        $this->config = $config;
    }
    
    /**
     * Get a response from the best available AI service
     */
    public function getResponse($prompt, $context = []) {
        $errors = [];
        
        // Try each API in priority order
        $priority = ['claude', 'openai', 'gemini'];
        
        foreach ($priority as $service) {
            if (isset($this->apis[$service])) {
                try {
                    $response = $this->apis[$service]->getCompletion($prompt, $context);
                    if (!empty($response)) {
                        return [
                            'success' => true,
                            'response' => $response,
                            'service' => $service,
                            'timestamp' => date('c')
                        ];
                    }
                } catch (Exception $e) {
                    $errors[$service] = $e->getMessage();
                    error_log("MultiAI Error ($service): " . $e->getMessage());
                }
            }
        }
        
        // If all APIs fail, return error
        return [
            'success' => false,
            'error' => 'All AI services unavailable',
            'errors' => $errors,
            'timestamp' => date('c')
        ];
    }
    
    /**
     * Check which APIs are available
     */
    public function getAvailableServices() {
        $available = [];
        
        foreach ($this->apis as $service => $client) {
            try {
                if ($client->hasValidKey()) {
                    $available[] = $service;
                }
            } catch (Exception $e) {
                // Service not available
            }
        }
        
        return $available;
    }
    
    /**
     * Get system status
     */
    public function getStatus() {
        return [
            'available_services' => $this->getAvailableServices(),
            'total_services' => count($this->apis),
            'timestamp' => date('c')
        ];
    }
}