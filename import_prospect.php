<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$conn = new mysqli('localhost', 'root', '', 'crm');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

function showAlert($message, $type = 'success') {
    echo "<script>
        alert('$message');
        window.location.href = 'liste_prospect.php'; // Remplacez par la page où vous voulez rediriger
    </script>";
}

if (isset($_POST['import']) && $_FILES['file']['error'] == 0) {
    $file = $_FILES['file']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    $total = 0;
    $errors = 0;
    $errorMessages = [];

    foreach ($rows as $index => $row) {
        if ($index === 0) continue;

        if (count($row) < 29) {
            $errorMessages[] = "Ligne $index ignorée: nombre de colonnes insuffisant (" . count($row) . ")";
            $errors++;
            continue;
        }

        $values = array_map(function ($val) use ($conn) {
            return $conn->real_escape_string($val ?? '');
        }, $row);

        list($id, $caisse_id, $statut, $enregistre_par, $agence_concernee, $date_enregistrement,
             $type, $nom_entreprise, $effectif, $classification, $nom, $prenom, $Region,
             $fonction, $telephone, $telephone_whatsapp, $email, $adresse, $activites,
             $besoins, $source_connaissance, $numero_membre, $personne_contact,
             $relation_contact, $telephone_contact, $commentaires,
             $a_beneficie_credit, $campagne_id, $numero_piece) = $values;

        $telephone_whatsapp = ($telephone_whatsapp === '0') ? '0' : $telephone_whatsapp;
        $a_beneficie_credit = ($a_beneficie_credit === '0') ? '0' : $a_beneficie_credit;

        $sql = "INSERT INTO prospects (
            caisse_id, statut, enregistre_par, agence_concernee, date_enregistrement,
            type, nom_entreprise, effectif, classification, nom, prenom, Region,
            fonction, telephone, telephone_whatsapp, email, adresse, activites,
            besoins, source_connaissance, numero_membre, personne_contact, relation_contact,
            telephone_contact, commentaires, a_beneficie_credit, campagne_id, numero_piece
        ) VALUES (
            '$caisse_id', '$statut', '$enregistre_par', '$agence_concernee', '$date_enregistrement',
            '$type', '$nom_entreprise', '$effectif', '$classification', '$nom', '$prenom', '$Region',
            '$fonction', '$telephone', '$telephone_whatsapp', '$email', '$adresse', '$activites',
            '$besoins', '$source_connaissance', '$numero_membre', '$personne_contact', '$relation_contact',
            '$telephone_contact', '$commentaires', '$a_beneficie_credit', '$campagne_id', '$numero_piece'
        )";

        if ($conn->query($sql) === TRUE) {
            $total++;
        } else {
            $errorMessages[] = "Erreur SQL ligne $index : " . $conn->error;
            $errors++;
        }
    }

    if ($total > 0) {
        $successMsg = "$total prospects importés avec succès!";
        if ($errors > 0) {
            $successMsg .= "\n$errors erreurs lors de l'import.";
        }
        showAlert($successMsg);
    } else {
        showAlert("Aucun prospect importé. $errors erreurs rencontrées.", 'error');
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