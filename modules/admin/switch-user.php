<?php
require_once('session.php');
require_once('../includes/fonctions-auth.php');

verifierConnexion();

$id = $_GET['id'] ?? null;

$users = lireUtilisateurs();

foreach ($users as $u) {
    if ($u['identifiant'] === $id) {

        $_SESSION['user'] = [
            'identifiant' => $u['identifiant'],
            'nom_complet' => $u['nom_complet'],
            'role' => $u['role']
        ];

        break;
    }
}

header("Location: ../index.php");
exit;