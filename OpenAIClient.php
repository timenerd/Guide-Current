<?php

class OpenAIClient {
    private $apiKey;
    private $model = 'gpt-4-turbo-preview';
    private $baseUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function hasValidKey() {
        return !empty($this->apiKey);
    }

    public function getCompletion($prompt, $context = []) {
        if (!$this->hasValidKey()) {
            throw new Exception('OpenAI API key not configured');
        }

        if (empty($prompt)) {
            throw new Exception('Prompt cannot be empty');
        }

        $ch = curl_init($this->baseUrl);
        
        $data = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 2000,
            'temperature' => 0.7
        ];

        // Add context if provided
        if (!empty($context)) {
            $data['messages'][] = [
                'role' => 'system',
                'content' => 'Context: ' . json_encode($context)
            ];
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => __DIR__ . '/cacert.pem'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $errorNo = curl_errno($ch);
        curl_close($ch);

        if ($error) {
            $errorMessage = "OpenAI API request failed: $error";
            if ($errorNo === CURLE_OPERATION_TIMEDOUT) {
                $errorMessage .= " (Request timed out)";
            } elseif ($errorNo === CURLE_COULDNT_CONNECT) {
                $errorMessage .= " (Could not connect to API)";
            } elseif ($errorNo === CURLE_SSL_CONNECT_ERROR) {
                $errorMessage .= " (SSL connection failed)";
            }
            throw new Exception($errorMessage);
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMessage = isset($errorData['error']['message']) 
                ? $errorData['error']['message'] 
                : "OpenAI API returned status code $httpCode";
            
            if ($httpCode === 401) {
                throw new Exception("OpenAI API authentication failed: $errorMessage");
            } elseif ($httpCode === 429) {
                throw new Exception("OpenAI API rate limit exceeded: $errorMessage");
            } elseif ($httpCode >= 500) {
                throw new Exception("OpenAI API server error: $errorMessage");
            } else {
                throw new Exception("OpenAI API error: $errorMessage");
            }
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to parse OpenAI API response: ' . json_last_error_msg());
        }

        if (!isset($result['choices'][0]['message']['content'])) {
            if (isset($result['error'])) {
                throw new Exception('OpenAI API error: ' . $result['error']['message']);
            }
            throw new Exception('Invalid response format from OpenAI API');
        }

        $content = $result['choices'][0]['message']['content'];
        if (empty($content)) {
            throw new Exception('Empty response from OpenAI API');
        }

        return $content;
    }
} 