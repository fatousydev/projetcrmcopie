<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'crm';

$conn = new mysqli($host, $user, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Traitement du formulaire d'ajout utilisateur
if (isset($_POST['ajouter'])) {
    // Récupération des données du formulaire
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mot_de_passe = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';

    // Vérification des champs requis
    if (!empty($nom) && !empty($email) && !empty($mot_de_passe) && !empty($role)) {
        // Vérifier si l'email existe déjà
        $check = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            echo "Cet email est déjà utilisé.";
        } else {
            // Hasher le mot de passe
            $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Préparer la requête d'insertion
            $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
            
            if ($stmt === false) {
                die("Erreur de préparation : " . $conn->error);
            }

            $stmt->bind_param("ssss", $nom, $email, $mot_de_passe_hache, $role);

            // Exécuter la requête
            if ($stmt->execute()) {
                // Redirection après succès
                header("Location: list_utilisateur.php");
                exit();
            } else {
                echo "Erreur lors de l'insertion : " . $stmt->error;
            }

            $stmt->close();
        }
        $check->close();
    } else {
        echo "Veuillez remplir tous les champs requis.";
    }
}

$conn->close();
?>