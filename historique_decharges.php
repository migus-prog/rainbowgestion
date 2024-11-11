<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'rainbow_2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération de l'historique des demandes de décharges (approuvées ou rejetées)
$stmt_historique = $pdo->query("SELECT * FROM decharges_budget WHERE statut != 'en_attente'");
$historique_decharges = $stmt_historique->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Décharges</title>
    <style>
        /* Styles similaires à la page principale */
        body {
            margin: 0;
            font-family: "Nunito", sans-serif;
            font-size: 1rem;
            background-color: #f4f4f9;
            color: #343a40;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100vh;
        }

        h1 {
            margin: 20px;
            color: #3490dc;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #3490dc;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        footer {
            margin-top: auto;
            padding: 10px;
            background-color: #343a40;
            color: white;
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>

<h1>Historique des Décharges</h1>

<!-- Tableau des demandes dans l'historique -->
<table>
    <thead>
        <tr>
            <th>Nom de l'Employé</th>
            <th>Somme</th>
            <th>Motif</th>
            <th>Lieu</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Nom du Comptable</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($historique_decharges as $decharge) : ?>
            <tr>
                <td><?= htmlspecialchars($decharge["nom_complet"]) ?></td>
                <td><?= htmlspecialchars($decharge["somme"]) ?></td>
                <td><?= htmlspecialchars($decharge["motif"]) ?></td>
                <td><?= htmlspecialchars($decharge["lieu"]) ?></td>
                <td><?= htmlspecialchars($decharge["date"]) ?></td>
                <td><?= htmlspecialchars($decharge["statut"]) ?></td>
                <td><?= htmlspecialchars($decharge["nom_comptable"]) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<p>
<div class="button-container">
    <a href="comptable.php">
        <button class="history-button">Retour </button>
    </a>
    <p>
<footer>
    © 2024 Gestion des Décharges - Historique
</footer>

</body>
</html>
