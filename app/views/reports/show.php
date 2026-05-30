<?php
/**
 * Vue de visualisation d'un rapport
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($report['title']); ?> - RapporAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8fbff;
        }
        .navbar {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }
        .report-header {
            background: white;
            padding: 30px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .report-title {
            color: #111827;
            font-weight: bold;
        }
        .report-content {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            line-height: 1.8;
            color: #1e3a8a;
            word-break: break-word;
        }
        .report-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
        }
        .report-content th,
        .report-content td {
            border: 1px solid #d1d5db;
            padding: 12px 14px;
            vertical-align: middle;
        }
        .report-content th {
            background: #f1f5f9;
            font-weight: 700;
        }
        .report-content tr:nth-child(even) td {
            background: #f8fafc;
        }
        .report-content h2 {
            color: #2563eb;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            text-transform: uppercase;
            font-size: 1.15rem;
        }
        .report-content h3 {
            color: #1e40af;
            margin-top: 22px;
            margin-bottom: 10px;
            font-size: 1rem;
        }
        .report-content p {
            margin-bottom: 15px;
        }
        .report-meta {
            background: #ffffff;
            border-left: 4px solid #111827;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #111827;
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
        }
        .btn-primary:hover {
            background-color: #1e40af;
        }
        .btn-success {
            background-color: #2563eb;
            border: none;
            color: white;
        }
        .btn-success:hover {
            background-color: #1e40af;
        }
        .btn-danger {
            border: none;
        }
        @media print {
            .navbar, .btn-group, .report-meta {
                display: none;
            }
            .report-content {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="?action=dashboard">
                <img src="assets/images/logo.jpg" alt="GCI Logo" style="height:24px; width:24px; object-fit:contain; margin-right:8px; vertical-align:middle;"> RapporAI
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="?action=reports">Retour aux rapports</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- En-tête du rapport -->
    <div class="report-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="report-title"><i class="fas fa-file-pdf"></i> <?php echo htmlspecialchars($report['title']); ?></h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar"></i> Généré le <?php echo date('d/m/Y à H:i', strtotime($report['created_at'])); ?>
                    </p>
                </div>
                <div class="col-auto btn-group">
                    <button onclick="exportToWord()" class="btn btn-primary">
                        <i class="fas fa-file-word"></i> Exporter Word
                    </button>
                    <button onclick="exportToPDF()" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Exporter PDF
                    </button>
                    <button onclick="window.print()" class="btn btn-success">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                    <a href="?action=reports/delete&id=<?php echo $report['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr ?')">
                        <i class="fas fa-trash"></i> Supprimer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu du rapport -->
    <div class="container-fluid">
        <div class="report-meta">
            <div class="row">
                <div class="col-md-3">
                    <strong>Type:</strong> <span class="badge bg-info"><?php echo ucfirst($report['report_type']); ?></span>
                </div>
                <div class="col-md-3">
                    <strong>Date du rapport:</strong> <?php echo date('d/m/Y', strtotime($report['report_date'])); ?>
                </div>
            </div>
        </div>

        <div class="report-content">
            <?php
                // Le contenu du rapport peut contenir des balises HTML encodées.
                $content = $report['content'];
                for ($i = 0; $i < 3; $i++) {
                    $decoded = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    if ($decoded === $content) {
                        break;
                    }
                    $content = $decoded;
                }
                echo $content;
            ?>
        </div>
        <div class="mt-4 mb-4">
            <a href="?action=reports" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour aux rapports
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function exportToWord() {
            const reportTitle = "<?php echo addslashes($report['title']); ?>";
            const reportContent = document.querySelector('.report-content').innerHTML;
            const reportMeta = document.querySelector('.report-meta').innerHTML;
            
            // Créer le contenu HTML pour Word
            const htmlContent = `
                <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
                <head>
                    <meta charset="utf-8">
                    <title>${reportTitle}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .report-title { color: #2563eb; font-size: 24px; font-weight: bold; margin-bottom: 10px; }
                        .report-meta { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
                        .report-content { line-height: 1.6; }
                        strong { font-weight: bold; }
                        u { text-decoration: underline; }
                        h2 { color: #2563eb; border-bottom: 2px solid #2563eb; padding-bottom: 5px; margin-top: 30px; }
                        h3 { color: #1e40af; margin-top: 20px; }
                    </style>
                </head>
                <body>
                    <h1 class="report-title">${reportTitle}</h1>
                    <div class="report-meta">${reportMeta}</div>
                    <div class="report-content">${reportContent}</div>
                </body>
                </html>
            `;
            
            // Créer un blob et le télécharger
            const blob = new Blob([htmlContent], { type: 'application/msword' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = reportTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.doc';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function exportToPDF() {
            const reportTitle = "<?php echo addslashes($report['title']); ?>";
            const reportId = <?php echo json_encode($report['id']); ?>;
            
            // Télécharger le PDF via le backend
            fetch(`?action=reports/export-pdf&id=${reportId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Erreur lors de la génération du PDF');
                    return response.blob();
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = reportTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '_' + new Date().toISOString().split('T')[0] + '.pdf';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la génération du PDF');
                    // Fallback: utiliser html2pdf si le backend échoue
                    const element = document.querySelector('.container-fluid');
                    const opt = {
                        margin: 1,
                        filename: reportTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2, useCORS: true },
                        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
                    };
                    html2pdf().set(opt).from(element).save();
                });
        }
    </script>
</body>
</html>
