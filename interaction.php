<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=crm;charset=utf8', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Récupération des campagnes et agents pour les menus déroulants
$campagnes = $pdo->query("SELECT id, nom FROM campagnes_marketing ORDER BY nom")->fetchAll();
$agents = $pdo->query("SELECT id, nom, prenom FROM utilisateurs ORDER BY nom")->fetchAll();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO interactions (
            campagne_id, cible_type, cible_id, canal, utilisteur_id, 
            date_interaction, duree, statut, notes, satisfaction, 
            pieces_jointes, created_at
        ) VALUES (
            :campagne_id, :cible_type, :cible_id, :canal, :utilisateur_id, 
            :date_interaction, :duree, :statut, :notes, :satisfaction, 
            :pieces_jointes, NOW()
        )");
        
        // Gestion de l'upload de fichier
        $file_path = null;
        if (!empty($_FILES['piece_jointe']['name'])) {
            $upload_dir = 'uploads/interactions/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = uniqid() . '_' . basename($_FILES['piece_jointe']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['piece_jointe']['tmp_name'], $target_file)) {
                $file_path = $target_file;
            }
        }
        
        $stmt->execute([
            ':campagne_id' => $_POST['campagne_id'],
            ':cible_type' => $_POST['cible_type'],
            ':cible_id' => $_POST['cible_id'],
            ':canal' => $_POST['canal'],
            ':utilisateur_id' => $_POST['utilisateur_id'],
            ':date_interaction' => $_POST['date_interaction'] . ' ' . $_POST['heure_interaction'],
            ':duree' => $_POST['duree'] ?? null,
            ':statut' => $_POST['statut'],
            ':notes' => $_POST['notes'],
            ':satisfaction' => $_POST['satisfaction'] ?? null,
            ':pieces_jointes' => $file_path
        ]);
        
        header('Location: liste_interactions.php?success=1');
        exit;
    } catch (PDOException $e) {
        $error = "Erreur lors de l'ajout de l'interaction: " . $e->getMessage();
    }
}

