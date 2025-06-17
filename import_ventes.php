<?php
// Activation du reporting d'erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si le fichier a été uploadé
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['fichierVentes'])) {
    echo json_encode(['success' => false, 'message' => 'Aucun fichier reçu']);
    exit;
}

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=crm;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
    exit;
}

// Chemin temporaire du fichier uploadé
$fileTmpPath = $_FILES['fichierVentes']['tmp_name'];
$fileName = $_FILES['fichierVentes']['name'];
$fileSize = $_FILES['fichierVentes']['size'];
$fileType = $_FILES['fichierVentes']['type'];
$fileNameCmps = explode(".", $fileName);
$fileExtension = strtolower(end($fileNameCmps));

// Vérifier l'extension du fichier
$allowed
