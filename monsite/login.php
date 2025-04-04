<?php
// À placer avant session_start()
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);


session_start(); // Assurez-vous que cette ligne est bien au tout début
include 'includes/db.php'; // Connexion à la base de données

header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Initialiser les variables de messages
$error_message = "";
$debug_info = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Ajouter des informations de débogage (à supprimer en production)
   
    // Préparer la requête pour éviter les injections SQL
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
   
    // Si l'utilisateur existe
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Récupérer les données de l'utilisateur
       
        // Vérification du mot de passe
        if (password_verify($password, $user['password'])) {
            // Mot de passe hashé correct
            $debug_info .= "Mot de passe hashé vérifié avec succès<br>";
            
            // Connexion réussie, définir les variables de session
            $_SESSION['username'] = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
            $_SESSION['user_id'] = $user['id'];
            
            $debug_info .= "Session initialisée: user_id=" . htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8') . "<br>";
            
            // Redirection vers la page d'accueil ou une page protégée
            $debug_info .= "Tentative de redirection vers index.php<br>";
            header("Location: index.php");
            exit();
        }
        // Pour les anciens comptes qui n'utilisent pas de hachage (comme 'admin')
        else if ($password == $user['password']) {
            // Connexion réussie pour les anciens comptes avec mot de passe en texte clair
            $debug_info .= "Mot de passe en texte clair vérifié avec succès (ancien compte)<br>";
            
            // Connexion réussie, définir les variables de session
            $_SESSION['username'] = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
            $_SESSION['user_id'] = $user['id'];

            
            
            $debug_info .= "Session initialisée: user_id=" . htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8') . "<br>";
            
            // Redirection vers la page d'accueil ou une page protégée
            $debug_info .= "Tentative de redirection vers index.php<br>";
            header("Location: index.php");
            exit();
        }
        else {
            // Mot de passe incorrect
            $error_message = "Mot de passe incorrect.";
        }
    } else {
        // L'utilisateur n'existe pas
        $error_message = "Cet utilisateur n'existe pas.";
        $debug_info .= "Utilisateur non trouvé dans la base de données<br>";
    }

    // Après authentification réussie
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];

    // Durée de vie de session de 2 heures
    
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <!-- Ajout du Content-Security-Policy (CSP) -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; object-src 'none';">
    <link rel="stylesheet" href="csslogin.css">
</head>
<body>
    <main>
        <h1 style="text-align: center;">Se connecter</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($debug_info)): ?>
            <div class="debug-info">
                <?php echo $debug_info; // Le contenu est déjà échappé avec htmlspecialchars plus haut ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="post" class="login-form">
            <label>Nom d'utilisateur :</label>
            <input type="text" name="username" required><br><br>
            <label>Mot de passe :</label>
            <input type="password" name="password" required><br><br>
            <button type="submit">Se connecter</button>
            <p class="mot-de-passe-oublie">
                <a href="reset_password.php">Mot de passe oublié ?</a>
            </p>
        </form>
        <p class="pas-encore-inscrit">Pas encore inscrit ? <a href="signup.php">Créer un compte</a></p>
        </main>
    <footer class="footer">
    <p >© 2025 MonSite - Tous droits réservés.</p>
</footer>
</body>
</html>