<?php
session_start();

// Supprimer toutes les variables de session
session_unset();

// Détruire la session
session_destroy();

// Rediriger l'utilisateur vers la page d'accueil ou une autre page
header("Location: index.php"); // Remplacez 'index.php' par le nom de votre page d'accueil ou d'autres pages
exit();
?>
