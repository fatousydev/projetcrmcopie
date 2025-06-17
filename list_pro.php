<?php
// Connexion à la base de données
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=crm;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des produits
$produits = $pdo->query("SELECT * FROM produits")->fetchAll(PDO::FETCH_ASSOC);
$columns = !empty($produits) ? array_keys($produits[0]) : [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits - PAMECAS</title>
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
        }
        
        /* En-tête d'impression */
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
            color: var(--primary-color);
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .print-date {
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        
        /* Interface normale */
        header {
            background-color: var(--primary-color);
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
            background-color: var(--dark-color);
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
            background-color: var(--primary-color);
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .no-print {
            display: none;
        }
        
        /* Boutons */
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
            background-color: var(--info-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
        }
        
        .search-box {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        /* Styles d'impression */
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
            
            nav, header, .pagination, 
            .actions-cell, .table-controls, 
            .search-box, .btn {
                display: none !important;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
            }
            
            th {
                background-color: var(--primary-color) !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<!-- En-tête pour l'impression -->
<div class="print-header">
    <img src="pamecas.jpg" alt="Logo PAMECAS">
    <div class="print-title">PAMECAS - LISTE DES PRODUITS</div>
    <div class="print-date">Imprimé le <?= date('d/m/Y à H:i') ?></div>
</div>

<!-- Interface utilisateur normale -->
<header>
    <div class="logo">
        <img src="pamecas.jpg" alt="PAMECAS">
        <h1>PAMECAS - GESTION DES PRODUITS</h1>
    </div>
    <div>
        <a href="logout.php" class="btn btn-danger">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </div>
</header>

<nav>
    <h2>Liste des produits</h2>
    <div class="table-controls">
        <input type="text" class="search-box" placeholder="Rechercher...">
        <a href="export.php" class="btn btn-info">
            <i class="fas fa-download"></i> Exporter
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
                <?php foreach ($columns as $column): ?>
                <th><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $column))) ?></th>
                <?php endforeach; ?>
                <th class="no-print">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($produits)): ?>
                <?php foreach ($produits as $p): ?>
                    <tr>
                        <?php foreach ($columns as $column): ?>
                            <td>
                                <?php if (in_array($column, ['created_at', 'updated_at'])): ?>
                                    <?= date('d/m/Y H:i', strtotime($p[$column] ?? '')) ?>
                                <?php else: ?>
                                    <?= htmlspecialchars($p[$column] ?? '') ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td class="no-print">
                            <button class="btn btn-danger" onclick="confirmDelete(<?= $p['id'] ?>)">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= count($columns) + 1 ?>" style="text-align: center;">Aucun produit trouvé</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Fonction pour confirmer la suppression
    function confirmDelete(id) {
        if (confirm('Voulez-vous vraiment supprimer ce produit ?')) {
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