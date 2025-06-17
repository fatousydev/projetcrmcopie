<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'crm';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérification si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    // Récupération et sécurisation des données du formulaire
    $numero_membre = htmlspecialchars($_POST['numero_membre']);
    $statut = htmlspecialchars($_POST['statut']);
    $date_admission = htmlspecialchars($_POST['date_admission']);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $fonction = htmlspecialchars($_POST['fonction']);
    $num_telephone = htmlspecialchars($_POST['num_telephone']);
    $email = htmlspecialchars($_POST['email']);
    $region = htmlspecialchars($_POST['region']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $numero_piece = htmlspecialchars($_POST['numero_piece']);
    $numero_passeport = htmlspecialchars($_POST['numero_passeport']);
    $besoins = htmlspecialchars($_POST['besoins']);
    $source_connaissance = htmlspecialchars($_POST['source_connaissance']);
    $personne_contact = htmlspecialchars($_POST['personne_contact']);
    $relation_contact = htmlspecialchars($_POST['relation_contact']);
    $telephone_contact = htmlspecialchars($_POST['telephone_contact']);
    $commentaire = htmlspecialchars($_POST['commentaire']);
    $campagne_id = intval($_POST['campagne_id']);
    $caisse_id = intval($_POST['caisse_id']);
    $guichet_id = intval($_POST['guichet_id']);
    $a_benefice_credit = intval($_POST['a_benefice_credit']);
    $id_prospect = intval($_POST['id_prospect']);

    try {
        // Préparation de la requête SQL
        $sql = "INSERT INTO membre_individuel (
            numero_membre, statut, date_admission, nom, prenom, fonction, 
            num_telephone, email, region, adresse, numero_piece, numero_passeport, 
            besoins, source_connaissance, personne_contact, relation_contact, 
            telephone_contact, commentaire, campagne_id, caisse_id, guichet_id, 
            a_benefice_credit, id_prospect
        ) VALUES (
            :numero_membre, :statut, :date_admission, :nom, :prenom, :fonction, 
            :num_telephone, :email, :region, :adresse, :numero_piece, :numero_passeport, 
            :besoins, :source_connaissance, :personne_contact, :relation_contact, 
            :telephone_contact, :commentaire, :campagne_id, :caisse_id, :guichet_id, 
            :a_benefice_credit, :id_prospect
        )";

        $stmt = $conn->prepare($sql);

        // Liaison des paramètres
        $stmt->bindParam(':numero_membre', $numero_membre);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':date_admission', $date_admission);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':fonction', $fonction);
        $stmt->bindParam(':num_telephone', $num_telephone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':region', $region);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':numero_piece', $numero_piece);
        $stmt->bindParam(':numero_passeport', $numero_passeport);
        $stmt->bindParam(':besoins', $besoins);
        $stmt->bindParam(':source_connaissance', $source_connaissance);
        $stmt->bindParam(':personne_contact', $personne_contact);
        $stmt->bindParam(':relation_contact', $relation_contact);
        $stmt->bindParam(':telephone_contact', $telephone_contact);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->bindParam(':campagne_id', $campagne_id);
        $stmt->bindParam(':caisse_id', $caisse_id);
        $stmt->bindParam(':guichet_id', $guichet_id);
        $stmt->bindParam(':a_benefice_credit', $a_benefice_credit);
        $stmt->bindParam(':id_prospect', $id_prospect);

        // Exécution de la requête
        if ($stmt->execute()) {
            // Redirection avec message de succès
            header('Location: interfaceadmin.php?success=1');
            exit();
        } else {
            // Redirection avec message d'erreur
            header('Location: ajout_membre_individuel_form.php?error=1');
            exit();
        }
    } catch(PDOException $e) {
        // En cas d'erreur, affichage du message et redirection
        error_log("Erreur lors de l'ajout du membre : " . $e->getMessage());
        header('Location: ajout_membre_individuel_form.php?error=1');
        exit();
    }
} else {
    // Si le formulaire n'a pas été soumis correctement
    header('Location: ajout_membre_individuel_form.php');
    exit();
}
?>