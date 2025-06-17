<?php
include('conn.php');

// Récupérer toutes les campagnes
$stmt = $pdo->query("SELECT * FROM campagnes_marketing");
$campagnes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si un formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $nom_campagne = $_POST['nom_campagne'];
    $date_lancement = $_POST['date_lancement'];
    $date_cloture = $_POST['date_cloture'];
    $cible = $_POST['cible'];
    $canal_utilise = $_POST['canal_utilise'];
    $resultats = $_POST['resultats'];
    $id = $_POST['id'];

    $updateStmt = $pdo->prepare("UPDATE campagnes_marketing SET 
        nom_campagne = ?, date_lancement = ?, date_cloture = ?, cible = ?, 
        canal_utilise = ?, resultats = ?, updated_at = NOW() WHERE id = ?");
    
    if ($updateStmt->execute([$nom_campagne, $date_lancement, $date_cloture, $cible, $canal_utilise, $resultats, $id])) {
        echo "<p style='color:green;'>Campagne mise à jour avec succès.</p>";
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
    <title>Liste des ventes</title>
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
        <h1>PAMECAS - GESTION DES ventes</h1>
    </div>
    <a href="index.html" class="logout">Logout</a>
</header>

<div class="container">
    <h2>Liste des Campagnes Marketing</h2>
    
    <table border="1">
        <tr>
            <th>Nom</th>
            <th>Date Lancement</th>
            <th>Date Clôture</th>
            <th>Cible</th>
            <th>Canal Utilisé</th>
            <th>Résultats</th>
            <th>Action</th>
        </tr>

        <?php foreach ($campagnes as $campagne): ?>
        <tr>
            <td><?= htmlspecialchars($campagne['nom_campagne']) ?></td>
            <td><?= htmlspecialchars($campagne['date_lancement']) ?></td>
            <td><?= htmlspecialchars($campagne['date_cloture']) ?></td>
            <td><?= htmlspecialchars($campagne['cible']) ?></td>
            <td><?= htmlspecialchars($campagne['canal_utilise']) ?></td>
            <td><?= htmlspecialchars($campagne['resultats']) ?></td>
            <td>
                <!-- Bouton Modifier qui ouvre un formulaire -->
                <button onclick="openForm(<?= $campagne['id'] ?>, '<?= addslashes($campagne['nom_campagne']) ?>', '<?= $campagne['date_lancement'] ?>', '<?= $campagne['date_cloture'] ?>', '<?= addslashes($campagne['cible']) ?>', '<?= addslashes($campagne['canal_utilise']) ?>', '<?= addslashes($campagne['resultats']) ?>')">Modifier</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Formulaire caché pour modifier la campagne -->
    <div id="editForm" style="display:none;">
        <h3>Modifier Campagne</h3>
        <form method="POST">
            <input type="hidden" id="id" name="id">
            
            <label>Nom :</label>
            <input type="text" id="nom_campagne" name="nom_campagne" required><br>

            <label>Date Lancement :</label>
            <input type="date" id="date_lancement" name="date_lancement" required><br>

            <label>Date Clôture :</label>
            <input type="date" id="date_cloture" name="date_cloture" required><br>

            <label>Cible :</label>
            <select id="cible" name="cible" required>
                <option value="Prospect">Prospect</option>
                <option value="Membre">Membre</option>
            </select><br>

            <label>Canal Utilisé :</label>
            <select id="canal_utilise" name="canal_utilise" required>
                <option value="SMS">SMS</option>
                <option value="Appel">Appel</option>
                <option value="WhatsApp">WhatsApp</option>
                <option value="Email">Email</option>
            </select><br>

            <label>Résultats :</label>
            <input type="text" id="resultats" name="resultats" required><br>

            <button type="submit">Mettre à jour</button>
            <button type="button" onclick="closeForm()">Annuler</button>
        </form>
    </div>

    <script>
        function openForm(id, nom, date_lancement, date_cloture, cible, canal, resultats) {
            document.getElementById('id').value = id;
            document.getElementById('nom_campagne').value = nom;
            document.getElementById('date_lancement').value = date_lancement;
            document.getElementById('date_cloture').value = date_cloture;
            document.getElementById('cible').value = cible;
            document.getElementById('canal_utilise').value = canal;
            document.getElementById('resultats').value = resultats;

            document.getElementById('editForm').style.display = 'block';
        }

        function closeForm() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>
</html>