<?php
include('conn.php');

// Récupérer tous les membres
$stmt = $pdo->query("SELECT * FROM membres");
$membres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si un formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $numero_membre = $_POST['numero_membre'];
    $statut = $_POST['statut'];
    $date_admission = $_POST['date_admission'];
    $type = $_POST['type'];
    $nom_entreprise = $_POST['nom_entreprise'];
    $effectif = $_POST['effectif'];
    $classification = $_POST['classification'];
    $fonction = $_POST['fonction'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];
    $activites = $_POST['activites'];
    $besoins = $_POST['besoins'];
    $personne_contact = $_POST['personne_contact'];
    $relation_contact = $_POST['relation_contact'];
    $telephone_contact = $_POST['telephone_contact'];
    $commentaires = $_POST['commentaires'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $region = $_POST['region'];
    $campagne_id = $_POST['campagne_id'];
    $prospect_id = $_POST['prospect_id'];
    $caisse_id = $_POST['caisse_id'];
    $guichet_id = $_POST['guichet_id'];
    $a_beneficie_credit = isset($_POST['a_beneficie_credit']) ? 1 : 0;

    $updateStmt = $pdo->prepare("UPDATE membres SET 
        numero_membre = ?, statut = ?, date_admission = ?, type = ?, 
        nom_entreprise = ?, effectif = ?, classification = ?, fonction = ?, 
        telephone = ?, email = ?, adresse = ?, activites = ?, besoins = ?, 
        personne_contact = ?, relation_contact = ?, telephone_contact = ?, 
        commentaires = ?, nom = ?, Prenom = ?, Region = ?, campagne_id = ?, 
        prospect_id = ?, caisse_id = ?, guichet_id = ?, a_beneficie_credit = ?, 
        updated_at = NOW() 
        WHERE id = ?");
    
    if ($updateStmt->execute([
        $numero_membre, $statut, $date_admission, $type, 
        $nom_entreprise, $effectif, $classification, $fonction, 
        $telephone, $email, $adresse, $activites, $besoins, 
        $personne_contact, $relation_contact, $telephone_contact, 
        $commentaires, $nom, $prenom, $region, $campagne_id, 
        $prospect_id, $caisse_id, $guichet_id, $a_beneficie_credit, 
        $id
    ])) {
        echo "<p style='color:green;'>Membre mis à jour avec succès.</p>";
        // Recharger la liste après modification
        header("Refresh:0");
    } else {
        echo "<p style='color:red;'>Erreur lors de la mise à jour.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Membres</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f4f4; 
            margin: 0;
        }
        header {
            background-color:  #003366;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        header .logout {
            background-color: rgb(236, 9, 9);
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .container { 
            width: 100%; 
            margin: auto; 
            background: white; 
            padding: 20px; 
            border-radius: 5px; 
            overflow-x: auto;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background-color: #003366; 
            color: white; 
        }
        button { 
            padding: 5px 10px; 
            cursor: pointer; 
        }
        #editForm { 
            display: none; 
            margin-top: 20px; 
            padding: 20px; 
            background: #f4f4f4; 
            border-radius: 5px;
        }
        input, select, textarea { 
            margin: 5px 0 15px; 
            padding: 8px; 
            width: 100%; 
        }
        .hidden { display: none; }
        .checkbox-group label { display: inline-block; margin-right: 15px; }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <img src="pamecas.jpg" alt="Pamecas" width="70">
        <h1>PAMECAS - GESTION DES MEMBRES</h1>
    </div>
    <a href="accueil.html" class="logout">Logout</a>
</header>

<div class="container">
    <h1>Liste des Membres</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Numéro Membre</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>

        <?php foreach ($membres as $m): ?>
        <tr>
            <td><?= htmlspecialchars($m['id']) ?></td>
            <td><?= htmlspecialchars($m['numero_membre']) ?></td>
            <td><?= htmlspecialchars($m['nom']) ?></td>
            <td><?= htmlspecialchars($m['Prenom']) ?></td>
            <td><?= htmlspecialchars($m['telephone']) ?></td>
            <td><?= htmlspecialchars($m['email']) ?></td>
            <td><?= htmlspecialchars($m['statut']) ?></td>
            <td>
                <button onclick="openForm(
                    <?= $m['id'] ?>, 
                    '<?= htmlspecialchars($m['numero_membre'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['statut'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['date_admission']) ?>',
                    '<?= htmlspecialchars($m['type'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['nom_entreprise'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['effectif']) ?>',
                    '<?= htmlspecialchars($m['classification'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['fonction'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['telephone'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['email'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['adresse'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['activites'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['besoins'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['personne_contact'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['relation_contact'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['telephone_contact'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['commentaires'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['nom'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['Prenom'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['Region'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($m['campagne_id']) ?>',
                    '<?= htmlspecialchars($m['caisse_id']) ?>',
                    '<?= htmlspecialchars($m['guichet_id']) ?>',
                    '<?= htmlspecialchars($m['a_beneficie_credit']) ?>'
                )">Modifier</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- Formulaire caché pour modifier le membre -->
<div id="editForm">
    <h3 style="text-align: center;">Modifier Membre</h3>
    <form method="POST">
        <input type="hidden" id="id" name="id">
        
        <label>Numéro Membre :</label>
        <input type="text" id="numero_membre" name="numero_membre" required>

        <label>Statut :</label>
        <select id="statut" name="statut" required>
            <option value="Actif">Actif</option>
            <option value="Inactif">Inactif</option>
            <option value="Suspendu">Suspendu</option>
            <option value="Radié">Radié</option>
        </select>

        <label>Date d'admission :</label>
        <input type="date" id="date_admission" name="date_admission" required>

        <label>Type :</label>
        <select id="type" name="type" onchange="toggleCompanyFields()" required>
            <option value="Particulier">Particulier</option>
            <option value="Entreprise">Entreprise</option>
            <option value="Association">Association</option>
            <option value="Groupement">Groupement</option>
            <option value="Etablissements">Etablissements et Institutions</option>
            <option value="Autres">Autres</option>
        </select>

        <div id="details_entreprise" class="hidden">
            <label>Nom Entreprise :</label>
            <input type="text" id="nom_entreprise" name="nom_entreprise">

            <label>Effectif :</label>
            <input type="number" id="effectif" name="effectif">

            <label>Classification :</label>
            <select id="classification" name="classification">
                <option value="PME">PME</option>
                <option value="Grande Entreprise">Grande Entreprise</option>
                <option value="Autre">Autre</option>
            </select>
        </div>

        <div id="fonction_field">
            <label>Fonction :</label>
            <input type="text" id="fonction" name="fonction">
        </div>

        <label>Nom :</label>
        <input type="text" id="nom" name="nom" required>

        <label>Prénom :</label>
        <input type="text" id="prenom" name="prenom" required>

        <label>Téléphone :</label>
        <input type="text" id="telephone" name="telephone" required>

        <label>Email :</label>
        <input type="email" id="email" name="email" required>

        <label>Adresse :</label>
        <input type="text" id="adresse" name="adresse" required>

        <label>Région :</label>
        <input type="text" id="region" name="region" required>

        <label>Activités :</label>
        <input type="text" id="activites" name="activites" required>

        <label>Besoins :</label>
        <div class="checkbox-group" id="besoins_container">
            <label><input type="checkbox" name="besoins[]" value="Crédit"> Crédit</label>
            <label><input type="checkbox" name="besoins[]" value="Épargne"> Épargne</label>
            <label><input type="checkbox" name="besoins[]" value="Partenariat"> Partenariat</label>
            <label><input type="checkbox" name="besoins[]" value="Mutuelle de santé"> Mutuelle de santé</label>
            <label><input type="checkbox" name="besoins[]" value="Service non financier"> Service non financier</label>
            <label><input type="checkbox" name="besoins[]" value="Autre"> Autre</label>
        </div>

        <label>Personne contact :</label>
        <input type="text" id="personne_contact" name="personne_contact" required>

        <label>Relation contact :</label>
        <select id="relation_contact" name="relation_contact">
            <option value="Famille">Famille</option>
            <option value="Amis">Amis</option>
            <option value="Professionnel">Professionnel</option>
            <option value="Garant">Garant</option>
            <option value="Autre">Autre</option>
        </select>

        <label>Téléphone contact :</label>
        <input type="text" id="telephone_contact" name="telephone_contact" required>

        <label>ID Campagne :</label>
        <input type="number" id="campagne_id" name="campagne_id">

        <label>ID Prospect :</label>
        <input type="number" id="prospect_id" name="prospect_id">

        <label>ID Caisse :</label>
        <input type="number" id="caisse_id" name="caisse_id">

        <label>ID Guichet :</label>
        <input type="number" id="guichet_id" name="guichet_id">

        <label>
            <input type="checkbox" id="a_beneficie_credit" name="a_beneficie_credit" value="1"> A bénéficié d'un crédit
        </label>

        <label>Commentaires :</label>
        <textarea id="commentaires" name="commentaires"></textarea>

        <button type="submit">Mettre à jour</button>
        <button type="button" onclick="closeForm()">Annuler</button>
    </form>
</div>

<script>
    function openForm(id, numero_membre, statut, date_admission, type, 
                     nom_entreprise, effectif, classification, fonction, 
                     telephone, email, adresse, activites, besoins, 
                     personne_contact, relation_contact, telephone_contact, 
                     commentaires, nom, prenom, region, campagne_id, 
                     prospect_id, caisse_id, guichet_id, a_beneficie_credit) {
        
        // Remplir les champs du formulaire
        document.getElementById('id').value = id;
        document.getElementById('numero_membre').value = numero_membre;
        document.getElementById('statut').value = statut;
        document.getElementById('date_admission').value = date_admission;
        document.getElementById('type').value = type;
        document.getElementById('nom_entreprise').value = nom_entreprise;
        document.getElementById('effectif').value = effectif;
        document.getElementById('classification').value = classification;
        document.getElementById('fonction').value = fonction;
        document.getElementById('telephone').value = telephone;
        document.getElementById('email').value = email;
        document.getElementById('adresse').value = adresse;
        document.getElementById('activites').value = activites;
        
        // Gérer les besoins (cases à cocher)
        const besoinsArray = besoins.split(',');
        const besoinsCheckboxes = document.querySelectorAll('input[name="besoins[]"]');
        besoinsCheckboxes.forEach(checkbox => {
            checkbox.checked = besoinsArray.includes(checkbox.value);
        });
        
        document.getElementById('personne_contact').value = personne_contact;
        document.getElementById('relation_contact').value = relation_contact;
        document.getElementById('telephone_contact').value = telephone_contact;
        document.getElementById('commentaires').value = commentaires;
        document.getElementById('nom').value = nom;
        document.getElementById('prenom').value = prenom;
        document.getElementById('region').value = region;
        document.getElementById('campagne_id').value = campagne_id;
        document.getElementById('prospect_id').value = prospect_id;
        document.getElementById('caisse_id').value = caisse_id;
        document.getElementById('guichet_id').value = guichet_id;
        document.getElementById('a_beneficie_credit').checked = (a_beneficie_credit == '1');
        
        // Afficher/masquer les champs selon le type
        toggleCompanyFields();
        
        // Afficher le formulaire
        document.getElementById('editForm').style.display = 'block';
        
        // Faire défiler jusqu'au formulaire
        document.getElementById('editForm').scrollIntoView({ behavior: 'smooth' });
    }

    function closeForm() {
        document.getElementById('editForm').style.display = 'none';
    }

    function toggleCompanyFields() {
        const type = document.getElementById('type').value;
        const companyFields = document.getElementById('details_entreprise');
        const fonctionField = document.getElementById('fonction_field');
        
        if (type === 'Entreprise' || type === 'Association' || type === 'Groupement' || type === 'Etablissements') {
            companyFields.classList.remove('hidden');
            fonctionField.classList.remove('hidden');
        } else {
            companyFields.classList.add('hidden');
            fonctionField.classList.add('hidden');
        }
    }
</script>
</body>
</html>