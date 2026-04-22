<?php
// ==============================
//  PRODUITS (VERSION STABLE)
// ==============================

function lireProduits() {

    $file = __DIR__ . '/../data/produits.json';

    if (!file_exists($file)) {
        return [];
    }

    $data = file_get_contents($file);

    return json_decode($data, true) ?? [];
}

function sauvegarderProduits($produits) {

    $file = __DIR__ . '/../data/produits.json';

    file_put_contents($file, json_encode($produits, JSON_PRETTY_PRINT));
}
function mettreAJourStock($code, $quantiteVendue) {

    $file = __DIR__ . '/../data/produits.json';

    $produits = file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : [];

    foreach ($produits as &$p) {

        if ($p['code_barre'] === $code) {

            $p['quantite_stock'] -= $quantiteVendue;

            if ($p['quantite_stock'] < 0) {
                $p['quantite_stock'] = 0;
            }

            break;
        }
    }

    file_put_contents($file, json_encode($produits, JSON_PRETTY_PRINT));
}
function trouverProduit($code) {

    $produits = lireProduits();

    if (!$produits) {
        return null;
    }

    foreach ($produits as $p) {
        if ($p['code_barre'] == $code) {
            return $p;
        }
    }

    return null;
}
?>