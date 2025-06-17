<?php 
// Connexion à la base de données avec PDO
$pdo = new PDO('mysql:host=localhost;dbname=crm;charset=utf8', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Récupérer toutes les ventes avec les informations des membres et produits
$stmt = $pdo->query("
    SELECT v.id, m.numero_membre, p.nom_produit, v.quantite, v.date_vente 
    FROM ventes v
    JOIN membres m ON v.membre_id = m.id
    JOIN produits p ON v.produit_id = p.id
");
$ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les informations des membres (numéro_membre) et des produits (nom_produit)
$stmt = $pdo->query("SELECT id, numero_membre FROM membres");
$membres = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT id, nom_produit FROM produits");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si un formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $membre_id = $_POST['membre_id'];
    $produit_id = $_POST['produit_id'];
    $quantite = $_POST['quantite'];
    $date_vente = $_POST['date_vente'];
    $id = $_POST['id'];

    $updateStmt = $pdo->prepare("UPDATE ventes SET 
        membre_id = ?, produit_id = ?, quantite = ?, date_vente = ?, 
        updated_at = NOW() WHERE id = ?");
    
    if ($updateStmt->execute([$membre_id, $produit_id, $quantite, $date_vente, $id])) {
        echo "<p style='color:green;'>Vente mise à jour avec succès.</p>";
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
    <h2>Liste des Ventes</h2>
    
    <table border="1">
        <tr>
            <th>Id</th>
            <th>Numéro Membre</th>
            <th>Nom Produit</th>
            <th>Quantité</th>
            <th>Date Vente</th>
            <th>Action</th>
        </tr>

        <?php foreach ($ventes as $vente): ?>
        <tr>
            <td><?= htmlspecialchars($vente['id']) ?></td>
            <td><?= htmlspecialchars($vente['numero_membre']) ?></td>
            <td><?= htmlspecialchars($vente['nom_produit']) ?></td>
            <td><?= htmlspecialchars($vente['quantite']) ?></td>
            <td><?= htmlspecialchars($vente['date_vente']) ?></td>
            <td>
                <button onclick="openForm(
                    <?= $vente['id'] ?>, 
                    '<?= htmlspecialchars($vente['numero_membre'], ENT_QUOTES) ?>', 
                    '<?= htmlspecialchars($vente['nom_produit'], ENT_QUOTES) ?>', 
                    '<?= htmlspecialchars($vente['quantite']) ?>', 
                    '<?= htmlspecialchars($vente['date_vente']) ?>'
                )">Modifier</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Formulaire caché pour modifier la vente -->
    <div id="editForm">
        <h3>Modifier Vente</h3>
        <form method="POST">
            <input type="hidden" id="id" name="id">
            
            <label>Membre :</label>
            <select id="membre_id" name="membre_id" required>
                <?php foreach ($membres as $membre): ?>
                    <option value="<?= $membre['id'] ?>"><?= htmlspecialchars($membre['numero_membre']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Produit :</label>
            <select id="produit_id" name="produit_id" required>
                <?php foreach ($produits as $produit): ?>
                    <option value="<?= $produit['id'] ?>"><?= htmlspecialchars($produit['nom_produit']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Quantité :</label>
            <input type="number" id="quantite" name="quantite" required>

            <label>Date Vente :</label>
            <input type="date" id="date_vente" name="date_vente" required>

            <button type="submit">Mettre à jour</button>
            <button type="button" onclick="closeForm()">Annuler</button>
        </form>
    </div>

    <script>
        function openForm(id, numero_membre, nom_produit, quantite, date_vente) {
            document.getElementById('id').value = id;
            
            // Sélectionner le bon membre et produit
            const membreSelect = document.getElementById('membre_id');
            for (let i = 0; i < membreSelect.options.length; i++) {
                if (membreSelect.options[i].text === numero_membre) {
                    membreSelect.selectedIndex = i;
                    break;
                }
            }
            
            const produitSelect = document.getElementById('produit_id');
            for (let i = 0; i < produitSelect.options.length; i++) {
                if (produitSelect.options[i].text === nom_produit) {
                    produitSelect.selectedIndex = i;
                    break;
                }
            }
            
            document.getElementById('quantite').value = quantite;
            document.getElementById('date_vente').value = date_vente;

            document.getElementById('editForm').style.display = 'block';
            
            // Faire défiler jusqu'au formulaire
            document.getElementById('editForm').scrollIntoView({ behavior: 'smooth' });
        }

        function closeForm() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>
</html>