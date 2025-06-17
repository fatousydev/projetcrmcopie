<?php
session_start();
$conn = new PDO("mysql:host=localhost;dbname=crm", "root", "");

// Vérification de l'authentification et des droits admin
if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: connecter.php");
    exit;
}

// Traitement de la suppression
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $conn->prepare("DELETE FROM acces_caisses WHERE id = ?");
    $stmt->execute([$id]);
    
    // Message de confirmation
    $_SESSION['message'] = [
        'type' => 'success',
        'text' => 'L\'accès a été supprimé avec succès.'
    ];
    
    header("Location: liste_acces.php");
    exit;
}

// Récupération des accès
$stmt = $conn->query("
    SELECT a.id, u.nom as utilisateur, u.role, c.nom as caisse, c.statut
    FROM acces_caisses a
    JOIN utilisateurs u ON u.id = a.utilisateur_id
    JOIN caisses c ON c.id = a.caisse_id
    ORDER BY u.nom, c.nom
");
$acces = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des accès - PAMECAS CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #003366;
            --secondary-color: #65a3f3;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(101, 163, 243, 0.1);
        }
        
        .badge-status {
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .btn-danger-custom {
            background-color: var(--danger-color);
            border: none;
        }
        
        .btn-danger-custom:hover {
            background-color: #c82333;
        }
        
        .back-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0"><i class="fas fa-user-lock me-2"></i>Gestion des accès aux caisses</h3>
                        <a href="tableau_de_bord.php" class="btn btn-outline-light">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible fade show">
                                <?= $_SESSION['message']['text'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['message']); ?>
                        <?php endif; ?>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-user me-1"></i> Utilisateur</th>
                                        <th><i class="fas fa-user-tag me-1"></i> Rôle</th>
                                        <th><i class="fas fa-cash-register me-1"></i> Caisse</th>
                                        <th><i class="fas fa-info-circle me-1"></i> Statut</th>
                                        <th><i class="fas fa-cog me-1"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($acces) > 0): ?>
                                        <?php foreach ($acces as $a): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($a['utilisateur']) ?></td>
                                                <td><?= htmlspecialchars($a['role']) ?></td>
                                                <td><?= htmlspecialchars($a['caisse']) ?></td>
                                                <td>
                                                    <span class="badge-status bg-<?= $a['statut'] === 'active' ? 'success' : 'secondary' ?>">
                                                        <?= ucfirst($a['statut']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="?supprimer=<?= $a['id'] ?>" 
                                                       class="btn btn-sm btn-danger-custom"
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet accès ?')">
                                                        <i class="fas fa-trash-alt me-1"></i> Supprimer
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="fas fa-info-circle me-2"></i>Aucun accès trouvé
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirmation avant suppression
        function confirmDelete(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet accès ?')) {
                e.preventDefault();
            }
        }
        
        // Ajouter l'événement à tous les liens de suppression
        document.querySelectorAll('a[href*="supprimer"]').forEach(link => {
            link.addEventListener('click', confirmDelete);
        });
    </script>
</body>
</html>