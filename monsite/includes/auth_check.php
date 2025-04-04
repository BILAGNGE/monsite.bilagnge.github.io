<?php
// Créer un fichier auth_check.php dans le dossier includes
// Ce fichier vérifiera si l'utilisateur est connecté

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>