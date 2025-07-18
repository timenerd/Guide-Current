<?php
/**
 * Configuration Example File
 * 
 * Copy this file to config.php and fill in your actual API keys
 * Never commit the actual config.php file with real API keys
 */

// API Configuration
$config = [
    // Anthropic/Claude API Configuration
    'anthropic_api_key' => 'your_anthropic_api_key_here',
    'anthropic_model' => 'claude-3-sonnet-20240229',
    
    // OpenAI API Configuration  
    'openai_api_key' => 'your_openai_api_key_here',
    'openai_model' => 'gpt-4',
    
    // Google Gemini API Configuration
    'gemini_api_key' => 'your_gemini_api_key_here',
    'gemini_model' => 'gemini-pro',
    
    // Application Settings
    'debug' => false,
    'log_level' => 'info',
    'cache_enabled' => true,
    'max_tokens' => 4000,
    'temperature' => 0.7,
    
    // Database Configuration (if needed)
    'db_host' => 'localhost',
    'db_name' => 'your_database_name',
    'db_user' => 'your_database_user',
    'db_pass' => 'your_database_password',
];

// Export configuration
return $config;
?> 