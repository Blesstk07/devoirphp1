echo "❌ Save ok";
exit;
<?php

require_once('../../auth/session.php');
verifierConnexion();

$file = '../data/produits.json'; // 

$produits = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

if (!is_array($produits)) {
    $produits = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'])) {

    $code = $_POST['code_barre'];
    $nom = $_POST['nom'];
    $prix = $_POST['prix_unitaire_ht'];
    $stock = $_POST['quantite_stock'];

    // vérifier doublon
    foreach ($produits as $p) {
        if ($p['code_barre'] === $code) {
            header("Location: enregistrer.php?error=exists");
            exit;
        }
    }

    $produits[] = [
        "code_barre" => $code,
        "nom" => $nom,
        "prix_unitaire_ht" => (float)$prix,
        "quantite_stock" => (int)$stock,
        "date_enregistrement" => date("Y-m-d H:i:s")
    ];

    file_put_contents(
        $file,
        json_encode($produits, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    header("Location: enregistrer.php?success=1");
    exit;
}