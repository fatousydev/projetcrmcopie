<?php
/**
 * FICHIER : db_connect.php
 * BUT : Gérer la connexion à la base de données
 * CONFIGURATION : À adapter selon votre environnement
 */

$host = 'localhost';
$dbname = 'crm';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>