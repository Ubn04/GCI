<?php
/**
 * Contrôleur des projets - Gestion CRUD des projets
 */

require_once ROOT_PATH . '/app/models/Project.php';
require_once ROOT_PATH . '/app/models/SiteData.php';

class ProjectController {
    private $projectModel;
    private $siteDataModel;

    public function __construct($pdo) {
        $this->projectModel = new Project($pdo);
        $this->siteDataModel = new SiteData($pdo);
    }

    /**
     * Afficher la liste des projets
     */
    public function index() {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        $projects = $this->projectModel->getByUserId($user_id);
        $error = getFlash('error');
        $success = getFlash('success');
        
        require VIEWS_PATH . '/projects/index.php';
    }

    /**
     * Afficher le formulaire de création
     */
    public function create() {
        requireLogin();
        redirect('projects');
    }

    /**
     * Traiter la création d'un projet
     */
    public function handleCreate() {
        requireLogin();
        $returnTo = $_POST['return_to'] ?? 'projects';
        $allowedReturnTo = ['projects', 'reports/select-project', 'dashboard'];
        if (!in_array($returnTo, $allowedReturnTo, true)) {
            $returnTo = 'projects';
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect($returnTo);
        }

        $name = trim($_POST['name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $project_type = trim($_POST['project_type'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $start_date = $_POST['start_date'] ?? '';

        // Validation
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Le nom du projet est requis';
        }

        if (empty($location)) {
            $errors[] = 'La localisation est requise';
        }

        if (empty($project_type) || !in_array($project_type, ['batiment', 'route', 'pont', 'château d\'eau'])) {
            $errors[] = 'Le type de projet est requis';
        }

        if (empty($start_date)) {
            $errors[] = 'La date de début est requise';
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            redirect($returnTo);
        }

        // Créer le projet
        $data = [
            'user_id' => $_SESSION['user_id'],
            'name' => $name,
            'location' => $location,
            'project_type' => $project_type,
            'description' => $description,
            'start_date' => $start_date,
            'maitre_ouvrage' => trim($_POST['maitre_ouvrage'] ?? null),
            'missions_controle' => trim($_POST['missions_controle'] ?? null)
        ];

        if ($this->projectModel->create($data)) {
            setFlash('success', 'Projet créé avec succès !');
            redirect($returnTo);
        } else {
            setFlash('error', 'Erreur lors de la création du projet');
            redirect($returnTo);
        }
    }

    /**
     * Afficher les détails d'un projet
     */
    public function show($id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        
        $project = $this->projectModel->getById($id);
        
        if (!$project || !$this->projectModel->isOwner($id, $user_id)) {
            redirect('projects');
        }

        $siteData = $this->siteDataModel->getByProjectId($id);
        $error = getFlash('error');
        $success = getFlash('success');
        
        require VIEWS_PATH . '/projects/show.php';
    }

    public function open($id) {
        requireLogin();

        if (!$id || !$this->projectModel->isOwner($id, $_SESSION['user_id'])) {
            redirect('projects');
        }

        redirect("reports/project-info&project_id=$id");
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        
        $project = $this->projectModel->getById($id);
        
        if (!$project || !$this->projectModel->isOwner($id, $user_id)) {
            redirect('projects');
        }

        $error = getFlash('error');
        require VIEWS_PATH . '/projects/edit.php';
    }

    /**
     * Traiter la mise à jour d'un projet
     */
    public function handleUpdate($id) {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("projects/edit/$id");
        }

        $user_id = $_SESSION['user_id'];
        $project = $this->projectModel->getById($id);
        
        if (!$project || !$this->projectModel->isOwner($id, $user_id)) {
            redirect('projects');
        }

        $name = trim($_POST['name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $project_type = trim($_POST['project_type'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $start_date = $_POST['start_date'] ?? '';

        // Validation
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Le nom du projet est requis';
        }

        if (empty($location)) {
            $errors[] = 'La localisation est requise';
        }

        if (empty($project_type) || !in_array($project_type, ['batiment', 'route', 'pont', 'château d\'eau'])) {
            $errors[] = 'Le type de projet est requis';
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            redirect("projects/edit&id=$id");
        }

        // Mettre à jour
        $data = [
            'name' => $name,
            'location' => $location,
            'project_type' => $project_type,
            'description' => $description,
            'start_date' => $start_date,
            'client' => trim($_POST['client'] ?? 'Non spécifié'),
            'control_mission' => trim($_POST['control_mission'] ?? 'Non spécifié')
        ];

        if ($this->projectModel->update($id, $data)) {
            setFlash('success', 'Projet mis à jour avec succès !');
            redirect("projects/show&id=$id");
        } else {
            setFlash('error', 'Erreur lors de la mise à jour');
            redirect("projects/edit&id=$id");
        }
    }

    /**
     * Supprimer un projet
     */
    public function delete($id) {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        
        $project = $this->projectModel->getById($id);
        
        if (!$project || !$this->projectModel->isOwner($id, $user_id)) {
            redirect('projects');
        }

        if ($this->projectModel->delete($id)) {
            setFlash('success', 'Projet supprimé avec succès !');
        } else {
            setFlash('error', 'Erreur lors de la suppression');
        }
        
        redirect('projects');
    }
}
?>
