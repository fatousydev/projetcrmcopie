<?php
// Connexion à la base de données avec PDO
$pdo = new PDO('mysql:host=localhost;dbname=crm;charset=utf8', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Récupérer le filtre
$filter = $_GET['filter'] ?? 'all';
$sql = match ($filter) {
    'active' => "SELECT * FROM membre_entreprise WHERE statut = 'actif'",
    'inactive' => "SELECT * FROM membre_entreprise WHERE statut = 'inactif'",
    'both' => "SELECT * FROM membre_entreprise WHERE statut IN ('actif', 'inactif')",
    default => "SELECT * FROM membre_entreprise"
};

// Exécution de la requête SQL
$entreprises = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les noms des colonnes
$columns = [];
if (!empty($entreprises)) {
    $columns = array_keys($entreprises[0]);
}

// Exporter en Excel
if (isset($_GET['export'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="entreprises_'.$filter.'.xls"');
    
    // En-têtes
    echo implode("\t", array_map(function($col) {
        return ucfirst(str_replace('_', ' ', $col));
    }, $columns)) . "\n";
    
    // Données
    foreach ($entreprises as $e) {
        echo implode("\t", array_values($e)) . "\n";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Membres Entreprises - PAMECAS</title>
    <!-- Le CSS reste identique -->
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
            min-width: 1200px;
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
        
        .action-btn-edit {
            background-color: #ffc107;
            color: #212529;
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
        
        /* Tooltip styles */
        .tooltip {
            position: relative;
            display: inline-block;
        }
        
        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.8rem;
        }
        
        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
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

        /* Styles pour l'impression */
        @media print {
            body * {
                visibility: hidden;
            }
            .table-container, .table-container * {
                visibility: visible;
            }
            .table-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 0;
                margin: 0;
            }
            nav, header, .pagination, .actions-cell {
                display: none;
            }
            table {
                width: 100% !important;
                min-width: 100% !important;
                border: 1px solid #000;
            }
            th {
                background-color: #003366 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            /* Ajouter un en-tête d'impression */
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
            /* Ajouter un titre avant la table */
            .table-container::before {
                content: "Liste des Membres Individuels - PAMECAS";
                display: block;
                text-align: center;
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 15px;
                color: #003366;
            }
            /* Ajouter une date d'impression */
            .table-container::after {
                content: "Document imprimé le " attr(data-print-date);
                display: block;
                text-align: right;
                font-size: 12px;
                margin-top: 10px;
                color: #666;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="pamecas.jpg" alt="Pamecas">
        <h1>PAMECAS - GESTION DES MEMBRES ENTREPRISES</h1>
    </div>
    <div class="header-buttons">
        <a href="ajout_membre_entreprise.html" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter
        </a>
        <a href="index.html" class="btn btn-danger">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </div>
</header>

<nav>
    <h2>Liste des entreprises membres</h2>
    <div class="table-controls">
        <input type="text" class="search-box" placeholder="Rechercher...">
        <a href="#" class="btn btn-info" onclick="showPopup('Importer des entreprises', 'upload_client.html')">
            <i class="fas fa-upload"></i> Importer
        </a>
        <a href="?export=1" class="btn btn-info">
            <i class="fas fa-download"></i> Exporter
        </a>
        <button class="btn btn-info" onclick="printMemberList()">
            <i class="fas fa-print"></i> Imprimer
        </button>
    </div>
</nav>

<div class="table-container" data-print-date="<?= date('d/m/Y à H:i') ?>">
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>N° Compte</th>
                <th>Nom Entreprise</th>
                <th>Nom Dirigeant</th>
                <th>Prénom Dirigeant</th>
                <th>Statut</th>
                <th>Date Admission</th>
                <th>Téléphone</th>
                <th>Pièce ID</th>
                <th>Passeport</th>
                <th>Email</th>
                <th>Adresse</th>
                <th>Région</th>
                <th>Classification</th>
                <th>Effectif</th>
                <th>Activités</th>
                <th>Besoins</th>
                <th>Source</th>
                <th>Contact</th>
                <th>Relation</th>
                <th>Tél Contact</th>
                <th>Commentaires</th>
                <th>ID Campagne</th>
                <th>ID Caisse</th>
                <th>ID Guichet</th>
                <th>Bénéficié crédit</th>
                <th>ID Prospect</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($entreprises)): ?>
                <?php foreach ($entreprises as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['id']) ?></td>
                        <td><?= htmlspecialchars($e['numero_compt_entreprise']) ?></td>
                        <td><?= htmlspecialchars($e['nom_entreprise']) ?></td>
                        <td><?= htmlspecialchars($e['nom_dirigant']) ?></td>
                        <td><?= htmlspecialchars($e['prenom_dirigant']) ?></td>
                        <td>
                            <?php $badge_class = [
                                'actif' => 'badge-success',
                                'inactif' => 'badge-danger',
                                'suspendu' => 'badge-warning',
                                'radié' => 'badge-danger'
                            ][strtolower($e['statut'])] ?? 'badge-info'; ?>
                            <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($e['statut']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($e['date_admission']) ?></td>
                        <td><?= htmlspecialchars($e['num_telephone']) ?></td>
                        <td><?= htmlspecialchars($e['numero_piece']) ?></td>
                        <td><?= htmlspecialchars($e['numero_passeport']) ?></td>
                        <td><?= htmlspecialchars($e['email']) ?></td>
                        <td><?= htmlspecialchars($e['adresse']) ?></td>
                        <td><?= htmlspecialchars($e['region']) ?></td>
                        <td><?= htmlspecialchars($e['classification']) ?></td>
                        <td><?= htmlspecialchars($e['effectif']) ?></td>
                        <td class="comments-cell"><?= htmlspecialchars($e['activites']) ?></td>
                        <td><?= htmlspecialchars($e['besoins']) ?></td>
                        <td><?= htmlspecialchars($e['source_connaissance']) ?></td>
                        <td><?= htmlspecialchars($e['personne_contact']) ?></td>
                        <td><?= htmlspecialchars($e['relation_contact']) ?></td>
                        <td><?= htmlspecialchars($e['telephone_contact']) ?></td>
                        <td class="comments-cell tooltip" title="<?= htmlspecialchars($e['commentaires']) ?>">
                            <?= strlen($e['commentaires']) > 30 ? htmlspecialchars(substr($e['commentaires'], 0, 30)) . '...' : htmlspecialchars($e['commentaires']) ?>
                        </td>
                        <td><?= htmlspecialchars($e['campagne_id']) ?></td>
                        <td><?= htmlspecialchars($e['caisse_id']) ?></td>
                        <td><?= htmlspecialchars($e['guichet_id']) ?></td>
                        <td><?= htmlspecialchars($e['a_beneficie_credit']) ?></td>
                        <td><?= htmlspecialchars($e['id_prospect']) ?></td>
                        <td class="actions-cell">
                            <a href="modifier_membre_entreprise.php?id=<?= $e['id'] ?>" class="action-btn action-btn-edit" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="action-btn action-btn-delete" title="Supprimer" onclick="deleteEntreprise(<?= $e['id'] ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="28" style="text-align: center;">Aucune entreprise membre trouvée</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="pagination">
    <a href="current">&laquo;</a>
    <a href="current">1</a>
    <span class="current">2</span>
    <a href="current">3</a>
    <a href="current">4</a>
    <a href="current">5</a>
    <a href="current">&raquo;</a>
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
    // Fonctions pour les actions
    
    function deleteEntreprise(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette entreprise membre ?')) {
            window.location.href = 'supprimer_membre_entreprise.php?id=' + id;
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

    // Fonction pour imprimer la liste des membres
    function printMemberList() {
        window.print();
    }
    
    // Fonctions pour la popup
    function showPopup(title, url) {
        document.getElementById('popupTitle').textContent = title;
        
        if (url.includes('export=1')) {
            window.location.href = url;
            return;
        }
        
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