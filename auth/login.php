<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../auth/session.php');
require_once('../includes/fonctions-auth.php');

$message = "";

if (isset($_POST['login'])) {

    $identifiant = trim($_POST['identifiant']);
    $mot_de_passe = trim($_POST['mot_de_passe']);

    $user = verifierLogin($identifiant, $mot_de_passe);

    if ($user) {

        $_SESSION['user'] = $user;

        // REDIRECTION UNIQUE (IMPORTANT POUR ÉVITER 404)
        header("Location: ../index.php");
        exit;

    } else {
        $message = "❌ Identifiants incorrects";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Login</title>

<style>
body {
    background: black;
    color: white;
    font-family: Arial;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.box {
    background: #111;
    padding: 20px;
    border-radius: 10px;
    width: 300px;
    text-align: center;
}

input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
}

button {
    width: 100%;
    padding: 10px;
    background: red;
    border: none;
    color: white;
}
</style>

</head>

<body>

<div class="box">

<h2>Connexion</h2>

<form method="POST">

<input type="text" name="identifiant" placeholder="Identifiant">
<input type="password" name="mot_de_passe" placeholder="Mot de passe">

<button name="login">Se connecter</button>

</form>

<p><?= $message ?></p>

</div>

</body>
</html>