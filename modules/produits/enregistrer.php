<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');
require_once('../../includes/fonctions-produits.php');

verifierConnexion();
verifierRole(['manager', 'super_admin', 'caissier']);

$code = $_GET['code'] ?? null;
$produit = null;
$erreurs = [];
$success = false;

if (!empty($code)) {
    $code = trim($code);
    $produit = trouverProduit($code);
}

/* =========================
    AJOUT PRODUIT
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code = trim($_POST['code']);
    $nom = trim($_POST['nom']);
    $prix = floatval($_POST['prix']);
    $date = $_POST['date'];
    $quantite = intval($_POST['quantite']);

    if ($nom === '') $erreurs[] = "Nom obligatoire";
    if ($prix <= 0) $erreurs[] = "Prix invalide";
    if ($quantite < 0) $erreurs[] = "Quantité invalide";
    if ($date === '') $erreurs[] = "Date obligatoire";

    if (empty($erreurs)) {

        $produits = lireProduits();
        $produits = is_array($produits) ? $produits : [];

        $exists = false;

        foreach ($produits as $p) {
            if ($p['code_barre'] === $code) {
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
            $erreurs[] = "Produit déjà existant";
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
    padding: 20px;
}

.box {
    max-width: 700px;
    margin: auto;
    background: #111;
    padding: 20px;
    border-radius: 12px;
}

input, button {
    width: 100%;
    padding: 10px;
    margin: 6px 0;
    border-radius: 8px;
    border: none;
}

.btn {
    background: linear-gradient(45deg,#ff6600,#ff3300);
    color: white;
    font-weight: bold;
    cursor: pointer;
}

.btn:hover {
    box-shadow: 0 0 15px #ff6600;
}

.success {
    background: rgba(0,255,0,0.15);
    padding: 10px;
    border-left: 5px solid green;
}

.error {
    background: rgba(255,0,0,0.15);
    padding: 10px;
    border-left: 5px solid red;
}
</style>
</head>

<body>

<div class="box">

<h1>📦 Enregistrement produit</h1>

<?php if ($success): ?>
<div class="success">✔ Produit enregistré</div>
<?php endif; ?>

<?php if (!empty($erreurs)): ?>
<div class="error">
    <?php foreach ($erreurs as $e): ?>
        <p><?= $e ?></p>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($code && !$produit): ?>

<form method="POST">

    <input type="hidden" name="code" value="<?= htmlspecialchars($code) ?>">

    <input type="text" name="nom" placeholder="Nom produit" required>
    <input type="number" step="0.01" name="prix" placeholder="Prix HT" required>
    <input type="date" name="date" required>
    <input type="number" name="quantite" placeholder="Quantité" required>

    <button class="btn" type="submit">💾 Enregistrer</button>
</form>

<?php elseif ($produit): ?>

<h3>✔ Produit déjà existant</h3>
<p><?= $produit['nom'] ?></p>

<?php endif; ?>

<hr>

<h3>📷 Scanner produit</h3>

<video id="video" width="300"></video>

<button class="btn" onclick="startScanner('/TP/modules/produits/enregistrer.php')">
    🎥 Activer caméra
</button>

<button onclick="stopScanner()" style="background:red;color:white;">
    ⛔ Stop caméra
</button>

<p id="result"></p>

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