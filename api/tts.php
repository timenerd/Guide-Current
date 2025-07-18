<?php
/**
 * GuideAI TTS API
 * OpenAI Text-to-Speech integration for read aloud functionality
 */

require_once '../config.php';
require_once '../languages.php';

// Set headers for audio response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get request data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['text'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Text parameter is required']);
    exit();
}

$text = trim($input['text']);
$voice = $input['voice'] ?? 'alloy'; // Default voice
$language = $input['language'] ?? 'en'; // Default language
$speed = $input['speed'] ?? 1.0; // Default speed

// Validate text length (OpenAI TTS has a limit of 4096 characters)
if (strlen($text) > 4096) {
    http_response_code(400);
    echo json_encode(['error' => 'Text too long. Maximum 4096 characters allowed.']);
    exit();
}

// Validate voice
$validVoices = ['alloy', 'echo', 'fable', 'onyx', 'nova', 'shimmer'];
if (!in_array($voice, $validVoices)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid voice. Valid voices: ' . implode(', ', $validVoices)]);
    exit();
}

// Validate speed
if ($speed < 0.25 || $speed > 4.0) {
    http_response_code(400);
    echo json_encode(['error' => 'Speed must be between 0.25 and 4.0']);
    exit();
}

// Check if OpenAI API key is available
if (!defined('CHATGPT_API_KEY') || empty(CHATGPT_API_KEY)) {
    http_response_code(500);
    echo json_encode([
        'error' => 'OpenAI API key not configured',
        'fallback_available' => true,
        'message' => 'TTS service is not configured. Please contact support.'
    ]);
    exit();
}

try {
    // Initialize language manager for text processing
    $languageManager = new LanguageManager($language);
    
    // Clean and prepare text
    $cleanText = strip_tags($text);
    $cleanText = preg_replace('/\s+/', ' ', $cleanText); // Normalize whitespace
    
    // OpenAI TTS API endpoint
    $url = 'https://api.openai.com/v1/audio/speech';
    
    // Prepare request data
    $requestData = [
        'model' => 'tts-1', // OpenAI's TTS model
        'input' => $cleanText,
        'voice' => $voice,
        'speed' => $speed
    ];
    
    // Initialize cURL
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestData),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . CHATGPT_API_KEY,
            'Content-Type: application/json',
            'User-Agent: GuideAI-TTS/1.0'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3
    ]);
    
    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    if ($error) {
        throw new Exception("cURL error: " . $error);
    }
    
    if ($httpCode !== 200) {
        // Try to parse error response
        $errorData = json_decode($response, true);
        $errorMessage = $errorData['error']['message'] ?? 'Unknown error';
        throw new Exception("OpenAI API error ($httpCode): " . $errorMessage);
    }
    
    // Convert audio data to base64 for frontend
    $audioBase64 = base64_encode($response);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'audio' => $audioBase64,
        'format' => 'mp3',
        'voice' => $voice,
        'language' => $language,
        'speed' => $speed,
        'text_length' => strlen($cleanText),
        'timestamp' => date('c')
    ]);
    
} catch (Exception $e) {
    debugLog("TTS API Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'error' => 'TTS generation failed',
        'message' => $e->getMessage(),
        'fallback_available' => true
    ]);
}
?> 