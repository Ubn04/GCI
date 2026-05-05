<?php
/**
 * Contrôleur des données terrain - Gestion de la collecte de données
 */

require_once ROOT_PATH . '/app/models/SiteData.php';
require_once ROOT_PATH . '/app/models/Project.php';

class SiteDataController {
    private $siteDataModel;
    private $projectModel;

    public function __construct($pdo) {
        $this->siteDataModel = new SiteData($pdo);
        $this->projectModel = new Project($pdo);
    }

    /**
     * Ajouter une donnée terrain
     */
    public function add($project_id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        
        // Vérifier que le projet appartient à l'utilisateur
        if (!$this->projectModel->isOwner($project_id, $user_id)) {
            redirect('projects');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("projects/show&id=$project_id");
        }

        $content = trim($_POST['content'] ?? '');
        $data_type = $_POST['data_type'] ?? 'text';
        $file_url = null;

        // Validation
        if (empty($content)) {
            setFlash('error', 'Le contenu est requis');
            redirect("projects/show&id=$project_id");
        }

        // Traiter l'upload de fichier si présent
        if ($data_type !== 'text' && isset($_FILES['file'])) {
            $file_url = $this->uploadFile($_FILES['file']);
            if (!$file_url) {
                setFlash('error', 'Erreur lors de l\'upload du fichier');
                redirect("projects/show&id=$project_id");
            }
        }

        // Créer l'entrée
        $data = [
            'project_id' => $project_id,
            'user_id' => $user_id,
            'content' => $content,
            'data_type' => $data_type,
            'file_url' => $file_url
        ];

        if ($this->siteDataModel->create($data)) {
            setFlash('success', 'Donnée ajoutée avec succès !');
        } else {
            setFlash('error', 'Erreur lors de l\'ajout de la donnée');
        }
        
        redirect("projects/show&id=$project_id");
    }

    /**
     * Supprimer une donnée terrain
     */
    public function delete($id, $project_id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        
        // Vérifier que le projet appartient à l'utilisateur
        if (!$this->projectModel->isOwner($project_id, $user_id)) {
            redirect('projects');
        }

        if ($this->siteDataModel->delete($id)) {
            setFlash('success', 'Donnée supprimée avec succès !');
        } else {
            setFlash('error', 'Erreur lors de la suppression');
        }
        
        redirect("projects/show&id=$project_id");
    }

    /**
     * Uploader un fichier (simulation - à remplacer par Cloudinary)
     */
    private function uploadFile($file) {
        // Pour l'instant, on simule l'upload
        // À remplacer par l'intégration Cloudinary
        
        $allowed_types = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'video/mp4',
            'video/quicktime',
            'audio/mpeg',
            'audio/mp3',
            'audio/wav',
            'audio/webm',
            'application/pdf',
            'text/plain',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $max_size = 15 * 1024 * 1024; // 15MB

        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }

        if ($file['size'] > $max_size) {
            return false;
        }

        // Créer le dossier uploads s'il n'existe pas
        $upload_dir = PUBLIC_PATH . '/uploads';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Générer un nom unique
        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = $upload_dir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return APP_URL . '/public/uploads/' . $filename;
        }

        return false;
    }
}
?>
