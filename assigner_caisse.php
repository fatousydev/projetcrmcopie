<?php
include "lien.php";

// Récupérer l'ID utilisateur depuis la requête GET
$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    die("ID utilisateur manquant");
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caisse_id = $_POST['caisse_id'];
    $user_id = $_POST['user_id']; // Récupéré depuis le formulaire
    
    $stmt = $conn->prepare("UPDATE utilisateurs SET caisse_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $caisse_id, $user_id);
    
    if ($stmt->execute()) {
        // Retourner une réponse JSON pour la modal
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $conn->error]);
        exit();
    }
}

// Récupérer les infos utilisateur
$user = $conn->query("SELECT * FROM utilisateurs WHERE id = $user_id")->fetch_assoc();

// Récupérer toutes les caisses
$caisses = $conn->query("SELECT * FROM caisses");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .modal-content {
            border-radius: 10px;
        }
        .modal-header {
            background-color: #65a3f3;
            color: white;
            border-bottom: none;
            border-radius: 10px 10px 0 0;
        }
        .modal-title {
            font-weight: bold;
        }
        .modal-body {
            padding: 25px;
        }
        .form-select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .btn-primary {
            background-color: #65a3f3;
            border: none;
            padding: 10px 20px;
        }
        .btn-secondary {
            padding: 10px 20px;
        }
    </style>
</head>
<body>

<!-- Modal d'assignation de caisse -->
<div class="modal fade" id="assignCaisseModal" tabindex="-1" aria-labelledby="assignCaisseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignCaisseModalLabel">Assigner une caisse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignCaisseForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <div class="mb-3">
                        <label for="caisse_id" class="form-label">Sélectionner une caisse pour <?= htmlspecialchars($user['nom']) ?></label>
                        <select class="form-select" id="caisse_id" name="caisse_id" required>
                            <option value="0">-- Aucune caisse --</option>
                            <?php while ($caisse = $caisses->fetch_assoc()): ?>
                                <option value="<?= $caisse['id'] ?>" 
                                    <?= $user['caisse_id'] == $caisse['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($caisse['nom']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS et dépendances -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery pour les requêtes AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Initialiser la modal
    var assignModal = new bootstrap.Modal(document.getElementById('assignCaisseModal'));
    assignModal.show();

    // Gérer la soumission du formulaire via AJAX
    $('#assignCaisseForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Fermer la modal et recharger la page parente
                    assignModal.hide();
                    window.parent.location.reload();
                } else {
                    alert('Erreur: ' + response.error);
                }
            },
            error: function() {
                alert('Une erreur est survenue lors de la requête.');
            }
        });
    });

    // Fermer la modal si on clique en dehors
    $('#assignCaisseModal').on('hidden.bs.modal', function () {
        // Retour à la page précédente ou fermeture si c'est une iframe
        if (window.parent !== window) {
            window.parent.$('#assignCaisseModal').modal('hide');
        }
    });
});
</script>

</body>
</html>