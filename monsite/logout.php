<?php
session_start();
session_unset(); // Supprimer toutes les variables de session
session_destroy(); // Détruire la session
header("Location: login.php"); // Rediriger vers la page d'accueil après la déconnexion
exit();

?>