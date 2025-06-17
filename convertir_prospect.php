<?php
require 'conf.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "UPDATE prospects SET statut = 'converti' WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $id]);
    header("Location: liste_prospect.php");
    exit();
}
?>
