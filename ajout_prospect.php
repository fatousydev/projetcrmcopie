<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'crm');

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupération des données du formulaire
if (isset($_POST['ajouter'])) {
    $statut = $_POST['statut'];
    $enregistre_par = $_POST['enregistre_par'];
    $agence_concernee = $_POST['agence_concernee'];
    $date_enregistrement = $_POST['date_enregistrement'];
    $type = $_POST['type'];
    $effectif  = $_POST['effectif'];
    $classification = $_POST['classification'];
    $nom_entreprise = $_POST['nom_entreprise']; // Cette variable manquait
    $nom  = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $Region = $_POST['Region'];
    $fonction = $_POST['fonction'];
    $telephone = $_POST['telephone'];
    $telephone_whatsapp = $_POST['telephone_whatsapp'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];
    $activites  = $_POST['activites'];
    $besoins  = $_POST['besoins'];
    $source_connaissance = $_POST['source_connaissance'];
    $numero_membre = $_POST['numero_membre'];
    $personne_contact = $_POST['personne_contact'];
    $relation_contact = $_POST['relation_contact'];
    $telephone_contact = $_POST['telephone_contact']; // Cette variable manquait aussi
    $numero_piece = $_POST['numero_piece'];
    $commentaires = $_POST['commentaires'];
    $created_at = date('Y-m-d H:i:s'); // Création automatique de la date

    // Vérification des champs requis
    if (!empty($statut) && !empty($enregistre_par)) {
        $stmt = $conn->prepare("INSERT INTO prospects(
            statut,
            enregistre_par,
            agence_concernee,
            date_enregistrement,
            type,
            nom_entreprise,
            effectif,
            classification, 
            nom,
            prenom,
            Region,
            fonction, 
            telephone, 
            telephone_whatsapp,
            email,
            adresse,
            activites,
            besoins,
            source_connaissance,
            numero_membre,
            personne_contact,
            relation_contact,
            telephone_contact,
            numero_piece,
            commentaires
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");

        if (!$stmt) {
            die("Erreur de préparation : " . $conn->error);
        }

        $stmt->bind_param("sssssssssssssssssssssssss",
            $statut,
            $enregistre_par,
            $agence_concernee,
            $date_enregistrement,
            $type,
            $nom_entreprise,
            $effectif,
            $classification,
            $nom,
            $prenom,
            $Region,
            $fonction,
            $telephone,
            $telephone_whatsapp,
            $email,
            $adresse,
            $activites,
            $besoins,
            $source_connaissance,
            $numero_membre,
            $personne_contact,
            $relation_contact,
            $telephone_contact,
            $numero_piece,
            $commentaires,
            
        );

        if ($stmt->execute()) {
            // ✅ Redirection après succès
            header("Location: liste_prospect.php");
            exit();
        } else {
            echo "Erreur lors de l'insertion : " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Veuillez remplir tous les champs requis.";
    }
}

$conn->close();
?>