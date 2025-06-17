<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'crm';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Traitement de l'ajout d'un produit si le formulaire est soumis
if (isset($_POST['ajouter'])) {
    // Récupérer les valeurs du formulaire
    $nom_produit = $_POST['nom_produit'];
    $type_produit= $_POST['type_produit'];
    $description = $_POST['description'];
    $conditions = $_POST['conditions'];
    $taux_interet = $_POST['taux_interet'];
    $montant = $_POST['montant'];
    $created_at = $_POST['created_at'];
    $updated_at = $_POST['updated_at'];
    

    // Vérifier que les champs requis sont renseignés (ici "nom" et "reference")
    if (!empty($nom_produit) && !empty($type_produit)) {
        // Préparation de la requête d'insertion dans la table produits_financiers
        $stmt = $conn->prepare("INSERT INTO produits (nom_produit,
            type_produit,
            description,
            conditions,
            taux_interet ,
            montant,
            created_at, 
            updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            die("Erreur de préparation : " . $conn->error);
        }
        
        // Liaison des paramètres
        // Types : "ssssddddds" => 4 chaînes, 5 nombres (double) et une chaîne pour la date
        if (!$stmt->bind_param("ssssssss", $nom_produit,
        $type_produit, 
        $description,
          $conditions,
           $taux_interet,
            $montant,
             $created_at,
              $updated_at,
               )) {
            die("Erreur lors du binding des paramètres : " . $stmt->error);
        }
        
        if ($stmt->execute()) {
            // ✅ Redirection vers la page de la liste des clients après ajout réussi
            header("Location: liste_produit.php"); 
            exit();
        } else {
            echo "Erreur lors de l'insertion : " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Veuillez remplir tous les champs requis.";
    }
}

$conn->close();

?>
