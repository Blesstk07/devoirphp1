<?php
require_once('../../auth/session.php');
require_once('../../includes/fonctions-factures.php');

verifierConnexion();
verifierRole(['caissier', 'manager', 'super_admin']);

session_start();
?>
// Recupération de la facture en cours
<?php
$articles = $_SESSION['facture'] ?? [];

if (empty($articles)) {
    echo "Aucune facture en cours.";
    exit;
}
?>
// Calcul de la facture
<?php
$total_ht = 0;

foreach ($articles as &$a) {
    $a['sous_total_ht'] = $a['prix_unitaire_ht'] * $a['quantite'];
    $total_ht += $a['sous_total_ht'];
}

$tva = $total_ht * 0.18;
$total_ttc = $total_ht + $tva;
?>
// Pour générer l'ID de la facture
<?php
$id_facture = "FAC-" . date("Ymd") . "-" . rand(100, 999);
?>
// Pour le sauvegarde dans le dictionnaire des factures
<?php
$factures = json_decode(file_get_contents('../../data/factures.json'), true);

if (!$factures) {
    $factures = [];
}

$factures[] = [
    "id_facture" => $id_facture,
    "date" => date("Y-m-d"),
    "heure" => date("H:i:s"),
    "caissier" => $_SESSION['user']['identifiant'],
    "articles" => $articles,
    "total_ht" => $total_ht,
    "tva" => $tva,
    "total_ttc" => $total_ttc
];

file_put_contents('../../data/factures.json', json_encode($factures, JSON_PRETTY_PRINT));

// vider session après validation
unset($_SESSION['facture']);
?>

// L'affichage de la facture finale
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<h1> La facture est validée</h1>

<p><strong>ID :</strong> <?= $id_facture ?></p>
<p><strong>Date :</strong> <?= date("Y-m-d") ?></p>
<p><strong>Heure :</strong> <?= date("H:i:s") ?></p>

<hr>

<table border="1">
    <tr>
        <th>Produit</th>
        <th>Prix</th>
        <th>Qté</th>
        <th>Sous-total</th>
    </tr>

    <?php foreach ($articles as $a): ?>
    <tr>
        <td><?= $a['nom'] ?></td>
        <td><?= $a['prix_unitaire_ht'] ?></td>
        <td><?= $a['quantite'] ?></td>
        <td><?= $a['sous_total_ht'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<hr>

<h3>Total HT : <?= $total_ht ?> CDF</h3>
<h3>TVA (18%) : <?= $tva ?> CDF</h3>
<h2>Total TTC : <?= $total_ttc ?> CDF</h2>

<br>

<a href="nouvelle-facture.php">Créer une nouvelle facture</a>

</body>
</html>