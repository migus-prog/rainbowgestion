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

// Initialisation des messages d'erreur
$messages = [];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom_complet = trim($_POST['nom_complet']);
    $fonction = trim($_POST['fonction']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $mot_passe = $_POST['mot_passe'];
    $date_embauche = $_POST['date_embauche'];

    // Validation des champs
    if (empty($nom_complet)) {
        $messages[] = "Le nom complet est requis.";
    }
    if (empty($fonction)) {
        $messages[] = "La fonction est requise.";
    }
    if (empty($email)) {
        $messages[] = "L'email est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messages[] = "L'email est invalide.";
    }
    if (empty($mot_passe)) {
        $messages[] = "Le mot de passe est requis.";
    }
    if (empty($messages)) {
        // Hachage du mot de passe
        $mot_passe_hache = password_hash($mot_passe, PASSWORD_DEFAULT);

        // Préparation de la requête SQL
        $sql = "INSERT INTO employes (nom_complet, fonction, email, telephone, mot_passe, date_embauche) 
                VALUES (:nom_complet, :fonction, :email, :telephone, :mot_passe, :date_embauche)";
        
        $stmt = $pdo->prepare($sql);

        try {
            // Exécution de la requête
            $stmt->execute([
                ':nom_complet' => $nom_complet,
                ':fonction' => $fonction,
                ':email' => $email,
                ':telephone' => $telephone ?: null,  // Téléphone peut être NULL
                ':mot_passe' => $mot_passe_hache,
                ':date_embauche' => $date_embauche ?: null  // Date d'embauche peut être NULL
            ]);
            $messages[] = "Employé ajouté avec succès.";
        } catch (PDOException $e) {
            $messages[] = "Erreur lors de l'ajout de l'employé : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Employé</title>
    <style>
        /* Styles simples pour le formulaire */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .container {
            width: 500px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #009579;
        }
        label, input, button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            font-size: 16px;
        }
        input, button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #009579;
            color: #fff;
            cursor: pointer;
        }
        button:hover {
            background-color: #007d64;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajouter un Employé</h1>

        <?php if (!empty($messages)) : ?>
            <div class="error">
                <?php foreach ($messages as $message) : ?>
                    <p><?php echo htmlspecialchars($message); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <label for="nom_complet">Nom Complet</label>
            <input type="text" id="nom_complet" name="nom_complet" required>

            <label for="fonction">Fonction</label>
            <input type="text" id="fonction" name="fonction" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="telephone">Téléphone</label>
            <input type="text" id="telephone" name="telephone">

            <label for="mot_passe">Mot de Passe</label>
            <input type="password" id="mot_passe" name="mot_passe" required>

            <label for="date_embauche">Date d'Embauche</label>
            <input type="date" id="date_embauche" name="date_embauche">

            <button type="submit">Ajouter</button>
        </form>
    </div>
</body>
</html>
