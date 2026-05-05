<?php
/**
 * API pour exporter un rapport en PDF ou Word - VERSION AMÉLIORÉE AVEC ESPACEMENT
 */

require_once 'config/config.php';
require_once 'config/Database.php';
require_once 'app/models/Report.php';

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

// Vérifier les paramètres
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['format'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Paramètres invalides']);
    exit;
}

$reportId = intval($_GET['id']);
$format = $_GET['format']; // 'pdf' ou 'word'
$userId = $_SESSION['user_id'];

try {
    // Connexion à la base de données
    $db = new Database();
    $pdo = $db->connect();
    $reportModel = new Report($pdo);
    
    // Récupérer le rapport
    $report = $reportModel->getById($reportId);
    
    if (!$report) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Rapport non trouvé']);
        exit;
    }
    
    // Vérifier que le rapport appartient à l'utilisateur connecté
    if ($report['user_id'] != $userId) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Accès refusé']);
        exit;
    }
    
    // Préparer le contenu formaté
    $content = formatReportContent($report['content']);
    $filename = sanitizeFilename($report['title']);
    
    if ($format === 'word') {
        exportToWord($report, $content, $filename);
    } elseif ($format === 'pdf') {
        exportToPDF($report, $content, $filename);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Format non supporté']);
    }
    
} catch (Exception $e) {
    error_log("Error in export_report.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur serveur']);
}

/**
 * Formater le contenu du rapport pour l'export
 */
function formatReportContent($content) {
    // Échapper le HTML d'abord
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    
    // Convertir le markdown en HTML
    // Titres
    $content = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', $content);
    $content = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $content);
    $content = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $content);
    
    // Gras et souligné
    $content = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $content);
    $content = preg_replace('/__(.*?)__/s', '<u>$1</u>', $content);
    
    // Listes
    $lines = explode("\n", $content);
    $inList = false;
    $result = [];
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        
        // Détecter les items de liste
        if (preg_match('/^[-•\*] (.+)$/', $trimmed, $matches)) {
            if (!$inList) {
                $result[] = '<ul>';
                $inList = true;
            }
            $result[] = '<li>' . $matches[1] . '</li>';
        } else {
            if ($inList) {
                $result[] = '</ul>';
                $inList = false;
            }
            $result[] = $line;
        }
    }
    
    if ($inList) {
        $result[] = '</ul>';
    }
    
    $content = implode("\n", $result);
    
    // Convertir les sauts de ligne en <br> sauf autour des balises HTML
    $content = preg_replace('/\n(?![<\/])/','<br>', $content);
    
    // Nettoyer les <br> en trop
    $content = preg_replace('/<br>\s*<(h[1-6]|ul|\/ul|li)>/i', '<$1>', $content);
    $content = preg_replace('/<\/(h[1-6]|ul|li)>\s*<br>/i', '</$1>', $content);
    
    return $content;
}

/**
 * Exporter en Word
 */
