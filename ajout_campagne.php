<?php
// Connexion à la base de données avec PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=crm;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // Activer les exceptions pour les erreurs PDO
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    // Récupérer les données du formulaire
    $nom_campagne = $_POST['nom_campagne'];
    $date_lancement = $_POST['date_lancement'];
    $date_cloture = $_POST['date_cloture']; // Correction de la variable
    $cible = $_POST['cible'];
    $canal_utilise = $_POST['canal_utilise'];
    $resultats = $_POST['resultats'];

    // Validation des données (exemple simple)
    if (empty($nom_campagne) || empty($date_lancement) || empty($date_cloture) || empty($cible) || empty($canal_utilise)) {
        die("Tous les champs obligatoires doivent être remplis.");
    }

    // Préparer la requête d'insertion
    try {
        $stmt = $pdo->prepare("INSERT INTO campagnes_marketing 
            (nom_campagne, date_lancement, date_cloture, cible, canal_utilise, resultats, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");

        // Exécuter la requête avec les données du formulaire
        $stmt->execute([$nom_campagne, $date_lancement, $date_cloture, $cible, $canal_utilise, $resultats]);

        // Rediriger vers la liste des campagnes après l'ajout
        header("Location: liste_campagne.php");
        exit;
    } catch (PDOException $e) {
        die("Erreur lors de l'insertion des données : " . $e->getMessage());
    }
}
?>
