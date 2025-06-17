<?php
// Activer les erreurs pour le debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'pdo.php';

// Traitement de l'ajout
if (isset($_POST['ajouter'])) {
    $nom = $_POST['nom'] ?? null;
    $localisation = $_POST['localisation'] ?? null;

    if ($nom && $localisation) {
        $stmt = $pdo->prepare("INSERT INTO caisses (nom, localisation) VALUES (?, ?)");
        $stmt->execute([$nom, $localisation]);
        // Redirection pour éviter la double soumission
        header("Location: gestion_caisses.php");
        exit();
    } else {
        $erreur = "Tous les champs sont obligatoires.";
    }
}

// Traitement de l'activation/désactivation
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $get = $pdo->prepare("SELECT statut FROM caisses WHERE id=?");
    $get->execute([$id]);
    $statut = $get->fetchColumn();
    $newStatut = ($statut === 'active') ? 'inactive' : 'active';
    $upd = $pdo->prepare("UPDATE caisses SET statut=? WHERE id=?");
    $upd->execute([$newStatut, $id]);
    header("Location: gestion_caisses.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Caisses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-retour {
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <!-- Bouton Retour -->
        <a href="javascript:history.back()" class="btn btn-secondary btn-retour">
            <i class="bi bi-arrow-left"></i> Retour
        </a>

        <h2 class="mb-4">Gestion des Caisses</h2>

        <!-- Message d'erreur -->
        <?php if (!empty($erreur)) : ?>
            <div class="alert alert-danger"><?= $erreur ?></div>
        <?php endif; ?>

        <!-- Formulaire d'ajout -->
        <form method="post" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="nom" class="form-control" placeholder="Nom de la caisse" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="localisation" class="form-control" placeholder="Localisation de la caisse" required>
            </div>
            <div class="col-md-2">
                <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
            </div>
        </form>

        <!-- Affichage des caisses -->
        <table class="table table-bordered bg-white shadow">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Statut</th>
                    <th>Localisation</th>
                    <th>Date création</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM caisses ORDER BY date_creation DESC");
                foreach ($stmt as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['nom']) ?></td>
                        <td>
                            <span class="badge bg-<?= $row['statut'] == 'active' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($row['statut']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($row['localisation']) ?></td>
                        <td><?= $row['date_creation'] ?></td>
                        <td>
                            <a href="?toggle=<?= $row['id'] ?>" class="btn btn-sm btn-outline-<?= $row['statut'] == 'active' ? 'danger' : 'success' ?>">
                                <?= $row['statut'] == 'active' ? 'Désactiver' : 'Activer' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Icônes Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>