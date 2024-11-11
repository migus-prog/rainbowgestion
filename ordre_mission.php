<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: index.php");
    exit();
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'rainbow_2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Initialisation des variables pour éviter les erreurs
$nom = $fonction = $se_rend_a = $accompagne_de = $moyen_transport = $date_depart = $date_retour = $delivre_par = $fonction_delivreur = '';
$errors = [];

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $nom = $_POST['nom'] ?? '';
    $fonction = $_POST['fonction'] ?? '';
    $se_rend_a = $_POST['se_rend_a'] ?? '';
    $accompagne_de = $_POST['accompagne_de'] ?? '';
    $moyen_transport = $_POST['moyen_transport'] ?? '';
    $date_depart = $_POST['date_depart'] ?? '';
    $date_retour = $_POST['date_retour'] ?? '';

    // Insertion des données dans la base de données
    $stmt = $pdo->prepare("INSERT INTO ordres_mission (employe_id, se_rend_a, accompagne_de, moyen_transport, date_depart, date_retour) 
                            VALUES (:employe_id, :se_rend_a, :accompagne_de, :moyen_transport, :date_depart, :date_retour)");
    
    $stmt->execute([
        ':employe_id' => $_SESSION['user_id'], // Récupère l'ID de l'utilisateur connecté
        ':se_rend_a' => $se_rend_a,
        ':accompagne_de' => $accompagne_de,
        ':moyen_transport' => $moyen_transport,
        ':date_depart' => $date_depart,
        ':date_retour' => $date_retour,

    ]);

    echo "Mission enregistrée avec succès.";
    header("Location: main.php"); // Rediriger l'utilisateur vers la page de connexion si non connecté
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordre de Mission</title>
    <style>
        /* Style simple pour le formulaire */
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

        input[type="text"], input[type="date"], input[type="submit"] {
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

        .dates-container {
            display: flex;
            gap: 15px;
        }

        .dates-container input[type="date"] {
            width: 48%;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h1>Ordre de Mission</h1>
        
        <form method="POST">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
            
            <label for="fonction">Fonction :</label>
            <input type="text" id="fonction" name="fonction" value="<?php echo htmlspecialchars($fonction); ?>" required>
            
            <label for="se_rend_a">Se rend à :</label>
            <input type="text" id="se_rend_a" name="se_rend_a" value="<?php echo htmlspecialchars($se_rend_a); ?>" required>
            
            <label for="accompagne_de">Accompagné de :</label>
            <input type="text" id="accompagne_de" name="accompagne_de" value="<?php echo htmlspecialchars($accompagne_de); ?>" required>
            
            <label for="moyen_transport">Moyen de transport :</label>
            <input type="text" id="moyen_transport" name="moyen_transport" value="<?php echo htmlspecialchars($moyen_transport); ?>" required>
            
            <label for="date_depart">Date de départ et retour :</label>
            <div class="dates-container">
                <input type="date" id="date_depart" name="date_depart" value="<?php echo htmlspecialchars($date_depart); ?>" required>
                <input type="date" id="date_retour" name="date_retour" value="<?php echo htmlspecialchars($date_retour); ?>" required>
            </div>
            <input type="submit" value="Soumettre">
        </form>
    </div>
    <footer>
        © 2024 Ordre de Mission Rainbow - Tous droits réservés.
    </footer>
</body>
</html>
