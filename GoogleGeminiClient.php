<?php

class GoogleGeminiClient {
    private $apiKey;
    private $model = 'gemini-1.5-flash';
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct($apiKey, $model = null) {
        $this->apiKey = $apiKey;
        if ($model) {
            $this->model = $model;
        }
    }

    public function hasValidKey() {
        return !empty($this->apiKey);
    }

    public function getCompletion($prompt, $options = []) {
        if (!$this->hasValidKey()) {
            throw new Exception('Gemini API key not configured');
        }

        if (empty($prompt)) {
            throw new Exception('Prompt cannot be empty');
        }

        $url = $this->baseUrl . $this->model . ':generateContent?key=' . $this->apiKey;
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? 0.7,
                'maxOutputTokens' => $options['max_tokens'] ?? 2000,
                'topP' => 0.8,
                'topK' => 10
            ]
        ];

        $ch = curl_init($url);
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Gemini API request failed: ' . $error);
        }
        
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Gemini API returned HTTP ' . $httpCode . ': ' . $response);
        }

        $decoded = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from Gemini API');
        }

        if (isset($decoded['error'])) {
            throw new Exception('Gemini API error: ' . $decoded['error']['message']);
        }

        if (!isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
            throw new Exception('Unexpected response structure from Gemini API');
        }

        return $decoded['candidates'][0]['content']['parts'][0]['text'];
    }

    public function setModel($model) {
        $this->model = $model;
    }

    public function getModel() {
        return $this->model;
    }
}

?> 