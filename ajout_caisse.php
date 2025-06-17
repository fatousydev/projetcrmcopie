<?php
session_start();
require_once 'config.php'; // Connexion à la base

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $statut = $_POST['statut'];
    $localisation = $_POST['localisation'];
    
    try {
        $sql = "INSERT INTO caisses (nom, statut, localisation, date_creation) VALUES (:nom, :statut, :localisation, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':statut' => $statut,
            ':localisation' => $localisation
        ]);
        
        $_SESSION['success'] = "Caisse ajoutée avec succès.";
        header("Location: liste_caisse.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de l'ajout : " . $e->getMessage();
        header("Location: ajout_caisse.php");
        exit();
    }
}
?>

<!-- Formulaire d'ajout -->
<form method="POST" action="ajout_caisse.php">
    <label>Nom de la caisse :</label>
    <input type="text" name="nom" required><br>
    
    <label>Statut :</label>
    <select name="statut">
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select><br>

    <label>Localisation :</label>
    <input type="text" name="localisation" required><br>

    <button type="submit">Ajouter</button>
</form>
