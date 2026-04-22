<?php
require_once('../auth/session.php');
require_once('../includes/fonctions-factures.php');

verifierConnexion();
verifierRole(['manager', 'super_admin']);

//  mois actuel
$mois = date("Y-m");

//  factures
$factures = lireFactures();

$total_ventes = 0;
$total_tva = 0;
$nb_factures = 0;

$factures_du_mois = [];

//  filtrage par mois
foreach ($factures as $f) {

    if (substr($f['date'], 0, 7) === $mois) {

        $factures_du_mois[] = $f;

        $total_ventes += $f['total_ttc'];
        $total_tva += $f['tva'];
        $nb_factures++;
    }
}
?>