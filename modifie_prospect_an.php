<?php
include('conn.php');

// Récupérer tous les prospects
$stmt = $pdo->query("SELECT * FROM prospects");
$prospects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si un formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $statut = $_POST['statut'];
    $enregistre_par = $_POST['enregistre_par'];
    $agence_concernee = $_POST['agence_concernee'];
    $date_enregistrement = $_POST['date_enregistrement'];
    $type = $_POST['type'];
    $nom_entreprise = $_POST['nom_entreprise'];
    $effectif = $_POST['effectif'];
    $classification = $_POST['classification'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $fonction = $_POST['fonction'];
    $telephone = $_POST['telephone'];
    $telephone_whatsapp = $_POST['telephone_whatsapp'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];
    $activites = $_POST['activites'];
    $besoins = $_POST['besoins'];
    $source_connaissance = $_POST['source_connaissance'];
    $numero_membre = $_POST['numero_membre'];
    $personne_contact = $_POST['personne_contact'];
    $relation_contact = $_POST['relation_contact'];
    $telephone_contact = $_POST['telephone_contact'];
    $commentaires = $_POST['commentaires'];

    $updateStmt = $pdo->prepare("UPDATE prospects SET 
        statut = ?, enregistre_par = ?, agence_concernee = ?, date_enregistrement = ?, 
        type = ?, nom_entreprise = ?, effectif = ?, classification = ?, nom = ?, 
        prenom = ?, fonction = ?, telephone = ?, telephone_whatsapp = ?, email = ?, 
        adresse = ?, activites = ?, besoins = ?, source_connaissance = ?, 
        numero_membre = ?, personne_contact = ?, relation_contact = ?, 
        telephone_contact = ?, commentaires = ?, updated_at = NOW() 
        WHERE id = ?");
    
    if ($updateStmt->execute([
        $statut, $enregistre_par, $agence_concernee, $date_enregistrement, 
        $type, $nom_entreprise, $effectif, $classification, $nom, 
        $prenom, $fonction, $telephone, $telephone_whatsapp, $email, 
        $adresse, $activites, $besoins, $source_connaissance, 
        $numero_membre, $personne_contact, $relation_contact, 
        $telephone_contact, $commentaires, $id
    ])) {
        echo "<p style='color:green;'>Prospect mis à jour avec succès.</p>";
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
    <title>Liste des Prospects</title>
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
        <h1>PAMECAS - GESTION DE LA RELATION CLIENT</h1>
    </div>
    <a href="index.html" class="logout">Logout</a>
</header>

<div class="container">
    <h1>Liste des Prospects</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>

        <?php foreach ($prospects as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['id']) ?></td>
            <td><?= htmlspecialchars($p['nom']) ?></td>
            <td><?= htmlspecialchars($p['prenom']) ?></td>
            <td><?= htmlspecialchars($p['telephone']) ?></td>
            <td><?= htmlspecialchars($p['email']) ?></td>
            <td><?= htmlspecialchars($p['statut']) ?></td>
            <td>
                <button onclick="openForm(
                    <?= $p['id'] ?>, 
                    '<?= htmlspecialchars($p['statut'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['enregistre_par'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['agence_concernee'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['date_enregistrement']) ?>',
                    '<?= htmlspecialchars($p['type'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['nom_entreprise'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['effectif']) ?>',
                    '<?= htmlspecialchars($p['classification'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['nom'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['prenom'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['fonction'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['telephone'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['telephone_whatsapp']) ?>',
                    '<?= htmlspecialchars($p['email'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['adresse'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['activites'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['besoins'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['source_connaissance'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['numero_membre']) ?>',
                    '<?= htmlspecialchars($p['personne_contact'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['relation_contact'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['telephone_contact'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($p['commentaires'], ENT_QUOTES) ?>'
                )">Modifier</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- Formulaire caché pour modifier le prospect -->
<div id="editForm">
    <h3 style="text-align: center;">Modifier Prospect</h3>
    <form method="POST">
        <input type="hidden" id="id" name="id">
        
        <label>Statut :</label>
        <select id="statut" name="statut" required>
            <option value="Prospect">Prospect</option>
            <option value="En cours de négociation">En cours de négociation</option>
            <option value="Perdu">Perdu</option>
            <option value="Membre">Membre</option>
        </select>

        <label>Enregistré par :</label>
        <input type="text" id="enregistre_par" name="enregistre_par" required>

        <label>Agence concernée :</label>
        <select id="agence_concernee" name="agence_concernee" required>
            <option value="Agence principale">Agence principale</option>
            <option value="Point de service 1">Point de service 1</option>
            <option value="Point de service 2">Point de service 2</option>
        </select>

        <label>Date d'enregistrement :</label>
        <input type="date" id="date_enregistrement" name="date_enregistrement" required>

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

        <label>Nom :</label>
        <input type="text" id="nom" name="nom">

        <label>Prénom :</label>
        <input type="text" id="prenom" name="prenom">

        <div id="fonction_field" class="hidden">
            <label>Fonction :</label>
            <input type="text" id="fonction" name="fonction">
        </div>

        <label>Téléphone :</label>
        <input type="text" id="telephone" name="telephone" required>

        <label>
            <input type="checkbox" id="telephone_whatsapp" name="telephone_whatsapp" value="1"> WhatsApp
        </label>

        <label>Email :</label>
        <input type="email" id="email" name="email" required>

        <label>Adresse :</label>
        <input type="text" id="adresse" name="adresse">

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

        <label>Source de connaissance :</label>
        <select id="source_connaissance" name="source_connaissance">
            <option value="Bouche à oreille">Bouche à oreille</option>
            <option value="Agence">Agence</option>
            <option value="Prospection">Prospection</option>
            <option value="Famille ou amis">Famille ou amis</option>
            <option value="Internet">Internet</option>
            <option value="Site web">Site web</option>
            <option value="Facebook">Facebook</option>
            <option value="Instagram">Instagram</option>
            <option value="TikTok">TikTok</option>
            <option value="LinkedIn">LinkedIn</option>
            <option value="Autre">Autre</option>
        </select>

        <label>Numéro membre (si conversion) :</label>
        <input type="text" id="numero_membre" name="numero_membre">

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

        <label>Commentaires :</label>
        <textarea id="commentaires" name="commentaires"></textarea>

        <button type="submit">Mettre à jour</button>
        <button type="button" onclick="closeForm()">Annuler</button>
    </form>
</div>

<script>
    function openForm(id, statut, enregistre_par, agence_concernee, date_enregistrement, type, 
                     nom_entreprise, effectif, classification, nom, prenom, fonction, 
                     telephone, telephone_whatsapp, email, adresse, activites, besoins, 
                     source_connaissance, numero_membre, personne_contact, relation_contact, 
                     telephone_contact, commentaires) {
        
        // Remplir les champs du formulaire
        document.getElementById('id').value = id;
        document.getElementById('statut').value = statut;
        document.getElementById('enregistre_par').value = enregistre_par;
        document.getElementById('agence_concernee').value = agence_concernee;
        document.getElementById('date_enregistrement').value = date_enregistrement;
        document.getElementById('type').value = type;
        document.getElementById('nom_entreprise').value = nom_entreprise;
        document.getElementById('effectif').value = effectif;
        document.getElementById('classification').value = classification;
        document.getElementById('nom').value = nom;
        document.getElementById('prenom').value = prenom;
        document.getElementById('fonction').value = fonction;
        document.getElementById('telephone').value = telephone;
        document.getElementById('telephone_whatsapp').checked = (telephone_whatsapp == '1');
        document.getElementById('email').value = email;
        document.getElementById('adresse').value = adresse;
        document.getElementById('activites').value = activites;
        
        // Gérer les besoins (cases à cocher)
        const besoinsArray = besoins.split(',');
        const besoinsCheckboxes = document.querySelectorAll('input[name="besoins[]"]');
        besoinsCheckboxes.forEach(checkbox => {
            checkbox.checked = besoinsArray.includes(checkbox.value);
        });
        
        document.getElementById('source_connaissance').value = source_connaissance;
        document.getElementById('numero_membre').value = numero_membre;
        document.getElementById('personne_contact').value = personne_contact;
        document.getElementById('relation_contact').value = relation_contact;
        document.getElementById('telephone_contact').value = telephone_contact;
        document.getElementById('commentaires').value = commentaires;
        
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