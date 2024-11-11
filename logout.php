<?php
session_start();  // Démarrer la session pour pouvoir la détruire
session_unset();  // Supprimer toutes les variables de session
session_destroy(); // Détruire la session
header("Location: index.php"); // Rediriger vers la page de connexion
exit();
?>
