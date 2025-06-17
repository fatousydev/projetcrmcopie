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
        window.location.href = 'liste_vente.php'; // Redirection vers la liste des ventes
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
        if ($index === 0) continue; // Ignorer l'en-tête

        if (count($row) < 7) {
            $errorMessages[] = "Ligne $index ignorée: nombre de colonnes insuffisant (" . count($row) . "/7 attendues)";
            $errors++;
            continue;
        }

        $values = array_map(function ($val) use ($conn) {
            return $conn->real_escape_string($val ?? '');
        }, $row);

        list($produit_id, $membre_id, $utilisateur_id, $quantite, $date_vente, $debut) = $values;

        // Validation des données
        if (!is_numeric($produit_id) || !is_numeric($membre_id) || !is_numeric($utilisateur_id) || !is_numeric($quantite)) {
            $errorMessages[] = "Ligne $index ignorée: valeurs numériques attendues pour produit_id, membre_id, utilisateur_id et quantite";
            $errors++;
            continue;
        }

        // Formatage de la date si nécessaire
        if (!empty($date_vente)) {
            $date_vente = date('Y-m-d H:i:s', strtotime($date_vente));
        } else {
            $date_vente = date('Y-m-d H:i:s');
        }

        $sql = "INSERT INTO ventes (
            produit_id, membre_id, utilisateur_id, quantite, date_vente, debut
        ) VALUES (
            '$produit_id', '$membre_id', '$utilisateur_id', '$quantite', '$date_vente', '$debut'
        )";

        if ($conn->query($sql) === TRUE) {
            $total++;
        } else {
            $errorMessages[] = "Erreur SQL ligne $index : " . $conn->error;
            $errors++;
        }
    }

    if ($total > 0) {
        $successMsg = "$total ventes importées avec succès!";
        if ($errors > 0) {
            $successMsg .= "\n$errors erreurs lors de l'import.";
        }
        showAlert($successMsg);
    } else {
        showAlert("Aucune vente importée. $errors erreurs rencontrées.", 'error');
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