<?php
session_start();

// Configuration de la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crm";

// Connexion sécurisée avec gestion des erreurs
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Échec de la connexion : " . $conn->connect_error);
    }

    // Récupération des produits actifs
    $produits = [];
    $result = $conn->query("
        SELECT id, nom_produit, description, plafond_montant, taux_interet, type_produit 
        FROM produits 
        WHERE taux_interet = 1
        ORDER BY nom_produit ASC
    ");
    while ($row = $result->fetch_assoc()) {
        $produits[] = $row;
    }

    // Récupération des catégories pour le filtre
    $categories = [];
    $result = $conn->query("SELECT DISTINCT type_produit FROM produits WHERE taux_interet = 1 ORDER BY type_produit");
    while ($row = $result->fetch_assoc()) {
        $type_produit[] = $row['type_produit'];
    }

    $conn->close();
} catch (Exception $e) {
    die("Erreur: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue Produits - CRM PAMECAS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2196F3;
            --primary-dark: #1976D2;
            --primary-light: #BBDEFB;
            --accent-color: #FF9800;
            --light-bg: #F5F9FF;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: #333;
        }
        
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            background: linear-gradient(180deg, var(--primary-dark), var(--primary-color));
            color: white;
            padding-top: 20px;
            box-shadow: 3px 0px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        
        /* (Conserver le reste du CSS du sidebar comme dans le tableau de bord) */
        
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .header {
            background-color: white;
            color: var(--primary-dark);
            padding: 20px;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .product-card {
            border: none;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s;
            margin-bottom: 20px;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .product-img {
            height: 200px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
            width: 100%;
        }
        
        .product-body {
            padding: 15px;
        }
        
        .product-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--primary-dark);
        }
        
        .product-category {
            display: inline-block;
            background-color: var(--primary-light);
            color: var(--primary-dark);
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }
        
        .product-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-color);
            margin: 10px 0;
        }
        
        .product-stock {
            font-size: 0.9rem;
        }
        
        .stock-in {
            color: #4CAF50;
        }
        
        .stock-low {
            color: #FF9800;
        }
        
        .stock-out {
            color: #F44336;
        }
        
        .filter-section {
            background-color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            margin-bottom: 20px;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                width: 200px;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar (identique au tableau de bord) -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="pamecas.jpg" class="img-fluid logo" alt="Logo PAMECAS">
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="dashboard_commercial.php" class="nav-link">
                    <i class="bi bi-speedometer2 nav-icon"></i>
                    <span class="nav-text">Tableau de Bord</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="prospects.php" class="nav-link">
                    <i class="bi bi-person-lines-fill nav-icon"></i>
                    <span class="nav-text">Prospects</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="membres.php" class="nav-link">
                    <i class="bi bi-people-fill nav-icon"></i>
                    <span class="nav-text">Membres</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="produits.php" class="nav-link active">
                    <i class="bi bi-box-seam-fill nav-icon"></i>
                    <span class="nav-text">Produits</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="ventes.php" class="nav-link">
                    <i class="bi bi-graph-up-arrow nav-icon"></i>
                    <span class="nav-text">Suivi des Ventes</span>
                </a>
            </li>
            <li class="nav-item mt-4">
                <a href="index.html" class="nav-link">
                    <i class="bi bi-box-arrow-right nav-icon"></i>
                    <span class="nav-text">Déconnexion</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content" id="mainContent">
        <div class="header">
            <h1 class="header-title">Catalogue des Produits</h1>
            <div class="user-info">
                <div class="user-avatar">CO</div>
                <div>
                    <div class="fw-bold">Commercial PAMECAS</div>
                    <small class="badge bg-primary">Commercial</small>
                </div>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="filter-section">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="categorieFilter" class="form-label">Type produits</label>
                    <select class="form-select" id="categorieFilter">
                        <option value="">Toutes les types de produits</option>
                        <?php foreach ($type_produits as $type_produit): ?>
                            <option value="<?php echo htmlspecialchars($type_produits); ?>"><?php echo htmlspecialchars($type_produit); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="searchFilter" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="searchFilter" placeholder="Nom du produit...">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="filterBtn">
                        <i class="bi bi-funnel"></i> Filtrer
                    </button>
                </div>
            </div>
        </div>
        
     
                    <?php endif; ?>
                    <div class="product-body">
                        <span class="product-category"><?php echo htmlspecialchars($produit['type_produit']); ?></span>
                        <h5 class="product-title"><?php echo htmlspecialchars($produit['nom_produit']); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($produit['description']); ?></p>
                        <div class="product-price">
                            <?php echo number_format($produit['plafond_montant'], 0, ',', ' '); ?> FCFA
                        </div>
                  
                        <div class="d-flex justify-content-between mt-3">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Détails
                            </button>
                            <button class="btn btn-sm btn-primary">
                                <i class="bi bi-cart-plus"></i> Vendre
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($produits)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle-fill"></i> Aucun produit disponible pour le moment.
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Filtrage des produits
        document.getElementById('filterBtn').addEventListener('click', function() {
            const category = document.getElementById('categorieFilter').value.toLowerCase();
            const search = document.getElementById('searchFilter').value.toLowerCase();
            
            document.querySelectorAll('.product-item').forEach(item => {
                const itemCategory = item.getAttribute('data-category').toLowerCase();
                const itemName = item.querySelector('.product-title').textContent.toLowerCase();
                const itemDesc = item.querySelector('p').textContent.toLowerCase();
                
                const categoryMatch = category === '' || itemCategory.includes(category);
                const searchMatch = search === '' || 
                    itemName.includes(search) || 
                    itemDesc.includes(search);
                
                if (categoryMatch && searchMatch) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Permettre la recherche avec la touche Entrée
        document.getElementById('searchFilter').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('filterBtn').click();
            }
        });
    </script>
</body>
</html>