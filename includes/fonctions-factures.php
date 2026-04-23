<?php

// ==============================
// CALCUL FACTURE CENTRALISÉ
// ==============================

function calculerFacture($articles) {

    $total_ht = 0;
    $articles_calcules = [];

    foreach ($articles as $a) {

        $prix = isset($a['prix_unitaire_ht']) ? floatval($a['prix_unitaire_ht']) : 0;
        $quantite = isset($a['quantite']) ? intval($a['quantite']) : 0;

        if ($prix < 0) $prix = 0;
        if ($quantite < 0) $quantite = 0;

        $sous_total = $prix * $quantite;

        $a['sous_total_ht'] = $sous_total;

        $articles_calcules[] = $a;
        $total_ht += $sous_total;
    }

    $taux_tva = 0.18;

    $tva = $total_ht * $taux_tva;
    $total_ttc = $total_ht + $tva;

    return [
        "articles" => $articles_calcules,
        "total_ht" => $total_ht,
        "tva" => $tva,
        "total_ttc" => $total_ttc
    ];
}


// ==============================
// LOGS
// ==============================

function ajouterLog($action, $details) {

    $file = __DIR__ . '/../data/logs.json';

    $logs = file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : [];

    if (!is_array($logs)) {
        $logs = [];
    }

    $logs[] = [
        "date" => date("Y-m-d H:i:s"),
        "action" => $action,
        "details" => $details
    ];

    file_put_contents($file, json_encode($logs, JSON_PRETTY_PRINT));
}