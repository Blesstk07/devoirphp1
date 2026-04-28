<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');
require_once('../../includes/fonctions-auth.php');


verifierRole(['super_admin']);

$file = '../../data/utilisateurs.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $identifiant = $_POST['identifiant'] ?? '';

    if (!empty($identifiant) && file_exists($file)) {

        $users = json_decode(file_get_contents($file), true);

        foreach ($users as $key => $u) {

            // 🔒 empêcher suppression du super admin
            if ($u['identifiant'] === $identifiant) {

                if ($u['role'] === 'super_admin') {
                    die("❌ Impossible de supprimer le super administrateur");
                }

                unset($users[$key]);
                break;
            }
        }

        // réindexation
        $users = array_values($users);

        file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
    }
}

// 🔁 retour vers gestion
header("Location: gestion-comptes.php");
exit;