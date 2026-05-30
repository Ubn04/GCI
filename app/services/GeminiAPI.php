<?php
/**
 * Service pour appeler l'API Gemini avec gestion des erreurs et retries
 */

class GeminiAPI {
    
    private $apiKey;
    private $apiUrl;
    private $maxRetries = 3;
    private $retryDelay = 2; // secondes
    
    public function __construct($apiKey, $apiUrl) {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
    }
    
    /**
     * Appeler l'API Gemini avec retry automatique
     */
    public function call($prompt, $maxTokens = 500, $temperature = 0.7) {
        $attempt = 0;
        $lastError = null;
        
        while ($attempt < $this->maxRetries) {
            $attempt++;
            
            try {
                $response = $this->makeRequest($prompt, $maxTokens, $temperature);
                
                // Vérifier les erreurs spécifiques
                if (isset($response['httpCode'])) {
                    $httpCode = $response['httpCode'];
                    
                    // Erreur 429 = Quota dépassé ou trop de requêtes
                    if ($httpCode === 429) {
                        return [
                            'success' => false,
                            'error' => '❌ QUOTA API DÉPASSÉ - Trop de requêtes. Réessayez plus tard.',
                            'quota_exceeded' => true,
                            'attempts' => $attempt,
                            'errorDetails' => $response['error'] ?? null
                        ];
                    }
                    
                    // Erreur 403 = Permissions ou quota
                    if ($httpCode === 403) {
                        $errorMsg = $response['error']['message'] ?? '';
                        if (strpos($errorMsg, 'quota') !== false || strpos($errorMsg, 'Quota') !== false) {
                            return [
                                'success' => false,
                                'error' => '❌ QUOTA API DÉPASSÉ - Vérifiez votre limite sur Google Cloud Console',
                                'quota_exceeded' => true,
                                'attempts' => $attempt,
                                'errorDetails' => $response['error'] ?? null
                            ];
                        }
                    }
                    
                    // Erreur 503 = Serveur surchargé (réessayable)
                    if ($httpCode === 503) {
                        $lastError = [
                            'code' => 503,
                            'message' => $response['error']['message'] ?? 'Serveur temporairement indisponible'
                        ];
                        
                        // Attendre avant de réessayer
                        if ($attempt < $this->maxRetries) {
                            error_log("Gemini API 503 - Tentative $attempt/$this->maxRetries, attente de {$this->retryDelay}s...");
                            sleep($this->retryDelay);
                            $this->retryDelay *= 2; // Backoff exponentiel
                            continue;
                        }
                    }
                }
                
                // Si pas d'erreur 503, retourner la réponse
                if (isset($response['success']) && $response['success']) {
                    return [
                        'success' => true,
                        'data' => $response['data'],
                        'attempts' => $attempt
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => $response['error'] ?? 'Erreur inconnue',
                        'attempts' => $attempt
                    ];
                }
                
            } catch (Exception $e) {
                $lastError = ['code' => 0, 'message' => $e->getMessage()];
                
                if ($attempt < $this->maxRetries) {
                    error_log("Gemini API Error - Tentative $attempt/$this->maxRetries: " . $e->getMessage());
                    sleep($this->retryDelay);
                    $this->retryDelay *= 2;
                    continue;
                }
            }
        }
        
        // Après tous les retries
        return [
            'success' => false,
            'error' => $lastError['message'] ?? 'Impossible d\'appeler l\'API après ' . $this->maxRetries . ' tentatives',
            'attempts' => $attempt
        ];
    }
    
    /**
     * Faire une requête cURL à Gemini
     */
    private function makeRequest($prompt, $maxTokens, $temperature) {
        $url = $this->apiUrl . '?key=' . $this->apiKey;
        
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => $maxTokens,
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            throw new Exception("Erreur cURL : $curlError");
        }
        
        $result = json_decode($response, true);
        
        return [
            'httpCode' => $httpCode,
            'success' => $httpCode === 200 && isset($result['candidates'][0]['content']['parts'][0]['text']),
            'data' => $result['candidates'][0]['content']['parts'][0]['text'] ?? null,
            'error' => $result['error'] ?? null,
            'raw' => $result
        ];
    }
}
?>
