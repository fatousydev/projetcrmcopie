<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crm";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Vérification si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Récupération des données du formulaire et sécurisation
    $numero_membre = $conn->real_escape_string($_POST['numero_membre']);
    $statut = $conn->real_escape_string($_POST['statut']);
    $date_admission = $conn->real_escape_string($_POST['date_admission']);
    $type = $conn->real_escape_string($_POST['type']);
    $nom_entreprise = $conn->real_escape_string($_POST['nom_entreprise']);
    $effectif = $conn->real_escape_string($_POST['effectif']);
    $classification = $conn->real_escape_string($_POST['classification']);
    $fonction = $conn->real_escape_string($_POST['fonction']);
    $telephone = $conn->real_escape_string($_POST['telephone']);
    $email = $conn->real_escape_string($_POST['email']);
    $adresse = $conn->real_escape_string($_POST['adresse']);
    $activites = $conn->real_escape_string($_POST['activites']);
    $besoins = $conn->real_escape_string($_POST['besoins']);
    $personne_contact = $conn->real_escape_string($_POST['personne_contact']);
    $relation_contact = $conn->real_escape_string($_POST['relation_contact']);
    $telephone_contact = $conn->real_escape_string($_POST['telephone_contact']);
    $commentaires = $conn->real_escape_string($_POST['commentaires']);
    $nom = $conn->real_escape_string($_POST['nom']);
    $Prenom = $conn->real_escape_string($_POST['Prenom']);
    $Region = $conn->real_escape_string($_POST['Region']);
    // Requête SQL d'insertion
    $sql = "INSERT INTO membres (numero_membre, statut, date_admission, type, nom_entreprise, effectif, classification, fonction, telephone, email, adresse, activites, besoins, personne_contact, relation_contact, telephone_contact, commentaires,nom,Prenom,Region ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?)";

    $stmt = $conn->prepare($sql);

    // Liaison des paramètres
    $stmt->bind_param("ssssssssssssssssssss", $numero_membre, $statut, $date_admission, $type, $nom_entreprise, $effectif, $classification, $fonction, $telephone, $email, $adresse, $activites, $besoins, $personne_contact, $relation_contact, $telephone_contact, $commentaires,$nom ,$Prenom,$Region);

    // Exécutiossn
    if ($stmt->execute()) {
        echo "<script>alert('Membre ajouté avec succès !'); window.location.href='liste_membre.php';</script>";
    } else {
        echo "Erreur : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
