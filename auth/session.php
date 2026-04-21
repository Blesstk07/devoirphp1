<?php
// Ici je gère la session de connexion d'un utilisateur
session_start();

function verifierConnexion() {
    if (!isset($_SESSION['user'])) {
        header("Location: ../auth/login.php");
        exit;
    }
}

function verifierRole($roles) {
    verifierConnexion();

    if (!in_array($_SESSION['user']['role'], $roles)) {
        echo "Accès refusé";
        exit;
    }
}
?>