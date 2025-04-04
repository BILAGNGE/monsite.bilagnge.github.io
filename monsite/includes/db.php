<?php
// db.php : Connexion à la base de données
$servername = "localhost";
$username = "root"; // Nom d'utilisateur de la base de données
$password = ""; // Mot de passe de la base de données
$dbname = "monsite_db"; // Nom de la base de données

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>