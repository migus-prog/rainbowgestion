<?php
session_start(); // Démarrer la session pour utiliser les variables $_SESSION

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Rediriger l'utilisateur vers la page de connexion si non connecté
    exit;
}

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

// Initialisation des variables
$nom_complet = $somme = $motif = $lieu = '';
$date = date('Y-m-d'); // Définit la date du jour
$errors = [];

// Traitement du formulaire lors de la soumission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom_complet = $_POST['nom_complet'] ?? '';
    $somme = $_POST['somme'] ?? 0;
    $motif = $_POST['motif'] ?? '';
    $lieu = $_POST['lieu'] ?? '';
    $nom_comptable = $_POST['nom_comptable'] ?? '';
    echo number_format($somme, 0, ',', ' ') . ' FCFA';

    // Ajout de l'ID de l'employé connecté à la table de décharges
    $employe_id = $_SESSION['user_id'];

    // Insertion des données dans la base
    $stmt = $pdo->prepare("INSERT INTO decharges_budget (employe_id, nom_complet, somme, motif, lieu, date, nom_comptable) VALUES (:employe_id, :nom_complet, :somme, :motif, :lieu, :date, :nom_comptable)");

    $stmt->execute([
        ':employe_id' => $employe_id,
        ':nom_complet' => $nom_complet,
        ':somme' => $somme,
        ':motif' => $motif,
        ':lieu' => $lieu,
        ':date' => $date,
        ':nom_comptable' => $nom_comptable,
    ]);

    echo "Décharge enregistrée avec succès.";
    header("Location: main.php");
            exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Décharge</title>
    <style>
        /* Styles du formulaire */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            width: 500px;
            background: #fff;
            border-radius: 7px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
            padding: 40px;
        }
        h1 {
            text-align: center;
            color: #009579;
            margin-bottom: 20px;
        }
        label {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"], input[type="number"], input[type="date"], textarea, input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #009579;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #006653;
        }
        .row {
            display: flex;
            gap: 15px;
        }
        .row input[type="text"], .row input[type="date"] {
            width: 48%;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h1>Décharge</h1>
        
        <form method="POST">
            <label for="nom_complet">Nom complet :</label>
            <input type="text" id="nom_complet" name="nom_complet" value="<?= htmlspecialchars($nom_complet); ?>" required>
            
            <label for="somme">Somme :</label>
            <input type="number" id="somme" name="somme" value="<?= htmlspecialchars($somme); ?>" required>
            
            <label for="motif">Pour (motif) :</label>
            <textarea id="motif" name="motif" rows="4" required><?= htmlspecialchars($motif); ?></textarea>
            
            <label for="lieu">Lieu et Date :</label>
            <div class="row">
                <input type="text" id="lieu" name="lieu" value="<?= htmlspecialchars($lieu); ?>" placeholder="Lieu" required>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($date); ?>" readonly>
            </div>
            
            <input type="submit" value="Soumettre">
        </form>
    </div>

</body>
</html>
