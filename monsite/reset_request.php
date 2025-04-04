<?php
// reset_request.php - Formulaire pour demander une réinitialisation
session_start();
include 'includes/db.php';

// Si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Vérifier si l'email existe dans la base de données
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Générer un token unique
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expiration après 1 heure
        
        // Mettre à jour la base de données
        $update_sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sss", $token, $expiry, $email);
        $update_stmt->execute();
        
        // Préparer l'email
        $reset_link = "https://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
        $subject = "Réinitialisation de votre mot de passe";
        $message = "Bonjour,\n\n";
        $message .= "Vous avez demandé une réinitialisation de mot de passe. Veuillez cliquer sur le lien suivant pour réinitialiser votre mot de passe :\n\n";
        $message .= $reset_link . "\n\n";
        $message .= "Ce lien expirera dans 1 heure.\n\n";
        $message .= "Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.\n\n";
        $message .= "Cordialement,\nL'équipe de MonSite";
        $headers = "From: noreply@monsite.com";
        
        // Envoyer l'email
        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['message'] = "Un email de réinitialisation a été envoyé à votre adresse email.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Impossible d'envoyer l'email de réinitialisation.";
        }
    } else {
        // Ne pas révéler si l'email existe ou non pour des raisons de sécurité
        $_SESSION['message'] = "Si cette adresse email est associée à un compte, un lien de réinitialisation a été envoyé.";
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de mot de passe</title>
    <style>
        .reset-form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 300px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type=email] {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        body {
            font-family: arial, sans-serif;
            background: rgb(165,165,165);
            background: radial-gradient(circle, rgba(165,165,165,1) 0%, rgba(251,169,63,1) 100%);
        }
    </style>
</head>
<body>
    <main>
        <h1 style="text-align: center;">Réinitialisation de mot de passe</h1>
        <?php if (isset($error)): ?>
            <p style="color: red; text-align: center;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post" class="reset-form">
            <p>Entrez votre adresse email pour recevoir un lien de réinitialisation de mot de passe.</p>
            <label>Email :</label>
            <input type="email" name="email" required><br><br>
            <button type="submit">Envoyer le lien de réinitialisation</button>
        </form>
        <p style="text-align: center;"><a href="login.php">Retour à la connexion</a></p>
    </main>
</body>
</html>