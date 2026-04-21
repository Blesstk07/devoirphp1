<?php
// Ici, je gère la vérification des produits via leurs code-barre
function lireProduits() {
    return json_decode(file_get_contents('../data/produits.json'), true);
}

function sauvegarderProduits($produits) {
    file_put_contents('../data/produits.json', json_encode($produits, JSON_PRETTY_PRINT));
}

function trouverProduit($code) {
    $produits = lireProduits();

    foreach ($produits as $p) {
        if ($p['code_barre'] == $code) {
            return $p;
        }
    }
    return null;
}
?>