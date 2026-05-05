<?php
/**
 * Page de test pour le modal et les exports
 */

require_once 'config/config.php';
require_once 'config/Database.php';
require_once 'app/models/Report.php';

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    echo "Vous devez être connecté pour tester cette fonctionnalité.<br>";
    echo "<a href='?action=auth/login'>Se connecter</a>";
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $db = new Database();
    $pdo = $db->connect();
    $reportModel = new Report($pdo);
    
    // Récupérer les rapports de l'utilisateur
    $reports = $reportModel->getByUserId($userId);
    
    echo "<h1>Test du Modal et des Exports</h1>";
    
    if (empty($reports)) {
        echo "<p>Aucun rapport trouvé. <a href='?action=reports/select-project'>Générer un rapport</a> d'abord.</p>";
        exit;
    }
    
    echo "<h2>Rapports disponibles :</h2>";
    echo "<ul>";
    foreach ($reports as $report) {
        echo "<li>";
        echo "<strong>" . htmlspecialchars($report['title']) . "</strong><br>";
        echo "Créé le : " . date('d/m/Y à H:i', strtotime($report['created_at'])) . "<br>";
        echo "<button onclick='testModal(" . $report['id'] . ")' class='btn btn-primary'>Tester Modal</button> ";
        echo "<a href='export_report.php?id=" . $report['id'] . "&format=word' target='_blank' class='btn btn-success'>Test Word</a> ";
        echo "<a href='export_report.php?id=" . $report['id'] . "&format=pdf' target='_blank' class='btn btn-danger'>Test PDF</a>";
        echo "</li><br>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Modal et Export</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { padding: 20px; }
        .btn { margin: 5px; }
        li { margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>

<!-- Modal de test -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt text-primary"></i> 
                    <span id="modalTitle">Test Modal</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div id="modalMeta" class="alert alert-info mb-3"></div>
                <div id="modalContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Fermer
                </button>
                <button type="button" class="btn btn-success" onclick="testPDFExport()">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button type="button" class="btn btn-primary" onclick="testWordExport()">
                    <i class="fas fa-file-word"></i> Word
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let currentTestReportId = null;

function testModal(reportId) {
    currentTestReportId = reportId;
    const modal = new bootstrap.Modal(document.getElementById('testModal'));
    
    // Réinitialiser
    document.getElementById('modalTitle').textContent = 'Chargement...';
    document.getElementById('modalMeta').innerHTML = '';
    document.getElementById('modalContent').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Charger via AJAX
    fetch(`get_report.php?id=${reportId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTestReport(data.report);
            } else {
                document.getElementById('modalContent').innerHTML = 
                    '<div class="alert alert-danger">Erreur : ' + data.error + '</div>';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('modalContent').innerHTML = 
                '<div class="alert alert-danger">Erreur de connexion</div>';
        });
}

function displayTestReport(report) {
    document.getElementById('modalTitle').textContent = report.title;
    
    const metaHtml = `
        <div class="row">
            <div class="col-md-4">
                <strong>Type:</strong> <span class="badge bg-info">${report.report_type}</span>
            </div>
            <div class="col-md-4">
                <strong>Date:</strong> ${new Date(report.report_date).toLocaleDateString('fr-FR')}
            </div>
            <div class="col-md-4">
                <strong>Créé:</strong> ${new Date(report.created_at).toLocaleDateString('fr-FR')}
            </div>
        </div>
    `;
    document.getElementById('modalMeta').innerHTML = metaHtml;
    
    // Formater le contenu
    let content = report.content.replace(/</g, '&lt;').replace(/>/g, '&gt;');
    content = content.replace(/\n/g, '<br>');
    content = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    content = content.replace(/^# (.*$)/gm, '<h1>$1</h1>');
    content = content.replace(/^## (.*$)/gm, '<h2>$1</h2>');
    content = content.replace(/^### (.*$)/gm, '<h3>$1</h3>');
    
    document.getElementById('modalContent').innerHTML = content;
}

function testPDFExport() {
    if (currentTestReportId) {
        window.open(`export_report.php?id=${currentTestReportId}&format=pdf`, '_blank');
    }
}

function testWordExport() {
    if (currentTestReportId) {
        window.open(`export_report.php?id=${currentTestReportId}&format=word`, '_blank');
    }
}
</script>

<div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
    <h3>Instructions de test :</h3>
    <ol>
        <li><strong>Test Modal :</strong> Cliquez sur "Tester Modal" pour voir le rapport dans une popup</li>
        <li><strong>Test Word :</strong> Cliquez sur "Test Word" pour télécharger le rapport en format Word (.doc)</li>
        <li><strong>Test PDF :</strong> Cliquez sur "Test PDF" pour voir le rapport formaté pour PDF</li>
        <li><strong>Dans le Modal :</strong> Utilisez les boutons "PDF" et "Word" en bas du modal</li>
    </ol>
    
    <h4>Fonctionnalités testées :</h4>
    <ul>
        <li>✅ Chargement AJAX du rapport</li>
        <li>✅ Affichage dans modal Bootstrap</li>
        <li>✅ Export Word avec formatage</li>
        <li>✅ Export PDF/HTML avec styles</li>
        <li>✅ Gestion des erreurs</li>
    </ul>
    
    <p><a href="?action=reports" class="btn btn-primary">Aller aux rapports normaux</a></p>
</div>

</body>
</html>