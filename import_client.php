<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// 1️⃣ Connexion à MySQL
$conn = new mysqli('localhost', 'root', '', 'crm');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Fonction pour afficher les alertes
function showAlert($message, $type = 'success') {
    echo "<script>
        alert('$message');
        window.location.href = 'liste_membre.php'; // Redirection vers la liste des membres
    </script>";
}

// 2️⃣ Vérifie si on a envoyé un fichier
if (isset($_POST['import']) && $_FILES['file']['error'] == 0) {
    $file = $_FILES['file']['tmp_name'];

    // 3️⃣ Lire le fichier Excel
    $spreadsheet = IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    $total = 0;
    $errors = 0;
    $errorMessages = [];

    // 4️⃣ Parcourir les lignes et insérer dans la base
    foreach ($rows as $index => $row) {
        if ($index === 0) continue; // Ignore l'entête (1ère ligne)

        // Vérification du nombre de colonnes (26 colonnes attendues)
        if (count($row) < 26) {
            $errorMessages[] = "Ligne $index ignorée: nombre de colonnes insuffisant (" . count($row) . ")";
            $errors++;
            continue;
        }

        // Sécurisation des données
        $numero_membre = $conn->real_escape_string($row[0] ?? '');
        $statut = $conn->real_escape_string($row[1] ?? '');
        $date_admission = $conn->real_escape_string($row[2] ?? '');
        $type = $conn->real_escape_string($row[3] ?? ''); 
        $nom_entreprise = $conn->real_escape_string($row[4] ?? '');
        $effectif = $conn->real_escape_string($row[5] ?? '');
        $classification = $conn->real_escape_string($row[6] ?? '');
        $fonction = $conn->real_escape_string($row[7] ?? '');
        $telephone = $conn->real_escape_string($row[8] ?? '');
        $email = $conn->real_escape_string($row[9] ?? '');
        $adresse = $conn->real_escape_string($row[10] ?? '');
        $activites = $conn->real_escape_string($row[11] ?? ''); 
        $besoins = $conn->real_escape_string($row[12] ?? '');
        $personne_contact = $conn->real_escape_string($row[13] ?? '');
        $relation_contact = $conn->real_escape_string($row[14] ?? '');
        $telephone_contact = $conn->real_escape_string($row[15] ?? '');
        $commentaires = $conn->real_escape_string($row[16] ?? '');
        $nom = $conn->real_escape_string($row[17] ?? '');
        $Prenom = $conn->real_escape_string($row[18] ?? '');
        $Region = $conn->real_escape_string($row[19] ?? '');
        $campagne_id = $conn->real_escape_string($row[20] ?? '');
        $caisse_id = $conn->real_escape_string($row[21] ?? '');
        $guichet_id = $conn->real_escape_string($row[22] ?? '');
        $a_beneficie_credit = $conn->real_escape_string($row[23] ?? '');
        $numero_piece = $conn->real_escape_string($row[24] ?? '');
        $id_prospect = $conn->real_escape_string($row[25] ?? '');

        // Requête SQL
        $sql = "INSERT INTO membres (
            numero_membre, statut, date_admission, type, nom_entreprise, effectif, 
            classification, fonction, telephone, email, adresse, activites, 
            besoins, personne_contact, relation_contact, telephone_contact, 
            commentaires, nom, Prenom, Region, campagne_id, caisse_id, 
            guichet_id, a_beneficie_credit, numero_piece, id_prospect
        ) VALUES (
            '$numero_membre', '$statut', '$date_admission', '$type', '$nom_entreprise', '$effectif', 
            '$classification', '$fonction', '$telephone', '$email', '$adresse', '$activites', 
            '$besoins', '$personne_contact', '$relation_contact', '$telephone_contact', 
            '$commentaires', '$nom', '$Prenom', '$Region', '$campagne_id', '$caisse_id', 
            '$guichet_id', '$a_beneficie_credit', '$numero_piece', '$id_prospect'
        )";

        if ($conn->query($sql) === TRUE) {
            $total++;
        } else {
            $errorMessages[] = "Erreur SQL ligne $index : " . $conn->error;
            $errors++;
        }
    }

    // 5️⃣ Gestion des messages de résultat
    if ($total > 0) {
        $successMsg = "$total membres importés avec succès!";
        if ($errors > 0) {
            $successMsg .= "\n$errors erreurs lors de l'import.";
        }
        showAlert($successMsg);
    } else {
        showAlert("Aucun membre importé. $errors erreurs rencontrées.", 'error');
    }

    // Afficher toutes les erreurs dans une seule alerte si nécessaire
    if (!empty($errorMessages)) {
        showAlert("Erreurs d'importation:\n" . implode("\n", $errorMessages), 'error');
    }
} else {
    $errorMsg = "Erreur lors de l'importation du fichier.";
    if ($_FILES['file']['error'] != 0) {
        $errorMsg .= "\nCode d'erreur: " . $_FILES['file']['error'];
    }
    showAlert($errorMsg, 'error');
}

$conn->close();
?>