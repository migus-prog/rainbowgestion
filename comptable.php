<?php
// Importation des bibliothèques nécessaires
require 'dg/fpdf.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Nécessaire pour PHPMailer si installé via Composer

$host = 'localhost';
$dbname = 'rainbow_2';
$username = 'root';
$password = '';

// Définir la langue en français pour strftime
setlocale(LC_TIME, 'fr_FR.UTF-8');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction pour générer un PDF pour les décharges de budgets
function genererPDFDechargeBudget($data) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Image('rainbow.png', 10, 10, 0, 24);
    $pdf->SetXY(50, 10);

    // Contenu du PDF ici
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->Cell(0, 10, 'DECHARGE DE BUDGET', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, 'Nom :', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $data['nom_complet'], 0, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, 'Montant :', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $data['somme'], 0, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, 'Motif :', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, $data['motif'], 0, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, 'Date de la decharge :', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $data['date'], 0, 1);

    $nomFichier = "decharge_budget_" . $data['nom_complet'] . ".pdf";
    $pdf->Output("F", $nomFichier);
    return $nomFichier;
}

// Fonction pour envoyer un e-mail avec pièce jointe
function envoyerEmailAvecPieceJointe($destinataire, $sujet, $message, $fichier) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'miguelgustave@gmail.com'; // Votre email SMTP
        $mail->Password   = 'Ntsama@21'; // Mot de passe SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Destinataires
        $mail->setFrom('miguelgustave@gmail.com', 'Nom de l\'expéditeur');
        $mail->addAddress($destinataire);

        // Contenu de l'e-mail
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $message;

        // Pièce jointe
        $mail->addAttachment($fichier);

        $mail->send();
        echo "L'email a été envoyé avec succès.";
    } catch (Exception $e) {
        echo "L'envoi de l'email a échoué. Erreur : {$mail->ErrorInfo}";
    }
}

// Gestion de la demande pour l'approbation ou le rejet de décharge de budget
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) ($_POST['id'] ?? -1);
    $action = $_POST['action'] ?? '';
    $motif_rejet = $_POST['motif_rejet'] ?? null;

    if ($id > 0) {
        if ($action === 'Approuver') {
            // Récupérer les données pour le PDF avec un JOIN pour obtenir l'email de l'employé
            $stmt = $pdo->prepare("
                SELECT decharges_budget.*, employes.email
                FROM decharges_budget
                JOIN employes ON decharges_budget.employe_id = employes.id
                WHERE decharges_budget.id = :id
            ");
            $stmt->execute(['id' => $id]);
            $demande = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($demande) {
                // Mettre à jour le statut de la demande en 'approuvé'
                $stmt = $pdo->prepare("UPDATE decharges_budget SET statut = 'approuvé' WHERE id = :id");
                $stmt->execute(['id' => $id]);

                // Générer le PDF
                $nomFichierPDF = genererPDFDechargeBudget($demande);

                // Envoi de l'email avec le PDF en pièce jointe
                $emailDestinataire = $demande['email'];
                $sujet = "Décharge de budget approuvée";
                $message = "Votre demande de décharge de budget a été approuvée. Veuillez trouver la décharge en pièce jointe.";
                envoyerEmailAvecPieceJointe($emailDestinataire, $sujet, $message, $nomFichierPDF);

                echo "La demande a été approuvée. Un email a été envoyé à l'employé.";
            }
        } elseif ($action === 'Rejeter') {
            // Mettre à jour le statut de la demande en 'rejeté' et enregistrer le motif de rejet
            $stmt = $pdo->prepare("UPDATE decharges_budget SET statut = 'rejeté', motif = :motif_rejet WHERE id = :id");
            $stmt->execute(['id' => $id, 'motif_rejet' => $motif_rejet]);
            echo "La demande a été rejetée avec motif : " . htmlspecialchars($motif_rejet);
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Erreur : ID de décharge non valide.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comptable - Examiner les Décharges</title>
    <style>
        /* Style CSS */
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
        button {
            padding: 8px 12px;
            background-color: #38c172;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2f7f57;
        }
        .reject-btn {
            background-color: #e3342f;
        }
        .reject-btn:hover {
            background-color: #c53030;
        }
        .comment-box {
            display: none;
            margin-top: 10px;
        }
        textarea {
            width: 100%;
            height: 60px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
        }
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
    </style>
    <script>
        function showCommentBox(id) {
            document.getElementById('comment-box-' + id).style.display = 'block';
        }
    </script>
</head>
<body>

    <h1>Demandes de Décharges de Budget en Attente</h1>

    <table>
        <tr>
            <th>Nom Complet</th>
            <th>Somme</th>
            <th>Motif</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>

        <?php
        $stmt = $pdo->query("SELECT * FROM decharges_budget WHERE statut = 'en_attente'");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['nom_complet']) . "</td>";
            echo "<td>" . htmlspecialchars($row['somme']) . "</td>";
            echo "<td>" . htmlspecialchars($row['motif']) . "</td>";
            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
            echo "<td class='actions'>";

            // Formulaire pour approuver la demande
            echo "<form action='' method='POST' style='display:inline;'>";
            echo "<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>";
            echo "<button type='submit' name='action' value='Approuver'>Approuver</button>";
            echo "</form>";

            // Formulaire pour rejeter la demande
            echo "<form action='' method='POST' style='display:inline;' onsubmit='return confirm(\"Voulez-vous vraiment rejeter cette demande ?\")'>";
            echo "<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>";
            echo "<button type='button' onclick='showCommentBox(" . htmlspecialchars($row['id']) . ")' class='reject-btn'>Rejeter</button>";
            echo "<div id='comment-box-" . htmlspecialchars($row['id']) . "' class='comment-box'>";
            echo "<textarea name='motif_rejet' placeholder='Entrez le motif du rejet' required></textarea>";
            echo "<button type='submit' name='action' value='Rejeter' class='reject-btn'>Confirmer le rejet</button>";
            echo "</div>";
            echo "</form>";

            echo "</td>";
            echo "</tr>";
        }
        ?>

    </table>

</body>
</html>
