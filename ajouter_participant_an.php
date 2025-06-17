<?php
// Connexion à la base de données
$conn = new PDO("mysql:host=localhost;dbname=crm", "root", "");

// Récupération des campagnes
$campagnes = $conn->query("SELECT id, nom_campagne FROM campagnes_marketing")->fetchAll(PDO::FETCH_ASSOC);

// Récupération des membres
$membres = $conn->query("SELECT id, nom, prenom FROM membres")->fetchAll(PDO::FETCH_ASSOC);

// Récupération des prospects
$prospects = $conn->query("SELECT id, nom, prenom FROM prospects")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $campagne_id = $_POST['campagne_id'];
    $participant_type = $_POST['participant_cible'];
    $participant_id = $_POST['participant_id'];
    $a_beneficie_credit = isset($_POST['a_beneficie_credit']) ? 1 : 0;
    $date_lancement = $_POST['date_lancement'];
    $date_cloture = $_POST['date_cloture'];

    // Insertion du participant dans la campagne
    $stmt = $conn->prepare("INSERT INTO campagne_participants (campagne_id, participant_cible, participant_id, a_beneficie_credit, date_lancement, date_cloture)
                       VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$campagne_id, 'membre', $membre_id, $credit_beneficie, $date_lancement, $date_cloture]);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter Vente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    .form-client {
      margin-bottom: 100px;
      padding: 15px;
      background-color: #f4f4f4;
      border-radius: 5px;
    }
    .form-client input, .form-client select {
      margin: 10px 0;
      padding: 10px;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
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
      color: white;
      padding: 5px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    main {
      padding: 20px;
      background-color: #2c3e50;
    }
    h2 {
      text-align: center;
      color: white;
    }
  </style>
</head>
<body>

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
  <h2>Ajouter participant à campagne</h2>
  <div class="form-client">

        <form action="ajouter_participant.php" method="POST">
            <div class="mb-3">
                <label for="campagne_id" class="form-label">Campagne</label>
                <select id="campagne_id" name="campagne_id" class="form-select" required>
                    <option value="">Sélectionner une campagne</option>
                    <?php foreach ($campagnes as $campagne): ?>
                        <option value="<?= $campagne['id'] ?>"><?= htmlspecialchars($campagne['nom_campagne']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="participant_cible" class="form-label">Type de participant</label>
                <select id="participant_cible" name="participant_cible" class="form-select" required>
                    <option value="">Sélectionner un type de participant</option>
                    <option value="membre">Membre</option>
                    <option value="prospect">Prospect</option>
                </select>
            </div>

            <div class="mb-3" id="participant_id_div">
                <!-- Liste des membres ou prospects ici -->
            </div>

            <div class="mb-3">
                <label for="a_beneficie_credit" class="form-label">A bénéficié d'un crédit</label>
                <input type="checkbox" id="a_beneficie_credit" name="a_beneficie_credit">
            </div>

             <div class="mb-3">
                <label for="date_lancement" class="form-label">Date lancment</label>
                <input type="date" id="date_lancement" name="">
            </div>
            <div class="mb-3">
                <label for="date_lancement" class="form-label">Date cloture</label>
                <input type="date" id="date_lancement" name="">
            </div>



            <button type="submit" class="btn btn-primary">Ajouter le participant</button>
            <a href="interfaceanimatrice.php" class="btn btn-secondary">Retour</a>
        </form>
    </div>

    <script>
        // Injecter les données PHP en JS
        const membres = <?= json_encode($membres) ?>;
        const prospects = <?= json_encode($prospects) ?>;

        const participantType = document.getElementById('participant_type');
        const participantIdDiv = document.getElementById('participant_id_div');

        participantType.addEventListener('change', function () {
            let html = '';
            if (this.value === 'membre') {
                html += `<label for="participant_id" class="form-label">Sélectionner un membre</label>`;
                html += `<select id="participant_id" name="participant_id" class="form-select" required>`;
                html += `<option value="">Sélectionner un membre</option>`;
                membres.forEach(membre => {
                    html += `<option value="${membre.id}">${membre.nom} ${membre.prenom}</option>`;
                });
                html += `</select>`;
            } else if (this.value === 'prospect') {
                html += `<label for="participant_id" class="form-label">Sélectionner un prospect</label>`;
                html += `<select id="participant_id" name="participant_id" class="form-select" required>`;
                html += `<option value="">Sélectionner un prospect</option>`;
                prospects.forEach(prospect => {
                    html += `<option value="${prospect.id}">${prospect.nom} ${prospect.prenom}</option>`;
                });
                html += `</select>`;
            }
            participantIdDiv.innerHTML = html;
        });
    </script>
</body>
</html>
