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

// Requête SQL pour récupérer les interactions avec jointures
$sql = "SELECT 
            i.*, 
            m.nom AS nom_membres, m.prenom AS prenom_membres, 
            p.nom AS nom_prospects, p.prenom AS prenom_prospects,
            c.nom_campagne 
        FROM interaction i
        LEFT JOIN membres m ON i.id_membre = m.id
        LEFT JOIN prospects p ON i.id_prospect = p.id
        LEFT JOIN campagnes_marketing c ON i.campagne_id = c.id
        ORDER BY i.date_interaction DESC";

// Exécution de la requête SQL
$interactions = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Colonnes à afficher
$columns = ['id', 'nom_campagne', 'type_cible', 'canal_interaction', 'date_interaction', 'description'];

// Exporter en Excel
if (isset($_GET['export'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="interactions.xls"');
    
    // En-têtes
    $headers = ['ID', 'Campagne', 'Cible', 'Type', 'Canal', 'Date', 'Description'];
    echo implode("\t", $headers) . "\n";
    
    // Données
    foreach ($interactions as $i) {
        $cible = '';
        if ($i['type_cible'] == 'membres' && !empty($i['nom_membres'])) {
            $cible = $i['nom_membres'] . " " . $i['prenom_membres'];
        } elseif ($i['type_cible'] == 'prospects' && !empty($i['nom_prospects'])) {
            $cible = $i['nom_prospects'] . " " . $i['prenom_prospects'];
        } else {
            $cible = "Non défini";
        }
        
        $row = [
            $i['id'],
            $i['nom_campagne'] ?? 'Non défini',
            $cible,
            $i['type_cible'] ?? 'Non défini',
            $i['canal_interaction'] ?? 'Non défini',
            $i['date_interaction'] ?? 'Non défini',
            $i['description'] ?? 'Non défini'
        ];
        echo implode("\t", $row) . "\n";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Interactions - PAMECAS</title>
    <style>
        :root {
            --primary-color: #003366;
            --secondary-color: #65a3f3;
            --danger-color: #ec0909;
            --info-color: #5bc0de;
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
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
            border: 1px solid var(--danger-color);
        }
        
        .btn-danger:hover {
            background-color: #c40808;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
            border: 1px solid var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: #4a8fe0;
        }
        
        .btn-info {
            background-color: var(--info-color);
            color: white;
            border: 1px solid var(--info-color);
        }
        
        .btn-info:hover {
            background-color: #46b8da;
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
            min-width: 1000px;
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
        
        .comments-cell {
            max-width: 200px;
            white-space: normal;
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
        
        .action-btn-edit {
            background-color: #ffc107;
            color: #212529;
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
        
        /* Styles pour l'impression */
        @media print {
            header .header-buttons,
            nav .table-controls,
            .pagination,
            .actions-cell {
                display: none !important;
            }
            
            header {
                padding: 5px;
                text-align: center;
                box-shadow: none;
                page-break-after: avoid;
            }
            
            header .logo {
                justify-content: center;
            }
            
            header .logo img {
                height: 40px;
            }
            
            header .logo h1 {
                font-size: 1rem;
            }
            
            nav h2 {
                display: none;
            }
            
            body {
                font-size: 12px;
                color: #000;
                background: none;
                margin: 0;
                padding: 0;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: auto;
            }
            
            th, td {
                border: 1px solid #ddd;
                padding: 4px;
                page-break-inside: avoid;
            }
            
            th {
                background-color: #003366 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            tr:nth-child(even) {
                background-color: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .table-container {
                padding: 0;
                overflow: visible;
                width: 100%;
                margin: 0;
            }
            
            @page {
                size: auto;
                margin: 10mm;
            }
            
            tr {
                page-break-inside: avoid;
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
            max-width: 90%;
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
        <h1>PAMECAS - GESTION DES INTERACTIONS</h1>
    </div>
    <div class="header-buttons">
        <a href="index.html" class="btn btn-danger">
            <i class="fas fa-sign-out-alt"></i>Logout
        </a>
    </div>
</header>

<nav>
    <h2>📋 Liste des interactions</h2>
    <div class="table-controls">
        <input type="text" class="search-box" placeholder="Rechercher...">
        <a href="export_interaction.php" class="btn btn-info" onclick="return confirm('Êtes-vous sûr de vouloir exporter les interactions ?')">
            <i class="fas fa-download"></i> Exporter
        </a>
        <button class="btn btn-info" onclick="printInteractions()">
            <i class="fas fa-print"></i> Imprimer
        </button>
    </div>
</nav>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Campagne</th>
                <th>Cible</th>
                <th>Type de Cible</th>
                <th>Canal</th>
                <th>Date</th>
                <th>Description</th>
                <th class="actions-cell">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($interactions as $i): ?>
                <tr>
                    <td><?= htmlspecialchars($i['id']) ?></td>
                    <td><?= htmlspecialchars($i['nom_campagne'] ?? 'Non défini') ?></td>
                    <td>
                        <?php
                            if ($i['type_cible'] === 'membres') {
                                echo htmlspecialchars($i['nom_membres'] . ' ' . $i['prenom_membres']);
                            } elseif ($i['type_cible'] === 'prospects') {
                                echo htmlspecialchars($i['nom_prospects'] . ' ' . $i['prenom_prospects']);
                            } else {
                                echo 'Non défini';
                            }
                        ?>
                    </td>
                    <td><?= htmlspecialchars($i['type_cible'] ?? 'Non défini') ?></td>
                    <td><?= htmlspecialchars($i['canal_interaction'] ?? 'Non défini') ?></td>
                    <td><?= htmlspecialchars($i['date_interaction'] ?? 'Non défini') ?></td>
                    <td><?= nl2br(htmlspecialchars($i['description'] ?? '')) ?></td>
                    <td class="actions-cell">
                        <button class="action-btn action-btn-delete" title="Supprimer" onclick="deleteInteraction(<?= $i['id'] ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
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

<!-- Popup overlay -->
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
    // Fonction pour imprimer les interactions
    function printInteractions() {
        window.print();
    }
    
    // Fonctions pour les actions
    function deleteInteraction(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette interaction ?')) {
            window.location.href = 'supprimer_interaction.php?id=' + id;
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

    // Fonctions pour la popup
    function showPopup(title, url) {
        document.getElementById('popupTitle').textContent = title;
        
        fetch(url)
            .then(response => response.text())
            .then(data => {
                document.getElementById('popupContent').innerHTML = data;
                document.getElementById('popupOverlay').style.display = 'flex';
            })
            .catch(error => {
                document.getElementById('popupContent').innerHTML = `
                    <p>Une erreur s'est produite lors du chargement du contenu.</p>
                    <p>${error.message}</p>
                `;
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