<?php 
session_start(); 
include 'includes/db.php'; 

if (!isset($_SESSION['username'])) {     
    header("Location: login.php");     
    exit(); 
} 

// Stocker le résultat dans une variable différente de $result pour éviter les conflits
$users_result = $conn->query("SELECT id, username, email FROM users"); 

// Inclure le header après avoir exécuté notre requête et stocké le résultat
include 'includes/header.php'; 
?> 

<main style="text-align: center;margin-bottom: 25%;">     
    <h1>Liste des utilisateurs</h1>     
    <table style="margin-left: auto; margin-right: auto;">         
        <thead>             
            <tr>                 
                <th>Nom d'utilisateur</th>                 
                <th style="padding-left: 20%;">Email</th>             
            </tr>         
        </thead>         
        <tbody>             
            <?php if($users_result && $users_result->num_rows > 0): ?>
                <?php while ($row = $users_result->fetch_assoc()): ?>
                <tr>                 
                    <td><?php echo htmlspecialchars($row['username']); ?></td>                 
                    <td style="padding-left: 10%;"><?php echo htmlspecialchars($row['email']); ?></td>             
                </tr>             
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">Aucun utilisateur trouvé</td>
                </tr>
            <?php endif; ?>
        </tbody>     
    </table> 
</main> 

<footer style="margin-top: 20%;">
    <p style="margin-top: -7%;">© 2025 MonSite - Tous droits réservés.</p>
</footer>