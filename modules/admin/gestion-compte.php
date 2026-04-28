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

/* =========================
   AJOUT UTILISATEUR
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {

    $users[] = [
        "nom_complet" => $_POST['nom_complet'] ?? '',
        "role" => $_POST['role'] ?? 'caissier'
    ];

    file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));

    header("Location: gestion-compte.php");
    exit;
}

/* =========================
   SUPPRESSION UTILISATEUR
========================= */
if (isset($_GET['delete'])) {

    $email = $_GET['delete'];

    $users = array_filter($users, function($u) use ($email) {
        return ($u['email'] ?? '') !== $email;
    });

    $users = array_values($users);

    file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));

    header("Location: gestion-compte.php");
    exit;
}

$showForm = isset($_GET['add']);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des comptes</title>

<style>

/* ================= BASE ================= */
body{
    margin:0;
    font-family:Arial;
    background:#000;
    color:#fff;
}

/* ================= TITRE ================= */
h1{
    color:red;
    padding:20px;
    margin:0;
}

/* ================= TABLE ================= */
table{
    width:100%;
    border-collapse:collapse;
}

th, td{
    border:1px solid #222;
    padding:10px;
    text-align:center;
}

th{
    background:#111;
    color:red;
}

/* ================= BOUTONS ================= */
.btn{
    padding:8px 12px;
    border-radius:5px;
    text-decoration:none;
    color:white;
    display:inline-block;
}

.add{background:red;}
.delete{background:#444;}
.delete:hover{background:#ff3333;}

/* ================= FORMULAIRE CENTRÉ ================= */
.overlay{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100vh;
    background:rgba(0,0,0,0.8);
    display:flex;
    justify-content:center;
    align-items:center;
}

.form-box{
    width:350px;
    background:#111;
    padding:25px;
    border-radius:10px;
    border:1px solid #222;
}

.form-box h2{
    text-align:center;
    color:red;
}

input, select{
    width:100%;
    padding:10px;
    margin:10px 0;
    border:none;
    border-radius:5px;
    background:#1a1a1a;
    color:#fff;
}

button{
    width:100%;
    padding:10px;
    background:red;
    border:none;
    color:white;
    border-radius:5px;
    cursor:pointer;
}

.close{
    display:block;
    text-align:center;
    margin-top:10px;
    color:#aaa;
    text-decoration:none;
}

</style>
</head>

<body>

<h1>👤 Gestion des comptes</h1>

<!-- BOUTON AJOUT -->
<a class="btn add" href="gestion-compte.php?add=1">➕ Ajouter utilisateur</a>

<!-- ================= TABLE ================= -->
<table>

<tr>
<th>Nom</th>
<th>Rôle</th>
<th>Action</th>
</tr>

<?php foreach ($users as $u): ?>
<tr>
<td><?= $u['nom_complet'] ?? '' ?></td>
<td><?= $u['role'] ?? '' ?></td>
<td>
<a class="btn delete"
href="gestion-compte.php?delete=<?= urlencode($u['email'] ?? '') ?>"
onclick="return confirm('Supprimer cet utilisateur ?')">
🗑️ Supprimer
</a>
</td>
</tr>
<?php endforeach; ?>

</table>

<!-- ================= FORMULAIRE CENTRÉ ================= -->
<?php if ($showForm): ?>

<div class="overlay">

<div class="form-box">

<h2>➕ Ajouter utilisateur</h2>

<form method="POST">

<input type="hidden" name="add_user" value="1">

<input type="text" name="nom_complet" placeholder="Nom complet" required>

<select name="role">
    <option value="caissier">Caissier</option>
    <option value="manager">Manager</option>
    <option value="super_admin">Super Admin</option>
</select>

<button type="submit">Ajouter</button>

</form>

<a class="close" href="gestion-compte.php">❌ Fermer</a>

</div>

</div>

<?php endif; ?>

</body>
</html>