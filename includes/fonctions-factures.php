<?php
// Ici, je gère la logique du calcul des factures
function lireFactures() {
    return json_decode(file_get_contents('../data/factures.json'), true);
}

function sauvegarderFactures($factures) {
    file_put_contents('../data/factures.json', json_encode($factures, JSON_PRETTY_PRINT));
}

function calculerTotal($articles) {
    $total = 0;

    foreach ($articles as $a) {
        $total += $a['sous_total_ht'];
    }

    $tva = $total * 0.18;

    return [
        'total_ht' => $total,
        'tva' => $tva,
        'total_ttc' => $total + $tva
    ];
}
?>