<?php
session_start();
include 'includes/db.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
// Récupérer les informations de l'utilisateur
$email = isset($user['email']) ? htmlspecialchars($user['email']) : "Non disponible";
$created_at = isset($user['created_at']) ? htmlspecialchars($user['created_at']) : "Non disponible";

// Nouvelles informations récupérées
$birthday = isset($user['birthday']) ? htmlspecialchars($user['birthday']) : "Non disponible";
$address = isset($user['address']) ? htmlspecialchars($user['address']) : "Non disponible";
$bio = isset($user['bio']) ? htmlspecialchars($user['bio']) : "Non disponible";
$profile_picture = isset($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : "";
?>
<?php include 'includes/header.php'; ?>

<main style="text-align: center;">
    <h1>✨ Profil de <?php echo htmlspecialchars($username); ?> ✨ </h1>
    
    <!-- Affichage de la photo de profil -->
    <?php if(!empty($profile_picture)): ?>
        <img src="galerie_profile/<?php echo $profile_picture; ?>" alt="Photo de profil" class="profile-pic-large">
    <?php else: ?>
        <img src="galerie_profile/default.png" alt="Photo par défaut" class="profile-pic-large">
    <?php endif; ?>
    
    <p><strong>Email :</strong> <?php echo $email; ?></p>
    <p><strong>Date d'inscription :</strong> <?php echo $created_at; ?></p>
    
    <!-- Affichage des nouvelles informations -->
    <p><strong>Date de naissance :</strong> <?php echo $birthday; ?></p>
    <p><strong>Adresse :</strong> <?php echo $address; ?></p>
    <div>
        <strong>Biographie :</strong>
        <p><?php echo $bio; ?></p>
    </div>
    
    <a href="edit_profile.php">Modifier mon profil</a>
</main>
<footer style="margin-top: 20%;">
    <p style="margin-top: -17%;">© 2025 MonSite - Tous droits réservés.</p>
</footer>