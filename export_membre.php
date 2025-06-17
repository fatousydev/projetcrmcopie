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

// Définir l'en-tête pour téléchargement CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=prospects_export.csv');

// Ouvrir la sortie en mode écriture
$output = fopen('php://output', 'w');

// Écrire l'en-tête des colonnes
$sql = "SELECT * FROM membres";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Récupérer les noms des colonnes
    $fields = $result->fetch_fields();
    $columnNames = array_map(fn($field) => $field->name, $fields);
    fputcsv($output, $columnNames);

    // Écrire les lignes de données
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
} else {
    // Si aucun résultat, afficher une ligne vide avec un message
    fputcsv($output, ['Aucun Membres trouvé']);
}

fclose($output);
$conn->close();
exit;
?>
