<?php
session_start();
include 'pdo.php';

// Exemple : récupération des infos de session (à adapter à ton système)
$id_user = $_SESSION['utilisateur_id'];
$role = $_SESSION['role'];

// Requête selon rôle
if ($role === 'admin') {
    $stmt = $pdo->query("SELECT * FROM caisses");
} else {
    $stmt = $pdo->prepare("
        SELECT c.* FROM caisses c
        JOIN caisse_utilisateur cu ON cu.id_caisse = c.id
        WHERE cu.id_utilisateur = ?
    ");
    $stmt->execute([$id_user]);
}

// Affichage
while ($caisse = $stmt->fetch()) {
    echo "<div class='border p-2 m-1'>";
    echo "<strong>{$caisse['nom']}</strong> - Statut : {$caisse['statut']}";
    echo "</div>";
}
?>