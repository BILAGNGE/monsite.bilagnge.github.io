<?php
// Vérifier si la connexion à la base de données est déjà établie
if (!isset($conn)) {
    // Si non, inclure le fichier de connexion
    include_once 'includes/db.php';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Mon site</title>
    <style>
        /* Styles généraux */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
        }
        
        header {
            padding: 15px;
        }
        
        /* Style pour la navigation */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        nav ul {
            display: flex;
            align-items: center;
            list-style-type: none;
            padding: 0;
            flex-wrap: wrap;
        }
        
        nav ul li {
            margin: 5px 10px;
        }
        
        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        nav a:hover {
            color: #ff6c00;
        }
        
        /* Style pour les images de profil */
        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        
        .profile-pic-large {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        
        .user-nav {
            display: flex;
            align-items: center;
        }
        
        /* Icônes sociales */
        .social-icons-vertical {
            position: fixed;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 1000;
        }
        
        .social-icons-vertical a {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f5f5f5;
            color: #f00;
            transition: all 0.3s ease;
        }
        
        .social-icons-vertical a:hover {
            background-color: #333;
            color: #ff6c00;
        }
        
        /* Menu hamburger */
        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
        }
        
        .menu-toggle span {
            height: 3px;
            width: 25px;
            background-color: #333;
            margin: 2px 0;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        /* Media queries pour la responsivité */
        @media screen and (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }
            
            nav ul {
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background-color: #f8f8f8;
                flex-direction: column;
                align-items: flex-start;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                z-index: 999;
            }
            
            nav ul.active {
                max-height: 500px;
            }
            
            nav ul li {
                width: 100%;
                margin: 0;
                padding: 15px;
                border-bottom: 1px solid #e1e1e1;
            }
            
            .social-icons-vertical {
                display: none;
            }
            
            /* Version horizontale pour mobile */
            .social-icons-horizontal {
                display: flex;
                justify-content: center;
                gap: 15px;
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul id="navMenu">
                <?php if(isset($_SESSION['username']) && isset($conn)):
                    // Récupérer la photo de profil
                    $username = $_SESSION['username'];
                    $query = "SELECT profile_picture FROM users WHERE username = '$username'";
                    $result = $conn->query($query);
                    if ($result) {
                        $user_data = $result->fetch_assoc();
                        $profile_pic = $user_data['profile_picture'] ?? '';
                    } else {
                        $profile_pic = '';
                    }
                ?>
                <li class="user-nav">
                    <?php if(!empty($profile_pic)): ?>
                        <img src="galerie_profile/<?php echo $profile_pic; ?>" alt="Photo de profil" class="profile-pic">
                        <span><?php echo $username; ?></span>
                    <?php else: ?>
                        <img src="galerie_profile/default.png" alt="Photo par défaut" class="profile-pic">
                        <span><?php echo $username; ?></span>
                    <?php endif; ?>
                </li>
                <?php endif; ?>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="users_list.php">Liste des utilisateurs</a></li>
                <li><a href="contact.php">Formulaire de contact</a></li>
                <li><a href="logout.php">Se déconnecter</a></li>
            </ul>
        </nav>
    </header>

    <!-- Script pour le menu responsive -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');
            
            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                });
            }
        });
    </script>