<?php
// config.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $db = new PDO(
        'mysql:host=localhost;dbname=crm;charset=utf8',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Fonction pour récupérer les membres individuels
function getMembresIndividuels($db) {
    $query = "SELECT id, numero_membre, statut, date_admission, nom, prenom, fonction, 
                     num_telephone, email, region, adresse, numero_piece, besoins, 
                     source_connaissance, personne_contact, relation_contact, telephone_contact
              FROM membre_individuel";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les membres entreprises
function getMembresEntreprises($db) {
    $query = "SELECT id, numero_compt_entreprise, nom_entreprise, nom_dirigant, prenom_dirigant,
                     statut, date_admission, num_telephone, numero_piece, email, adresse, 
                     region, classification, effectif, activites, besoins, source_connaissance,
                     personne_contact, relation_contact, telephone_contact
              FROM membre_entreprise";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les groupements avec leurs membres
function getGroupementsAvecMembres($db) {
    $query = "SELECT id, numero_groupement, nom_groupement, statut, date_admission, 
                     email, num_telephone, num_piece_identite, adresse, region, 
                     source_connaissance, personne_contact, relation_contact, 
                     telephone_contact, effectif, activites, besoins
              FROM groupement";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $groupements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($groupements as &$groupement) {
        $query = "SELECT id, nom, prenom, adress, region, date_naissance, 
                         num_piece_identite, num_passeport, num_telephone, email,
                         poste_dans_groupement, date_adhesion, statut
                  FROM membre_groupe
                  WHERE id_groupement = :id_groupement";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_groupement', $groupement['id']);
        $stmt->execute();
        $groupement['membres'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return $groupements;
}
?>