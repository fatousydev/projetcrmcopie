<?php
require 'pubic.php'.php';
verifierAuthentification();

$caisseId = $_GET['id'] ?? 0;

// Vérifier l'accès
if (!$permissions->verifierAccesCaisse($_SESSION['user_id'], $caisseId)) {
    die("Accès refusé à cette caisse");
}

// Récupérer les infos de la caisse
$stmt = $pdo->prepare("SELECT * FROM caisses WHERE id = ?");
$stmt->execute([$caisseId]);
$caisse = $stmt->fetch();
?>

<h1>Caisse : <?= htmlspecialchars($caisse['nom']) ?></h1>

<div class="caisse-content">
    <?php if ($_SESSION['user_role'] === 'directeur'): ?>
        <!-- Interface spécifique pour les directeurs -->
        <h2>Tableau de bord directeur</h2>
        <!-- Statistiques, gestion, etc. -->
        
    <?php elseif (in_array($_SESSION['user_role'], ['commercial', 'animatrice'])): ?>
        <!-- Interface pour commerciaux/animatrices -->
        <h2>Espace opérationnel</h2>
        <!-- Formulaires de gestion courante -->
        
    <?php else: ?>
        <!-- Interface admin complète -->
        <h2>Administration de la caisse</h2>
        <!-- Outils avancés -->
    <?php endif; ?>
</div>