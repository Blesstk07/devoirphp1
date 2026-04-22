<?php
// ==============================
//  AUTHENTIFICATION UTILISATEURS
// ==============================

/**
 * Lire tous les utilisateurs
 */
function lireUtilisateurs() {

    $file = __DIR__ . '/../data/utilisateurs.json';

    if (!file_exists($file)) {
        return [];
    }

    $data = file_get_contents($file);
    return json_decode($data, true) ?? [];
}

/**
 * Vérifier login utilisateur
 */
function verifierLogin($id, $mot_de_passe) {

    $utilisateurs = lireUtilisateurs();

    foreach ($utilisateurs as $u) {

        if (
            $u['identifiant'] === $id &&
            password_verify($mot_de_passe, $u['mot_de_passe'])
        ) {
            return $u;
        }
    }

    return null;
}

/**
 * Connexion utilisateur (session)
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
?>