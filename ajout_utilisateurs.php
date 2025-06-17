
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pamecas - Gestion Client</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    /* En-tête */
    header {
      background-color: #65a3f3;
      color: white;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header .logo {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    header .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    header .user-info span {
      background-color: #0368d9;
      border-radius: 50%;
      padding: 10px 15px;
      font-weight: bold;
      color: white;
    }
    header .logout {
      background-color: rgb(236, 9, 9);
      color:white;
      padding: 5px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    /* Contenu principal */
    main {
      padding: 20px;
      background-color:#2c3e50;
    }

    /* Formulaire d'ajout de client */
    .form-client {
      margin-bottom:100px;
      padding: 15px;
      background-color: #f4f4f4;
      border-radius: 5px;
    }
    .form-client input, .form-client select, .form-client textarea {
      margin: 10px 0;
      padding: 10px;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    /* Tableau des clients */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    table, th, td {
      border: 1px solid #ccc;
    }
    th, td {
      padding: 10px;
      text-align: left;
    }
  </style>
</head>
<body>
  <!-- En-tête -->
  <header>
    <div class="logo">
      <img src="pamecas.jpg" alt="Pamecas" width="70">
      <h1>PAMECAS - GESTION DE LA RELATION CLIENT</h1>
    </div>
    <div class="user-info">
      <span>A</span>
      <a href="accueil.html" class="logout">Logout</a>
    </div>
  </header>
  <main>
    <!-- Formulaire d'ajout de client -->
    <h2 style="text-align: center; color:white" >Ajouter utilisateur</h2>
    <div class="form-client">
      <form action="ajout_utilisateur.php" method="POST">
        <label>Nom:</label>
        <input type="text" name="nom" required><br>
    
        <label>Email:</label>
        <input type="email" name="email" required><br>
    
        <label>Mot de passe:</label>
        <input type="password" name="mot_de_passe" required><br>
    
        <label>Rôle:</label>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="directeur">Directeur</option>
            <option value="commercial">Commercial</option>
            <option value="animatrice">Animatrice</option>
        </select><br>
          <button type="submit" name="ajouter">Ajouter</button>
         
          <button type="submit" name="retour"><a href="interfaceadmin.php">Retour</a></button>
         
    </form>
    </div>
  </main>
  <script>
    // Fonction pour le bouton "Logout"
    document.querySelector('.logout').addEventListener('click', () => {
      alert('Déconnexion en cours...');
    });
  </script>
</body>
</html>
