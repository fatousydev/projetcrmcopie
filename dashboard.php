<?php
require 'public.php';
verifierAuthentification();

// Récupérer les caisses accessibles
$caisses = $permissions->getCaissesAccessibles($_SESSION['user_id']);
?>

<h1>Bienvenue, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>

<?php if ($_SESSION['user_role'] === 'admin'): ?>
    <a href="admin.php">Administration</a>
<?php endif; ?>

<h2>Vos caisses</h2>
<div class="caisses-list">
    <?php foreach ($caisses as $caisse): ?>
        <div class="caisse-card">
            <h3><?= htmlspecialchars($caisse['nom']) ?></h3>
            <p><?= htmlspecialchars($caisse['description']) ?></p>
            <a href="caisse.php?id=<?= $caisse['id'] ?>">Accéder</a>
        </div>
    <?php endforeach; ?>
</div>