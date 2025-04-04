<?php
// reset_password.php - Version améliorée avec sécurité

session_start();
include 'includes/db.php';

// Partie 1: Demande de réinitialisation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && !isset($_POST['token'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Utilisation de requête préparée pour éviter les injections SQL
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Génération d'un token sécurisé
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expire après 1 heure
        
        // Mise à jour de la base de données avec le token et sa date d'expiration
        $update_sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sss", $token, $expiry, $email);
        $update_stmt->execute();
        
        // Création du lien de réinitialisation
        $reset_link = "https://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
        // Dans un environnement de production, envoyer par email
        // mail($email, "Réinitialisation de mot de passe", "Votre lien: $reset_link");
        
        // Pour simplifier le test en développement, on affiche le lien
        $_SESSION['message'] = "Un lien de réinitialisation a été envoyé à votre email. <br>
                              <small style='color:blue'>(Pour le développement: <a href='" . htmlspecialchars($reset_link, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($reset_link, ENT_QUOTES, 'UTF-8') . "</a>)</small>";
    } else {
        // Message générique pour ne pas révéler si l'email existe
        $_SESSION['message'] = "Si cette adresse email est associée à un compte, un lien de réinitialisation vous sera envoyé.";
    }
}

// Partie 2: Traitement du nouveau mot de passe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password']) && isset($_POST['token'])) {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Vérification que les mots de passe correspondent
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
    } 
    // Vérification de la force du mot de passe
    elseif (strlen($new_password) < 8) {
        $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    else {
        // Vérification du token
        $current_time = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $token, $current_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Hashage du mot de passe
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            
            // Mise à jour du mot de passe et suppression du token
            $update_sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $hashed_password, $token);
            
            if ($update_stmt->execute()) {
                $_SESSION['message'] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['error'] = "Une erreur s'est produite lors de la réinitialisation du mot de passe.";
            }
        } else {
            $_SESSION['error'] = "Le token de réinitialisation est invalide ou a expiré.";
        }
    }
}

// Affichage de la page pour saisir un nouveau mot de passe si un token est fourni
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $current_time = date('Y-m-d H:i:s');
    
    // Vérification de la validité du token
    $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $token, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Le lien de réinitialisation est invalide ou a expiré.";
        header("Location: reset_password.php");
        exit();
    }
    
    // Si le token est valide, afficher le formulaire pour le nouveau mot de passe
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Définir un nouveau mot de passe</title>
        <!-- Ajout du Content-Security-Policy -->
        <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; object-src 'none';">
        <style>
            body {
                font-family: arial, sans-serif;
                background:rgb(165,165,165);
                background:radial-gradient(circle, rgba(165,165,165,1) 0%, rgba(251,169,63,1) 100%);
            }
            
            form {
                background-color: white;
                padding: 20px;
                border-radius:8px;
                max-width: 300px;
                margin:auto;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }

            input[type="password"] {
                width: 100%;
                padding:8px;
                margin:6px 0;
                box-sizing: border-box;
            }

            button:hover {
                background-color: #45a049;
            }
            button {
                background-color: #4CAF50;
                color: white;
                border:none;
                padding:10px;
                width: 100%;
                cursor: pointer;
                margin-top: 10px;
            }
            .alert {
                padding: 10px;
                margin: 10px 0;
                border-radius: 5px;
                max-width: 300px;
                margin: 10px auto;
            }
            .alert-danger {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }
        </style>
    </head>
    <body>
    <main>
        <h1 style="text-align: center;">Définir un nouveau mot de passe</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="reset_password.php">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
            
            <div class="form-group">
                <label for="new_password">Nouveau mot de passe:</label>
                <input type="password" id="new_password" name="new_password" required minlength="8" class="form-control">
                <small class="form-text text-muted">Le mot de passe doit contenir au moins 8 caractères.</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe:</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary">Réinitialiser le mot de passe</button>
        </form>
    </main>
    <footer style="margin-top: 20%; color: black; text-align: center;">
        <p>© 2025 MonSite - Tous droits réservés.</p>
    </footer>
    </body>
    </html>
    <?php
    exit();
}

// Affichage du formulaire de demande de réinitialisation
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
    <!-- Ajout du Content-Security-Policy -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; object-src 'none';">
    <link rel="stylesheet" href="cssreset_password.css">
</head>
<body>
<main>
    <h1 style="text-align: center;">Réinitialiser le mot de passe</h1>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="reset_password.php">
        <div class="form-group">
            <label for="email">Entrez votre email :</label>
            <input type="email" id="email" name="email" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Demander une réinitialisation</button>
        <p class="retour">
        <a href="login.php">Retour à la page de connexion</a>
        </p>
    </form>
</main>

<footer style="margin-top: 20%; color: black; text-align: center;">
    <p>© 2025 MonSite - Tous droits réservés.</p>
</footer>
</body>
</html>