<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crm";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion: " . $conn->connect_error);
}

// Récupération des ventes
$sql = "SELECT v.id, m.numero_membre, u.nom, u.role, p.nom_produit, v.quantite, v.date_vente
        FROM ventes v
        JOIN membres m ON v.membre_id = m.id
        JOIN produits p ON v.produit_id = p.id
        JOIN utilisateurs u ON v.utilisateur_id = u.id
        ORDER BY v.date_vente DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Ventes - PAMECAS</title>
    <style>
        /* Styles de base */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        /* En-tête d'impression (masqué à l'écran) */
        .print-header {
            display: none;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .print-header img {
            height: 70px;
            margin-bottom: 10px;
        }
        
        .print-title {
            color: #003366;
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .print-date {
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        
        /* Styles pour l'impression */
        @media print {
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
            
            body * {
                visibility: hidden;
            }
            
            .print-header, .table-container, 
            .table-container table, 
            .table-container thead, 
            .table-container tbody, 
            .table-container th, 
            .table-container td, 
            .table-container tr {
                visibility: visible;
            }
            
            .print-header {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
            }
            
            .table-container {
                position: absolute;
                top: 120px;
                left: 0;
                width: 100%;
                padding: 0 20px;
            }
            
            /* Masquer les éléments inutiles */
            nav, header, .pagination, 
            .actions-cell, .table-controls, 
            .search-box, .btn {
                display: none !important;
            }
            
            /* Styles du tableau */
            table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: auto;
            }
            
            th {
                background-color: #003366 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            td, th {
                border: 1px solid #ddd;
                padding: 8px;
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
        
        /* Interface normale */
        header {
            background-color: #003366;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo img {
            height: 50px;
        }
        
        nav {
            background-color: #2c3e50;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-container {
            padding: 20px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        th {
            background-color: #003366;
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .no-print {
            display: none;
        }
        
        /* Boutons et contrôles */
        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-info {
            background-color: #17a2b8;
        }
        
        .btn-success {
            background-color: #28a745;
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .search-box {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

<!-- En-tête pour l'impression -->
<div class="print-header">
    <img src="pamecas.jpg" alt="Logo PAMECAS">
    <div class="print-title">PAMECAS - GESTION DES VENTES</div>
    <div class="print-date">Imprimé le <?= date('d/m/Y à H:i') ?></div>
</div>

<!-- Interface utilisateur normale -->
<header>
    <div class="logo">
        <img src="pamecas.jpg" alt="PAMECAS">
        <h1>PAMECAS - GESTION DES VENTES</h1>
    </div>
    <div>
        <a href="logout.php" class="btn btn-danger">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </div>
</header>

<nav>
    <h2>Liste des ventes</h2>
    <div class="table-controls">
        <input type="text" class="search-box" placeholder="Rechercher...">
        <button class="btn btn-success">
            <i class="fas fa-file-import"></i> Importer
        </button>
        <a href="export.php" class="btn btn-info">
            <i class="fas fa-download"></i> Exporter
        </a>
        <a href="stats.php" class="btn btn-info">
            <i class="fas fa-chart-bar"></i> Statistiques
        </a>
        <button class="btn btn-info" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimer
        </button>
    </div>
</nav>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>N° Membre</th>
                <th>Produit</th>
                <th>Utilisateur</th>
                <th>Rôle</th>
                <th>Quantité</th>
                <th>Date</th>
                <th class="no-print">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['numero_membre']) ?></td>
                        <td><?= htmlspecialchars($row['nom_produit']) ?></td>
                        <td><?= htmlspecialchars($row['nom']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td><?= htmlspecialchars($row['quantite']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['date_vente'])) ?></td>
                        <td class="no-print">
                            <button class="btn btn-danger" onclick="confirmDelete(<?= $row['id'] ?>)">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">Aucune vente enregistrée</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Fonction pour confirmer la suppression
    function confirmDelete(id) {
        if (confirm('Voulez-vous vraiment supprimer cette vente ?')) {
            window.location.href = 'delete.php?id=' + id;
        }
    }
    
    // Fonction de recherche
    document.querySelector('.search-box').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    });
    
    // Préparation de l'impression
    window.print = function() {
        document.querySelector('.print-header').style.display = 'block';
        setTimeout(() => {
            document.defaultView.print();
            document.querySelector('.print-header').style.display = 'none';
        }, 100);
    };
</script>

<!-- Icônes Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>
</html>