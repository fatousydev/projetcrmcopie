<?php
session_start();

$host = 'localhost';
$dbname = 'crm';
$user = 'root'; // Modifier si nécessaire
$pass = ''; // Modifier si nécessaire

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}




?>
