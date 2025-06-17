<?php
//Si tu veux conserver l'état de connexion, il faut démarrer une session
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "acteur";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérification des champs remplis
if (!isset($_POST['nom'], $_POST['mot_de_passe'])) {
    die("Veuillez remplir tous les champs.");
}

// Sécurisation des données saisies
$nom = trim($_POST['nom']);
$mot_de_passe = trim($_POST['mot_de_passe']);

// Requête préparée pour éviter les injections SQL
$sql = "SELECT id, mot_de_passe FROM authentification WHERE nom = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nom);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Vérification du mot de passe haché
    if (password_verify($mot_de_passe, $row['mot_de_passe'])) {
        // Démarrer une session et rediriger
        $_SESSION['utilisateur'] = $nom;
        header("Location:tableaubord.php");
        exit();
    } else {
        echo "Nom ou mot de passe incorrect.";
    }
} else {
    echo "Nom ou mot de passe incorrect.";
}

$stmt->close();
$conn->close();
?>
