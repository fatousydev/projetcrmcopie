<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=crm", "root", "");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_nom'] = $user['nom'];

        // Redirection selon le rôle
        if ($user['role'] === 'admin') {
            header("Location:tableaubordadmin.php");
        } elseif ($user['role'] === 'directeur') {
            header("Location: tableauborddirecteur.php");
        } elseif ($user['role'] === 'commercial') {
            header("Location: tableaubordcommercial.php");
        } elseif ($user['role'] === 'animatrice') {
            header("Location: tableaubordanimatrice.php");
        } else {
            // Par défaut
            header("Location: tableaubord1.php");
        }
        exit();
    } else {
        echo "Identifiants incorrects.";
    }
}
?>
