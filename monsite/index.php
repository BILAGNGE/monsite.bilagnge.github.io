<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php'; // Assurez-vous d'inclure la connexion à la base de données avant le header

// Récupérer les liens sociaux de l'utilisateur connecté
$username = $_SESSION['username'];
$sql = "SELECT facebook, twitter, instagram FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$social_links = $result->fetch_assoc();

include 'includes/header.php';

// Code pour le message de bienvenue dynamique
$hour = date('H');
if ($hour < 12) {
    $message = "Bonjour";
} elseif ($hour < 18) {
    $message = "Bon après-midi";
} else {
    $message = "Bonsoir";
}
?>
<div class="bg"></div>



<!-- Icônes sociales verticales -->
<div class="social-icons-vertical">
    <?php if(!empty($social_links['facebook'])): ?>
    <a href="<?php echo $social_links['facebook']; ?>" target="_blank" style="background-color: #1877F2;"><i class="fab fa-facebook-f"></i></a>
    <?php endif; ?>
    
    <?php if(!empty($social_links['twitter'])): ?>
    <a href="<?php echo $social_links['twitter']; ?>" target="_blank" style="background-color: #14171a;"><i class="fab fa-twitter"></i></a>
    <?php endif; ?>
    
    <?php if(!empty($social_links['instagram'])): ?>
    <a href="<?php echo $social_links['instagram']; ?>" target="_blank" style="background-color: white;"><i class="fab fa-instagram"></i></a>
    <?php endif; ?>
</div>

<main style="text-align: center;margin-bottom: -16%;">
    <h2><?php echo $message; ?>, bienvenue sur MonSite!</h2>
    <p>Ceci est la page d'accueil.</p>
    <p>La date et l'heure actuelles sont : <?php echo date('d/m/Y H:i:s'); ?></p>
    <a href="https://www.google.com" target="_blank">Visiter Google</a> <br><br>
    <img src="iimage.jpg" alt="Une belle image" width="300">
</main>
<footer style="margin-top: 20%;">
    <p style="margin-top: 30%;">© 2025 MonSite - Tous droits réservés.</p>
</footer>