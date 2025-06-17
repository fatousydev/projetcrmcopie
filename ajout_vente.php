<?php
$conn = new mysqli('localhost', 'root', '', 'crm');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
?>
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
    .form-client {
      margin-bottom: 100px;
      padding: 15px;
      background-color: #f4f4f4;
      border-radius: 5px;
    }
    .form-client input, .form-client select {
      margin: 10px 0;
      padding: 10px;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
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
      color: white;
      padding: 5px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    main {
      padding: 20px;
      background-color: #2c3e50;
    }
    h2 {
      text-align: center;
      color: white;
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
    <a href="accueil.html" class="logout">Logout</a>
  </div>
</header>

<main>
  <h2>Ajouter une Vente</h2>
  <div class="form-client">
    <form action="traitement_vente.php" method="POST">

      <!-- Membre -->
      <label for="membre_id">Numéro membre</label>
      <select class="form-select" name="membre_id" required>
        <option value="">Choisir un membre</option>
        <?php
        $result = $conn->query("SELECT id, numero_membre FROM membres");
        while($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['numero_membre']}</option>";
        }
        ?>
      </select>

      <!-- Produit -->
      <label for="produit_id">Nom produit</label>
      <select class="form-select" name="produit_id" required>
        <option value="">Choisir un produit</option>
        <?php
        $result = $conn->query("SELECT id, nom_produit FROM produits");
        while($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['nom_produit']}</option>";
        }
        ?>
      </select>

      <!-- Utilisateur -->
      <label for="utilisateur_id">Utilisateur (Nom - Rôle)</label>
      <select class="form-select" name="utilisateur_id" required>
        <option value="">Choisir un utilisateur</option>
        <?php
        $result = $conn->query("SELECT id, nom, role FROM utilisateurs");
        while($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['nom']} - {$row['role']}</option>";
        }
        ?>
      </select>

      <!-- Quantité -->
      <label for="quantite">Quantité</label>
      <input type="number" name="quantite" min="1" required>

      <!-- Date vente -->
      <label for="date_vente">Date de Vente</label>
      <input type="date" name="date_vente" required>

      <button type="submit" class="btn btn-primary">Ajouter</button>
      <a href="interfaceadmin.php" class="btn btn-secondary">Voir les ventes</a>
    </form>
  </div>
</main>
</body>
</html>
