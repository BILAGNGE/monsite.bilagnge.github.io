<?php
session_start();
include 'includes/db.php';
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
// Récupérer les informations actuelles de l'utilisateur
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Mettre à jour les informations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    // Nouvelles informations supplémentaires
    $birthday = $_POST['birthday'];
    $address = htmlspecialchars($_POST['address']);
    $bio = htmlspecialchars($_POST['bio']);
    $facebook = htmlspecialchars($_POST['facebook']);
    $twitter = htmlspecialchars($_POST['twitter']);
    $instagram = htmlspecialchars($_POST['instagram']);
    
    // Traitement de l'upload de la photo de profil
    $profile_picture = $user['profile_picture']; // Valeur par défaut (garder l'ancienne photo)
    
    if(isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        // Vérifier si le répertoire existe, sinon le créer
        $target_dir = "galerie_profile/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Générer un nom de fichier unique pour éviter les écrasements
        $file_extension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $new_filename = $username . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Vérifier que c'est bien une image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if($check !== false) {
            // Déplacer le fichier uploadé vers le répertoire cible
            if(move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $new_filename;
            } else {
                echo "<p class='error'>Désolé, une erreur est survenue lors de l'upload de votre photo.</p>";
            }
        } else {
            echo "<p class='error'>Le fichier n'est pas une image valide.</p>";
        }
    }
    
    // Mettre à jour toutes les informations dans la base de données incluant les réseaux sociaux
    $sql = "UPDATE users SET username = '$new_username', email = '$new_email', 
            birthday = '$birthday', address = '$address', bio = '$bio',
            profile_picture = '$profile_picture', facebook = '$facebook',
            twitter = '$twitter', instagram = '$instagram' 
            WHERE username = '$username'";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['username'] = $new_username;
        echo "<p class='success'>Profil mis à jour avec succès!</p>";
    } else {
        echo "<p class='error'>Erreur : " . $conn->error . "</p>";
    }
}
?>
<?php include 'includes/header.php'; ?>
<main style="margin-bottom: -5%;">
    <h1 style="text-align: center;">Modifier mon profil</h1>
    <form method="POST" action="edit_profile.php" enctype="multipart/form-data">
        <label for="username">Nouveau nom d'utilisateur :</label>
        <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
        
        <label for="email">Nouvel email :</label>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
        
        <!-- Nouveaux champs ajoutés -->
        <label for="birthday">Date de naissance :</label>
        <input type="date" name="birthday" id="birthday" value="<?php echo $user['birthday']; ?>">
        
        <label for="address">Adresse :</label>
        <input type="text" name="address" id="address" value="<?php echo $user['address']; ?>">
        
        <label for="bio">Biographie :</label> <br>
        <textarea name="bio" id="bio"><?php echo $user['bio']; ?></textarea>
        
        <br> <br>
        <!-- Ajout du champ pour l'upload de photo -->
        <label for="profile_picture">Photo de profil :</label>
        <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
        <?php if(!empty($user['profile_picture'])): ?>
            <div class="current-photo">
                <p>Photo actuelle :</p>
                <img src="galerie_profile/<?php echo $user['profile_picture']; ?>" alt="Photo de profil" class="profile-pic">
            </div>
        <?php endif; ?>
        
        <!-- Après la section de la photo de profil, ajoutez ceci -->
        <label for="facebook">Lien Facebook :</label>
        <input type="url" name="facebook" id="facebook" value="<?php echo $user['facebook']; ?>">

        <label for="twitter">Lien Twitter :</label>
        <input type="url" name="twitter" id="twitter" value="<?php echo $user['twitter']; ?>">

        <label for="instagram">Lien Instagram :</label>
        <input type="url" name="instagram" id="instagram" value="<?php echo $user['instagram']; ?>">

        <button type="submit">Mettre à jour</button>
    </form>
</main>
<footer style="margin-top: 20%;">
    <p style="margin-top: -15%;">© 2025 MonSite - Tous droits réservés.</p>
</footer>