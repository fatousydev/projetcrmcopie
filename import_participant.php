<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['fichier']) && isset($_POST['campagne_id'])) {
        $campagne_id = $_POST['campagne_id'];
        $fichier = $_FILES['fichier']['tmp_name'];

        if (($handle = fopen($fichier, "r")) !== FALSE) {
            // Connexion à la base
            $conn = new PDO("mysql:host=localhost;dbname=crm", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Lire l'en-tête du fichier
            $header = fgetcsv($handle, 1000, ",");

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row = array_combine($header, $data);

                // Exemple attendu du CSV : participant_cible,participant_id,a_beneficie_credit,date_lancement,date_cloture
                $stmt = $conn->prepare("
                    INSERT INTO campagne_participants 
                    (campagne_id, participant_cible, participant_id, a_beneficie_credit, date_lancement, date_cloture)
                    VALUES (:campagne_id, :cible, :id, :benef, :lancement, :cloture)
                ");

                $stmt->execute([
                    ':campagne_id' => $campagne_id,
                    ':cible' => $row['participant_cible'],
                    ':id' => $row['participant_id'],
                    ':benef' => $row['a_beneficie_credit'] ?? 0,
                    ':lancement' => $row['date_lancement'] ?? null,
                    ':cloture' => $row['date_cloture'] ?? null,
                ]);
            }

            fclose($handle);
            echo "Importation réussie !";
        } else {
            echo "Erreur lors de l'ouverture du fichier.";
        }
    } else {
        echo "Fichier ou campagne non défini.";
    }
} else {
    echo "Méthode non autorisée.";
}
?>
