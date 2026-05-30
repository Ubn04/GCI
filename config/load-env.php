<?php
/**
 * Charger les variables d'environnement depuis le fichier .env
 * Utilisé pour le développement local
 */

if (file_exists(dirname(__DIR__) . '/.env')) {
    $env_file = dirname(__DIR__) . '/.env';
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos($line, '#') === 0) {
            continue;
        }
        
        // Parser la ligne VAR=valeur
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Supprimer les guillemets si présents
            if (in_array($value[0] ?? '', ['"', "'"])) {
                $value = substr($value, 1, -1);
            }
            
            // Définir la variable d'environnement si elle n'existe pas
            if (!getenv($key)) {
                putenv("$key=$value");
            }
        }
    }
}
?>
