<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'rainbow_2';  // Remplacez par le nom de votre base de données
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'accueil - Demande de Congé et Permissions</title>
    <style>
        /* CSS Global */
        body {
            margin: 0;
            font-family: "Nunito", sans-serif;
            font-size: 0.9rem;
            font-weight: 400;
            line-height: 1.6;
            color: #212529;
            background-color: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100vh;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        :root {
            --primary: #3490dc;
            --secondary: #6c757d;
            --success: #38c172;
            --info: #6cb2eb;
            --warning: #ffed4a;
            --danger: #e3342f;
            --light: #f8f9fa;
            --dark: #343a40;
            --font-family-sans-serif: "Nunito", sans-serif;
        }

        html {
            font-family: sans-serif;
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
        }

        /* Espace pour l'image */
        .image-container {
            width: 100%;
            height: 150px; /* Taille réduite de l'image */
            background-color: #eee; /* Arrière-plan en attendant l'image */
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 10px 0;
        }

        .image-container img {
            max-width: 50%; /* Taille réduite de l'image */
            max-height: 100%;
            object-fit: contain;
        }

        /* Barre de navigation avec les menus et bouton de connexion */
        .navbar {
            width: 100%;
            background: var(--indigo);
            padding: 5px; /* Réduction de la taille des éléments du menu */
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .navbar a, .navbar .dropdown {
            color: var(--light);
            text-decoration: none;
            padding: 8px 15px; /* Taille réduite pour s'adapter au texte */
            font-size: 14px; /* Taille de police plus petite */
            background: var(--primary);
            border-radius: 4px; /* Bords plus arrondis */
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease; /* Ajout de transition */
        }

        .navbar a:hover, .navbar .dropdown:hover, .navbar .login-btn:hover {
            background: var(--dark);
            transform: translateY(-5px); /* Animation de soulèvement au survol */
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: var(--light);
            color: var(--dark);
            min-width: 160px; /* Réduction de la largeur */
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
            z-index: 1;
            animation: fadeIn 0.5s ease-in-out; /* Animation d'apparition */
        }

        .dropdown-content a {
            padding: 8px 12px; /* Ajustement du padding pour s'adapter au texte */
            display: block;
            text-decoration: none;
            color: var(--dark);
        }

        .dropdown-content a:hover {
            background-color: var(--secondary);
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Conteneur principal */
        .container {
            width: 500px; /* Taille légèrement réduite */
            background: var(--light);
            border-radius: 7px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
            padding: 30px; /* Réduction du padding */
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: fadeIn 2s ease-in-out;
            margin-top: 20px; /* Espace supplémentaire entre les boutons et le message */
        }

        h1 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 2rem; /* Agrandissement du message */
        }

        p {
            font-size: 18px; /* Taille de police légèrement réduite */
            color: var(--dark);
            margin-bottom: 20px;
        }

        footer {
            margin-top: 20px;
            font-size: 14px;
            color: var(--secondary);
        }

        /* Animation fade-in */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <!-- Espace pour une image -->
    <div class="image-container">
        <!-- Remplacez l'URL par celle de votre image -->
        <img src="rainbow.png" alt="Image d'illustration">
    </div>

    <!-- Barre de navigation avec les menus et bouton de connexion -->
    <div class="navbar">
        <!-- Menu déroulant pour les demandes -->
        <div class="dropdown">
            Demandes
            <div class="dropdown-content">
                <a href="demande.php">Demander un Congé</a>
                <a href="demande_p.php">Demander une Permission</a>
                <a href="historique.php">Historique des Demandes</a>
            </div>
        </div>

        <!-- Menu déroulant pour Décharges et Ordres de missions -->
        <div class="dropdown">
            Autres Options
            <div class="dropdown-content">
                <a href="decharge.php">Décharges Budget</a>
                <a href="ordre_mission.php">Ordres de Missions</a>
            </div>
        </div>

        <!-- Bouton de connexion -->
        <a href="logout.php" class="signout-btn">Deconnexion</a>
        
    </div>

    <!-- Contenu principal animé et centré -->
    <div class="container">
        <h1>Bienvenue dans l'application de gestion de congé et de demande de permissions</h1>
        <p>Vous avez <strong>18 jours</strong> de congés restants pour cette année.</p>
    </div>

    <footer>
        © 2024 Gestion des Congés Rainbow - Tous droits réservés.
    </footer>

</body>
</html>
