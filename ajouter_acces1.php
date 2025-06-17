<?php
session_start();
$conn = new PDO("mysql:host=localhost;dbname=crm", "root", "");

// Vérification de l'authentification et des droits admin
if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: connecter.php");
    exit;
}

// Traitement du formulaire
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $utilisateur_id = $_POST['utilisateur_id'];
    $caisse_id = $_POST['caisse_id'];

    $check = $conn->prepare("SELECT * FROM acces_caisses WHERE utilisateur_id=? AND caisse_id=?");
    $check->execute([$utilisateur_id, $caisse_id]);

    if ($check->rowCount() === 0) {
        $stmt = $conn->prepare("INSERT INTO acces_caisses (utilisateur_id, caisse_id) VALUES (?, ?)");
        $stmt->execute([$utilisateur_id, $caisse_id]);
        $message = '<div class="alert alert-success">Accès ajouté avec succès.</div>';
    } else {
        $message = '<div class="alert alert-warning">Cet accès existe déjà.</div>';
    }
}

// Récupération des données
$utilisateurs = $conn->query("SELECT * FROM utilisateurs WHERE role != 'admin'")->fetchAll();
$caisses = $conn->query("SELECT * FROM caisses")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un accès - PAMECAS CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #003366;
            --secondary-color: #65a3f3;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
        }
        
        .card-form {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .btn-primary-custom {
            background-color: var(--secondary-color);
            border: none;
        }
        
        .btn-primary-custom:hover {
            background-color: #4a8fe0;
        }
        
        .form-select, .form-control {
            border-radius: 5px;
            padding: 10px;
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
            <div class="col-lg-8">
                <div class="card card-form">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-user-shield me-2"></i>Ajouter un accès</h3>
                    </div>
                    <div class="card-body">
                        <?= $message ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="utilisateur_id" class="form-label fw-bold">
                                    <i class="fas fa-user me-2"></i>Utilisateur
                                </label>
                                <select class="form-select" id="utilisateur_id" name="utilisateur_id" required>
                                    <?php foreach ($utilisateurs as $u): ?>
                                        <option value="<?= $u['id'] ?>">
                                            <?= htmlspecialchars($u['nom']) ?> (<?= htmlspecialchars($u['role']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="caisse_id" class="form-label fw-bold">
                                    <i class="fas fa-cash-register me-2"></i>Caisse
                                </label>
                                <select class="form-select" id="caisse_id" name="caisse_id" required>
                                    <?php foreach ($caisses as $c): ?>
                                        <option value="<?= $c['id'] ?>">
                                            <?= htmlspecialchars($c['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="tableau_de_bord1.php" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-arrow-left me-1"></i> Retour
                                </a>
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="fas fa-plus-circle me-1"></i> Ajouter l'accès
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation du formulaire
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
        })();
    </script>
</body>
</html>