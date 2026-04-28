<?php

require_once('../../includes/fonctions-factures.php');

session_start();

$articles = $_SESSION['facture'] ?? [];

/* =========================
   CALCUL CENTRALISÉ
========================= */
$result = calculerFacture($articles);

/* =========================
   RETOUR JSON (utile scan / ajax)
========================= */
header('Content-Type: application/json');

echo json_encode($result);