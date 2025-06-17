<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "crm");
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Vider les anciennes affectations
$conn->query("DELETE FROM caisse_utilisateur");

// Récupérer tous les utilisateurs
$utilisateurs = $conn->query("SELECT * FROM utilisateurs");

while ($user = $utilisateurs->fetch_assoc()) {
    $userId = $user['id'];
    $role = strtolower($user['role']);

    switch ($role) {
        case 'admin':
            // Assigner toutes les caisses
            $res = $conn->query("SELECT id FROM caisses");
            while ($caisse = $res->fetch_assoc()) {
                $caisseId = $caisse['id'];
                $conn->query("INSERT INTO caisse_utilisateur (utilisateur_id, caisse_id) VALUES ($userId, $caisseId)");
            }
            break;

        case 'directeur':
            // Assigner seulement les caisses dont il est directeur
            $res = $conn->query("SELECT id FROM caisses WHERE id_directeur = $userId");
            while ($caisse = $res->fetch_assoc()) {
                $caisseId = $caisse['id'];
                $conn->query("INSERT INTO caisse_utilisateur (utilisateur_id, caisse_id) VALUES ($userId, $caisseId)");
            }
            break;

        case 'commercial':
        case 'animatrice':
            // Assigner les caisses où il/elle est adhérent(e)
            $res = $conn->query("SELECT caisse_id FROM adherents WHERE utilisateur_id = $userId");
            while ($caisse = $res->fetch_assoc()) {
                $caisseId = $caisse['caisse_id'];
                $conn->query("INSERT INTO caisse_utilisateur (utilisateur_id, caisse_id) VALUES ($userId, $caisseId)");
            }
            break;
    }
}

echo "✅ Affectation automatique terminée.";
$conn->close();
?>
