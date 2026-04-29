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
/* =========================
   CYBERPUNK GESTION COMPTES
========================= */

body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#050505;
    color:#fff;
}

/* ===== GRID BACKGROUND ===== */
body::before{
    content:"";
    position:fixed;
    inset:0;
    background:
        linear-gradient(rgba(0,255,255,0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,0,120,0.05) 1px, transparent 1px);
    background-size:50px 50px;
    animation:gridMove 8s linear infinite;
    z-index:-2;
}

@keyframes gridMove{
    from{transform:translateY(0);}
    to{transform:translateY(50px);}
}

/* ===== GLOW ===== */
body::after{
    content:"";
    position:fixed;
    width:600px;
    height:600px;
    top:10%;
    left:50%;
    transform:translateX(-50%);
    background:radial-gradient(circle, rgba(255,0,120,0.25), transparent 60%);
    filter:blur(100px);
    z-index:-1;
    animation:pulse 5s infinite alternate;
}

@keyframes pulse{
    from{transform:translateX(-50%) scale(1); opacity:0.5;}
    to{transform:translateX(-50%) scale(1.3); opacity:1;}
}

/* ===== TITLE ===== */
h1{
    margin:0;
    padding:20px;
    text-align:center;
    color:#00fff2;
    text-shadow:0 0 12px #00fff2;
    letter-spacing:2px;
}

/* ===== ACTION BUTTON ===== */
.btn{
    display:inline-block;
    margin:15px;
    padding:10px 14px;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
    transition:0.25s;
}

.add{
    background:linear-gradient(45deg,#ff0077,#ff2e93);
    color:#fff;
    box-shadow:0 0 15px rgba(255,0,120,0.3);
}

.add:hover{
    transform:scale(1.05);
}

/* ===== TABLE ===== */
table{
    width:95%;
    margin:20px auto;
    border-collapse:collapse;
    background:rgba(20,20,20,0.8);
    backdrop-filter:blur(10px);
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 0 25px rgba(255,0,120,0.1);
}

th{
    background:linear-gradient(90deg,#ff0077,#ff2e93);
    color:white;
    padding:12px;
    text-transform:uppercase;
    letter-spacing:1px;
}

td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid rgba(255,255,255,0.05);
}

tr:hover td{
    background:rgba(255,0,120,0.08);
}

/* ===== DELETE BUTTON ===== */
.delete{
    background:#111;
    border:1px solid rgba(255,0,120,0.3);
    padding:6px 10px;
    border-radius:6px;
    color:#fff;
    transition:0.2s;
}

.delete:hover{
    background:#ff003c;
    box-shadow:0 0 10px rgba(255,0,120,0.4);
}

/* ===== OVERLAY FORM ===== */
.overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.85);
    display:flex;
    justify-content:center;
    align-items:center;
}

/* ===== FORM BOX ===== */
.form-box{
    width:360px;
    background:rgba(20,20,20,0.9);
    padding:25px;
    border-radius:12px;
    border:1px solid rgba(255,0,120,0.3);
    box-shadow:0 0 25px rgba(255,0,120,0.2);
    animation:fadeIn 0.5s ease;
}

.form-box h2{
    text-align:center;
    color:#ff0077;
    text-shadow:0 0 8px #ff0077;
}

/* ===== INPUTS ===== */
input, select{
    width:100%;
    padding:10px;
    margin:10px 0;
    border-radius:8px;
    border:1px solid rgba(255,255,255,0.1);
    background:rgba(255,255,255,0.05);
    color:#fff;
    outline:none;
}

input:focus, select:focus{
    border-color:#ff0077;
    box-shadow:0 0 10px rgba(255,0,120,0.3);
}

/* ===== SUBMIT ===== */
button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    background:linear-gradient(45deg,#ff003c,#ff0077);
    color:white;
    font-weight:bold;
    cursor:pointer;
    transition:0.25s;
}

button:hover{
    transform:scale(1.03);
}

/* ===== CLOSE LINK ===== */
.close{
    display:block;
    text-align:center;
    margin-top:12px;
    color:#aaa;
    text-decoration:none;
}

.close:hover{
    color:#ff0077;
}

/* ===== ANIMATION ===== */
@keyframes fadeIn{
    from{opacity:0; transform:translateY(15px);}
    to{opacity:1; transform:translateY(0);}
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