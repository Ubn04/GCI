<?php
/**
 * Fonctions utilitaires globales
 */

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Obtenir l'utilisateur actuel
 */
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Exiger une connexion
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('auth/login');
    }
}

/**
 * Redirection
 */
function redirect($path) {
    header('Location: ' . APP_URL . '?action=' . $path);
    exit;
}

/**
 * Définir un message flash
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Obtenir et supprimer un message flash
 */
function getFlash($type = null) {
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    if ($type && $flash['type'] !== $type) {
        return null;
    }

    return $flash['message'] ?? null;
}

/**
 * Formater une date
 */
function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

/**
 * Formater une date et heure
 */
function formatDateTime($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

/**
 * Échapper une chaîne pour l'affichage HTML
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Valider une adresse email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Générer un token aléatoire
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Envoyer un email via SMTP
 */
function sendSMTPMail($to, $subject, $message, $fromEmail = null, $fromName = null) {
    if (empty(SMTP_HOST) || empty(SMTP_USER) || empty(SMTP_PASS)) {
        return false;
    }

    $fromEmail = $fromEmail ?: SMTP_FROM_EMAIL;
    $fromName = $fromName ?: SMTP_FROM_NAME;
    $hostname = gethostname() ?: 'localhost';
    $secure = strtolower(SMTP_SECURE);
    $port = SMTP_PORT ?: 587;
    $remoteHost = SMTP_HOST;

    $contextOptions = [];
    $transport = '';
    if ($secure === 'ssl') {
        $transport = 'ssl://';
    }

    $socket = stream_socket_client($transport . $remoteHost . ':' . $port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, stream_context_create($contextOptions));
    if (!$socket) {
        return false;
    }

    stream_set_timeout($socket, 30);
    $response = fgets($socket, 512);
    if (strpos($response, '220') !== 0) {
        fclose($socket);
        return false;
    }

    $sendCommand = function ($command, $expectedCodes) use ($socket) {
        fwrite($socket, $command . "\r\n");
        $response = '';
        while ($line = fgets($socket, 512)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        foreach ((array)$expectedCodes as $code) {
            if (strpos($response, (string)$code) === 0) {
                return [$response, true];
            }
        }
        return [$response, false];
    };

    list(, $ok) = $sendCommand("EHLO {$hostname}", [250]);
    if (!$ok) {
        fclose($socket);
        return false;
    }

    if ($secure === 'tls') {
        list(, $ok) = $sendCommand('STARTTLS', [220]);
        if (!$ok || !stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            return false;
        }
        list(, $ok) = $sendCommand("EHLO {$hostname}", [250]);
        if (!$ok) {
            fclose($socket);
            return false;
        }
    }

    list(, $ok) = $sendCommand('AUTH LOGIN', [334]);
    if (!$ok) {
        fclose($socket);
        return false;
    }

    list(, $ok) = $sendCommand(base64_encode(SMTP_USER), [334]);
    if (!$ok) {
        fclose($socket);
        return false;
    }

    list(, $ok) = $sendCommand(base64_encode(SMTP_PASS), [235]);
    if (!$ok) {
        fclose($socket);
        return false;
    }

    list(, $ok) = $sendCommand('MAIL FROM:<'. $fromEmail .'>', [250]);
    if (!$ok) {
        fclose($socket);
        return false;
    }

    list(, $ok) = $sendCommand('RCPT TO:<'. $to .'>', [250, 251]);
    if (!$ok) {
        fclose($socket);
        return false;
    }

    list(, $ok) = $sendCommand('DATA', [354]);
    if (!$ok) {
        fclose($socket);
        return false;
    }

    $headers = "From: {$fromName} <{$fromEmail}>\r\n";
    $headers .= "To: {$to}\r\n";
    $headers .= "Subject: {$subject}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";
    $headers .= "\r\n";

    $body = str_replace("\r\n", "\n", $message);
    $body = str_replace("\n", "\r\n", $body);
    $data = $headers . $body . "\r\n.\r\n";
    fwrite($socket, $data);

    $response = fgets($socket, 512);
    if (strpos($response, '250') !== 0) {
        fclose($socket);
        return false;
    }

    $sendCommand('QUIT', [221]);
    fclose($socket);

    return true;
}

/**
 * Vérifier si une chaîne est vide
 */
function isEmpty($value) {
    return empty(trim($value));
}

/**
 * Limiter la longueur d'une chaîne
 */
function truncate($string, $length = 100, $suffix = '...') {
    if (strlen($string) <= $length) {
        return $string;
    }
    return substr($string, 0, $length) . $suffix;
}

/**
 * Convertir un tableau en JSON
 */
function toJSON($data) {
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * Décoder un JSON
 */
function fromJSON($json) {
    return json_decode($json, true);
}
?>
