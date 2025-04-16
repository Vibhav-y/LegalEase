<?php
class DocumentGenerator {
    private $apiKey;
    private $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct($apiKey) {
        if (empty($apiKey)) {
            throw new Exception('API key is required');
        }
        $this->apiKey = $apiKey;
    }

    public function generateDocument($documentType, $details, $format = 'html') {
        try {
            if (empty($documentType) || empty($details)) {
                throw new Exception('Document type and details are required');
            }

            $prompt = $this->getPromptForType($documentType, $details);
            
            $data = [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a legal document generator. Generate professional legal documents based on the provided details.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000
            ];

            if (!function_exists('curl_init')) {
                throw new Exception('cURL extension is not enabled in PHP');
            }

            $ch = curl_init($this->apiUrl);
            if ($ch === false) {
                throw new Exception('Failed to initialize cURL');
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new Exception('Curl error: ' . $error);
            }
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                throw new Exception('API request failed with HTTP code: ' . $httpCode);
            }

            $result = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON decode error: ' . json_last_error_msg());
            }

            if (!isset($result['choices'][0]['message']['content'])) {
                throw new Exception('Invalid API response format: ' . print_r($result, true));
            }

            $content = $result['choices'][0]['message']['content'];
            return $this->formatDocument($content, $format);
        } catch (Exception $e) {
            error_log("AI Document Generation Error: " . $e->getMessage());
            throw new Exception("Failed to generate document: " . $e->getMessage());
        }
    }

    private function getPromptForType($documentType, $details) {
        $prompts = [
            'Legal Notice' => "Generate a legal notice with the following details: $details",
            'Contract Agreement' => "Create a professional contract agreement with these terms: $details",
            'Power of Attorney' => "Draft a power of attorney document with these specifications: $details",
            'Will' => "Prepare a last will and testament with these instructions: $details",
            'Affidavit' => "Compose an affidavit with these statements: $details",
            'Lease Agreement' => "Write a lease agreement with these conditions: $details",
            'Non-Disclosure Agreement' => "Generate a non-disclosure agreement with these terms: $details",
            'Employment Contract' => "Create an employment contract with these terms: $details"
        ];

        return $prompts[$documentType] ?? "Generate a legal document of type '$documentType' with these details: $details";
    }

    private function formatDocument($content, $format) {
        switch ($format) {
            case 'pdf':
                return $this->convertToPDF($content);
            case 'docx':
                return $this->convertToDOCX($content);
            default:
                return $content;
        }
    }

    private function convertToPDF($content) {
        // For now, return the content as is
        // We'll implement PDF conversion later
        return $content;
    }

    private function convertToDOCX($content) {
        // For now, return the content as is
        // We'll implement DOCX conversion later
        return $content;
    }
}
?> 