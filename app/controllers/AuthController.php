<?php
/**
 * Contrôleur d'authentification - Gestion inscription/connexion
 */

require_once ROOT_PATH . '/app/models/User.php';

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    /**
     * Afficher la page de connexion
     */
    public function login() {
        if (isLoggedIn()) {
            redirect('dashboard');
        }

        $error = getFlash('error');
        $success = getFlash('success');
        require VIEWS_PATH . '/auth/login.php';
    }

    /**
     * Traiter la connexion
     */
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth/login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($email) || empty($password)) {
            setFlash('error', 'Veuillez remplir tous les champs');
            redirect('auth/login');
        }

        // Vérifier les identifiants
        $user = $this->userModel->findByEmail($email);
        if ($user && !password_verify($password, $user['password'])) {
            $user = false;
        }

        if ($user) {
            if (empty($user['is_verified'])) {
                $verificationLink = APP_URL . '?action=auth/verify&token=' . urlencode($user['verification_token']);
                setFlash('error', 'Votre compte n\'est pas encore vérifié. Veuillez confirmer votre email avant de vous connecter. <br>Si vous n\'avez pas reçu le mail, cliquez ici : <a href="' . $verificationLink . '">Activer mon compte</a>');
                redirect('auth/login');
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'photo' => $user['photo'] ?? null
            ];
            setFlash('success', 'Connexion réussie !');
            redirect('dashboard');
        } else {
            setFlash('error', 'Email ou mot de passe incorrect');
            redirect('auth/login');
        }
    }

    /**
     * Afficher la page d'inscription
     */
    public function register() {
        if (isLoggedIn()) {
            redirect('dashboard');
        }

        $error = getFlash('error');
        $success = getFlash('success');
        require VIEWS_PATH . '/auth/register.php';
    }

    /**
     * Traiter l'inscription
     */
    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth/register');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'ingénieur';

        // Validation
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Le nom est requis';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide';
        }

        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
        }

        if ($this->userModel->emailExists($email)) {
            $errors[] = 'Cet email est déjà utilisé';
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            redirect('auth/register');
        }

        // Créer l'utilisateur
        $verificationToken = generateToken(32);
        $verificationTokenExpiresAt = date('Y-m-d H:i:s', strtotime('+12 hours'));
        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'is_verified' => 0,
            'verification_token' => $verificationToken,
            'verification_token_expires_at' => $verificationTokenExpiresAt
        ];

        if ($this->userModel->create($userData)) {
            $verificationLink = APP_URL . '?action=auth/verify&token=' . urlencode($verificationToken);
            $sent = $this->sendVerificationEmail($email, $name, $verificationLink);

            $message = 'Votre compte est créé. ';
            if ($sent) {
                $message .= 'Un email de confirmation a été envoyé, vérifiez votre boîte de réception. ';
            } else {
                $message .= 'L’envoi automatique par email a échoué. ';
            }
            $message .= 'Ce lien est valable 12 heures. Cliquez sur ce lien pour activer votre compte : <a href="' . $verificationLink . '">Activer mon compte</a>';

            setFlash('success', $message);
            redirect('auth/login');
        } else {
            setFlash('error', 'Une erreur est survenue lors de l\'inscription');
            redirect('auth/register');
        }
    }

    /**
     * Vérifier le compte via token d'email
     */
    public function verifyEmail() {
        $token = $_GET['token'] ?? null;

        if (empty($token)) {
            setFlash('error', 'Lien de vérification invalide.');
            redirect('auth/login');
        }

        $user = $this->userModel->findByVerificationToken($token);
        if (!$user) {
            setFlash('error', 'Token de vérification invalide ou expiré.');
            redirect('auth/login');
        }

        if ($this->userModel->verify($user['id'])) {
            setFlash('success', 'Votre adresse email a bien été vérifiée. Vous pouvez maintenant vous connecter.');
        } else {
            setFlash('error', 'Impossible de vérifier votre compte. Réessayez plus tard.');
        }

        redirect('auth/login');
    }

    /**
     * Envoyer un email de vérification
     */
    private function sendVerificationEmail($email, $name, $verificationLink) {
        $subject = 'Vérifiez votre email ChantierAI';
        $message = "Bonjour $name,\n\n";
        $message .= "Merci de vous être inscrit sur ChantierAI. Cliquez sur le lien suivant pour activer votre compte :\n\n";
        $message .= "$verificationLink\n\n";
        $message .= "Ce lien est valable 12 heures.\n\n";
        $message .= "Si vous n'avez pas demandé cette inscription, ignorez ce message.\n";

        $fromEmail = SMTP_FROM_EMAIL ?: 'no-reply@' . parse_url(APP_URL, PHP_URL_HOST);
        $fromName = SMTP_FROM_NAME ?: 'ChantierAI';

        return sendSMTPMail($email, $subject, $message, $fromEmail, $fromName);
    }

    /**
     * Afficher le profil utilisateur
     */
    public function profile() {
        requireLogin();
        $user = $this->userModel->findById($_SESSION['user_id']);
        if (!$user) {
            redirect('dashboard');
        }
        $error = getFlash('error');
        $success = getFlash('success');
        require VIEWS_PATH . '/auth/profile.php';
    }

    /**
     * Mettre à jour le profil utilisateur
     */
    public function handleUpdateProfile() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth/profile');
        }

        $user_id = $_SESSION['user_id'];
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];

        if (empty($name)) {
            $errors[] = 'Le nom est requis';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide';
        }

        if (!empty($password) && strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
        }

        if ($this->userModel->emailExistsExceptId($email, $user_id)) {
            $errors[] = 'Cet email est déjà utilisé';
        }

        $currentUser = $this->userModel->findById($user_id);

        $photoPath = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Erreur lors du téléchargement de la photo';
            } else {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['photo']['type'], $allowedTypes)) {
                    $errors[] = 'Format d\'image non pris en charge. Utilisez JPG, PNG ou GIF.';
                }
                if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
                    $errors[] = 'La photo doit faire moins de 2 Mo.';
                }

                if (empty($errors)) {
                    $uploadDir = ROOT_PATH . '/uploads/profiles';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('user_') . '.' . strtolower($extension);
                    $targetPath = $uploadDir . '/' . $filename;

                    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                        $errors[] = 'Impossible d\'enregistrer la photo.';
                    } else {
                        $photoPath = 'uploads/profiles/' . $filename;
                        if (!empty($currentUser['photo'])) {
                            $oldPhoto = ROOT_PATH . '/' . $currentUser['photo'];
                            if (file_exists($oldPhoto)) {
                                @unlink($oldPhoto);
                            }
                        }
                    }
                }
            }
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            redirect('auth/profile');
        }

        $updateData = [
            'name' => $name,
            'email' => $email,
        ];

        if (!empty($password)) {
            $updateData['password'] = $password;
        }

        if (!empty($photoPath)) {
            $updateData['photo'] = $photoPath;
        }

        if (!empty($password)) {
            $updateData['password'] = $password;
        }

        if ($this->userModel->update($user_id, $updateData)) {
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            if (!empty($photoPath)) {
                $_SESSION['user']['photo'] = $photoPath;
            }
            setFlash('success', 'Profil mis à jour avec succès');
            redirect('auth/profile');
        }

        setFlash('error', 'Une erreur est survenue pendant la mise à jour du profil');
        redirect('auth/profile');
    }

    /**
     * Supprimer le compte utilisateur
     */
    public function deleteAccount() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth/profile');
        }

        $user_id = $_SESSION['user_id'];

        if ($this->userModel->delete($user_id)) {
            session_destroy();
            redirect('auth/login');
        }

        setFlash('error', 'Impossible de supprimer le compte.');
        redirect('auth/profile');
    }

    /**
     * Déconnexion
     */
    public function logout() {
        session_destroy();
        redirect('auth/login');
    }
}
?>
