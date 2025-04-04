<?php
session_start();
include 'includes/db.php';

// Génération du token CSRF si non défini
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Erreur CSRF !";
    } else {
        // Récupération et validation des données
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($username) || empty($email) || empty($password)) {
            $error_message = "Tous les champs sont obligatoires.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Format d'email invalide !";
        } elseif (strlen($password) < 6) {
            $error_message = "Le mot de passe doit contenir au moins 6 caractères !";
        } else {
            // Vérifier si l'utilisateur existe déjà
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error_message = "Le nom d'utilisateur ou l'email est déjà utilisé.";
            } else {
                $stmt->close();

                // Hashage sécurisé du mot de passe
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insérer l'utilisateur
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $hashed_password);

                if ($stmt->execute()) {
                    $success_message = "Compte créé avec succès! <a href='login.php'>Se connecter</a>";
                } else {
                    $error_message = "Erreur lors de la création du compte.";
                }

                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte</title>
    <!-- Ajout du Content-Security-Policy -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; object-src 'none';">
    <link rel="stylesheet" href="csssignup.css">
</head>
<body>
<main>
    <h1 class="cc">Créer un compte</h1>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php else: ?>
        <form action="signup.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" required minlength="3" maxlength="20">
            
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required minlength="6">
            
            <button type="submit">Créer un compte</button>
            
            <p class="sc">
                <a href="login.php">Déjà inscrit ? Se connecter</a>
            </p>
        </form>
    <?php endif; ?>
</main>

<footer style="margin-top: 20%;">
    <p style="margin-top: -7%;">© 2025 MonSite - Tous droits réservés.</p>
</footer>
</body>
</html>