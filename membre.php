<?php
// Connexion à la base de données
$db = new PDO('mysql:host=localhost;dbname=crm', 'root', '');

// Fonction pour récupérer les membres individuels
function getMembresIndividuels($db) {
    $query = "SELECT m.*, mi.nom, mi.prenom 
              FROM membre m
              JOIN membre_individuel mi ON m.id = mi.id_membre
              WHERE m.type = 'individuel'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les membres entreprises
function getMembresEntreprises($db) {
    $query = "SELECT m.*, me.raison_sociale, me.siret 
              FROM membre m
              JOIN membre_entreprise me ON m.id = me.id_membre
              WHERE m.type = 'entreprise'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les groupements avec leurs membres
function getGroupementsAvecMembres($db) {
    // D'abord récupérer tous les groupements
    $query = "SELECT * FROM groupement";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $groupements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Pour chaque groupement, récupérer ses membres
    foreach ($groupements as &$groupement) {
        $query = "SELECT m.*, mg.role, mg.date_adhesion
                  FROM membre m
                  JOIN membre_groupe mg ON m.id = mg.id_membre
                  WHERE mg.id_groupement = :id_groupement";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_groupement', $groupement['id']);
        $stmt->execute();
        $groupement['membres'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return $groupements;
}

// Récupération des données
$membresIndividuels = getMembresIndividuels($db);
$membresEntreprises = getMembresEntreprises($db);
$groupements = getGroupementsAvecMembres($db);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des membres</title>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <h1>Gestion des membres</h1>
    
    <div class="tabs">
        <button onclick="showTab('individuels')">Membres individuels</button>
        <button onclick="showTab('entreprises')">Membres entreprises</button>
        <button onclick="showTab('groupements')">Groupements</button>
    </div>
    
    <!-- Onglet Membres individuels -->
    <div id="individuels" class="tab-content active">
        <h2>Membres individuels</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Date création</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($membresIndividuels as $membre): ?>
                <tr>
                    <td><?= htmlspecialchars($membre['id']) ?></td>
                    <td><?= htmlspecialchars($membre['nom']) ?></td>
                    <td><?= htmlspecialchars($membre['prenom']) ?></td>
                    <td><?= htmlspecialchars($membre['date_creation']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Onglet Membres entreprises -->
    <div id="entreprises" class="tab-content">
        <h2>Membres entreprises</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Raison sociale</th>
                    <th>SIRET</th>
                    <th>Date création</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($membresEntreprises as $membre): ?>
                <tr>
                    <td><?= htmlspecialchars($membre['id']) ?></td>
                    <td><?= htmlspecialchars($membre['raison_sociale']) ?></td>
                    <td><?= htmlspecialchars($membre['siret']) ?></td>
                    <td><?= htmlspecialchars($membre['date_creation']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Onglet Groupements -->
    <div id="groupements" class="tab-content">
        <h2>Groupements et leurs membres</h2>
        <?php foreach ($groupements as $groupement): ?>
        <div class="groupement">
            <h3><?= htmlspecialchars($groupement['nom']) ?></h3>
            <p><?= htmlspecialchars($groupement['description']) ?></p>
            
            <h4>Membres du groupement</h4>
            <table>
                <thead>
                    <tr>
                        <th>ID Membre</th>
                        <th>Date adhésion</th>
                        <th>Rôle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groupement['membres'] as $membre): ?>
                    <tr>
                        <td><?= htmlspecialchars($membre['id']) ?></td>
                        <td><?= htmlspecialchars($membre['date_adhesion']) ?></td>
                        <td><?= htmlspecialchars($membre['role']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>
    </div>
    
    <script>
        function showTab(tabId) {
            // Masquer tous les onglets
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Afficher l'onglet sélectionné
            document.getElementById(tabId).classList.add('active');
        }
    </script>
</body>
</html>