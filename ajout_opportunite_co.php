<?php
// Activation des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crm";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $membre_id = $conn->real_escape_string($_POST["membre_id"]);
    $produit_id = $conn->real_escape_string($_POST["produit_id"]);
    $utilisateur_id = $conn->real_escape_string($_POST["utilisateur_id"]);
    $caisse_id = $conn->real_escape_string($_POST["caisse_id"]);
    $type_opportunite = $conn->real_escape_string($_POST["type_opportunite"]);
    $statut = $conn->real_escape_string($_POST["statut"]);
    $date_creation = date("Y-m-d H:i:s");

    if (empty($membre_id) || empty($produit_id) || empty($utilisateur_id) || empty($caisse_id) || empty($type_opportunite) || empty($statut)) {
        echo "<script>alert('Veuillez remplir tous les champs !');</script>";
    } else {
        $sql = "INSERT INTO opportunites (membre_id, produit_id, utilisateur_id, caisse_id, type_opportunite, statut, date_creation) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erreur SQL : " . $conn->error);
        }
        $stmt->bind_param("iiiisss", $membre_id, $produit_id, $utilisateur_id, $caisse_id, $type_opportunite, $statut, $date_creation);
        if ($stmt->execute()) {
            echo "<script>alert('Opportunité ajoutée avec succès !'); window.location.href='liste_opportunites.php';</script>";
            exit();
        } else {
            echo "<script>alert('Erreur : " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    }
}

// Récupération des données pour les listes déroulantes
$membres = $conn->query("SELECT id, numero_membre, nom FROM membres");
$produits = $conn->query("SELECT id, nom_produit FROM produits");
$utilisateurs = $conn->query("SELECT id, nom, role FROM utilisateurs");
$caisses = $conn->query("SELECT id, nom FROM caisses");

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter Opportunité - PAMECAS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
    header {
        background-color: #003366; color: white; padding: 10px 20px;
        display: flex; justify-content: space-between; align-items: center;
    }
    header .logo img { height: 50px; margin-right: 10px; }
    header .logo h1 { font-size: 1.3rem; margin: 0; }
    .logout-btn {
        background-color: #ec0909; color: white; border: none;
        padding: 8px 15px; border-radius: 4px; text-decoration: none;
    }
    .form-container {
        max-width: 800px; margin: 30px auto; background: white;
        padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .form-title { color: #003366; text-align: center; margin-bottom: 30px; }
    .form-group label { font-weight: 500; margin-bottom: 8px; }
    .btn-submit {
        background-color: #65a3f3; color: white; border: none;
        padding: 10px 20px; border-radius: 4px; font-weight: 500;
    }
    .btn-submit:hover { background-color: #4a8fe0; }
    .btn-back {
        background-color: #6c757d; color: white; padding: 10px 20px;
        border-radius: 4px; text-decoration: none; margin-right: 10px;
    }
    @media (max-width: 768px) {
        .form-container { padding: 15px; }
    }
  </style>
</head>
<body>
<header>
    <div class="logo d-flex align-items-center">
        <img src="pamecas.jpg" alt="Pamecas">
        <h1>PAMECAS - GESTION DES OPPORTUNITÉS</h1>
    </div>
    <a href="index.html" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>Logout
    </a>
</header>

<main>
    <div class="form-container">
        <h2 class="form-title"><i class="fas fa-plus-circle"></i> Ajouter une opportunité</h2>
        <form action="ajout_opportunite.php" method="POST">
            <div class="form-group mb-3">
                <label for="membre_id">Numéro membre :</label>
                <select name="membre_id" class="form-control" required>
                    <option value="">-- Sélectionner --</option>
                    <?php while ($row = $membres->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['numero_membre'] ?> - <?= $row['nom'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="produit_id">Produit :</label>
                <select name="produit_id" class="form-control" required>
                    <option value="">-- Sélectionner --</option>
                    <?php while ($row = $produits->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['nom_produit'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="utilisateur_id">Utilisateur :</label>
                <select name="utilisateur_id" class="form-control" required>
                    <option value="">-- Sélectionner --</option>
                    <?php while ($row = $utilisateurs->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['nom'] ?> (<?= $row['role'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="caisse_id">Caisse :</label>
                <select name="caisse_id" class="form-control" required>
                    <option value="">-- Sélectionner --</option>
                    <?php while ($row = $caisses->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['nom'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="type_opportunite">Type d'opportunité :</label>
                <select name="type_opportunite" class="form-control" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="Crédit">Crédit</option>
                    <option value="Partenariat">Partenariat</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label for="statut">Statut :</label>
                <select name="statut" class="form-control" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="En cours">En cours</option>
                    <option value="Validée">Validée</option>
                    <option value="Rejetée">Rejetée</option>
                </select>
            </div>
            <div class="d-flex justify-content-between">
                <a href="interfacecommercial.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</main>
</body>
</html>
