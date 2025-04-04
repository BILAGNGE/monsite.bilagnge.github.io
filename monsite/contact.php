<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Génération du token CSRF si non défini
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Erreur CSRF !");
    }

    // Vérification anti-spam (honeypot)
    if (!empty($_POST['botcheck'])) {
        die("Erreur : tentative de spam détectée !");
    }

    // Récupération et validation des données
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars($_POST['message']);

    if (!$email) {
        die("Email invalide !");
    }

    if (empty($name) || empty($message)) {
        die("Tous les champs sont obligatoires.");
    }

    echo "Message envoyé ! Nous vous contacterons bientôt.";
}
?>

<?php include 'includes/header.php'; ?>
<main style="margin-bottom: -5%;">
    <h1 style="text-align: center;">Contactez-nous</h1>
    <form method="POST" action="contact.php">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <label for="name">Votre nom :</label>
        <input type="text" name="name" required>
        
        <label for="email">Votre email :</label>
        <input type="email" name="email" required>
        
        <label for="message">Votre message :</label>
        <textarea name="message" required></textarea>
        
        <!-- Champ anti-spam (honeypot) -->
        <input type="text" name="botcheck" style="display:none;">
        
        <button type="submit">Envoyer</button>
    </form>
</main>

<style>
input, textarea {
    width: 100%;
    padding: 8px;
    margin: 6px 0;
    box-sizing: border-box;
    border: solid 1px #ccc;
    border-radius: 5px;
}

button {
    width: 100%;
    padding: 10px;
    background-color:rgb(4, 212, 21);
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

button:hover {
    background-color:rgb(117, 154, 112);
}
</style>

<footer style="margin-top: 20%;">
    <p style="margin-top: -11%;">© 2025 MonSite - Tous droits réservés.</p>
</footer>