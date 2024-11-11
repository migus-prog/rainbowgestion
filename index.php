<?php
session_start(); // Toujours démarrer la session en premier

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

// Initialisation des messages d'erreur et du token CSRF
$messages = [];

// Génération du token CSRF si nécessaire
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $messages[] = "Erreur de vérification du formulaire. Veuillez réessayer.";
    } else {
        // Validation des champs
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email)) {
            $messages[] = "L'adresse email est requise.";
        }

        if (empty($password)) {
            $messages[] = "Le mot de passe est requis.";
        }

        // Procéder à l'authentification si aucun message d'erreur
        if (empty($messages)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM employes WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['mot_passe'])) {
                    // Création de la session utilisateur avec l'ID
                    $_SESSION['user_id'] = $user['employe_id']; // Enregistrer l'ID de l'utilisateur dans la session
                    $_SESSION['email'] = $user['email']; // Enregistrer l'email dans la session

                    // Redirection en fonction de l'email
                    if ($email === 'aubintchinda@rainbowenvironment.com') {
                        header("Location: dt\dt.php");
                        exit;
                    } elseif ($email === 'fometetim@gmail.com') {
                        header("Location: dg\dg.php");
                        exit;
                    } elseif ($email === 'hugueskombou@rainbowenvironment.com') {
                        header("Location: daf\daf.php");
                        exit;
                    } elseif ($email === 'nzokouericmartial@gmail.com') {
                        header("Location: comptable.php");
                        exit;
                    } elseif ($email === 'miguelgustave@gmail.com') {
                        header("Location: ajout.php");
                        exit;
                    } else {
                        // Redirection par défaut
                        header("Location: main.php");
                    }
                } else {
                    $messages[] = "Adresse email ou mot de passe incorrect.";
                }
            } catch (PDOException $e) {
                $messages[] = "Erreur lors de l'authentification : " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        /* CSS */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        h1 {
            margin: 20px 0;
        }

        body {
            min-height: 100vh;
            width: 100%;
            background: #009579;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 400px;
            background: #fff;
            border-radius: 7px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
        }

        .container .form {
            padding: 30px;
        }

        label {
            padding: 10px 0;
            color: #009579;
            cursor: pointer;
        }

        input {
            width: 100%;
            padding: 10px;
            font-size: 17px;
            border: 1px solid #ddd;
            border-radius: 6px;
            outline: none;
            margin-bottom: 15px;
            margin-top: 10px;
        }

        a {
            font-size: 16px;
            color: #009579;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        button {
            color: #fff;
            background: #009579;
            width: 100%;
            padding: 10px;
            font-size: 17px;
            margin-top: 10px;
            border: none;
            border-radius: 6px;
            outline: none;
        }

        button:hover {
            background: #006653;
        }

        .link {
            text-align: center;
            margin-top: 20px;
        }

        .error {
            background-color: red;
            color: #fff;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form">
            <h1>Connexion</h1>

            <?php if (!empty($messages)) : ?>
            <div class="error-messages">
                <?php foreach ($messages as $message) : ?>
                <p class="error"><?= htmlspecialchars($message) ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="post" action="index.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <label for="email">Adresse mail</label>
                <input type="text" name="email" required>
                <label for="password">Mot de passe</label>
                <input type="password" name="password" required>
                <button type="submit">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>
