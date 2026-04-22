<?php

// ==============================
// CALCUL FACTURE CENTRALISÉ
// ==============================

function calculerFacture($articles) {

    $total_ht = 0;

    foreach ($articles as &$a) {

        $a['sous_total_ht'] = $a['prix_unitaire_ht'] * $a['quantite'];
        $total_ht += $a['sous_total_ht'];
    }

    $tva = $total_ht * 0.18;
    $total_ttc = $total_ht + $tva;

    return [
        "articles" => $articles,
        "total_ht" => $total_ht,
        "tva" => $tva,
        "total_ttc" => $total_ttc
    ];
}
// ==============================
// MISE À JOUR STOCK
// ==============================
function ajouterLog($action, $details) {

    $file = __DIR__ . '/../data/logs.json';

    $logs = file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : [];

    $logs[] = [
        "date" => date("Y-m-d H:i:s"),
        "action" => $action,
        "details" => $details
    ];

    file_put_contents($file, json_encode($logs, JSON_PRETTY_PRINT));
}