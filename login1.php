<?php
// Vérifie si la méthode de requête est POST (quand le formulaire est soumis)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère l'email depuis le formulaire
    $email = $_POST['email'];
    // Récupère le mot de passe depuis le formulaire
    $password = $_POST['password'];
    
    // Prépare une requête SQL pour trouver l'utilisateur par email
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    // Exécute la requête avec l'email comme paramètre
    $stmt->execute([$email]);
    // Récupère le résultat sous forme de tableau associatif
    $user = $stmt->fetch();
    
    // Vérifie si l'utilisateur existe ET si le mot de passe correspond
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        // Stocke l'ID de l'utilisateur en session
        $_SESSION['user_id'] = $user['id'];
        // Stocke le rôle de l'utilisateur en session
        $_SESSION['user_role'] = $user['role'];
        // Redirige vers le tableau de bord
        header('Location: dashboard.php');
        // Termine l'exécution du script
        exit;
    } else {
        // Message d'erreur si l'authentification échoue
        $error = "Identifiants incorrects";
    }
}
?>

<!-- Formulaire de connexion HTML -->
<form method="post">
    <!-- Champ email -->
    <input type="email" name="email" required placeholder="Email">
    <!-- Champ mot de passe -->
    <input type="password" name="password" required placeholder="Mot de passe">
    <!-- Bouton de soumission -->
    <button type="submit">Se connecter</button>
    <!-- Affiche l'erreur si elle existe -->
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
</form>