<?php
session_start();
$conn = new PDO("mysql:host=localhost;dbname=crm", "root", "");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $mot_de_passe = $_POST["mot_de_passe"];

    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['utilisateur_id'] = $user['id'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['role'] = $user['role'];
        header("Location: tableau_de_bord.php");
        exit;
    } else {
        echo "<p style='color:red'>Email ou mot de passe incorrect.</p>";
    }
}
?>

<h2>Connexion</h2>
<form method="POST">
    Email : <input type="email" name="email" required><br>
    Mot de passe : <input type="password" name="mot_de_passe" required><br>
    <button type="submit">Se connecter</button>
</form>
