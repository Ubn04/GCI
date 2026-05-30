<?php
$months = [
    1 => 'Janvier',
    2 => 'Fevrier',
    3 => 'Mars',
    4 => 'Avril',
    5 => 'Mai',
    6 => 'Juin',
    7 => 'Juillet',
    8 => 'Aout',
    9 => 'Septembre',
    10 => 'Octobre',
    11 => 'Novembre',
    12 => 'Decembre',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generer un rapport mensuel - ChantierAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-style.css">
    <style>
        .projects-grid, .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }
        .action-card, .report-row-card, .period-panel {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.07);
        }
        .action-card {
            padding: 24px;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        }
        .action-card h2 {
            font-size: 20px;
            color: #1e3a8a;
            margin: 0 0 10px;
        }
        .muted-line {
            color: #64748b;
            margin: 0 0 16px;
        }
        .period-panel {
            padding: 20px;
            margin-bottom: 24px;
        }
        .report-row-card {
            padding: 18px;
        }
        .report-row-card h3 {
            font-size: 17px;
            margin: 0 0 8px;
            color: #111827;
        }
        .report-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            color: #64748b;
            font-size: 14px;
        }
        .generate-bar {
            position: sticky;
            bottom: 0;
            background: rgba(248, 251, 255, .94);
            border-top: 1px solid #dbeafe;
            margin: 28px -32px -32px;
            padding: 18px 32px;
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include 'app/views/components/sidebar.php'; ?>

        <div class="main-content">
            <div class="page-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-greeting">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Rapport mensuel</span>
                        </div>
                        <div class="header-title">
                            <h1>Generer un rapport mensuel</h1>
                            <p>Choisissez un projet, verifiez les rapports journaliers du mois, puis genereez la synthese mensuelle.</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if (empty($project)): ?>
                <?php if (!empty($projects)): ?>
                    <div class="projects-grid">
                        <?php foreach ($projects as $item): ?>
                            <a class="action-card text-decoration-none" href="?action=reports/monthly&project_id=<?php echo $item['id']; ?>">
                                <h2><?php echo htmlspecialchars($item['name']); ?></h2>
                                <p class="muted-line"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($item['location']); ?></p>
                                <span class="btn btn-primary w-100">Choisir ce projet</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Aucun projet disponible. Creez d'abord un projet.</div>
                <?php endif; ?>
            <?php else: ?>
                <div class="period-panel">
                    <form method="GET" action="" class="row g-3 align-items-end">
                        <input type="hidden" name="action" value="reports/monthly">
                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                        <div class="col-md-4">
                            <label class="form-label" for="month">Mois</label>
                            <select class="form-select" id="month" name="month">
                                <?php foreach ($months as $number => $label): ?>
                                    <option value="<?php echo $number; ?>" <?php echo intval($selectedMonth) === $number ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="year">Annee</label>
                            <input class="form-control" id="year" name="year" type="number" min="2000" max="2100" value="<?php echo htmlspecialchars($selectedYear); ?>">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" type="submit">Afficher</button>
                        </div>
                        <div class="col-md-2">
                            <a class="btn btn-outline-secondary w-100" href="?action=reports/monthly">Projets</a>
                        </div>
                    </form>
                </div>

                <h2 class="h4 mb-3">Rapports journaliers de <?php echo htmlspecialchars($months[intval($selectedMonth)] ?? $selectedMonth); ?> <?php echo htmlspecialchars($selectedYear); ?> - <?php echo htmlspecialchars($project['name']); ?></h2>

                <?php if (!empty($generatedMonthlyReports)): ?>
                    <div class="alert alert-secondary">
                        <?php echo count($generatedMonthlyReports); ?> rapport mensuel existe deja pour cette periode. Vous pouvez en generer un nouveau si les rapports journaliers ont change.
                    </div>
                <?php endif; ?>

                <?php if (!empty($sourceReports)): ?>
                    <div class="reports-grid">
                        <?php foreach ($sourceReports as $report): ?>
                            <div class="report-row-card">
                                <h3><?php echo htmlspecialchars($report['title']); ?></h3>
                                <div class="report-meta">
                                    <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($report['report_date'])); ?></span>
                                    <span><i class="fas fa-file-alt"></i> Rapport journalier</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="generate-bar">
                        <form method="POST" action="?action=reports/generate-monthly" class="d-flex justify-content-end">
                            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                            <input type="hidden" name="month" value="<?php echo htmlspecialchars($selectedMonth); ?>">
                            <input type="hidden" name="year" value="<?php echo htmlspecialchars($selectedYear); ?>">
                            <button class="btn btn-success btn-lg" type="submit">
                                <i class="fas fa-wand-magic-sparkles"></i> Generer rapport mensuel
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Aucun rapport journalier trouve pour ce projet sur ce mois.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
