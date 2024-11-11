<?php
session_start(); // Toujours démarrer la session

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$employe_id = $_SESSION['user_id']; // Récupérer l'employe_id à partir de la session

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

// Initialisation des messages d'erreur et de succès
$errorMessage = [];
$successMessage = "";
$type = isset($_POST['type']) ? $_POST['type'] : '';

// Traitement du formulaire lorsqu'il est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = trim($_POST['type'] ?? '');
    $debut = $_POST['debut'] ?? '';
    $fin = $_POST['fin'] ?? '';
    $commentaire = trim($_POST['commentaire'] ?? '');

    // Vérifier les champs obligatoires
    if (empty($type)) {
        $errorMessage[] = "Le type de permission est obligatoire.";
    }
    if (empty($debut)) {
        $errorMessage[] = "La date de début est obligatoire.";
    }
    if (empty($fin)) {
        $errorMessage[] = "La date de fin est obligatoire.";
    }
    if (!empty($debut) && !empty($fin) && $fin < $debut) {
        $errorMessage[] = "La date de fin ne peut pas être avant la date de début.";
    }

    // Si aucune erreur, insérer les données dans la base
    if (empty($errorMessage)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO permissions (employe_id, type_permission, date_debut, date_fin, commentaire) VALUES (:employe_id, :type, :debut, :fin, :commentaire)");
            $stmt->execute([
                ':employe_id' => $employe_id,
                ':type' => $type,
                ':debut' => $debut,
                ':fin' => $fin,
                ':commentaire' => $commentaire,
            ]);

            $successMessage = "Votre demande a été envoyée avec succès.";
            header("Location: main.php");
            exit;
        } catch (PDOException $e) {
            $errorMessage[] = "Erreur lors de l'insertion : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demander une Permission</title>
    <style>
        /* CSS */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { min-height: 100vh; background: #f4f4f4; display: flex; justify-content: center; align-items: center; }
        .container { width: 500px; background: #fff; border-radius: 7px; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3); padding: 40px; }
        h1 { color: #009579; text-align: center; margin-bottom: 30px; }
        label { font-size: 16px; color: #333; display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 10px; margin-bottom: 20px; font-size: 16px; border: 1px solid #ddd; border-radius: 6px; }
        button { width: 100%; padding: 10px; font-size: 18px; color: #fff; background: #009579; border: none; border-radius: 6px; cursor: pointer; }
        button:hover { background: #006653; }
        .error { background-color: red; color: white; padding: 10px; border-radius: 6px; margin-bottom: 10px; text-align: center; }
        .success { background-color: green; color: white; padding: 10px; border-radius: 6px; margin-bottom: 10px; text-align: center; display: <?php echo $successMessage ? 'block' : 'none'; ?>; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Demander une Permission</h1>

        <!-- Affichage des erreurs -->
        <?php if (!empty($errorMessage)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errorMessage as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Affichage du message de succès -->
        <div class="success"><?php echo htmlspecialchars($successMessage); ?></div>

        <form method="POST" action="">
            <label for="type">Type de Permission</label>
            <select id="type" name="type" required>
                <option value="">Sélectionner un type</option>
                <option value="maladie" <?php echo ($type == 'maladie') ? 'selected' : ''; ?>>Maladie</option>
                <option value="deces" <?php echo ($type == 'deces') ? 'selected' : ''; ?>>Décès</option>
                <option value="formations" <?php echo ($type == 'formations') ? 'selected' : ''; ?>>Formations</option>
                <option value="missions_etranger" <?php echo ($type == 'missions_etranger') ? 'selected' : ''; ?>>Missions à l'étranger</option>
                <option value="reunion_familiale" <?php echo ($type == 'reunion_familiale') ? 'selected' : ''; ?>>Réunion familiale</option>
                <option value="ceremonies_familiales" <?php echo ($type == 'ceremonies_familiales') ? 'selected' : ''; ?>>Cérémonies familiales</option>
            </select>

            <label for="debut">Date de Début</label>
            <input type="date" id="debut" name="debut" value="<?php echo htmlspecialchars($debut ?? ''); ?>" required>

            <label for="fin">Date de Fin</label>
            <input type="date" id="fin" name="fin" value="<?php echo htmlspecialchars($fin ?? ''); ?>" required>

            <label for="commentaire">Commentaires (optionnel)</label>
            <textarea id="commentaire" name="commentaire" rows="4" placeholder="Ajoutez un commentaire ici..."><?php echo htmlspecialchars($commentaire ?? ''); ?></textarea>

            <button type="submit">Envoyer la Demande</button>
        </form>
    </div>
</body>
</html>
