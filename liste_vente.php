<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crm";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Requête SQL pour récupérer les ventes avec jointures
$sql = "
SELECT v.id, m.numero_membre, u.nom, u.role, p.nom_produit, v.quantite, v.date_vente
FROM ventes v
JOIN membres m ON v.membre_id = m.id
JOIN produits p ON v.produit_id = p.id
JOIN utilisateurs u ON v.utilisateur_id = u.id
ORDER BY v.date_vente DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Ventes - PAMECAS</title>
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
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
            border: 1px solid var(--success-color);
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
        
        .actions-cell {
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
        
        /* Formulaire d'import */
        .import-form {
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .submit-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .submit-btn:hover {
            background-color: #4a8fe0;
        }
        
        .file-format-info {
            background-color: #f8f9fa;
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
            font-size: 0.85rem;
        }
        
        .file-format-info h4 {
            margin-top: 0;
            color: var(--primary-color);
        }
        
        .file-format-info ul {
            padding-left: 20px;
            margin-bottom: 0;
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
                padding: 20px;
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
            /* Ajouter un en-tête d'impression */
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
            /* Ajouter un titre avant la table */
            .table-container::before {
                content: "LISTE DES VENTES - PAMECAS";
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
        <h1>PAMECAS - GESTION DES VENTES</h1>
    </div>
    <div class="header-buttons">
        <a href="index.html" class="btn btn-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</header>

<nav>
    <h2>Liste des ventes</h2>
    <div class="table-controls">
        <input type="text" class="search-box" placeholder="Rechercher...">
        <button class="btn btn-success" onclick="showPopup('Importer des ventes', 'upload_vente.html')">
            <i class="fas fa-file-import"></i> Importer
        </button>
        <a href="export_vente.php" class="btn btn-info" onclick="return confirm('Êtes-vous sûr de vouloir exporter les ventes ?')">
            <i class="fas fa-download"></i> Exporter
        </a>
        <a href="statistique.php" class="btn btn-info" onclick="return confirm('Êtes-vous sûr de vouloir voir les statistiques des ventes ?')">
            <i class="fas fa-chart-bar"></i> Statistique vente
        </a>
        <button class="btn btn-info" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimer
        </button>
    </div>
</nav>

<div class="table-container" data-print-date="<?= date('d/m/Y à H:i') ?>">
    <table border="1">
        <thead>
            <tr>
                <th>ID Vente</th>
                <th>Numéro Membre</th>
                <th>Nom Produit</th>
                <th>Nom utilisateur</th>
                <th>Role utilisateur</th>
                <th>Quantité</th>
                <th>Date Vente</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($v = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="compact-cell"><?= htmlspecialchars($v['id']) ?></td>
                        <td class="compact-cell"><?= htmlspecialchars($v['numero_membre']) ?></td>
                        <td class="compact-cell"><?= htmlspecialchars($v['nom_produit']) ?></td>
                        <td class="compact-cell"><?= htmlspecialchars($v['nom']) ?></td>
                        <td class="compact-cell"><?= htmlspecialchars($v['role']) ?></td>
                        <td class="compact-cell"><?= htmlspecialchars($v['quantite']) ?></td>
                        <td class="compact-cell"><?= date('d/m/Y H:i', strtotime($v['date_vente'])) ?></td>
                        <td class="actions-cell">
                            <form action="supprimer_vente.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Voulez-vous vraiment supprimer cette vente ?');">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($v['id']) ?>">
                                <button class="action-btn action-btn-delete" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">Aucune vente trouvée</td>
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
    function deleteVente(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette vente ?')) {
            window.location.href = 'supprimer_vente.php?id=' + id;
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
        
        // Si c'est pour exporter, on redirige directement
        if (url.includes('export=1')) {
            window.location.href = url;
            return;
        }
        
        // Chargement du contenu via AJAX
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