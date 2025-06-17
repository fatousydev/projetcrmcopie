


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter Vente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
/* Formulaire d'ajout de client */
.form-client {
      margin-bottom:100px;
      padding: 15px;
      background-color: #f4f4f4;
      border-radius: 5px;
    }
    .form-client input, .form-client select, .form-client textarea {
      margin: 10px 0;
      padding: 10px;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    /* En-tête */
    header {
      background-color: #65a3f3;
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
    header .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    header .user-info span {
      background-color: #0368d9;
      border-radius: 50%;
      padding: 10px 15px;
      font-weight: bold;
      color: white;
    }
    header .logout {
      background-color: rgb(236, 9, 9);
      color:white;
      padding: 5px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    /* Contenu principal */
    main {
      padding: 20px;
      background-color:#2c3e50;
    }

    /* Tableau des clients */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    table, th, td {
      border: 1px solid #ccc;
    }
    th, td {
      padding: 10px;
      text-align: left;
    }
  </style>
</head>
<body>

<header>
    <div class="logo">
      <img src="pamecas.jpg" alt="Pamecas" width="70">
      <h1>PAMECAS - GESTION DE LA RELATION CLIENT</h1>
    </div>
    <div class="user-info">
      <span>A</span>
      <a href="index.html" class="logout">Logout</a>
    </div>
  </header>
  <main>
  <h2 style="text-align: center; color:white" >Ajouter interacaction</h2>
  <div  class="form-client">
  <form action="traitement_vente.php" method="POST">
    <label>Campagne associée:</label>
    <select name="id_campagne" required>
        <option value="">Sélectionner une interaction</option>
        <?php
        $pdo = new PDO("mysql:host=localhost;dbname=crm", "root", "");
        $campagnes = $pdo->query("SELECT * FROM campagnes_marketing")->fetchAll();
        foreach ($campagnes as $campagne) {
            echo "<option value='" . $campagne['id'] . "'>" . $campagne['nom_campagne'] . "</option>";
        }
        ?>
    </select><br><br>

    <label>Type de cible:</label>
    <select name="type_cible" id="type_cible" onchange="toggleSelect()" required>
        <option value="">Sélectionner</option>
        <option value="membres">Membre</option>
        <option value="prospects">Prospect</option>
    </select><br><br>

    <div id="membre_select" style="display:none;">
        <label>Choisir un membre:</label>
        <select name="id_membre">
            <option value="">Sélectionner un membre</option>
            <?php
            $membres = $pdo->query("SELECT * FROM membres")->fetchAll();
            foreach ($membres as $membre) {
                echo "<option value='" . $membre['id'] . "'>" . $membre['nom'] . " " . $membre['prenom'] . "</option>";
            }
            ?>
        </select><br><br>
    </div>

    <div id="prospect_select" style="display:none;">
        <label>Choisir un prospect:</label>
        <select name="id_prospect">
            <option value="">Sélectionner un prospect</option>
            <?php
            $prospects = $pdo->query("SELECT * FROM prospects")->fetchAll();
            foreach ($prospects as $prospect) {
                echo "<option value='" . $prospect['id'] . "'>" . $prospect['nom'] . " " . $prospect['prenom'] . "</option>";
            }
            ?>
        </select><br><br>
    </div>

    <label>Canal interaction:</label>
    <select name="canal_interaction" id="canal_interaction">
        <option value="Appel">Appel</option>
        <option value="SMS">SMS</option>
        <option value="EMAIL">EMAIL</option>
        <option value="WhatsApp">WhatsApp</option>
    </select><br><br>

    <label>Date d'interaction:</label>
    <input type="date" name="date_interaction" required><br><br>

    <label>Description:</label>
    <textarea name="description" required></textarea><br><br>

    <input type="submit" value="Ajouter l'interaction">
    <a href="interfaceadmin.php" class="btn btn-secondary">Retour</a>
</form>

<script>
function toggleSelect() {
    var type = document.getElementById("type_cible").value;
    if (type === "membres") {
        document.getElementById("membre_select").style.display = "block";
        document.getElementById("prospect_select").style.display = "none";
    } else if (type === "prospects") {
        document.getElementById("membre_select").style.display = "none";
        document.getElementById("prospect_select").style.display = "block";
    } else {
        document.getElementById("membre_select").style.display = "none";
        document.getElementById("prospect_select").style.display = "none";
    }
}
</script>

</body>
</html>
