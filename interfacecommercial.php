<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Interface Utilisateur - PAMECAS</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    /* Styles de base */
    body {
      font-family: 'Roboto', sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      background-color: #f4f4f9;
      flex-direction: column;
      min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
      background-color: #1a2a43;
      color: white;
      width: 250px;
      height: 100%;
      position: fixed;
      padding-top: 20px;
      text-align: center;
      overflow-y: auto;
      z-index: 1000;
    }

    .sidebar h2 {
      margin-bottom: 20px;
      color: #fff;
      padding: 10px;
      background-color: #003366;
      border-radius: 5px;
    }

    /* Menu principal */
    .menu-item {
      position: relative;
      margin-bottom: 5px;
    }

    .menu-item > a {
      color: white;
      padding: 12px 15px;
      display: block;
      text-decoration: none;
      font-size: 16px;
      transition: all 0.3s ease;
      border-left: 4px solid transparent;
    }

    .menu-item > a:hover {
      background-color: #0054A6;
      border-left: 4px solid #fff;
    }

    .menu-item > a.active {
      background-color: #0054A6;
      border-left: 4px solid #fff;
    }

    .menu-item > a i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }

    /* Sous-menu */
    .submenu {
      display: none;
      background-color:green;
      padding: 0;
      margin: 0;
    }

    .submenu a {
      color: #e0e0e0;
      padding: 10px 15px 10px 30px;
      display: block;
      text-decoration: none;
      font-size: 14px;
      transition: all 0.3s ease;
      border-left: 4px solid transparent;
    }

    .submenu a:hover, 
    .submenu a.active {
      background-color:orange;
      color: white;
      border-left: 4px solid #65a3f3;
    }

    .menu-item.active .submenu {
      display: block;
    }

    /* Indicateur de menu ouvert */
    .menu-item.has-submenu > a::after {
      content: '\f078';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      float: right;
      transition: transform 0.3s;
    }

    .menu-item.has-submenu.active > a::after {
      transform: rotate(180deg);
    }

    /* Contenu principal */
    .main-content {
      margin-left: 250px;
      padding: 20px;
      flex: 1;
    }

    /* En-tête */
    .header {
      background-color: #003366;
      color: white;
      padding: 15px;
      text-align: right;
      margin-bottom: 20px;
      border-radius: 5px;
    }

    .header .user {
      font-size: 16px;
    }

    /* Contenu dynamique */
    #dynamic-content {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      min-height: 70vh;
    }

    /* Pied de page */
    .footer {
      background-color: #003366;
      color: white;
      text-align: center;
      padding: 10px;
      width: 100%;
      margin-left: 250px;
    }

    /* Responsive */
    @media screen and (max-width: 768px) {
      .sidebar {
        width: 200px;
      }
      .main-content {
        margin-left: 200px;
      }
    }

    @media screen and (max-width: 500px) {
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
      }
      .main-content {
        margin-left: 0;
      }
      .footer {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2><i class="fas fa-credit-card"></i> PAMECAS CRM</h2>
    
    <!-- Accueil -->
    <div class="menu-item">
      <a href="index.html" class="load-page" data-page="index.html">
        <i class="fas fa-home"></i> Accueil
      </a>
    </div>
    
    <!-- Tableau de bord -->
    <div class="menu-item">
      <a href="tableaubordcommercial.php" class="load-page" data-page="tableaubordcommercial.php">
        <i class="fas fa-tachometer-alt"></i> Tableau de bord
      </a>
    </div>

    <!-- Gestion Prospects -->
    <div class="menu-item has-submenu">
      <a href="#">
        <i class="fas fa-users"></i> Gestion Prospects
      </a>
      <div class="submenu">
        <a href="liste_prospect_co.php" class="load-page" data-page="liste_prospect_co.php">Liste prospects</a>
        <a href="ajoutprospect_co.html" class="load-page" data-page="ajoutprospect_co.html">Ajouter prospect</a>
        <a href="modifie_prospect_co.php" class="load-page" data-page="modifie_prospect_co.php">Modifier prospect</a>
        
      </div>
    </div>

    <!-- Gestion Membres -->
    <div class="menu-item has-submenu">
      <a href="#">
        <i class="fas fa-user-friends"></i> Gestion Membres
      </a>
      <div class="submenu">
        <a href="liste_membre_co.php" class="load-page" data-page="liste_membre_co.php">liste membre</a>
        <a href="ajout_membre_co.html" class="load-page" data-page="ajout_membre_co.html">Ajouter membre</a>
        <a href="modifier_membre_co.php" class="load-page" data-page="modifier_membre_co.php">Modifier membre</a>
      </div>
    </div>

    <!-- Gestion Produits -->
    <div class="menu-item has-submenu">
      <a href="#">
        <i class="fas fa-box-open"></i> Gestion Produits
      </a>
      <div class="submenu">
        <a href="liste_produit_co.php" class="load-page" data-page="liste_produit_co.php">liste produit</a>
        <a href="ajout_produit_co.html" class="load-page" data-page="ajout_produit_co.html">Ajouter produit</a>
        <a href="modifier_produits_co.php" class="load-page" data-page="modifier_produits_co.php">Modifier produit</a>
      </div>
    </div>
 <!-- Interactions -->
 <div class="menu-item has-submenu">
      <a href="#">
        <i class="fas fa-comments"></i>Gestion Interactions
      </a>
      <div class="submenu">
        <a href="liste_interaction_co.php" class="load-page" data-page="liste_interaction_co.php">Liste interactions</a>
        <a href="ajout_interaction_co.php" class="load-page" data-page="ajout_interaction_co.php">Ajouter interaction</a>
        <a href="modifie_interaction_co.php" class="load-page" data-page="modifie_interaction_co.php">Modifier interaction</a>
      </div>
    </div>



    <!-- Ventes et Opportunités -->
    <div class="menu-item has-submenu">
      <a href="#">
        <i class="fas fa-chart-line"></i>Suivi Ventes & Opportunités
      </a>
      <div class="submenu">
        <a href="liste_vente_co.php" class="load-page" data-page="liste_vente_co.php">Liste ventes</a>
        <a href="ajout_vente_co.php" class="load-page" data-page="ajout_vente_co.php">Ajouter vente</a>
        <a href="modifie_vente_co.php" class="load-page" data-page="modifie_vente_co.php">Modifier vente</a>
        <a href="liste_opportunites_co.php" class="load-page" data-page="liste_opportunites_co.php">liste Opportunités</a>
        <a href="ajout_opportunite_co.php" class="load-page" data-page="ajout_opportunite_co.php">Ajouter opportunité</a>
      </div>
    </div>
<!-- suivi des Campagnes -->
<div class="menu-item has-submenu">
      <a href="#">
        <i class="fas fa-chart-line"></i>Suivi des Campagnes
      </a>
      <div class="submenu">
        <a href="liste_campagne_co.php" class="load-page" data-page="liste_campagne_co.php">Liste campagnes</a>
        <a href="ajout_campagne_co.php" class="load-page" data-page="ajout_campagne_co.php">Ajouter campagnes</a>
        <a href="modifie_compagne_co.php" class="load-page" data-page="modifie_campagne_co.php">Modifier campagnes</a>
        <a href="participants_co.php" class="load-page" data-page="participants_co.php">Liste des participants</a>
        <a href="ajout_participant_co.php" class="load-page" data-page="ajout_participant_co.php">Ajouter participants</a>
        <a href="selection_campagne_co.php" class="load-page" data-page="selection_campagne_co.php">Rapport campagne</a>
      </div>
    </div>

    <!-- Déconnexion -->
    <div class="menu-item">
      <a href="index.html"  class="load-page" data-page="index.html"         >
        <i class="fas fa-sign-out-alt"></i> Déconnexion
      </a>
    </div>
  </div>


  <!-- Main content -->
  <div class="main-content">
    <div class="header">
      <span class="user">Bienvenue, <strong>DALAL AKH DIAM THI</strong> - PAMECAS GESTION DES RELATIONS CLIENTS</span>
    </div>

    <div id="dynamic-content">
      <h2><i class="fas fa-tachometer-alt"></i> Bienvenue sur l'interface du CRM PAMECAS</h2>
      <p>Veuillez sélectionner une option dans le menu de gauche pour commencer.</p>
    </div>
  </div>

  <div class="footer">
    &copy; 2025 PAMECAS. Tous droits réservés.
  </div>

  <script>
    $(document).ready(function(){
      // Gestion des menus déroulants
      $(".has-submenu > a").click(function(e){
        e.preventDefault();
        $(this).parent().toggleClass("active");
        $(this).parent().siblings().removeClass("active");
      });

      // Gestion du chargement des pages
      $(".load-page").click(function(e){
        e.preventDefault();
        var page = $(this).data("page");
        
        // Mise à jour des classes actives
        $(".menu-item > a").removeClass("active");
        $(".submenu a").removeClass("active");
        $(this).addClass("active");
        $(this).parents(".menu-item").addClass("active");
        $(this).parents(".menu-item").find("> a").addClass("active");

        // Chargement du contenu
        $("#dynamic-content").html('<div style="text-align:center; padding:50px;"><i class="fas fa-spinner fa-spin fa-3x"></i><p>Chargement en cours...</p></div>');
        $("#dynamic-content").load(page);
      });

      // Charger la page par défaut
      $("#dynamic-content").load("lance_cam.php");
    });
  </script>
</body>
</html>