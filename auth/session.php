<?php
// ==============================
//  GESTION DE SESSION GLOBALE
// ==============================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function verifierConnexion() {

    if (!isset($_SESSION['user'])) {
        header("Location: /TP/auth/login.php");
        exit;
    }
}

/**
 * Vérifier le rôle utilisateur
 */
function verifierRole($rolesAutorises = []) {

    verifierConnexion();

    // sécurisation du rôle (évite espaces/maj/min bugs)
    $roleUser = strtolower(trim($_SESSION['user']['role']));

    // normalisation des rôles autorisés
    $rolesAutorises = array_map(function ($r) {
        return strtolower(trim($r));
    }, $rolesAutorises);

    if (!in_array($roleUser, $rolesAutorises)) {

        http_response_code(403);

        echo "<div style='
            font-family:Arial;
            text-align:center;
            margin-top:50px;
        '>";

        echo "<h2>🚫 Accès refusé</h2>";
        echo "<p>Vous n'avez pas les permissions nécessaires.</p>";
        echo "<a href='/TP/index.php'>Retour dashboard</a>";

        echo "</div>";

        exit;
    }
}

/**
 * Récupérer l'utilisateur connecté
 */
function userConnecte() {
    return $_SESSION['user'] ?? null;
}

/**
 * Déconnexion propre (optionnel mais utile)
 */
function deconnexion() {
    session_unset();
    session_destroy();
    header("Location: /TP/auth/login.php");
    exit;
}
?>