<?php

require_once('../../auth/session.php');
require_once('../../includes/fonctions-auth.php');

verifierConnexion();

$user = getUser();

if (($user['role'] ?? '') !== 'super_admin') {
    die("Accès refusé");
}

$file = __DIR__ . '/../../data/utilisateurs.json';

$users = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

if (!is_array($users)) $users = [];

/* ===== AJOUT ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $users[] = [
        "nom_complet" => $_POST['nom_complet'] ?? '',
        "role" => $_POST['role'] ?? 'caissier'
    ];

    file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));

    header("Location: gestion-compte.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ajouter utilisateur</title>

<style>

/* ===== BASE ===== */
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#000;
    font-family:Arial;
    color:#fff;
}

/* ===== CARD CENTRÉE ===== */
.form-container{
    width:350px;
    background:#111;
    padding:25px;
    border-radius:12px;
    border:1px solid #222;
    box-shadow:0 0 15px rgba(255,0,0,0.2);
}

/* ===== TITRE ===== */
h1{
    text-align:center;
    color:red;
    margin-bottom:20px;
}

/* ===== INPUTS ===== */
input, select{
    width:100%;
    padding:10px;
    margin:10px 0;
    border:none;
    border-radius:6px;
    background:#1a1a1a;
    color:#fff;
}

/* ===== BUTTON ===== */
button{
    width:100%;
    padding:10px;
    background:red;
    border:none;
    color:white;
    border-radius:6px;
    cursor:pointer;
    font-weight:bold;
}

button:hover{
    background:#ff3333;
}

</style>
</head>

<body>

<div class="form-container">

<h1>➕ Ajouter utilisateur</h1>

<form method="POST">

<input type="text" name="nom_complet" placeholder="Nom complet" required>

<select name="role">
    <option value="caissier">Caissier</option>
    <option value="manager">Manager</option>
    <option value="super_admin">Super Admin</option>
</select>

<button type="submit">Enregistrer</button>

</form>

</div>

</body>
</html>