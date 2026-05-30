<?php
/**
 * Service de génération de PDF formatés pour les rapports de chantier
 */

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFGenerator {
    private $dompdf;
    
    public function __construct() {
        $options = new Options();
        $options->set([
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
        ]);
        
        $this->dompdf = new Dompdf($options);
    }

    /**
     * Générer un rapport complet au format PDF
     */
    public function generateReport($projectData, $siteData) {
        $html = $this->buildReportHTML($projectData, $siteData);
        
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        
        return $this->dompdf->output();
    }

    /**
     * Générer un rapport structuré avec informations administratives
     */
    public function generateStructuredReport($project, $equipments, $personnels, $materials, $weather, $additionalContent = '') {
        $html = $this->buildStructuredReportHTML($project, $equipments, $personnels, $materials, $weather, $additionalContent);
        
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        
        return $this->dompdf->output();
    }

    /**
     * Construire le HTML du rapport structuré
     */
    private function buildStructuredReportHTML($project, $equipments, $personnels, $materials, $weather, $additionalContent) {
        $generationDate = date('d/m/Y à H:i');
        $generationDateOnly = date('d/m/Y');
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport de chantier - ' . htmlspecialchars($project['name']) . '</title>
    <style>
        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #111827;
            font-size: 24px;
            margin: 0 0 10px 0;
            font-weight: bold;
        }
        
        .header-info {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 12px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 10px;
        }
        
        .info-item {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #1e3a8a;
            font-size: 11px;
        }
        
        .info-value {
            color: #333;
            font-size: 12px;
        }
        
        .section {
            margin: 30px 0;
            page-break-inside: avoid;
        }
        
        .section-title {
            color: #111827;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #d1d5db;
        }
        
        .subsection-title {
            color: #2563eb;
            font-size: 13px;
            font-weight: bold;
            margin: 15px 0 10px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12px;
        }
        
        table thead {
            background: #f8fafc;
            color: #111827;
        }
        
        table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #d1d5db;
        }
        
        table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
        }
        
        table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        
        table tbody tr:hover {
            background: #f0f4ff;
        }
        
        .content-text {
            font-size: 12px;
            line-height: 1.7;
            color: #333;
            margin-bottom: 15px;
            text-align: justify;
        }
        
        .empty-message {
            color: #94a3b8;
            font-style: italic;
            font-size: 12px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 11px;
            color: #64748b;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>';
        
        // En-tête
        $html .= '
    <div class="header">
        <h1>' . htmlspecialchars($project['name']) . '</h1>
        <p style="margin: 5px 0; color: #64748b; font-size: 13px;">Rapport de suivi de chantier</p>
    </div>
    
    <div class="header-info">
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <div class="info-label">Type du projet</div>
                    <div class="info-value">Génie Civil</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Localisation</div>
                    <div class="info-value">' . htmlspecialchars($project['location'] ?? 'Non spécifiée') . '</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Maître d\'ouvrage</div>
                    <div class="info-value">' . htmlspecialchars($project['owner'] ?? 'Non spécifié') . '</div>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <div class="info-label">Mission contrôle</div>
                    <div class="info-value">' . htmlspecialchars($project['control'] ?? 'Non spécifiée') . '</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Entreprise exécutante</div>
                    <div class="info-value">' . htmlspecialchars($project['company'] ?? 'Non spécifiée') . '</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Météo</div>
                    <div class="info-value">' . htmlspecialchars($weather ?? 'Ensoleillé') . '</div>
                </div>
            </div>
        </div>
        <div class="info-item" style="margin-top: 15px; text-align: center;">
            <div class="info-label">Date de génération</div>
            <div class="info-value">' . $generationDate . '</div>
        </div>
    </div>';
        
        // Introduction
        $html .= '
    <div class="section">
        <div class="section-title">✓ Introduction</div>
        <div class="content-text">
            Ce rapport rend compte du suivi de chantier effectué ce jour sur le projet ' . htmlspecialchars($project['name']) . '. 
            Les informations détaillées ci-après couvrent les conditions de travail, le personnel mobilisé, les équipements utilisés 
            et les matériaux présents sur le chantier.
        </div>
    </div>';
        
        // Tableau 1 - Équipements
        $html .= '
    <div class="section">
        <div class="section-title">✓ Tableau 1 - Conditions et matériel</div>';
        
        if (!empty($equipments)) {
            $html .= '
        <table>
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th style="width: 15%;">Présent</th>
                    <th style="width: 15%;">Marche</th>
                    <th style="width: 15%;">Immob</th>
                    <th style="width: 15%;">Panne</th>
                </tr>
            </thead>
            <tbody>';
            
            foreach ($equipments as $eq) {
                $html .= '
                <tr>
                    <td>' . htmlspecialchars($eq['designation'] ?? '') . '</td>
                    <td>' . htmlspecialchars($eq['present'] ?? 'N/A') . '</td>
                    <td>' . htmlspecialchars($eq['marche'] ?? 'N/A') . '</td>
                    <td>' . htmlspecialchars($eq['immob'] ?? 'N/A') . '</td>
                    <td>' . htmlspecialchars($eq['panne'] ?? 'N/A') . '</td>
                </tr>';
            }
            
            $html .= '
            </tbody>
        </table>';
        } else {
            $html .= '<p class="empty-message">Aucun équipement enregistré</p>';
        }
        
        $html .= '</div>';
        
        // Tableau 2 - Personnel
        $html .= '
    <div class="section">
        <div class="section-title">✓ Tableau 2 - Personnel</div>';
        
        if (!empty($personnels)) {
            $html .= '
        <table>
            <thead>
                <tr>
                    <th>Profil</th>
                    <th style="width: 20%;">Nombre</th>
                </tr>
            </thead>
            <tbody>';
            
            foreach ($personnels as $per) {
                $html .= '
                <tr>
                    <td>' . htmlspecialchars($per['profile'] ?? '') . '</td>
                    <td>' . htmlspecialchars($per['nbr'] ?? '0') . '</td>
                </tr>';
            }
            
            $html .= '
            </tbody>
        </table>';
        } else {
            $html .= '<p class="empty-message">Aucun personnel enregistré</p>';
        }
        
        $html .= '</div>';
        
        // Tableau 3 - Matériaux
        $html .= '
    <div class="section">
        <div class="section-title">✓ Tableau 3 - Matériaux</div>';
        
        if (!empty($materials)) {
            $html .= '
        <table>
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th style="width: 15%;">Unité</th>
                    <th style="width: 20%;">Quantité</th>
                </tr>
            </thead>
            <tbody>';
            
            foreach ($materials as $mat) {
                $html .= '
                <tr>
                    <td>' . htmlspecialchars($mat['designation'] ?? '') . '</td>
                    <td>' . htmlspecialchars($mat['unite'] ?? '') . '</td>
                    <td>' . htmlspecialchars($mat['quantite'] ?? '0') . '</td>
                </tr>';
            }
            
            $html .= '
            </tbody>
        </table>';
        } else {
            $html .= '<p class="empty-message">Aucun matériau enregistré</p>';
        }
        
        $html .= '</div>';
        
        // Contenu additionnel si présent
        if (!empty($additionalContent)) {
            $html .= '
    <div class="section">
        <div class="section-title">✓ Observations et remarques</div>
        <div class="content-text">' . nl2br(htmlspecialchars($additionalContent)) . '</div>
    </div>';
        }
        
        // Pied de page
        $html .= '
    <div class="footer">
        <p>Rapport généré automatiquement par ChantierAI</p>
        <p>Date: ' . $generationDateOnly . '</p>
    </div>
    
</body>
</html>';
        
        return $html;
    }

    /**
     * Construire le HTML du rapport classique
     */
    private function buildReportHTML($projectData, $siteData) {
        $date = date('d/m/Y à H:i');
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport de chantier</title>
    <style>
        body { font-family: "DejaVu Sans", Arial; line-height: 1.6; color: #333; margin: 20px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #2563eb; margin: 0; }
        .content { font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #2563eb; color: white; padding: 8px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>' . htmlspecialchars($projectData['name'] ?? 'Rapport') . '</h1>
        <p>Généré le ' . $date . '</p>
    </div>
    <div class="content">
        ' . nl2br(htmlspecialchars(json_encode($siteData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) . '
    </div>
</body>
</html>';        
        return $html;
    }

    /**
     * Télécharger le PDF généré
     */
    public function downloadPDF($filename) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $this->dompdf->output();
    }

    /**
     * Obtenir le contenu PDF en base64
     */
    public function getPDFBase64() {
        return base64_encode($this->dompdf->output());
    }
}
?>
