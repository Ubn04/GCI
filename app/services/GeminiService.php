<?php
/**
 * Service Gemini - Gestion de l'API Google Gemini
 * Supporte les requêtes multimodales (texte + image)
 */

class GeminiService
{
    private $apiKey;
    private $apiUrl;

    /**
     * Constructeur
     * @param string $apiKey Clé API Google Gemini
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = defined('GEMINI_API_URL') ? GEMINI_API_URL : 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent';
    }

    /**
     * Envoyer une requête multimodale à Gemini
     * Support : texte seul, image seule, ou texte + image ensemble
     *
     * @param string $message Message texte (optionnel)
     * @param string $imagePath Chemin du fichier image (optionnel)
     * @param string $mimeType Type MIME de l'image
     * @return array Réponse structurée avec 'success', 'message', 'data'
     */
    public function sendMultimodalMessage($message = null, $imagePath = null, $mimeType = 'image/jpeg', $options = [])
    {
        // Augmenter le timeout PHP pour les images volumineux (de 120s à 300s)
        set_time_limit(300);

        try {
            // Validation : au moins un contenu (texte ou image)
            if (empty($message) && empty($imagePath)) {
                return [
                    'success' => false,
                    'message' => 'Veuillez fournir un message ou une image',
                    'data' => null
                ];
            }

            // Prompt système pour spécialiser l'IA en génie civil et français
            $systemPrompt = "Vous êtes un expert en génie civil avec plus de 20 ans d'expérience. Vous travaillez exclusivement dans le domaine du bâtiment, des routes, des ponts et des infrastructures. Répondez TOUJOURS en français. Répondez de manière brève, concise et directe. Utilisez des phrases courtes, sans explications trop longues. Ne soyez pas bavard. Soyez précis, technique et spécifique aux problèmes du génie civil. Répondez en une ou deux phrases lorsque cela est possible. Évitez les réponses générales ou hors sujet. Utilisez le vocabulaire technique approprié du génie civil. Si on vous pose une question générale, ramenez-la toujours au contexte du génie civil.";

            // Construire dynamiquement les parts (texte + image)
            $parts = [];

            // Ajouter la part texte utilisateur si elle existe
            if (!empty($message)) {
                $parts[] = [
                    'text' => $message
                ];
            }

            // Ajouter la part image si elle existe
            if (!empty($imagePath) && file_exists($imagePath)) {
                $imageData = $this->encodeImageToBase64($imagePath);
                if (!$imageData) {
                    return [
                        'success' => false,
                        'message' => 'Erreur lors du traitement de l\'image',
                        'data' => null
                    ];
                }

                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => $imageData
                    ]
                ];
            }

            // Construire la requête JSON selon la structure Gemini avec system instruction
            $payload = [
                'contents' => [
                    [
                        'parts' => $parts
                    ]
                ],
                'system_instruction' => [
                    'parts' => [
                        [
                            'text' => $systemPrompt
                        ]
                    ]
                ],
                'generation_config' => [
                    'temperature' => $options['temperature'] ?? 0.3,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => $options['maxOutputTokens'] ?? 2048
                ]
            ];

            // Envoyer la requête à l'API Gemini
            $response = $this->callGeminiAPI($payload);

            if (!$response['success']) {
                return $response;
            }

