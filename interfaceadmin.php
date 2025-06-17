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

    /* Sidebar amélioré */
    .sidebar {
      background-color: #1a2a43;
      color: white;
      width: 250px;
      height: 100vh;
      position: fixed;
      padding-top: 15px;
      overflow-y: auto;
      z-index: 1000;
      box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    .sidebar h2 {
      margin: 0 15px 20px;
      padding: 12px;
      color: #fff;
      background-color: #003366;
      border-radius: 5px;
      text-align: center;
      font-size: 1.1rem;
    }

    /* Menu principal aligné */
    .menu-container {
      padding: 0 10px;
    }

    .menu-item {
      position: relative;
      margin-bottom: 3px;
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
      margin-right: 12px;
      width: 20px;
      text-align: center;
      font-size: 1rem;
    }
    
    /* Sous-menu */
    .submenu {
      display: none;
      background-color: #0d1a2b;
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
      background-color: #0054A6;
      color: white;
      border-left: 4px solid #65a3f3;
    }

    .menu-item.active .submenu {
      display: block;
    }

    /* Sous-sous-menu */
    .sub-submenu {
      display: none;
      background-color: #1a2a43;
      padding-left: 15px;
    }
    
    .sub-submenu a {
      padding-left: 45px;
    }
    
    .submenu-item.active .sub-submenu {
      display: block;
    }

    /* Indicateur de menu ouvert */
    .menu-item.has-submenu > a::after,
    .submenu-item.has-submenu > a::after {
      content: '\f078';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      float: right;
      transition: transform 0.3s;
      font-size: 12px;
    }

    .menu-item.has-submenu.active > a::after,
    .submenu-item.has-submenu.active > a::after {
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
      padding: 15px 20px;
      text-align: right;
      margin-bottom: 20px;
      border-radius: 5px;
    }

    .header .user {
      font-size: 0.95rem;
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
      padding: 12px;
      width: calc(100% - 250px);
      margin-left: 250px;
    }

    /* Responsive */
    @media screen and (max-width: 992px) {
      .sidebar {
        width: 70px;
        overflow: hidden;
        transition: width 0.3s ease;
      }
      
      .sidebar:hover {
        width: 250px;
      }
      
      .sidebar h2 {
        font-size: 0;
        padding: 12px 5px;
      }
      
      .sidebar h2::after {
        content: 'CRM';
        font-size: 1.1rem;
      }
      
      .menu-item > a {
        justify-content: center;
        padding: 12px 5px;
      }
      
      .menu-item > a span {
        display: none;
      }
      
      .sidebar:hover .menu-item > a span {
        display: inline;
      }
      
      .menu-item > a i {
        margin-right: 0;
        font-size: 1.1rem;
      }
      
      .sidebar:hover .menu-item > a i {
        margin-right: 12px;
      }
      
      .submenu, .sub-submenu {
        display: none !important;
        position: absolute;
        left: 70px;
        top: 0;
        width: 180px;
        background-color: #1a2a43;
        z-index: 1001;
      }
      
      .sidebar:hover .submenu,
      .sidebar:hover .sub-submenu {
        position: static;
        width: auto;
      }
      
      .main-content {
        margin-left: 70px;
      }
      
      .footer {
        width: calc(100% - 70px);
        margin-left: 70px;
      }
    }

    @media screen and (max-width: 576px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        max-height: 60px;
        overflow: hidden;
        transition: max-height 0.3s ease;
      }
      
      .sidebar:hover {
        max-height: 100vh;
      }
      
      .sidebar h2 {
        font-size: 1.1rem;
        text-align: left;
        padding-left: 15px;
      }
      
      .sidebar h2::after {
        content: none;
      }
      
      .menu-item > a {
        justify-content: flex-start;
        padding: 10px 15px;
      }
      
      .menu-item > a span {
        display: inline;
      }
      
      .menu-item > a i {
        margin-right: 12px;
      }
      
      .submenu, .sub-submenu {
        position: static;
        width: auto;
        display: none;
        background-color: rgba(0, 84, 166, 0.2);
      }
      
      .menu-item.has-submenu.active .submenu,
      .submenu-item.has-submenu.active .sub-submenu {
        display: block;
      }
      
      .main-content {
        margin-left: 0;
      }
      
      .footer {
        width: 100%;
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2><i class="fas fa-credit-card"></i> PAMECAS CRM</h2>
    
    <div class="menu-container">
      <!-- Accueil -->
      <div class="menu-item">
        <a href="index.html" class="load-page" data-page="index.html">
          <i class="fas fa-home"></i> <span>Accueil</span>
        </a>
      </div>
      
      <!-- Tableau de bord -->
      <div class="menu-item">
        <a href="tableaubordadmin.php" class="load-page" data-page="tableaubordadmin.php">
          <i class="fas fa-tachometer-alt"></i> <span>Tableau de bord</span>
        </a>
      </div>

      <!-- Gestion Prospects -->
      <div class="menu-item has-submenu">
        <a href="#">
          <i class="fas fa-users"></i> <span>Gestion Prospects</span>
        </a>
        <div class="submenu">
          <a href="liste_prospect.php" class="load-page" data-page="liste_prospect.php">Liste prospects</a>
          <a href="ajoutprospect.html" class="load-page" data-page="ajoutprospect.html">Ajouter prospect</a>
          <a href="modifie_prospect.php" class="load-page" data-page="modifie_prospect.php">Modifier prospect</a>
        </div>
      </div>

      <!-- Gestion Membres -->
      <div class="menu-item has-submenu">
        <a href="#">
          <i class="fas fa-user-friends"></i> <span>Gestion Membres</span>
        </a>
        <div class="submenu">
          <!-- Membre Individuel -->
          <div class="submenu-item has-submenu">
            <a href="#">Membre Individuel</a>
            <div class="sub-submenu">
              <a href="liste_membre_individuel.php" class="load-page" data-page="liste_membre_individuel.php">Liste membres</a>
              <a href="ajout_membre_individuel.html" class="load-page" data-page="ajout_membre_individuel.html">Ajouter membre</a>
            </div>
          </div>
          
          <!-- Membre Groupe -->
          <div class="submenu-item has-submenu">
            <a href="#">Membre Groupe</a>
            <div class="sub-submenu">
              <a href="liste_membre_groupe.php" class="load-page" data-page="liste_membre_groupe.php">Liste groupes</a>
              <a href="ajout_membre_groupe.html" class="load-page" data-page="ajout_membre_groupe.html">Ajouter groupe</a>
              
            </div>
          </div>
          
          <!-- Membre Entreprise -->
          <div class="submenu-item has-submenu">
            <a href="#">Membre Entreprise</a>
            <div class="sub-submenu">
              <a href="liste_membre_entreprise.php" class="load-page" data-page="liste_membre_entreprise.php">Liste entreprises</a>
              <a href="ajout_membre_entreprise.html" class="load-page" data-page="ajout_membre_entreprise.html">Ajouter entreprise</a>
              <a href="modifier_membre_entreprise.php" class="load-page" data-page="modifier_membre_entreprise.php">Modifier entreprise</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Gestion Produits -->
      <div class="menu-item has-submenu">
        <a href="#">
          <i class="fas fa-box-open"></i> <span>Gestion Produits</span>
        </a>
        <div class="submenu">
          <a href="liste_produit.php" class="load-page" data-page="liste_produit.php">Liste produit</a>
          <a href="ajout_produit.html" class="load-page" data-page="ajout_produit.html">Ajouter produit</a>
          <a href="modifier_produits.php" class="load-page" data-page="modifier_produits.php">Modifier produit</a>
        </div>
      </div>
      
      <!-- Interactions -->
      <div class="menu-item has-submenu">
        <a href="#">
          <i class="fas fa-comments"></i> <span>Gestion Interactions</span>
        </a>
        <div class="submenu">
          <a href="liste_interaction.php" class="load-page" data-page="liste_interaction.php">Liste interactions</a>
          <a href="ajout_interaction.php" class="load-page" data-page="ajout_interaction.php">Ajouter interaction</a>
          <a href="modifie_interaction.php" class="load-page" data-page="modifie_interaction.php">Modifier interaction</a>
        </div>
      </div>
      
      <!-- Ventes et Opportunités -->
      <div class="menu-item has-submenu">
        <a href="#">
          <i class="fas fa-chart-line"></i> <span>Suivi Ventes & Opportunités</span>
        </a>
        <div class="submenu">
          <a href="liste_vente.php" class="load-page" data-page="liste_vente.php">Liste ventes</a>
          <a href="ajout_vente.php" class="load-page" data-page="ajout_vente.php">Ajouter vente</a>
          <a href="modifie_vente.php" class="load-page" data-page="modifie_vente.php">Modifier vente</a>
          <a href="liste_opportunites.php" class="load-page" data-page="liste_opportunites.php">Liste Opportunités</a>
          <a href="ajout_opportunite.php" class="load-page" data-page="ajout_opportunite.php">Ajouter opportunité</a>
        </div>
      </div>

      <!-- Campagnes Marketing -->
      <div class="menu-item has-submenu">
        <a href="#">
          <i class="fas fa-bullhorn"></i> <span>Suivi Campagnes Marketing</span>
        </a>
        <div class="submenu">
          <a href="liste_campagne.php" class="load-page" data-page="liste_campagne.php">Liste campagnes</a>
          <a href="ajout_campagne.html" class="load-page" data-page="ajout_campagne.html">Ajouter campagne</a>
          <a href="modifie_campagne.php" class="load-page" data-page="modifie_campagne.php">Modifier campagne</a>
          <a href="participant.php" class="load-page" data-page="participant.php">Liste participants</a>
          <a href="ajouter_participant.php" class="load-page" data-page="ajouter_participant.php">Ajouter participant</a>
          <a href="selection_campagne.php" class="load-page" data-page="selection_campagne.php">Rapport campagnes</a>
        </div>
      </div>
    
      <!-- Utilisateurs -->
      <div class="menu-item has-submenu">
        <a href="#">
          <i class="fas fa-user-cog"></i> <span>Gestion Utilisateurs</span>
        </a>
        <div class="submenu">
          <a href="list_utilisateur.php" class="load-page" data-page="list_utilisateur.php">Liste utilisateurs</a>
          <a href="ajout_utilisateurs.php" class="load-page" data-page="ajout_utilisateurs.php">Ajouter utilisateur</a>
          <a href="modifie_utilisateur.php" class="load-page" data-page="modifie_utilisateur.php">Modifier utilisateur</a>
          <a href="liste_utilisateur.php" class="load-page" data-page="liste_utilisateur.php">Assignations</a>
        </div>
      </div>

      <!-- Caisses -->
      <div class="menu-item has-submenu">
        <a href="#">
          <i class="fas fa-cash-register"></i> <span>Caisses</span>
        </a>
        <div class="submenu">
          <a href="liste_caisse1.php" class="load-page" data-page="liste_caisse1.php">Liste caisses</a>
        </div>
      </div>

      <!-- Déconnexion -->
      <div class="menu-item">
        <a href="index.html" class="load-page" data-page="index.html">
          <i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span>
        </a>
      </div>
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
        $(".sub-submenu a").removeClass("active");
        $(this).addClass("active");
        $(this).parents(".menu-item").addClass("active");
        $(this).parents(".submenu-item").addClass("active");
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