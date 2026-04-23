<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../../auth/session.php');
require_once(__DIR__ . '/../../includes/fonctions-auth.php');

verifierConnexion();
verifierRole(['super_admin', 'manager']);

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: /TP/modules/admin/gestion_compte.php?error=no_id");
    exit;
}

$id = trim($id);

$users = lireUtilisateurs();

if (!is_array($users)) {
    $users = [];
}

$userFound = null;

// =========================
// RECHERCHE USER
// =========================
foreach ($users as $u) {

    if (isset($u['identifiant']) && trim($u['identifiant']) === $id) {
        $userFound = $u;
        break;
    }
}

// =========================
// SI USER EXISTE
// =========================
if ($userFound) {

    // sécurité minimale
    if (!isset($userFound['role'])) {
        header("Location: /TP/modules/admin/gestion_compte.php?error=invalid_user");
        exit;
    }

    session_regenerate_id(true); // 🔐 sécurité

    $_SESSION['user'] = [
        'identifiant'  => $userFound['identifiant'],
        'nom_complet'  => $userFound['nom_complet'],
        'role'         => strtolower(trim($userFound['role']))
    ];

    header("Location: /TP/index.php?switched=1");
    exit;
}

// =========================
// SI USER NON TROUVÉ
// =========================
header("Location: /TP/modules/admin/gestion_compte.php?error=user_not_found");
exit;
?>