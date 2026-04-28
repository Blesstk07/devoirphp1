<?php

// =========================
// AUTHENTIFICATION SYSTEM
// =========================

/**
 * Charger les utilisateurs
 */
function chargerUtilisateurs() {

    $file = __DIR__ . '/../data/utilisateurs.json';

    if (!file_exists($file)) {
        return [];
    }

    $data = file_get_contents($file);
    return json_decode($data, true) ?? [];
}

/**
 * Trouver un utilisateur par identifiant
 */
function trouverUtilisateur($identifiant) {

    $users = chargerUtilisateurs();

    foreach ($users as $user) {

        if ($user['identifiant'] === $identifiant && $user['actif'] === true) {
            return $user;
        }
    }

    return null;
}

/**
 * Vérifier login
 */
function verifierLogin($identifiant, $mot_de_passe) {

    $user = trouverUtilisateur($identifiant);

    if (!$user) {
        return null;
    }

    if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
        return $user;
    }

    return null;
}

/**
 * Connexion utilisateur (SESSION)
 */
function connecterUtilisateur($user) {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['user'] = [
        'identifiant' => $user['identifiant'],
        'nom_complet' => $user['nom_complet'],
        'role' => $user['role']
    ];
}

/**
 * Vérifier si connecté
 */
function estConnecte() {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['user']);
}

/**
 * Récupérer utilisateur connecté
 */
function utilisateurConnecte() {

    if (!estConnecte()) return null;

    return $_SESSION['user'];
}

/**
 * Vérifier rôle
 */
function verifierRole($rolesAutorises = []) {

    $user = utilisateurConnecte();

    if (!$user) {
        header("Location: /TP/auth/login.php");
        exit;
    }

    if (!in_array($user['role'], $rolesAutorises)) {
        die("⛔ Accès refusé");
    }
}

/**
 * Déconnexion
 */
function deconnecter() {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    session_destroy();

    header("Location: /TP/auth/login.php");
    exit;
}

?>