<?php
// ==============================
//  GESTION DE SESSION GLOBALE
// ==============================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifier si utilisateur connecté
 */
function verifierConnexion() {

    if (!isset($_SESSION['user'])) {
        header("Location: ../../auth/login.php");
        exit;
    }
}

/**
 * Vérifier rôle utilisateur
 */
function verifierRole($rolesAutorises = []) {

    verifierConnexion();

    if (!in_array($_SESSION['user']['role'], $rolesAutorises)) {

        echo "<h2> Accès refusé</h2>";
        echo "<p>Vous n'avez pas les permissions nécessaires.</p>";
        exit;
    }
}

/**
 * Récupérer utilisateur connecté
 */
function userConnecte() {
    return $_SESSION['user'] ?? null;
}
?>