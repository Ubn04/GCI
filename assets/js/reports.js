/**
 * Gestion des rapports - JavaScript
 */

let currentReportData = null;
let reportsData = [];

// Initialiser les données des rapports
function initReportsData(data) {
    reportsData = data;
    console.log('Reports data loaded:', reportsData.length, 'reports');
}

// Fonction pour afficher le modal avec le rapport
function showReportModal(reportId) {
    console.log('Opening modal for report ID:', reportId);
    const modal = new bootstrap.Modal(document.getElementById('reportModal'));
    
    // Trouver le rapport dans les données
    const reportData = reportsData.find(r => r.id == reportId);
    
    if (reportData) {
        currentReportData = reportData;
        
        // Afficher l'état de chargement professionnel
        showLoadingState();
        modal.show();
        
        // Afficher le contenu avec un délai pour l'effet
        setTimeout(() => {
            displayReportInModal(reportData);
        }, 800);
    } else {
        console.error('Rapport non trouvé:', reportId);
        alert('Erreur: Rapport non trouvé');
    }
}

// Fonction pour afficher l'état de chargement
function showLoadingState() {
    document.getElementById('reportTitle').textContent = 'Chargement...';
    document.getElementById('reportReference').textContent = 'RAP-0000-000';
    document.getElementById('reportType').textContent = 'Chargement...';
    document.getElementById('reportDate').textContent = '--/--/----';
    document.getElementById('reportGenerated').textContent = '--/--/---- --:--';
    document.getElementById('reportContent').innerHTML = `
        <div class="pro-loading-state">
            <div class="pro-loading-animation">
                <div class="pro-loading-circle"></div>
                <div class="pro-loading-circle"></div>
                <div class="pro-loading-circle"></div>
            </div>
            <div class="pro-loading-text">
                <i class="fas fa-brain"></i>
                Chargement du rapport généré par IA...
            </div>
        </div>
    `;
}

// Fonction pour afficher le rapport dans le modal
function displayReportInModal(report) {
    // Générer une référence unique
    const reportRef = `RAP-${new Date(report.created_at).getFullYear()}-${String(report.id).padStart(4, '0')}`;
    document.getElementById('reportReference').textContent = reportRef;
    
    // Mettre à jour le titre et sous-titre
    document.getElementById('reportTitle').textContent = report.title;
    document.getElementById('reportSubtitle').textContent = 
        `Projet: ${report.project_name || 'Non spécifié'}`;
    
    // Mettre à jour les métadonnées
    const reportTypeLabels = {
        'daily': 'Journalier',
        'monthly': 'Mensuel',
        'annual': 'Annuel'
    };
    
    document.getElementById('reportType').textContent = 
        reportTypeLabels[report.report_type] || report.report_type.charAt(0).toUpperCase() + report.report_type.slice(1);
    document.getElementById('reportDate').textContent = formatDate(report.report_date);
    document.getElementById('reportGenerated').textContent = formatDateTime(report.created_at);
    
    // Afficher le contenu du rapport
    const contentElement = document.getElementById('reportContent');
    
    // Si le contenu contient déjà du HTML (tableaux, etc.), l'afficher directement
    const normalizedContent = decodeHtmlEntitiesRecursively(report.content || '');

    if (/<(table|h1|h2|h3|p|div|tr|td|th)\b/i.test(normalizedContent)) {
        contentElement.innerHTML = normalizedContent;
    } else {
        // Sinon, formater le texte brut en HTML
        let content = escapeHtml(normalizedContent);
        
        // Remplacer les sauts de ligne par des <br>
        content = content.replace(/\n/g, '<br>');
        
        // Formater les titres
        content = content.replace(/^# (.*$)/gm, '<h1>$1</h1>');
        content = content.replace(/^## (.*$)/gm, '<h2>$1</h2>');
        content = content.replace(/^### (.*$)/gm, '<h3>$1</h3>');
        
        // Formater le texte en gras
        content = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Formater les listes
        content = content.replace(/^- (.*$)/gm, '<li>$1</li>');
        content = content.replace(/^• (.*$)/gm, '<li>$1</li>');
        content = content.replace(/^\* (.*$)/gm, '<li>$1</li>');
        
        // Envelopper les listes dans des balises <ul>
        content = content.replace(/(<li>.*?<\/li>(?:<br>)?)+/gs, function(match) {
            return '<ul>' + match.replace(/<br>/g, '') + '</ul>';
        });
        
        // Nettoyer les balises br en trop
        content = content.replace(/<br>\s*<h([1-6])>/g, '<h$1>');
        content = content.replace(/<\/h([1-6])>\s*<br>/g, '</h$1>');
        content = content.replace(/<br>\s*<ul>/g, '<ul>');
        content = content.replace(/<\/ul>\s*<br>/g, '</ul>');
        
        // Créer des paragraphes
        content = content.replace(/(<br>\s*){2,}/g, '</p><p>');
        content = '<p>' + content + '</p>';
        
        // Nettoyer les paragraphes vides
        content = content.replace(/<p>\s*<\/p>/g, '');
        content = content.replace(/<p>\s*(<h[1-6]>)/g, '$1');
        content = content.replace(/(<\/h[1-6]>)\s*<\/p>/g, '$1');
        content = content.replace(/<p>\s*(<ul>)/g, '$1');
        content = content.replace(/(<\/ul>)\s*<\/p>/g, '$1');
        
        contentElement.innerHTML = content;
    }
    
    contentElement.classList.add('loaded');
    
    // Animation d'apparition du contenu
    contentElement.style.opacity = '0';
    contentElement.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        contentElement.style.transition = 'all 0.6s ease';
        contentElement.style.opacity = '1';
        contentElement.style.transform = 'translateY(0)';
    }, 100);
}

// Fonction pour exporter le rapport actuel en PDF
function exportCurrentReportToPDF() {
    if (!currentReportData) return;
    
    // Utiliser l'endpoint serveur pour un meilleur rendu
    const url = `export_report.php?id=${currentReportData.id}&format=pdf`;
    window.open(url, '_blank');
}

// Fonction pour exporter le rapport actuel en Word
function exportCurrentReportToWord() {
    if (!currentReportData) return;
    
    // Utiliser l'endpoint serveur pour un meilleur rendu
    const url = `export_report.php?id=${currentReportData.id}&format=word`;
    window.open(url, '_blank');
}

// Fonctions utilitaires
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function decodeHtmlEntitiesRecursively(text) {
    let decoded = String(text || '');
    const textarea = document.createElement('textarea');

    for (let i = 0; i < 4; i++) {
        textarea.innerHTML = decoded;
        const next = textarea.value;
        if (next === decoded) {
            break;
        }
        decoded = next;
    }

    return decoded;
}

function sanitizeFilename(filename) {
    return filename.replace(/[^a-z0-9]/gi, '_').toLowerCase();
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR') + ' à ' + date.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
}
