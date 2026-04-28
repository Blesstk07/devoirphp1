<?php

require_once(__DIR__ . '/../../auth/session.php');
verifierConnexion();

$file = __DIR__ . '/../../data/produits.json';

if (!file_exists($file)) {
    file_put_contents($file, "[]");
}

$produits = json_decode(file_get_contents($file), true);

if (!is_array($produits)) $produits = [];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Produits</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: #0a0a0a;
    color: white;
}

h1 {
    text-align: center;
    color: red;
    margin-top: 20px;
}

/* TABLE */
table {
    width: 95%;
    margin: auto;
    margin-top: 20px;
    border-collapse: collapse;
    background: #111;
    border-radius: 10px;
    overflow: hidden;
}

th, td {
    padding: 12px;
    border: 1px solid #222;
    text-align: center;
}

th {
    background: red;
}

tr:hover {
    background: #1a1a1a;
}

/* STOCK */
.low {
    color: orange;
}

.ok {
    color: lime;
}
</style>

</head>

<body>

<h1>📦 Stock Produits</h1>

<table>

<tr>
    <th>Code</th>
    <th>Nom</th>
    <th>Prix</th>
    <th>Stock</th>
    <th>Enregistrement</th>
    <th>Expiration</th>
</tr>

<?php foreach ($produits as $p): ?>

<?php $class = ($p['quantite_stock'] <= 10) ? 'low' : 'ok'; ?>

<tr>
    <td><?= $p['code_barre'] ?></td>
    <td><?= $p['nom'] ?></td>
    <td><?= $p['prix_unitaire_ht'] ?> FC</td>
    <td class="<?= $class ?>"><?= $p['quantite_stock'] ?></td>
    <td><?= $p['date_enregistrement'] ?></td>
    <td><?= $p['date_expiration'] ?></td>
</tr>

<?php endforeach; ?>

</table>

</body>
</html>