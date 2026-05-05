<?php
/**
 * Vue de la page Informations du chantier avant collecte des données.
 */
$draftJson = json_encode($draft, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations du chantier - ChantierAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-style.css">
    <style>
        /* Correction de l'espacement pour cette page */
        .content-area {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }
        
        .page-header {
            margin-bottom: 24px !important;
        }
        
        /* Styles spécifiques à la page informations chantier */
        .option-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border-radius: 999px;
            padding: 12px 18px;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        .option-pill input {
            display: none;
        }
        .option-pill.selected {
            background: #eff6ff;
            border-color: #93c5fd;
            color: #1e3a8a;
        }
        .empty-row {
            color: #475569;
        }
        .save-status {
            font-size: 14px;
            color: #64748b;
            margin-top: 8px;
        }
        
        /* Variantes de couleurs pour les cards */
        .row .col-lg-4:nth-child(1) .card {
            position: relative;
            overflow: hidden;
        }
        
        .row .col-lg-4:nth-child(1) .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #06b6d4, #22d3ee);
        }
        
        .row .col-lg-4:nth-child(1) .card .card-header h2 {
            color: #0891b2;
        }
        
        .row .col-lg-8:nth-child(2) .card {
            position: relative;
            overflow: hidden;
        }
        
        .row .col-lg-8:nth-child(2) .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #9333ea, #c084fc);
        }
        
        .row .col-lg-8:nth-child(2) .card .card-header h2 {
            color: #7c3aed;
        }
        
        .row .col-lg-6:nth-child(3) .card {
            position: relative;
            overflow: hidden;
        }
        
        .row .col-lg-6:nth-child(3) .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #34d399);
        }
        
        .row .col-lg-6:nth-child(3) .card .card-header h2 {
            color: #059669;
        }
        
        .row .col-lg-6:nth-child(4) .card {
            position: relative;
            overflow: hidden;
        }
        
        .row .col-lg-6:nth-child(4) .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }
        
        .row .col-lg-6:nth-child(4) .card .card-header h2 {
            color: #d97706;
        }
        
        .card {
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
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
                            <i class="fas fa-clipboard-list"></i>
                            <span>Préparation du rapport</span>
                        </div>
                        <div class="header-title">
                            <h1>Informations du chantier</h1>
                            <p>Projet : <?php echo htmlspecialchars($project['name']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="content-area">
                <div class="card">
                    <div class="card-header">
                        <h2>Préparation du rapport</h2>
                        <p>Indiquez les informations du chantier avant de continuer vers la collecte des données.</p>
                    </div>
                    <div class="card-body">

                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h2>1. Météo</h2>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-column gap-3">
                                            <label class="option-pill<?php echo $draft['weather'] === 'Ensoleille' ? ' selected' : ''; ?>" data-value="Ensoleille">
                                                <input type="radio" name="weather" value="Ensoleille" <?php echo $draft['weather'] === 'Ensoleille' ? 'checked' : ''; ?>>
                                                Ensoleille
                                            </label>
                                            <label class="option-pill<?php echo $draft['weather'] === 'Pluvieuse' ? ' selected' : ''; ?>" data-value="Pluvieuse">
                                                <input type="radio" name="weather" value="Pluvieuse" <?php echo $draft['weather'] === 'Pluvieuse' ? 'checked' : ''; ?>>
                                                Pluvieuse
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h2>2. Matériel sur le chantier</h2>
                                    </div>
                                    <div class="card-body">
                            <div class="row g-3 align-items-end mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Écrire ou choisir un matériel</label>
                                    <input id="equipmentInput" list="equipmentList" class="form-control" placeholder="Écrire ou choisir un matériel">
                                    <datalist id="equipmentList">
                                        <option value="Pelleteuse"></option>
                                        <option value="Bétonnière"></option>
                                        <option value="Nacelle"></option>
                                        <option value="Camion"></option>
                                        <option value="Compresseur"></option>
                                    </datalist>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Présent</label>
                                    <input id="equipmentPresent" type="number" min="0" class="form-control" value="0">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Marche</label>
                                    <input id="equipmentMarche" type="number" min="0" class="form-control" value="0">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Immob</label>
                                    <input id="equipmentImmob" type="number" min="0" class="form-control" value="0">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Panne</label>
                                    <input id="equipmentPanne" type="number" min="0" class="form-control" value="0">
                                </div>
                                <div class="col-md-12 text-end">
                                    <button type="button" class="btn btn-primary rounded-4" onclick="addEquipment()">
                                        <i class="fas fa-plus"></i> Créer
                                    </button>
                                </div>
                                <div class="col-12">
                                    <div id="equipmentError" class="text-danger small mt-1"></div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Désignation</th>
                                            <th>Present</th>
                                            <th>Marche</th>
                                            <th>Immob</th>
                                            <th>Panne</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="equipmentTableBody"></tbody>
                                </table>
                            </div>
                                        <div id="equipmentEmpty" class="empty-row">Aucune ligne ajoutée. Utilisez le bouton "Créer".</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h2>3. Personnel sur le chantier</h2>
                                    </div>
                                    <div class="card-body">
                            <div class="row g-3 align-items-end mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Écrire ou choisir un profil</label>
                                    <input id="personnelInput" list="personnelList" class="form-control" placeholder="Écrire ou choisir un profil">
                                    <datalist id="personnelList">
                                        <option value="Chef de chantier"></option>
                                        <option value="Conducteur"></option>
                                        <option value="Ouvrier"></option>
                                        <option value="Ingénieur"></option>
                                        <option value="Technicien"></option>
                                    </datalist>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nbr</label>
                                    <input id="personnelNbr" type="number" min="1" class="form-control" value="1">
                                </div>
                                <div class="col-md-3 text-end">
                                    <button type="button" class="btn btn-primary rounded-4" onclick="addPersonnel()">
                                        <i class="fas fa-plus"></i> Créer
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Profil</th>
                                            <th>Nbr</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="personnelTableBody"></tbody>
                                </table>
                            </div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div id="personnelEmpty" class="empty-row">Aucune ligne ajoutée. Utilisez le bouton "Créer".</div>
                                            <div><strong>Total :</strong> <span id="personnelTotal">0</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h2>4. Matériaux</h2>
                                    </div>
                                    <div class="card-body">
                            <div class="row g-3 align-items-end mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Écrire ou choisir un matériau</label>
                                    <input id="materialInput" list="materialList" class="form-control" placeholder="Écrire ou choisir un matériau">
                                    <datalist id="materialList">
                                        <option value="Ciment"></option>
                                        <option value="Sable"></option>
                                        <option value="Gravier"></option>
                                        <option value="Acier"></option>
                                        <option value="Bois"></option>
                                    </datalist>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unité</label>
                                    <select id="materialUnite" class="form-select">
                                        <option value="">Choisir une unité</option>
                                        <option value="kg">kg</option>
                                        <option value="m">m</option>
                                        <option value="m²">m²</option>
                                        <option value="m³">m³</option>
                                        <option value="l">l</option>
                                        <option value="pièce(s)">pièce(s)</option>
                                        <option value="tonne(s)">tonne(s)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Quantité</label>
                                    <input id="materialQuantite" type="number" min="0" class="form-control" value="0">
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-primary rounded-4" onclick="addMaterial()">
                                        <i class="fas fa-plus"></i> Créer
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Désignation</th>
                                            <th>Unité</th>
                                            <th>Quantité</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="materialTableBody"></tbody>
                                </table>
                            </div>
                                        <div id="materialEmpty" class="empty-row">Aucune ligne ajoutée. Utilisez le bouton "Créer".</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="?action=reports/generate&project_id=<?php echo $project['id']; ?>" class="btn btn-primary btn-lg px-5">
                                Suivant <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const projectId = <?php echo json_encode($project['id'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        const draft = <?php echo $draftJson; ?>;
        const saveStatus = document.getElementById('saveStatus');

        let equipments = draft.equipments || [];
        let personnels = draft.personnels || [];
        let materials = draft.materials || [];
        let weather = draft.weather || 'Ensoleille';

        function updateWeatherSelection() {
            document.querySelectorAll('.option-pill').forEach(element => {
                const value = element.getAttribute('data-value');
                const radio = element.querySelector('input');
                const selected = weather === value;
                radio.checked = selected;
                element.classList.toggle('selected', selected);
            });
        }

        function addEquipment() {
            const designation = document.getElementById('equipmentInput').value.trim();
            const present = parseInt(document.getElementById('equipmentPresent').value, 10);
            const marche = parseInt(document.getElementById('equipmentMarche').value, 10);
            const immob = parseInt(document.getElementById('equipmentImmob').value, 10);
            const panne = parseInt(document.getElementById('equipmentPanne').value, 10);
            const errorElement = document.getElementById('equipmentError');

            errorElement.textContent = '';
            if (!designation) {
                errorElement.textContent = 'Veuillez saisir une désignation.';
                return;
            }
            if (isNaN(present) || present < 0 || isNaN(marche) || marche < 0 || isNaN(immob) || immob < 0 || isNaN(panne) || panne < 0) {
                errorElement.textContent = 'Les valeurs doivent être des nombres positifs ou nuls.';
                return;
            }
            if (marche > present) {
                errorElement.textContent = 'Le nombre de matériel en marche ne peut pas dépasser le nombre de matériel présent.';
                return;
            }
            if (panne > present) {
                errorElement.textContent = 'Le nombre de matériel en panne ne peut pas dépasser le nombre de matériel présent.';
                return;
            }
            if (marche + panne > present) {
                errorElement.textContent = 'La somme du matériel en marche et en panne ne peut pas dépasser le matériel présent.';
                return;
            }

            equipments.push({designation, present, marche, immob, panne});
            document.getElementById('equipmentInput').value = '';
            document.getElementById('equipmentPresent').value = 0;
            document.getElementById('equipmentMarche').value = 0;
            document.getElementById('equipmentImmob').value = 0;
            document.getElementById('equipmentPanne').value = 0;
            renderEquipmentTable();
            saveDraft();
        }

        function addPersonnel() {
            const profile = document.getElementById('personnelInput').value.trim();
            const nbr = parseInt(document.getElementById('personnelNbr').value, 10) || 0;
            if (!profile || nbr < 1) return;

            personnels.push({profile, nbr});
            document.getElementById('personnelInput').value = '';
            document.getElementById('personnelNbr').value = 1;
            renderPersonnelTable();
            saveDraft();
        }

        function addMaterial() {
            const designation = document.getElementById('materialInput').value.trim();
            const unite = document.getElementById('materialUnite').value.trim();
            const quantite = parseFloat(document.getElementById('materialQuantite').value) || 0;
            if (!designation || !unite || quantite <= 0) return;

            materials.push({designation, unite, quantite});
            document.getElementById('materialInput').value = '';
            document.getElementById('materialUnite').value = '';
            document.getElementById('materialQuantite').value = 0;
            renderMaterialTable();
            saveDraft();
        }

        function removeEquipment(index) {
            equipments.splice(index, 1);
            renderEquipmentTable();
            saveDraft();
        }

        function removePersonnel(index) {
            personnels.splice(index, 1);
            renderPersonnelTable();
            saveDraft();
        }

        function removeMaterial(index) {
            materials.splice(index, 1);
            renderMaterialTable();
            saveDraft();
        }

        function renderEquipmentTable() {
            const tbody = document.getElementById('equipmentTableBody');
            tbody.innerHTML = '';
            if (equipments.length === 0) {
                document.getElementById('equipmentEmpty').style.display = 'block';
                return;
            }
            document.getElementById('equipmentEmpty').style.display = 'none';
            equipments.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${escapeHtml(item.designation)}</td>
                    <td>${escapeHtml(item.present)}</td>
                    <td>${escapeHtml(item.marche)}</td>
                    <td>${escapeHtml(item.immob)}</td>
                    <td>${escapeHtml(item.panne)}</td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEquipment(${index})"><i class="fas fa-trash"></i></button></td>
                `;
                tbody.appendChild(row);
            });
        }

        function renderPersonnelTable() {
            const tbody = document.getElementById('personnelTableBody');
            tbody.innerHTML = '';
            if (personnels.length === 0) {
                document.getElementById('personnelEmpty').style.display = 'block';
                document.getElementById('personnelTotal').textContent = '0';
                return;
            }
            document.getElementById('personnelEmpty').style.display = 'none';
            let total = 0;
            personnels.forEach((item, index) => {
                total += item.nbr;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${escapeHtml(item.profile)}</td>
                    <td>${item.nbr}</td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removePersonnel(${index})"><i class="fas fa-trash"></i></button></td>
                `;
                tbody.appendChild(row);
            });
            document.getElementById('personnelTotal').textContent = total;
        }

        function renderMaterialTable() {
            const tbody = document.getElementById('materialTableBody');
            tbody.innerHTML = '';
            if (materials.length === 0) {
                document.getElementById('materialEmpty').style.display = 'block';
                return;
            }
            document.getElementById('materialEmpty').style.display = 'none';
            materials.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${escapeHtml(item.designation)}</td>
                    <td>${escapeHtml(item.unite)}</td>
                    <td>${item.quantite}</td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMaterial(${index})"><i class="fas fa-trash"></i></button></td>
                `;
                tbody.appendChild(row);
            });
        }

        function escapeHtml(text) {
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function saveDraft() {
            const payload = {
                weather,
                equipments,
                personnels,
                materials
            };

            fetch(`?action=reports/save-draft&project_id=${encodeURIComponent(projectId)}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    saveStatus.textContent = 'Brouillon enregistré.';
                } else {
                    saveStatus.textContent = 'Erreur lors de la sauvegarde.';
                }
            })
            .catch(() => {
                saveStatus.textContent = 'Erreur lors de la sauvegarde.';
            });
        }

        document.querySelectorAll('.option-pill').forEach(element => {
            element.addEventListener('click', () => {
                weather = element.getAttribute('data-value');
                updateWeatherSelection();
                saveDraft();
            });
        });

        updateWeatherSelection();
        renderEquipmentTable();
        renderPersonnelTable();
        renderMaterialTable();
        saveDraft();
    </script>
</body>
</html>
