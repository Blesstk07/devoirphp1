<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   VERIFIER CONNEXION
========================= */
function verifierConnexion() {

    if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {

        header("Location: /TP/auth/login.php");
        exit;
    }
}

/* =========================
   UTILISATEUR CONNECTÉ
   (SAFE - pas de duplication métier)
========================= */
function getUser() {
    return $_SESSION['user'] ?? null;
}

/* =========================
   NOM COMPLET
========================= */
function getNomComplet() {

    return $_SESSION['user']['nom_complet'] ?? 'Invité';
}

/* =========================
   ROLE ACTUEL
========================= */
function getRole() {

    return $_SESSION['user']['role'] ?? null;
}