// Fonction pour récupérer les cibles selon le type
function getCibles($pdo, $type) {
    switch ($type) {
        case 'prospect':
            return $pdo->query("SELECT id, CONCAT(prenom, ' ', nom) AS nom FROM prospects ORDER BY nom")->fetchAll();
        case 'membre_individuel':
            return $pdo->query("SELECT id, CONCAT(prenom, ' ', nom) AS nom FROM membre_individuel ORDER BY nom")->fetchAll();
        case 'membre_groupe':
            return $pdo->query("SELECT id, nom FROM groupes ORDER BY nom")->fetchAll();
        case 'dirigeant_entreprise':
            return $pdo->query("SELECT id, CONCAT(prenom_dirigant, ' ', nom_dirigant, ' - ', nom_entreprise) AS nom FROM membre_entreprise ORDER BY nom")->fetchAll();
        default:
            return [];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Interaction - PAMECAS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        select, input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        textarea {
            min-height: 100px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        .error {
            color: #e74c3c;
            margin-top: 5px;
        }
        
        .success {
            color: #2ecc71;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #d5f5e3;
            border-radius: 4px;
        }
        
        .required:after {
            content: " *";
            color: #e74c3c;
        }
        
        .file-upload {
            border: 2px dashed #ddd;
            padding: 20px;
            text-align: center;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .file-upload:hover {
            border-color: #3498db;
        }
        
        #cible-container {
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-comments"></i> Nouvelle Interaction</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form action="ajout_interaction.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="campagne_id" class="required">Campagne</label>
                    <select id="campagne_id" name="campagne_id" required>
                        <option value="">-- Sélectionnez une campagne --</option>
                        <?php foreach ($campagnes as $campagne): ?>
                            <option value="<?= $campagne['id'] ?>"><?= htmlspecialchars($campagne['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="a_id" class="required">Agent</label>
                    <select id="agent_id" name="agent_id" required>
                        <option value="">-- Sélectionnez un agent --</option>
                        <?php foreach ($agents as $agent): ?>
                            <option value="<?= $agent['id'] ?>"><?= htmlspecialchars($agent['prenom'] . ' ' . $agent['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="cible_type" class="required">Type de cible</label>
                    <select id="cible_type" name="cible_type" required onchange="updateCibleList()">
                        <option value="">-- Sélectionnez un type --</option>
                        <option value="prospect">Prospect</option>
                        <option value="membre_individuel">Membre Individuel</option>
                        <option value="membre_groupe">Membre Groupe</option>
                        <option value="dirigeant_entreprise">Dirigeant d'Entreprise</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="cible_id" class="required">Cible</label>
                    <div id="cible-container">
                        <select id="cible_id" name="cible_id" required>
                            <option value="">-- Sélectionnez d'abord le type --</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="canal" class="required">Canal d'interaction</label>
                    <select id="canal" name="canal" required>
                        <option value="">-- Sélectionnez un canal --</option>
                        <option value="appel">Appel téléphonique</option>
                        <option value="email">Email</option>
                        <option value="visite">Visite</option>
                        <option value="reunion">Réunion</option>
                        <option value="sms">SMS</option>
                        <option value="chat">Chat</option>
                        <option value="courrier">Courrier</option>
                        <option value="reseaux_sociaux">Réseaux sociaux</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="statut" class="required">Statut</label>
                    <select id="statut" name="statut" required>
                        <option value="planifié">Planifié</option>
                        <option value="réalisé">Réalisé</option>
                        <option value="annulé">Annulé</option>
                        <option value="reporté">Reporté</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="date_interaction" class="required">Date</label>
                    <input type="date" id="date_interaction" name="date_interaction" required 
                           value="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="form-group">
                    <label for="heure_interaction" class="required">Heure</label>
                    <input type="time" id="heure_interaction" name="heure_interaction" required 
                           value="<?= date('H:i') ?>">
                </div>
                
                <div class="form-group">
                    <label for="duree">Durée (minutes)</label>
                    <input type="number" id="duree" name="duree" min="1" max="300">
                </div>
            </div>
            
            <div class="form-group">
                <label for="satisfaction">Niveau de satisfaction</label>
                <select id="satisfaction" name="satisfaction">
                    <option value="">-- Non évalué --</option>
                    <option value="1">1 ★</option>
                    <option value="2">2 ★★</option>
                    <option value="3">3 ★★★</option>
                    <option value="4">4 ★★★★</option>
                    <option value="5">5 ★★★★★</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" placeholder="Détails de l'interaction..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Pièce jointe</label>
                <div class="file-upload">
                    <input type="file" id="piece_jointe" name="piece_jointe" style="display: none;">
                    <label for="piece_jointe" style="cursor: pointer;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 24px;"></i><br>
                        <span id="file-name">Glissez-déposez un fichier ou cliquez pour sélectionner</span>
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer l'interaction
                </button>
                <a href="liste_interactions.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>

    <script>
        // Afficher le nom du fichier sélectionné
        document.getElementById('piece_jointe').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Glissez-déposez un fichier ou cliquez pour sélectionner';
            document.getElementById('file-name').textContent = fileName;
        });
        
        // Mettre à jour la liste des cibles selon le type sélectionné
        function updateCibleList() {
            const cibleType = document.getElementById('cible_type').value;
            const cibleContainer = document.getElementById('cible-container');
            const cibleSelect = document.getElementById('cible_id');
            
            if (!cibleType) {
                cibleContainer.style.display = 'none';
                return;
            }
            
            // Afficher le conteneur
            cibleContainer.style.display = 'block';
            
            // Charger les cibles via AJAX
            fetch(`get_cibles.php?type=${cibleType}`)
                .then(response => response.json())
                .then(data => {
                    cibleSelect.innerHTML = '<option value="">-- Sélectionnez une cible --</option>';
                    
                    data.forEach(cible => {
                        const option = document.createElement('option');
                        option.value = cible.id;
                        option.textContent = cible.nom;
                        cibleSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    cibleSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                });
        }
        
        // Initialiser la date et l'heure actuelles
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('date_interaction').valueAsDate = new Date();
        });
    </script>
</body>
</html>