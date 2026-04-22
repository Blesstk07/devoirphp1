<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../../auth/session.php');
require_once(__DIR__ . '/../../includes/fonctions-auth.php');

verifierConnexion();
verifierRole(['super_admin']);

$file = __DIR__ . '/../../data/utilisateurs.json';

$utilisateurs = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

if (!is_array($utilisateurs)) {
    $utilisateurs = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $utilisateurs[] = [
        "identifiant" => trim($_POST['identifiant']),
        "mot_de_passe" => password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT),
        "role" => $_POST['role'],
        "nom_complet" => trim($_POST['nom_complet']),
        "date_creation" => date("Y-m-d"),
        "actif" => 1
    ];

    file_put_contents($file, json_encode($utilisateurs, JSON_PRETTY_PRINT));

    header("Location: gestion_compte.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter utilisateur</title>

    <link rel="stylesheet" href="/TP/assets/css/style.css">

    <style>

        body {
            font-family: Arial;
            background: #000000;
            margin: 0;
            
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: black
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        input:focus, select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 5px rgba(37,99,235,0.3);
        }

        button {
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

        button:hover {
            background: #1d4ed8;
        }

        .btn-back {
            display: block;
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            background: #111827;
            color: white;
            border-radius: 8px;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #2563eb;
        }

    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <h2>➕ Ajouter utilisateur</h2>

        <form method="POST">

            <input type="text" name="identifiant" placeholder="Identifiant" required>

            <input type="text" name="nom_complet" placeholder="Nom complet" required>

            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>

            <select name="role" required>
                <option value="caissier">Caissier</option>
                <option value="manager">Manager</option>
                <option value="super_admin">Super Admin</option>
            </select>

            <button type="submit">Créer utilisateur</button>

        </form>

        <a href="gestion_compte.php" class="btn-back">⬅ Retour</a>

    </div>

</div>

</body>
</html>