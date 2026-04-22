<?php
require_once('../includes/fonctions-factures.php');

// exemple d'utilisation (test)
$articles = [
    ["prix_unitaire_ht" => 1200, "quantite" => 2],
    ["prix_unitaire_ht" => 500, "quantite" => 3]
];

$resultat = calculerFacture($articles);

echo "<pre>";
print_r($resultat);
echo "</pre>";
?>