<?php
// Démarrer la session si elle n'existe pas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🧹 Supprimer toutes les variables de session
$_SESSION = [];

// 🧨 Détruire la session côté serveur
session_destroy();

// 🍪 Supprimer le cookie de session (sécurité renforcée)
if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 🔁 Redirection vers login
header("Location: login.php");
exit;