<?php

class ClaudeClient {
    private $apiKey;
    private $model;
    private $baseUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct($apiKey, $model = 'claude-3-haiku-20240307') {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    public function hasValidKey() {
        return !empty($this->apiKey) && $this->apiKey !== 'your-claude-api-key';
    }

    public function getCompletion($prompt, $options = []) {
        if (!$this->hasValidKey()) {
            throw new Exception('Invalid Claude API key');
        }

        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => $options['max_tokens'] ?? 4096,
            'temperature' => $options['temperature'] ?? 0.7
        ];

        $ch = curl_init($this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $this->apiKey,
            'anthropic-version: 2023-06-01'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new Exception("Curl error: " . $curlError);
        }

        if ($httpCode !== 200) {
            throw new Exception("HTTP error: " . $httpCode . " Response: " . substr($response, 0, 1000));
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON decode error: " . json_last_error_msg());
        }

        if (empty($result['content'][0]['text'])) {
            throw new Exception("Empty response from Claude API");
        }

        return $result['content'][0]['text'];
    }
}