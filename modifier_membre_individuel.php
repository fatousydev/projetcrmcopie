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

// Vérification si l'ID du membre à modifier est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: liste_membres.php?error=invalid_id');
    exit();
}

$id_membre = intval($_GET['id']);

// Récupération des données existantes du membre
try {
    $sql = "SELECT * FROM membre_individuel WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id_membre, PDO::PARAM_INT);
    $stmt->execute();
    
    $membre = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$membre) {
        header('Location: liste_membres.php?error=member_not_found');
        exit();
    }
} catch(PDOException $e) {
    header('Location: liste_membres.php?error=db_error');
    exit();
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
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
        // Préparation de la requête SQL de mise à jour
        $sql = "UPDATE membre_individuel SET
                numero_membre = :numero_membre,
                statut = :statut,
                date_admission = :date_admission,
                nom = :nom,
                prenom = :prenom,
                fonction = :fonction,
                num_telephone = :num_telephone,
                email = :email,
                region = :region,
                adresse = :adresse,
                numero_piece = :numero_piece,
                numero_passeport = :numero_passeport,
                besoins = :besoins,
                source_connaissance = :source_connaissance,
                personne_contact = :personne_contact,
                relation_contact = :relation_contact,
                telephone_contact = :telephone_contact,
                commentaire = :commentaire,
                campagne_id = :campagne_id,
                caisse_id = :caisse_id,
                guichet_id = :guichet_id,
                a_benefice_credit = :a_benefice_credit,
                id_prospect = :id_prospect
                WHERE id = :id";

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
        $stmt->bindParam(':id', $id_membre, PDO::PARAM_INT);

        // Exécution de la requête
        if ($stmt->execute()) {
            // Redirection avec message de succès
            header('Location: details_membre.php?id='.$id_membre.'&success=1');
            exit();
        } else {
            // Redirection avec message d'erreur
            header('Location: modifier_membre_form.php?id='.$id_membre.'&error=1');
            exit();
        }
    } catch(PDOException $e) {
        // En cas d'erreur, affichage du message et redirection
        error_log("Erreur lors de la modification du membre : " . $e->getMessage());
        header('Location: modifier_membre_form.php?id='.$id_membre.'&error=1');
        exit();
    }
}

// Si on arrive ici, c'est qu'on veut afficher le formulaire de modification
// On va donc inclure le formulaire pré-rempli avec les données du membre
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier Membre Individuel</title>
  <style>
    /* Styles identiques à votre formulaire d'ajout */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    header {
      background-color: #65a3f3;
      color: white;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    /* ... (reprendre tous les styles de votre formulaire d'ajout) ... */
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <img src="pamecas.jpg" alt="Pamecas" width="70">
      <h1>PAMECAS - MODIFICATION MEMBRE INDIVIDUEL</h1>
    </div>
    <div class="user-info">
      <span>A</span>
      <a href="logout.php" class="logout">Déconnexion</a>
    </div>
  </header>
  
  <main>
    <h2 style="text-align: center; color:white">Modifier membre individuel</h2>
    
    <?php if (isset($_GET['error'])): ?>
      <div style="color: red; text-align: center; margin: 10px 0;">
        Une erreur est survenue lors de la modification. Veuillez réessayer.
      </div>
    <?php endif; ?>
    
    <div class="form-client">
      <form action="modifier_membre_individuel.php?id=<?= $id_membre ?>" method="POST">
        <!-- Tous les champs du formulaire pré-remplis avec les données existantes -->
        <div class="form-group">
          <div>
            <label for="numero_membre">Numéro membre</label>
            <input type="text" id="numero_membre" name="numero_membre" value="<?= htmlspecialchars($membre['numero_membre']) ?>" required>
          </div>
          <div>
            <label for="statut">Statut</label>
            <select id="statut" name="statut" required>
              <option value="Actif" <?= $membre['statut'] == 'Actif' ? 'selected' : '' ?>>Actif</option>
              <option value="Inactif" <?= $membre['statut'] == 'Inactif' ? 'selected' : '' ?>>Inactif</option>
            </select>
          </div>
           <div>
            <label for="date_admission">Date adhésion</label>
            <input type="date" id="date_admission" name="date_admission" value="<?= htmlspecialchars($membre['date_admission']) ?>" required>
          </div>
          <div>
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($membre['nom']) ?>" required>
          </div>
          <div>
            <label for="prenom ">Prenom</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($membre['prenom']) ?>" required>
          </div>
          <div>
            <label for="fonction ">Fonctio</label>
            <input type="text" id="fonction" name="fonction" value="<?= htmlspecialchars($membre['fonction']) ?>" required>
          </div>
           <div>
            <label for="num_telephone">Numero tel</label>
            <input type="text" id="num_telephone" name="num_telephone" value="<?= htmlspecialchars($membre['num_telephone']) ?>" required>
          </div>
          <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($membre['email']) ?>" required>
          </div>
          <div>
            <label for="region">Region</label>
            <input type="text" id="region" name="region" value="<?= htmlspecialchars($membre['region']) ?>" required>
          </div>
          <div>
            <label for="adresse">Adresse</label>
            <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($membre['adresse']) ?>" required>
          </div>
          <div>
            <label for="numero_piece">Numero piece </label>
            <input type="text" id="numero_piece" name="numero_piece" value="<?= htmlspecialchars($membre['numero_piece']) ?>" required>
          </div>
          <div>
            <label for="numero_passeport">Numero passeport</label>
            <input type="text" id="numero_passeport" name="numero_passeport" value="<?= htmlspecialchars($membre['numero_passeport']) ?>" required>
          </div>
          <div>
            <label for="besoins">Besoins</label>
            <input type="text" id="besoins" name="besoins" value="<?= htmlspecialchars($membre['besoins']) ?>" required>
          </div>
          <div>
            <label for="source_connaissance">Source connaissance</label>
            <input type="text" id="source_connaissance" name="source_connaissance" value="<?= htmlspecialchars($membre['source_connaissance']) ?>" required>
          </div>
          <div>
            <label for="personne_contact">Personne contact</label>
            <input type="text" id="personne_contact" name="personne_contact" value="<?= htmlspecialchars($membre['personne_contact']) ?>" required>
          </div>
          <div>
            <label for="relation_contact">Relation contact</label>
            <input type="text" id="relation_contact" name="relation_contact" value="<?= htmlspecialchars($membre['relation_contact']) ?>" required>
          </div>
          <div>
            <label for="telephone_contact">Telephone contact</label>
            <input type="text" id="telephone_contact" name="telephone_contact" value="<?= htmlspecialchars($membre['telephone_contact']) ?>" required>
          </div>
          <div>
            <label for="commentaire">Commentaire</label>
            <input type="text" id="commentaire" name="commentaire" value="<?= htmlspecialchars($membre['commentaire']) ?>" required>
          </div>
        </div>
        
        <!-- Continuer avec tous les autres champs du formulaire -->
        <!-- ... -->
        
        <button type="submit" name="modifier">Enregistrer les modifications</button>
        <a href="details_membre.php?id=<?= $id_membre ?>" class="btn btn-secondary">Annuler</a>
      </form>
    </div>
  </main>
</body>
</html>