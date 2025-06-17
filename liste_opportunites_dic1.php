<?php
// Affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données avec PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=crm;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Requête SQL pour récupérer les opportunités avec jointures
$sql = "SELECT 
            o.id,
            m.numero_membre,
            p.nom_produit, 
            u.nom AS nom_utilisateur,
            u.role AS role_utilisateur,
            c.nom AS nom_caisse,
            o.type_opportunite,
            o.statut,
            o.date_creation
        FROM opportunites o
        JOIN membres m ON o.membre_id = m.id
        JOIN produits p ON o.produit_id = p.id
        JOIN utilisateurs u ON o.utilisateur_id = u.id
        JOIN caisses c ON o.caisse_id = c.id
        ORDER BY o.date_creation DESC";

$opportunites = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Export en Excel
if (isset($_GET['export'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="opportunites.xls"');
    
    // En-têtes
    echo "ID\tMembre\tProduit\tUtilisateur\tRôle\tCaisse\tType\tStatut\tDate Création\n";
    
    // Données
    foreach ($opportunites as $o) {
        echo implode("\t", [
            $o['id'],
            $o['numero_membre'],
            $o['nom_produit'],
            $o['nom_utilisateur'],
            $o['role_utilisateur'],
            $o['nom_caisse'],
            $o['type_opportunite'],
            $o['statut'],
            $o['date_creation']
        ]) . "\n";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Opportunités - PAMECAS</title>
    <style>
        :root {
            --primary-color: #003366;
            --secondary-color: #65a3f3;
            --danger-color: #ec0909;
            --info-color: #5bc0de;
            --success-color: #28a745;
            --dark-color: #2c3e50;
            --light-color: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        
        header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        header .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        header .logo h1 {
            font-size: 1.3rem;
            margin: 5px 0;
            font-weight: 600;
        }
        
        header .logo img {
            height: 50px;
            object-fit: contain;
        }
        
        .header-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            border: 1px solid transparent;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c40808;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #4a8fe0;
        }
        
        .btn-info {
            background-color: var(--info-color);
            color: white;
        }
        
        .btn-info:hover {
            background-color: #46b8da;
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        nav {
            background-color: var(--dark-color);
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        nav h2 {
            color: white;
            margin: 0;
            font-size: 1.2rem;
            font-weight: 500;
        }
        
        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .table-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-box {
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 0.85rem;
            min-width: 200px;
        }
        
        .table-container {
            width: 100%;
            padding: 15px;
            box-sizing: border-box;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            min-width: 800px;
        }
        
        th, td {
            border: 1px solid #e0e0e0;
            padding: 8px;
            text-align: left;
            vertical-align: top;
            font-size: 0.8rem;
        }
        
        th {
            background-color: var(--primary-color);
            color: white;
            position: sticky;
            top: 0;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f1f5ff;
        }
        
        .compact-cell {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .actions-cell {
            white-space: nowrap;
        }
        
        .action-btn {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 0.75rem;
            margin-right: 3px;
            cursor: pointer;
            border: none;
        }
        
        .action-btn-view {
            background-color: #17a2b8;
            color: white;
        }
        
        .action-btn-delete {
            background-color: #dc3545;
            color: white;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 15px;
            gap: 5px;
        }
        
        .pagination a, .pagination span {
            padding: 6px 12px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: var(--primary-color);
            border-radius: 3px;
        }
        
        .pagination a:hover {
            background-color: #f1f1f1;
        }
        
        .pagination .current {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        @media screen and (max-width: 992px) {
            body {
                font-size: 13px;
            }
            
            header .logo h1 {
                font-size: 1.1rem;
            }
            
            .table-container {
                padding: 10px 5px;
            }
            
            th, td {
                padding: 6px;
                font-size: 0.75rem;
            }
        }
        
        /* Popup styles */
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .popup-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow: auto;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        
        .close-popup {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
            color: #555;
        }
        
        .popup-title {
            margin-top: 0;
            color: var(--primary-color);
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="pamecas.jpg" alt="Pamecas">
        <h1>PAMECAS - GESTION DES OPPORTUNITÉS</h1>
    </div>
    <div class="header-buttons">
        <a href="tableauborddirecteur.php" class="btn btn-danger">
            <i class="fas fa-sign-out-alt"></i> Retour
        </a>
    </div>
</header>

<nav>
    <h2>Liste des opportunités</h2>
    <div class="table-controls">
        <input type="text" class="search-box" placeholder="Rechercher...">
        
        
    </div>
</nav>

<div class="table-container">
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Membre</th>
                <th>Produit</th>
                <th>Utilisateur</th>
                <th>Rôle</th>
                <th>Caisse</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Date Création</th>
               
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($opportunites)): ?>
                <?php foreach ($opportunites as $o): ?>
                    <tr>
                        <td class="compact-cell"><?= htmlspecialchars($o['id']) ?></td>
                        <td class="compact-cell"><?= htmlspecialchars($o['numero_membre']) ?></td>
                        <td class="compact-cell"><?= htmlspecialchars($o['nom_produit']) ?></td>
                        <td class="compact-cell"><?= htmlspecialchars($o['nom_utilisateur']) ?></td>
                        <td class="compact-cell"><?= htmlspecialchars($o['role_utilisateur']) ?></td>
                        <td class="compact-cell"><?= htmlspecialchars($o['nom_caisse']) ?></td>
                        <td class="compact-cell"><?= htmlspecialchars($o['type_opportunite']) ?></td>
                        <td class="compact-cell">
                            <span class="badge 
                                <?= $o['statut'] === 'Terminé' ? 'badge-success' : 
                                   ($o['statut'] === 'En cours' ? 'badge-info' : 
                                   ($o['statut'] === 'Annulé' ? 'badge-danger' : 'badge-warning')) ?>">
                                <?= htmlspecialchars($o['statut']) ?>
                            </span>
                        </td>
                        <td class="compact-cell"><?= date('d/m/Y H:i', strtotime($o['date_creation'])) ?></td>
                        
                           
                          
                        
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align: center;">Aucune opportunité trouvée</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="pagination">
    <a href="#">&laquo;</a>
    <a href="#">1</a>
    <span class="current">2</span>
    <a href="#">3</a>
    <a href="#">4</a>
    <a href="#">5</a>
    <a href="#">&raquo;</a>
</div>

<!-- Popup overlay générique -->
<div class="popup-overlay" id="popupOverlay">
    <div class="popup-content">
        <span class="close-popup" onclick="closePopup()">&times;</span>
        <h3 class="popup-title" id="popupTitle">Titre</h3>
        <div id="popupContent">
            <!-- Le contenu sera chargé ici -->
        </div>
    </div>
</div>

<script>
    // Fonctions pour les actions
    function viewOpportunite(id) {
        window.location.href = 'voir_opportunite.php?id=' + id;
    }
    
    function deleteOpportunite(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette opportunité ?')) {
            window.location.href = 'supprimer_opportunite.php?id=' + id;
        }
    }
    
    // Fonction de recherche
    document.querySelector('.search-box').addEventListener('keyup', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(searchTerm) ? '' : 'none';
        });
    });
    
    // Fonctions pour la popup générique
    function showPopup(title, url) {
        document.getElementById('popupTitle').textContent = title;
        
        fetch(url)
            .then(response => response.text())
            .then(data => {
                document.getElementById('popupContent').innerHTML = data;
                document.getElementById('popupOverlay').style.display = 'flex';
            })
            .catch(error => {
                document.getElementById('popupContent').innerHTML = 
                    `<p>Une erreur s'est produite lors du chargement du contenu.</p>
                    <p>${error.message}</p>`;
                document.getElementById('popupOverlay').style.display = 'flex';
            });
    }
    
    function closePopup() {
        document.getElementById('popupOverlay').style.display = 'none';
    }
    
    // Fermer la popup en cliquant en dehors
    document.getElementById('popupOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            closePopup();
        }
    });
</script>

<!-- Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>
</html>