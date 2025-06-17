<?php
include('conn.php');

// Récupérer tous les produits
$stmt = $pdo->query("SELECT * FROM produits");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si un formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_produit = $_POST['nom_produit'];
    $type_produit = $_POST['type_produit'];
    $description = $_POST['description'];
    $conditions = $_POST['conditions'];
    $taux_interet = $_POST['taux_interet'];
    $plafond_montant = $_POST['plafond_montant'];
    $id_produit = $_POST['id_produit'];

    $updateStmt = $pdo->prepare("UPDATE produits SET 
        nom_produit = ?, type_produit = ?, description = ?, conditions = ?, 
        taux_interet = ?, plafond_montant = ?, updated_at = NOW() WHERE id = ?");
    
    if ($updateStmt->execute([$nom_produit, $type_produit, $description, $conditions, $taux_interet, $plafond_montant, $id_produit])) {
        echo "<script>alert('Produit mis à jour avec succès.'); window.location.href='liste_produit.php';</script>";
    } else {
        echo "<script>alert('Erreur lors de la mise à jour.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Produits</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 0; }
        header {
            background-color: #003366;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header .logo { display: flex; align-items: center; gap: 15px; }
        header .logout {
            background: rgb(236, 9, 9);
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .container {
            margin: 20px;
            background: white;
            padding: 20px;
            border-radius: 5px;
            overflow-x: auto;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
        }
        th {
            background-color: #003366;
            color: white;
        }
        button {
            padding: 5px 10px;
            cursor: pointer;
        }

        /* Modal style */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 80px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            width: 60%;
        }
        .close {
            color: red;
            float: right;
            font-size: 25px;
            font-weight: bold;
            cursor: pointer;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <img src="pamecas.jpg" width="60" alt="logo">
        <h2>PAMECAS - GESTION DES PRODUITS</h2>
    </div>
    <a href="index.html" class="logout">Logout</a>
</header>

<div class="container">
    <h2>Liste des Produits</h2>

    <table>
        <tr>
            <th>Nom</th>
            <th>Type</th>
            <th>Description</th>
            <th>Conditions</th>
            <th>Taux d'intérêt</th>
            <th>Plafond</th>
            <th>Action</th>
        </tr>
        <?php foreach ($produits as $produit): ?>
        <tr>
            <td><?= htmlspecialchars($produit['nom_produit']) ?></td>
            <td><?= htmlspecialchars($produit['type_produit']) ?></td>
            <td><?= htmlspecialchars($produit['description']) ?></td>
            <td><?= htmlspecialchars($produit['conditions']) ?></td>
            <td><?= htmlspecialchars($produit['taux_interet']) ?></td>
            <td><?= htmlspecialchars($produit['plafond_montant']) ?></td>
            <td>
                <button onclick="openModal(
                    <?= $produit['id'] ?>, 
                    '<?= htmlspecialchars($produit['nom_produit'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($produit['type_produit'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($produit['description'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($produit['conditions'], ENT_QUOTES) ?>',
                    '<?= $produit['taux_interet'] ?>',
                    '<?= $produit['plafond_montant'] ?>'
                )">Modifier</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Modifier Produit</h3>
        <form method="POST">
            <input type="hidden" id="id_produit" name="id_produit">

            <label>Nom :</label>
            <input type="text" id="nom_produit" name="nom_produit" required>

            <label>Type :</label>
            <select id="type_produit" name="type_produit" required>
                <option value="Crédit">Crédit</option>
                <option value="Épargne">Épargne</option>
                <option value="Assurance">Assurance</option>
                <option value="Autre">Autre</option>
            </select>

            <label>Description :</label>
            <input type="text" id="description" name="description" required>

            <label>Conditions :</label>
            <input type="text" id="conditions" name="conditions" required>

            <label>Taux d'intérêt :</label>
            <input type="text" id="taux_interet" name="taux_interet" required>

            <label>Plafond montant :</label>
            <input type="text" id="plafond_montant" name="plafond_montant" required>

            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</div>

<script>
    function openModal(id, nom, type, description, conditions, taux, plafond) {
        document.getElementById('editModal').style.display = 'block';
        document.getElementById('id_produit').value = id;
        document.getElementById('nom_produit').value = nom;
        document.getElementById('type_produit').value = type;
        document.getElementById('description').value = description;
        document.getElementById('conditions').value = conditions;
        document.getElementById('taux_interet').value = taux;
        document.getElementById('plafond_montant').value = plafond;
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Fermer le modal si on clique en dehors
    window.onclick = function(event) {
        let modal = document.getElementById('editModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