function exportToWord($report, $content, $filename) {
    $date = date('d/m/Y', strtotime($report['report_date']));
    $createdAt = date('d/m/Y à H:i', strtotime($report['created_at']));
    $reportRef = 'RAP-' . date('Y', strtotime($report['created_at'])) . '-' . str_pad($report['id'], 4, '0', STR_PAD_LEFT);
    
    // Chemin absolu vers le logo
    $logoPath = __DIR__ . '/assets/images/logo.jpg';
    $logoData = '';
    
    // Convertir le logo en base64 pour l'inclure dans le Word
    if (file_exists($logoPath)) {
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoSrc = 'data:image/jpeg;base64,' . $logoData;
    } else {
        $logoSrc = '';
    }
    
    $htmlContent = '
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta charset="utf-8">
        <title>' . htmlspecialchars($report['title']) . '</title>
        <!--[if gte mso 9]>
        <xml>
            <w:WordDocument>
                <w:View>Print</w:View>
                <w:Zoom>100</w:Zoom>
            </w:WordDocument>
        </xml>
        <![endif]-->
        <style>
            @page {
                margin: 2.5cm;
            }
            body { 
                font-family: "Calibri", Arial, sans-serif; 
                margin: 0;
                padding: 30px;
                line-height: 2;
                color: #1e3a8a;
                font-size: 11pt;
            }
            .document-header {
                border-bottom: 4px solid #2563eb;
                padding-bottom: 30px;
                margin-bottom: 40px;
                position: relative;
            }
            .document-header::after {
                content: "";
                position: absolute;
                bottom: -4px;
                left: 0;
                width: 120px;
                height: 4px;
                background: #f59e0b;
            }
            .header-top {
                display: table;
                width: 100%;
                margin-bottom: 35px;
                padding-bottom: 25px;
                border-bottom: 2px solid #e2e8f0;
            }
            .header-left {
                display: table-cell;
                vertical-align: top;
                width: 60%;
            }
            .logo-section {
                margin-bottom: 15px;
            }
            .logo-img {
                width: 60px;
                height: 60px;
                border-radius: 8px;
                border: 2px solid #2563eb;
                vertical-align: middle;
                margin-right: 15px;
            }
            .header-right {
                display: table-cell;
                vertical-align: top;
                text-align: right;
                width: 40%;
            }
            .company-subtitle {
                font-size: 9pt;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                margin: 0 0 10px 0;
            }
            .company-name {
                font-size: 20pt;
                font-weight: bold;
                color: #1e3a8a;
                margin: 0;
            }
            .reference-label {
                font-size: 9pt;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 1.2px;
                margin: 0 0 8px 0;
            }
            .reference-number {
                font-size: 15pt;
                font-weight: bold;
                color: #2563eb;
                font-family: "Courier New", monospace;
                margin: 0;
            }
            .report-title { 
                color: #1e3a8a; 
                font-size: 22pt; 
                font-weight: bold; 
                margin: 35px 0 18px 0;
                text-transform: uppercase;
                letter-spacing: -0.5px;
            }
            .report-subtitle {
                color: #64748b;
                font-size: 11pt;
                margin: 0 0 35px 0;
            }
            .report-meta { 
                background: #f8fafc; 
                padding: 25px 30px; 
                margin: 35px 0;
                border-left: 5px solid #2563eb;
            }
            .report-meta p {
                margin: 10px 0;
                line-height: 2;
            }
            .report-content { 
                line-height: 2.2;
                margin-top: 45px;
            }
            h1 { 
                color: #1e3a8a; 
                font-size: 18pt;
                font-weight: bold;
                background: #eff6ff;
                padding: 20px 28px;
                border-left: 6px solid #2563eb;
                margin: 45px 0 30px 0;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            h2 { 
                color: #1e3a8a; 
                font-size: 15pt;
                font-weight: bold;
                border-bottom: 3px solid #2563eb;
                padding-bottom: 12px;
                margin: 40px 0 25px 0;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            h2::before {
                content: "■ ";
                color: #2563eb;
                margin-right: 10px;
            }
            h3 { 
                color: #2563eb; 
                font-size: 13pt;
                font-weight: bold;
                margin: 30px 0 18px 0;
            }
            h3::before {
                content: "▸ ";
                color: #f59e0b;
                margin-right: 10px;
            }
            p {
                margin: 15px 0;
                text-align: justify;
            }
            strong { 
                font-weight: bold; 
                color: #1e3a8a; 
            }
            u { 
                text-decoration: underline; 
            }
            ul, ol { 
                margin: 25px 0;
                padding-left: 35px; 
            }
            li { 
                margin: 12px 0;
                line-height: 2;
            }
            .footer {
                margin-top: 60px;
                padding-top: 25px;
                border-top: 2px solid #e2e8f0;
                text-align: center;
                color: #64748b;
                font-size: 9pt;
            }
        </style>
    </head>
    <body>
        <div class="document-header">
            <div class="header-top">
                <div class="header-left">
                    <div class="logo-section">';
    
    if ($logoSrc) {
        $htmlContent .= '<img src="' . $logoSrc . '" alt="GCI Logo" class="logo-img">';
    }
    
    $htmlContent .= '
                    </div>
                    <p class="company-subtitle">Génie Civil Intelligent</p>
                    <h1 class="company-name">GCI - ChantierAI</h1>
                </div>
                <div class="header-right">
                    <p class="reference-label">Référence</p>
                    <p class="reference-number">' . $reportRef . '</p>
                </div>
            </div>
            <h2 class="report-title">' . htmlspecialchars($report['title']) . '</h2>
            <p class="report-subtitle">Rapport de chantier généré par ChantierAI</p>
        </div>
        
        <div class="report-meta">
            <p><strong>Type de rapport :</strong> ' . ucfirst(htmlspecialchars($report['report_type'])) . '</p>
            <p><strong>Date du rapport :</strong> ' . $date . '</p>
            <p><strong>Généré le :</strong> ' . $createdAt . '</p>
            <p><strong>Système :</strong> ChantierAI - Gemini AI</p>
        </div>
        
        <div class="report-content">
            ' . $content . '
        </div>
        
        <div class="footer">
            <p>Rapport généré automatiquement par ChantierAI - Gemini AI</p>
            <p>Date d\'export : ' . date('d/m/Y à H:i') . '</p>
        </div>
    </body>
    </html>';
    
    header('Content-Type: application/vnd.ms-word');
    header('Content-Disposition: attachment; filename="' . $filename . '.doc"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    
    echo $htmlContent;
}

/**
 * Exporter en PDF avec DomPDF
 */
function exportToPDF($report, $content, $filename) {
    // Charger l'autoloader de Composer
    require_once __DIR__ . '/vendor/autoload.php';
    
    $date = date('d/m/Y', strtotime($report['report_date']));
    $createdAt = date('d/m/Y à H:i', strtotime($report['created_at']));
    $reportRef = 'RAP-' . date('Y', strtotime($report['created_at'])) . '-' . str_pad($report['id'], 4, '0', STR_PAD_LEFT);
    
    // Chemin absolu vers le logo
    $logoPath = __DIR__ . '/assets/images/logo.jpg';
    $logoData = '';
    
    // Convertir le logo en base64 pour l'inclure dans le PDF
    if (file_exists($logoPath)) {
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoSrc = 'data:image/jpeg;base64,' . $logoData;
    } else {
        // Logo par défaut si le fichier n'existe pas
        $logoSrc = '';
    }
    
    // Créer le contenu HTML pour le PDF
    $htmlContent = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            @page {
                margin: 2cm;
            }
            body { 
                font-family: DejaVu Sans, Arial, sans-serif; 
                margin: 0;
                padding: 0;
                line-height: 1.8;
                color: #1e3a8a;
                font-size: 11pt;
            }
            .document-header {
                border-bottom: 4px solid #2563eb;
                padding-bottom: 20px;
                margin-bottom: 30px;
                position: relative;
            }
            .document-header::after {
                content: "";
                position: absolute;
                bottom: -4px;
                left: 0;
                width: 100px;
                height: 4px;
                background: #f59e0b;
            }
            .header-top {
                width: 100%;
                margin-bottom: 25px;
                padding-bottom: 20px;
                border-bottom: 2px solid #e2e8f0;
                overflow: hidden;
            }
            .header-left {
                float: left;
                width: 60%;
            }
            .header-right {
                float: right;
                width: 35%;
                text-align: right;
            }
            .logo-section {
                overflow: hidden;
                margin-bottom: 15px;
            }
            .logo-img {
                float: left;
                width: 60px;
                height: 60px;
                margin-right: 15px;
                border-radius: 8px;
                border: 2px solid #2563eb;
            }
            .company-info {
                overflow: hidden;
            }
            .company-subtitle {
                font-size: 9pt;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                margin: 0 0 8px 0;
            }
            .company-name {
                font-size: 18pt;
                font-weight: bold;
                color: #1e3a8a;
                margin: 0;
            }
            .reference-label {
                font-size: 9pt;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin: 0 0 6px 0;
            }
            .reference-number {
                font-size: 14pt;
                font-weight: bold;
                color: #2563eb;
                font-family: Courier, monospace;
                margin: 0;
            }
            .report-title { 
                color: #1e3a8a; 
                font-size: 20pt; 
                font-weight: bold; 
                margin: 25px 0 12px 0;
                text-transform: uppercase;
                clear: both;
            }
            .report-subtitle {
                color: #64748b;
                font-size: 11pt;
                margin: 0 0 25px 0;
            }
            .report-meta { 
                background: #f8fafc; 
                padding: 20px 25px; 
                margin: 25px 0;
                border-left: 5px solid #2563eb;
            }
            .report-meta p {
                margin: 8px 0;
                line-height: 1.6;
            }
            .report-content { 
                line-height: 2;
                margin-top: 35px;
            }
            h1 { 
                color: #1e3a8a; 
                font-size: 16pt;
                font-weight: bold;
                background: #eff6ff;
                padding: 15px 20px;
                border-left: 6px solid #2563eb;
                margin: 35px 0 25px 0;
                text-transform: uppercase;
                page-break-after: avoid;
            }
            h2 { 
                color: #1e3a8a; 
                font-size: 14pt;
                font-weight: bold;
                border-bottom: 3px solid #2563eb;
                padding-bottom: 10px;
                margin: 30px 0 20px 0;
                text-transform: uppercase;
                page-break-after: avoid;
            }
            h3 { 
                color: #2563eb; 
                font-size: 12pt;
                font-weight: bold;
                margin: 25px 0 15px 0;
                page-break-after: avoid;
            }
            p {
                margin: 12px 0;
                text-align: justify;
                orphans: 3;
                widows: 3;
            }
            strong { 
                font-weight: bold; 
                color: #1e3a8a; 
            }
            ul, ol { 
                margin: 20px 0;
                padding-left: 30px; 
            }
            li { 
                margin: 10px 0;
                line-height: 1.8;
            }
            .footer {
                margin-top: 50px;
                padding-top: 20px;
                border-top: 2px solid #e2e8f0;
                text-align: center;
                color: #64748b;
                font-size: 9pt;
            }
            .clearfix::after {
                content: "";
                display: table;
                clear: both;
            }
        </style>
    </head>
    <body>
        <div class="document-header">
            <div class="header-top clearfix">
                <div class="header-left">
                    <div class="logo-section clearfix">';
    
    if ($logoSrc) {
        $htmlContent .= '<img src="' . $logoSrc . '" alt="GCI Logo" class="logo-img">';
    }
    
    $htmlContent .= '
                        <div class="company-info">
                            <p class="company-subtitle">Génie Civil Intelligent</p>
                            <h1 class="company-name">GCI - ChantierAI</h1>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <p class="reference-label">Référence</p>
                    <p class="reference-number">' . $reportRef . '</p>
                </div>
            </div>
            <h2 class="report-title">' . htmlspecialchars($report['title']) . '</h2>
            <p class="report-subtitle">Rapport de chantier généré par ChantierAI</p>
        </div>
        
        <div class="report-meta">
            <p><strong>Type de rapport :</strong> ' . ucfirst(htmlspecialchars($report['report_type'])) . '</p>
            <p><strong>Date du rapport :</strong> ' . $date . '</p>
            <p><strong>Généré le :</strong> ' . $createdAt . '</p>
            <p><strong>Système :</strong> ChantierAI - Gemini AI</p>
        </div>
        
        <div class="report-content">
            ' . $content . '
        </div>
        
        <div class="footer">
            <p>Rapport généré automatiquement par ChantierAI - Gemini AI</p>
            <p>Date d\'export : ' . date('d/m/Y à H:i') . '</p>
        </div>
    </body>
    </html>';
    
    // Créer une instance de DomPDF
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('chroot', __DIR__);
    
    $dompdf = new \Dompdf\Dompdf($options);
    
    // Charger le HTML
    $dompdf->loadHtml($htmlContent);
    
    // Définir le format et l'orientation
    $dompdf->setPaper('A4', 'portrait');
    
    // Générer le PDF
    $dompdf->render();
    
    // Envoyer le PDF au navigateur pour téléchargement
    $dompdf->stream($filename . '.pdf', [
        'Attachment' => 1,  // 1 = télécharger, 0 = afficher dans le navigateur
        'compress' => 1
    ]);
}

/**
 * Nettoyer le nom de fichier
 */
function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $filename);
    $filename = preg_replace('/_{2,}/', '_', $filename);
    return trim($filename, '_');
}
?>
