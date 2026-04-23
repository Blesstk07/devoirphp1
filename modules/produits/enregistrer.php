<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');
require_once('../../includes/fonctions-produits.php');

verifierConnexion();
verifierRole(['manager', 'super_admin']);

/* =========================
   CODE BARRE (GET + POST SAFE)
========================= */
$code = $_GET['code'] ?? $_POST['code'] ?? '';
$code = trim($code);

$produit = null;
$erreurs = [];
$success = false;

/* =========================
   RECHERCHE PRODUIT
========================= */
if ($code !== '') {
    $produit = trouverProduit($code);
}

/* =========================
   AJOUT PRODUIT
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code = trim($_POST['code'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $date = $_POST['date'] ?? '';
    $quantite = intval($_POST['quantite'] ?? 0);

    /* VALIDATION */
    if ($code === '') $erreurs[] = "Code barre manquant ❌";
    if ($nom === '') $erreurs[] = "Nom obligatoire";
    if ($prix <= 0) $erreurs[] = "Prix invalide";
    if ($quantite < 0) $erreurs[] = "Quantité invalide";
    if ($date === '') $erreurs[] = "Date obligatoire";

    if (empty($erreurs)) {

        $produits = lireProduits();

        $exists = false;

        foreach ($produits as $p) {
            if (($p['code_barre'] ?? '') === $code) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {

            $produits[] = [
                "code_barre" => $code,
                "nom" => $nom,
                "prix_unitaire_ht" => $prix,
                "date_expiration" => $date,
                "quantite_stock" => $quantite,
                "date_enregistrement" => date('Y-m-d')
            ];

            sauvegarderProduits($produits);
            $success = true;

        } else {
            $erreurs[] = "Produit déjà existant ❌";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Enregistrer produit</title>

<link rel="stylesheet" href="/TP/assets/css/style.css">

<style>
body {
    background: #000;
    color: white;
    font-family: Arial;
    margin: 0;
    display: flex;
    justify-content: center;
}

.container {
    width: 100%;
    max-width: 750px;
    text-align: center;
    margin-top: 40px;
}

.box {
    background: #111;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(255,102,0,0.2);
}

/* INPUTS */
input {
    width: 100%;
    padding: 10px;
    margin: 6px 0;
    border-radius: 8px;
    border: none;
    outline: none;
}

/* BUTTONS */
.btn {
    background: linear-gradient(45deg,#ff6600,#ff3300);
    color: white;
    font-weight: bold;
    cursor: pointer;
    border: none;
    padding: 10px;
    border-radius: 8px;
    width: 100%;
    margin-top: 8px;
}

.btn:hover {
    box-shadow: 0 0 15px #ff6600;
}

/* CAMERA */
.camera-box {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

video {
    width: 260px;
    height: 260px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #ff6600;
}

/* BUTTON GROUP */
.camera-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
}

.stop-btn {
    background: red;
}

/* ALERTS */
.success {
    background: rgba(0,255,0,0.15);
    padding: 10px;
    border-left: 5px solid green;
    margin-bottom: 10px;
}

.error {
    background: rgba(255,0,0,0.15);
    padding: 10px;
    border-left: 5px solid red;
    margin-bottom: 10px;
}

h1 {
    color: #ff6600;
}
</style>
</head>

<body>

<div class="container">

<div class="box">

<h1>📦 Enregistrement produit</h1>

<!-- SUCCESS -->
<?php if ($success): ?>
<div class="success">✔ Produit enregistré avec succès</div>
<?php endif; ?>

<!-- ERRORS -->
<?php if (!empty($erreurs)): ?>
<div class="error">
    <?php foreach ($erreurs as $e): ?>
        <p><?= $e ?></p>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- PRODUIT EXISTANT -->
<?php if ($produit): ?>
    <h3>✔ Produit trouvé</h3>
    <p><strong><?= $produit['nom'] ?></strong></p>
<?php endif; ?>

<!-- FORM -->
<?php if ($code && !$produit): ?>

<form method="POST">

    <input type="hidden" name="code" value="<?= htmlspecialchars($code) ?>">

    <input type="text" name="nom" placeholder="Nom produit" required>
    <input type="number" step="0.01" name="prix" placeholder="Prix HT" required>
    <input type="date" name="date" required>
    <input type="number" name="quantite" placeholder="Quantité" required>

    <button class="btn" type="submit">💾 Enregistrer</button>
</form>

<?php endif; ?>

<!-- SCANNER -->
<h3 style="color:#ff6600; margin-top:20px;">📷 Scanner produit</h3>

<div class="camera-box">
    <video id="video"></video>
</div>

<div class="camera-buttons">
    <button class="btn" onclick="startScanner('/TP/modules/produits/enregistrer.php')">
        🎥 Activer caméra
    </button>

    <button class="btn stop-btn" onclick="stopScanner()">
        ⛔ Stop
    </button>
</div>

<p id="result"></p>

</div>
</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script src="/TP/assets/js/scanner.js"></script>

<script>
window.onload = function () {
    startScanner('/TP/modules/produits/enregistrer.php');
};
</script>

</body>
</html>