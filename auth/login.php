<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../auth/session.php');
require_once('../includes/fonctions-auth.php');

// déjà connecté
if (isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

$erreur = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = $_POST['identifiant'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    $user = verifierLogin($id, $mot_de_passe);

    if ($user) {
        connecterUtilisateur($user);
        header("Location: ../index.php");
        exit;
    } else {
        $erreur = "Identifiants incorrects";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>

    <!-- CSS GLOBAL -->
    <link rel="stylesheet" href="/TP/assets/css/style.css">

    <!-- CSS LOCAL LOGIN -->
    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 360px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            animation: fadeIn 0.5s ease-in-out;
        }

        .login-box h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #111827;
        }

        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        .login-box input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 5px rgba(37,99,235,0.4);
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .login-box button:hover {
            background: #1d4ed8;
            transform: scale(1.02);
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            text-align: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

    </style>
</head>

<body>

<div class="login-box">

    <h1>Connexion</h1>

    <?php if ($erreur): ?>
        <div class="error"><?= $erreur ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="text" name="identifiant" placeholder="Identifiant" required>

        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>

        <button type="submit">Se connecter</button>

    </form>

</div>

</body>
</html>