            // Extraire la réponse depuis : candidates -> content -> parts
            return $this->parseGeminiResponse($response['data']);

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Appeler l'API Gemini avec cURL
     * @param array $payload Données JSON à envoyer
     * @return array Réponse de l'API
     */
    private function callGeminiAPI($payload)
    {
        try {
            $maxRetries = 2;
            $retryDelay = 1;
            $attempt = 0;

            while ($attempt < $maxRetries) {
                $attempt++;
                $ch = curl_init();
                
                curl_setopt_array($ch, [
                    CURLOPT_URL => $this->apiUrl . '?key=' . $this->apiKey,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 60,
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json'
                    ],
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($payload)
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                if ($curlError) {
                    error_log("DEBUG: GeminiService cURL error attempt $attempt: $curlError");
                    if ($attempt < $maxRetries) {
                        sleep($retryDelay);
                        $retryDelay *= 2;
                        continue;
                    }
                    return [
                        'success' => false,
                        'message' => 'Erreur cURL : ' . $curlError,
                        'data' => null
                    ];
                }

                if (in_array($httpCode, [429, 503, 504], true)) {
                    $errorData = json_decode($response, true);
                    $errorMsg = $errorData['error']['message'] ?? 'Erreur API Gemini';
                    error_log("DEBUG: GeminiService HTTP transient error $httpCode attempt $attempt: $errorMsg");
                    if ($attempt < $maxRetries) {
                        sleep($retryDelay);
                        $retryDelay *= 2;
                        continue;
                    }
                    return [
                        'success' => false,
                        'message' => 'Erreur API (' . $httpCode . ') : ' . $errorMsg,
                        'data' => null
                    ];
                }

                if ($httpCode !== 200) {
                    $errorData = json_decode($response, true);
                    $errorMsg = $errorData['error']['message'] ?? 'Erreur API Gemini';
                    return [
                        'success' => false,
                        'message' => 'Erreur API (' . $httpCode . ') : ' . $errorMsg,
                        'data' => null
                    ];
                }

                $data = json_decode($response, true);
                return [
                    'success' => true,
                    'message' => 'Requête réussie',
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'message' => 'La requête Gemini a échoué après plusieurs tentatives.',
                'data' => null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception : ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Parser la réponse Gemini
     * Extrait le texte depuis : candidates[0].content.parts[0].text
     *
     * @param array $apiResponse Réponse brute de l'API
     * @return array Réponse structurée
     */
    private function parseGeminiResponse($apiResponse)
    {
        try {
            if (!isset($apiResponse['candidates']) || empty($apiResponse['candidates'])) {
                return [
                    'success' => false,
                    'message' => 'Pas de réponse valide de Gemini',
                    'data' => null
                ];
            }

            $candidate = $apiResponse['candidates'][0];

            if (!isset($candidate['content']['parts']) || empty($candidate['content']['parts'])) {
                return [
                    'success' => false,
                    'message' => 'Pas de contenu dans la réponse',
                    'data' => null
                ];
            }

            $text = $candidate['content']['parts'][0]['text'] ?? null;

            if (!$text) {
                return [
                    'success' => false,
                    'message' => 'Pas de texte dans la réponse Gemini',
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'message' => 'Réponse Gemini obtenue',
                'data' => [
                    'response' => $text,
                    'finishReason' => $candidate['finishReason'] ?? null
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur parsing : ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Encoder une image en base64
     * @param string $imagePath Chemin de l'image
     * @return string|false Base64 de l'image ou false si erreur
     */
    private function encodeImageToBase64($imagePath)
    {
        try {
            if (!file_exists($imagePath)) {
                return false;
            }

            // Lire l'image
            $imageData = file_get_contents($imagePath);
            if ($imageData === false) {
                return false;
            }

            // Compresser l'image si elle est trop volumineux (> 2MB)
            $fileSize = strlen($imageData);
            if ($fileSize > 350 * 1024) {
                error_log("DEBUG: Image volumineux détecté ($fileSize bytes), compression...");
                $compressedData = $this->compressImage($imagePath, $imageData);
                if ($compressedData) {
                    $imageData = $compressedData;
                    error_log("DEBUG: Image compressée à " . strlen($imageData) . " bytes");
                }
            }

            return base64_encode($imageData);
        } catch (Exception $e) {
            error_log("DEBUG: Error encoding image: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Compresser une image pour réduire la taille du payload
     * Réduit les dimensions et qualité pour accélérer le traitement
     */
    private function compressImage($imagePath, $imageData)
    {
        try {
            // Vérifier si GD est disponible
            if (!extension_loaded('gd')) {
                error_log("DEBUG: GD extension non disponible, image non compressée");
                return false;
            }

            // Créer une image depuis les données
            $image = imagecreatefromstring($imageData);
            if (!$image) {
                error_log("DEBUG: Impossible de créer l'image depuis les données");
                return false;
            }

            // Calculer les nouvelles dimensions (réduire à 75%)
            $width = imagesx($image);
            $height = imagesy($image);
            $maxSide = 768;
            $ratio = min(1, $maxSide / max($width, $height));
            $newWidth = max(1, intval($width * $ratio));
            $newHeight = max(1, intval($height * $ratio));

            // Créer une image redimensionnée
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Encoder en JPEG avec compression (qualité 70)
            ob_start();
            imagejpeg($resized, null, 60);
            $compressedData = ob_get_clean();

            // Libérer la mémoire
            imagedestroy($image);
            imagedestroy($resized);

            return $compressedData;
        } catch (Exception $e) {
            error_log("DEBUG: Erreur lors de la compression : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Valider et traiter un fichier image uploadé
     * Sécurité : vérifie le type MIME réel du fichier
     *
     * @param array $file Fichier uploadé ($_FILES['image'])
     * @param string $tempDir Répertoire temporaire pour stocker l'image
     * @return array ['success', 'path', 'mimeType', 'message']
     */
    public function processUploadedImage($file, $tempDir = 'uploads/temp')
    {
        try {
            // Vérifier qu'un fichier a été uploadé
            if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                return [
                    'success' => false,
                    'path' => null,
                    'mimeType' => null,
                    'message' => 'Aucun fichier uploadé'
                ];
            }

            // Vérifier la taille (max 5MB)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                return [
                    'success' => false,
                    'path' => null,
                    'mimeType' => null,
                    'message' => 'Image trop grande (max 5MB)'
                ];
            }

            // Vérifier le type MIME réel
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            // Accepter seulement les images
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mimeType, $allowedMimes)) {
                return [
                    'success' => false,
                    'path' => null,
                    'mimeType' => null,
                    'message' => 'Type de fichier non autorisé. Formats acceptés : JPEG, PNG, GIF, WebP'
                ];
            }

            // Créer le répertoire temporaire s'il n'existe pas
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Générer un nom de fichier sécurisé
            $ext = match($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => 'jpg'
            };

            $filename = uniqid('img_') . '.' . $ext;
            $filepath = $tempDir . '/' . $filename;

            // Déplacer le fichier uploadé
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return [
                    'success' => false,
                    'path' => null,
                    'mimeType' => null,
                    'message' => 'Erreur lors du déplacement du fichier'
                ];
            }

            return [
                'success' => true,
                'path' => $filepath,
                'mimeType' => $mimeType,
                'message' => 'Image traitée avec succès'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'path' => null,
                'mimeType' => null,
                'message' => 'Exception : ' . $e->getMessage()
            ];
        }
    }

    /**
     * Supprimer un fichier temporaire
     * @param string $filepath Chemin du fichier
     * @return bool
     */
    public function deleteTemporaryFile($filepath)
    {
        try {
            if (file_exists($filepath)) {
                return unlink($filepath);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
