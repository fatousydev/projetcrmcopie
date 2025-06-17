<?php
// Paramètres de connexion
$host = 'localhost'; // Adresse du serveur MySQL
$dbname = 'crm'; // Nom de la base de données
$username = 'root'; // Nom d'utilisateur MySQL
$password = ''; // Mot de passe MySQL

try {
    // Création de la connexion PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Définition du mode d'erreur pour afficher les erreurs SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur de connexion, afficher un message
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
