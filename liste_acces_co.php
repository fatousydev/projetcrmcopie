<?php
session_start();
$conn = new PDO("mysql:host=localhost;dbname=crm", "root", "");

if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: connecter.php");
    exit;
}

if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $conn->prepare("DELETE FROM acces_caisses WHERE id = ?");
    $stmt->execute([$id]);
}

$stmt = $conn->query("
    SELECT a.id, u.nom as utilisateur, u.role, c.nom 
    FROM acces_caisses a
    JOIN utilisateurs u ON u.id = a.utilisateur_id
    JOIN caisses c ON c.id = a.caisse_id
");
$acces = $stmt->fetchAll();
?>

<h2>Liste des accès aux caisses</h2>
<table border="1">
    <tr>
        <th>Utilisateur</th>
        <th>Rôle</th>
        <th>Caisse</th>
        <th>Action</th>
    </tr>
    <?php foreach ($acces as $a): ?>
    <tr>
        <td><?= $a['utilisateur'] ?></td>
        <td><?= $a['role'] ?></td>
        <td><?= $a['nom'] ?></td>
        <td><a href="?supprimer=<?= $a['id'] ?>" onclick="return confirm('Supprimer cet accès ?')">Supprimer</a></td>
    </tr>
    <?php endforeach; ?>
</table>

<a href="tableaubordcommercial.php">Retour</a>
