<?php
session_start(); // Toujours démarrer la session en premier

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    exit;
}

$user_id = $_SESSION['user_id']; // Récupérer l'ID de l'utilisateur connecté

$host = 'localhost';
$dbname = 'rainbow_2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour obtenir les demandes de congé de l'utilisateur connecté
    $stmtConges = $pdo->prepare("SELECT type_conge, date_debut, date_fin, statut FROM demandes_conge WHERE employe_id = :user_id");
    $stmtConges->bindParam(':user_id', $user_id);
    $stmtConges->execute();
    $demandeConges = $stmtConges->fetchAll(PDO::FETCH_ASSOC);

    // Requête pour obtenir les demandes de permission de l'utilisateur connecté
    $stmtPermissions = $pdo->prepare("SELECT type_permission, date_debut, date_fin, statut FROM permissions WHERE employe_id = :user_id");
    $stmtPermissions->bindParam(':user_id', $user_id);
    $stmtPermissions->execute();
    $demandePermissions = $stmtPermissions->fetchAll(PDO::FETCH_ASSOC);

    // Requête pour obtenir les décharges de budget de l'utilisateur connecté
    $stmtDecharges = $pdo->prepare("SELECT nom_complet, somme, motif, lieu, date, date_creation, statut FROM decharges_budget WHERE employe_id = :user_id");
    $stmtDecharges->bindParam(':user_id', $user_id);
    $stmtDecharges->execute();
    $decharges = $stmtDecharges->fetchAll(PDO::FETCH_ASSOC);

    // Requête pour obtenir les ordres de mission de l'utilisateur connecté
    $stmtMissions = $pdo->prepare("SELECT accompagne_de, se_rend_a, moyen_transport, date_depart, date_retour, date_ordre , statut FROM ordres_mission WHERE employe_id = :user_id");
    $stmtMissions->bindParam(':user_id', $user_id);
    $stmtMissions->execute();
    $missions = $stmtMissions->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Demandes</title>
    <style>
        /* CSS */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { min-height: 100vh; background: #f4f4f4; display: flex; justify-content: center; align-items: center; }
        .container { width: 800px; background: #fff; border-radius: 7px; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3); padding: 40px; text-align: center; }
        h1 { color: #009579; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        table th { background-color: #009579; color: white; }
        table tr:nth-child(even) { background-color: #f4f4f4; }
        .status { font-weight: bold; }
        .status.en_attente { color: orange; }
        .status.approuve { color: green; }
        .status.rejete { color: red; }
        button { padding: 5px 10px; color: #fff; background: #009579; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        button:hover { background: #006653; }
        footer { margin-top: 20px; font-size: 14px; color: #666; }
    </style>
</head>
<body>
    <div class="container">

        <!-- Historique des Demandes de Congé -->
        <h1>Historique des Demandes de Congé</h1>
        <table>
            <thead>
                <tr>
                    <th>Type de Congé</th>
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($demandeConges)) : ?>
                    <?php foreach ($demandeConges as $demande): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($demande['type_conge']); ?></td>
                            <td><?php echo htmlspecialchars($demande['date_debut']); ?></td>
                            <td><?php echo htmlspecialchars($demande['date_fin']); ?></td>
                            <td><span class="status <?php echo strtolower(str_replace(' ', '_', $demande['statut'])); ?>"><?php echo ucfirst($demande['statut']); ?></span></td>
                            <td><button>Voir Détails</button></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="5">Aucune demande de congé trouvée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Historique des Demandes de Permission -->
        <h1>Historique des Demandes de Permission</h1>
        <table>
            <thead>
                <tr>
                    <th>Type de Permission</th>
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($demandePermissions)) : ?>
                    <?php foreach ($demandePermissions as $demande): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($demande['type_permission']); ?></td>
                            <td><?php echo htmlspecialchars($demande['date_debut']); ?></td>
                            <td><?php echo htmlspecialchars($demande['date_fin']); ?></td>
                            <td><span class="status <?php echo strtolower(str_replace(' ', '_', $demande['statut'])); ?>"><?php echo ucfirst($demande['statut']); ?></span></td>
                            <td><button>Voir Détails</button></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="5">Aucune demande de permission trouvée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Historique des Décharges de Budget -->
        <h1>Historique des Décharges de Budget</h1>
        <table>
            <thead>
                <tr>
                    <th>Nom Complet</th>
                    <th>Somme</th>
                    <th>Motif</th>
                    <th>Lieu</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Date de Création</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($decharges)) : ?>
                    <?php foreach ($decharges as $decharge): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($decharge['nom_complet']); ?></td>
                            <td><?php echo number_format($decharge['somme'], 0, ',', ' '); ?> FCFA</td>
                            <td><?php echo htmlspecialchars($decharge['motif']); ?></td>
                            <td><?php echo htmlspecialchars($decharge['lieu']); ?></td>
                            <td><?php echo htmlspecialchars($decharge['date']); ?></td>
                            <td><span class="status <?php echo strtolower(str_replace(' ', '_', $decharge['statut'])); ?>"><?php echo ucfirst($decharge['statut']); ?></span></td>
                            <td><?php echo htmlspecialchars($decharge['date_creation']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="7">Aucune décharge de budget trouvée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Historique des Ordres de Mission -->
        <h1>Historique des Ordres de Mission</h1>
        <table>
            <thead>
                <tr>
                    <th>Destination</th>
                    <th>Accompagné de</th>
                    <th>Moyen de Transport</th>
                    <th>Date de Départ</th>
                    <th>Date de Retour</th>
                    <th>Date de l'Ordre</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($missions)) : ?>
                    <?php foreach ($missions as $mission): ?>
                        <tr>
                            
                            <td><?php echo htmlspecialchars($mission['accompagne_de']); ?></td>
                            <td><?php echo htmlspecialchars($mission['se_rend_a']); ?>    </td>
                            <td><?php echo htmlspecialchars($mission['moyen_transport']); ?></td>
                            <td><?php echo htmlspecialchars($mission['date_depart']); ?></td>
                            <td><?php echo htmlspecialchars($mission['date_retour']); ?></td>
                            <td><?php echo htmlspecialchars($mission['date_ordre']); ?></td>
                            <td><span class="status <?php echo strtolower(str_replace(' ', '_', $mission['statut'])); ?>"><?php echo ucfirst($mission['statut']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="8">Aucun ordre de mission trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <footer>
            © 2024 Gestion des Demandes Rainbow - Tous droits réservés.
        </footer>
    </div>
</body>
</html>
