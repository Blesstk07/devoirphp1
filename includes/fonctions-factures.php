<?php
// ==============================
// GESTION FACTURES (JSON)
// ==============================

/**
 * Lire toutes les factures
 */
function lireFactures() {

    $file = __DIR__ . '/../data/factures.json';

    if (!file_exists($file)) {
        return [];
    }

    $data = file_get_contents($file);
    return json_decode($data, true) ?? [];
}

/**
 * Sauvegarder toutes les factures
 */
function sauvegarderFactures($factures) {

    $file = __DIR__ . '/../data/factures.json';

    file_put_contents(
        $file,
        json_encode($factures, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

/**
 * Calcul complet d'une facture
 */
function calculerTotal($articles) {

    $total_ht = 0;

    foreach ($articles as $a) {
        $total_ht += $a['sous_total_ht'];
    }

    $tva = $total_ht * 0.18;
    $total_ttc = $total_ht + $tva;

    return [
        'total_ht' => $total_ht,
        'tva' => $tva,
        'total_ttc' => $total_ttc
    ];
}
?>