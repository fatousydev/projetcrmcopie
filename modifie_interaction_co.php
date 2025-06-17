<?php 
// Connexion à la base de données avec PDO
$pdo = new PDO('mysql:host=localhost;dbname=crm;charset=utf8', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Récupérer toutes les interactions avec le nom de la campagne
$stmt = $pdo->query("
    SELECT i.*, c.nom_campagne 
    FROM interaction i 
    LEFT JOIN campagnes_marketing c ON i.campagne_id = c.id
");
$interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les campagnes pour la liste déroulante
$campagnes = $pdo->query("SELECT * FROM campagnes_marketing")->fetchAll(PDO::FETCH_ASSOC);

// Initialisation de la variable pour l'édition
$interactionToEdit = null;

// Récupérer les informations de l'interaction à modifier
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("
        SELECT i.*, c.nom_campagne 
        FROM interaction i 
        LEFT JOIN campagnes_marketing c ON i.campagne_id = c.id 
        WHERE i.id = ?
    ");
    $stmt->execute([$_GET['edit']]);
    $interactionToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Interactions</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { width: 80%; margin: auto; background: white; padding: 20px; border-radius: 5px; }
        .hidden { display: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #65a3f3; color: white; }
        .btn { padding: 5px 10px; color: white; text-decoration: none; border-radius: 3px; }
        .edit { background-color: #f0ad4e; }
        .delete { background-color: #d9534f; }
        input, label { display: block; margin: 10px 0; width: 100%; padding: 8px; }
        button { background-color: #65a3f3; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <h1>Liste des Interactions</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Canal</th>
            <th>Date Interaction</th>
            <th>Description</th>
            <th>Type Cible</th>
            <th>Campagne</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($interactions as $i): ?>
            <tr>
                <td><?= htmlspecialchars($i['id']) ?></td>
                <td><?= htmlspecialchars($i['canal_interaction']) ?></td>
                <td><?= htmlspecialchars($i['date_interaction']) ?></td>
                <td><?= htmlspecialchars($i['description']) ?></td>
                <td><?= htmlspecialchars($i['type_cible']) ?></td>
                <td><?= htmlspecialchars($i['nom_campagne']) ?></td>
                <td>
                    <a href="?edit=<?= $i['id'] ?>" class="btn edit">Modifier</a>
                    <a href="?delete=<?= $i['id'] ?>" class="btn delete" onclick="return confirm('Confirmer la suppression ?');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php if ($interactionToEdit): ?>
    <div class="container <?= isset($_GET['edit']) ? '' : 'hidden' ?>">
        <h2>Modifier l'Interaction</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($interactionToEdit['id']) ?>">

            <label for="canal_interaction">Canal</label>
            <select name="canal_interaction" required>
                <option value="Email" <?= $interactionToEdit['canal_interaction'] == "Email" ? "selected" : "" ?>>Email</option>
                <option value="SMS" <?= $interactionToEdit['canal_interaction'] == "SMS" ? "selected" : "" ?>>SMS</option>
                <option value="Appel" <?= $interactionToEdit['canal_interaction'] == "Appel" ? "selected" : "" ?>>Appel</option>
                <option value="WhatsApp" <?= $interactionToEdit['canal_interaction'] == "WhatsApp" ? "selected" : "" ?>>WhatsApp</option>
            </select>

            <label for="date_interaction">Date</label>
            <input type="date" name="date_interaction" value="<?= htmlspecialchars($interactionToEdit['date_interaction']) ?>" required>

            <label for="description">Description</label>
            <textarea name="description" required><?= htmlspecialchars($interactionToEdit['description']) ?></textarea>

            <label for="type_cible">Type Cible</label>
            <select name="type_cible" required>
                <option value="Prospect" <?= $interactionToEdit['type_cible'] == "Prospect" ? "selected" : "" ?>>Prospect</option>
                <option value="Membre" <?= $interactionToEdit['type_cible'] == "Membre" ? "selected" : "" ?>>Membre</option>
                <option value="Tous" <?= $interactionToEdit['type_cible'] == "Tous" ? "selected" : "" ?>>Tous</option>
            </select>

            <label for="id_campagne">Campagne</label>
            <select name="id_campagne">
                <?php foreach ($campagnes as $campagne): ?>
                    <option value="<?= $campagne['id'] ?>" <?= $interactionToEdit['campagne_id'] == $campagne['id'] ? "selected" : "" ?>>
                        <?= htmlspecialchars($campagne['nom_campagne']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Mettre à jour</button>
        </form>
    </div>
<?php endif; ?>

</body>
</html>
