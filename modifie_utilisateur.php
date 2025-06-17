<?php
include('conn.php');

// Récupérer tous les utilisateurs
$stmt = $pdo->query("SELECT id, nom, email, role FROM utilisateurs");
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si un formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_utilisateur = $_POST['id_utilisateur'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = $_POST['role'];

    // Vérifier si un nouveau mot de passe est fourni
    if (!empty($mot_de_passe)) {
        $mot_de_passe = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE utilisateurs SET 
            nom = ?, email = ?, mot_de_passe = ?, role = ?,  WHERE id = ?");
        $success = $updateStmt->execute([$nom, $email, $mot_de_passe, $role, $id_utilisateur]);
    } else {
        $updateStmt = $pdo->prepare("UPDATE utilisateurs SET 
            nom = ?, email = ?, role = ? WHERE id = ?");
        $success = $updateStmt->execute([$nom, $email, $role, $id_utilisateur]);
    }

    if ($success) {
        echo "<p style='color:green;'>Utilisateur mis à jour avec succès.</p>";
        // Recharger la liste après modification
        header("Refresh:0");
    } else {
        echo "<p style='color:red;'>Erreur lors de la mise à jour.</p>";
    }
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des ventes</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f4f4; 
            margin: 0;
        }
        header {
            background-color:  #003366;
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
        header .logout {
            background-color: rgb(236, 9, 9);
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .container { 
            width: 100%; 
            margin: auto; 
            background: white; 
            padding: 20px; 
            border-radius: 5px; 
            overflow-x: auto;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background-color: #003366; 
            color: white; 
        }
        button { 
            padding: 5px 10px; 
            cursor: pointer; 
        }
        #editForm { 
            display: none; 
            margin-top: 20px; 
            padding: 20px; 
            background: #f4f4f4; 
            border-radius: 5px;
        }
        input, select, textarea { 
            margin: 5px 0 15px; 
            padding: 8px; 
            width: 100%; 
        }
        .hidden { display: none; }
        .checkbox-group label { display: inline-block; margin-right: 15px; }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <img src="pamecas.jpg" alt="Pamecas" width="70">
        <h1>PAMECAS - GESTION DES ventes</h1>
    </div>
    <a href="index.html" class="logout">Logout</a>
</header>

<div class="container">
    <h2>Liste des Utilisateurs</h2>
    
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Action</th>
        </tr>

        <?php foreach ($utilisateurs as $utilisateur): ?>
        <tr>
            <td><?= htmlspecialchars($utilisateur['id']) ?></td>
            <td><?= htmlspecialchars($utilisateur['nom']) ?></td>
            <td><?= htmlspecialchars($utilisateur['email']) ?></td>
            <td><?= htmlspecialchars($utilisateur['role']) ?></td>
            <td>
                <button onclick="openForm(
                    <?= $utilisateur['id'] ?>, 
                    '<?= htmlspecialchars($utilisateur['nom'], ENT_QUOTES) ?>', 
                    '<?= htmlspecialchars($utilisateur['email'], ENT_QUOTES) ?>', 
                    '<?= htmlspecialchars($utilisateur['role'], ENT_QUOTES) ?>'
                )">Modifier</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Formulaire caché pour modifier l'utilisateur -->
    <div id="editForm">
        <h3>Modifier Utilisateur</h3>
        <form method="POST">
            <input type="hidden" id="id_utilisateur" name="id_utilisateur">
            
            <label>ID Utilisateur :</label>
            <div id="user_id_display" style="padding: 8px 0; font-weight: bold;"></div>

            <label>Nom :</label>
            <input type="text" id="nom" name="nom" required>

            <label>Email :</label>
            <input type="email" id="email" name="email" required>

            <label>Mot de passe (laisser vide pour ne pas changer) :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe">

            <label>Rôle :</label>
            <select id="role" name="role" required>
                <option value="Administrateur">Administrateur</option>
                <option value="Directeur">Directeur</option>
                <option value="Commercial">Commercial</option>
                <option value="Animatrice">Animatrice</option>
            </select>

            <button type="submit">Mettre à jour</button>
            <button type="button" onclick="closeForm()">Annuler</button>
        </form>
    </div>

    <script>
        function openForm(id, nom, email, role) {
            document.getElementById('id_utilisateur').value = id;
            document.getElementById('user_id_display').textContent = id;
            document.getElementById('nom').value = nom;
            document.getElementById('email').value = email;
            document.getElementById('role').value = role;

            document.getElementById('editForm').style.display = 'block';
            
            // Faire défiler jusqu'au formulaire
            document.getElementById('editForm').scrollIntoView({ behavior: 'smooth' });
        }

        function closeForm() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>
</html>