<?php

require_once('../../auth/session.php');
verifierConnexion();

$file = '../../data/produits.json';
$produits = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

$code = $_GET['code'] ?? '';

$produit = null;

foreach ($produits as $p) {
    if ($p['code_barre'] === $code) {
        $produit = $p;
        break;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Ajouter produit</title>

<style>
body {
    background: #0a0a0a;
    color: white;
    font-family: Arial;
}

.box {
    width: 400px;
    margin: auto;
    margin-top: 40px;
    background: #111;
    padding: 20px;
    border-radius: 10px;
}

input, button {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
}

button {
    background: red;
    color: white;
    border: none;
}
</style>

</head>

<body>

<div class="box">

<h2>📦 Ajouter produit</h2>

<?php if ($code && $produit): ?>

<p style="color:lime;">✔ Produit déjà existant</p>

<?php elseif ($code && !$produit): ?>

<form method="POST" action="../facturation/liste.php">

<input type="hidden" name="code_barre" value="<?= $code ?>">

<input name="nom" placeholder="Nom produit">
<input name="prix_unitaire_ht" placeholder="Prix">
<input name="quantite_stock" placeholder="Stock">

<!-- expiration auto -->
<input type="hidden" name="date_expiration" value="<?= date('Y-m-d', strtotime('+13 months')) ?>">

<button>Enregistrer</button>

</form>

<?php endif; ?>

</div>

</body>
</html>