<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crm";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Définir les en-têtes HTTP pour le téléchargement CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=prospects_export.csv');

// Ouvrir la sortie en mode écriture
$output = fopen('php://output', 'w');

// Exécuter la requête pour récupérer les données
$sql = "SELECT * FROM interaction";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Récupérer les noms des colonnes depuis les métadonnées du résultat
    $fields = $result->fetch_fields();
    $columnNames = array_map(function($field) {
        return $field->name;
    }, $fields);

    // Ligne 24 : Écrire les noms des colonnes dans le fichier CSV
    fputcsv($output, $columnNames);

    // Écrire chaque ligne de données
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
} else {
    // Si aucun résultat, écrire un message dans le CSV
    fputcsv($output, ['Aucune interaction trouvée']);
}

// Fermer le fichier et la connexion
fclose($output);
$conn->close();
exit;
?>
