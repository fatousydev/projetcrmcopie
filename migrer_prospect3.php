<?php
// Connexion à la base de données
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "crm";

$conn = new mysqli($host, $user, $pass, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("❌ Connexion échouée : " . $conn->connect_error);
}

// Récupération du numéro depuis un formulaire ou une requête GET/POST
$numero_piece = isset($_POST['numero_piece']) ? $_POST['numero_piece'] : (isset($_GET['numero_piece']) ? $_GET['numero_piece'] : '');

if (empty($numero_piece)) {
    echo "❗ Veuillez fournir un numéro de pièce pour effectuer la migration.";
    exit;
}

// Appel de la procédure VerifierEtMigrerProspect
$sql = "CALL VerifierEtMigrerProspect(?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $numero_piece);

if ($stmt->execute()) {
    do {
        if ($result = $stmt->get_result()) {
            echo "<table border='1' cellpadding='5'>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $col) {
                    echo "<td>" . htmlspecialchars($col) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            $result->free();
        }
    } while ($stmt->more_results() && $stmt->next_result());
} else {
    echo "❌ Erreur lors de l'exécution : " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
