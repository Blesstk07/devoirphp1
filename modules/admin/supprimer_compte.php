<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../../auth/session.php');
require_once(__DIR__ . '/../../includes/fonctions-auth.php');

verifierConnexion();
verifierRole(['super_admin']);

$file = __DIR__ . '/../../data/utilisateurs.json';

$users = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

if (!is_array($users)) {
    $users = [];
}

// =========================
// VALIDATION ID
// =========================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: gestion_compte.php");
    exit;
}

$id = $_GET['id'];

// =========================
// SUPPRESSION SAFE
// =========================
$users = array_values(array_filter($users, function($u) use ($id) {
    return $u['identifiant'] !== $id;
}));

file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));

// =========================
// REDIRECTION
// =========================
header("Location: gestion_compte.php?success=deleted");
exit;