<?php
//  1. SÉCURITÉ
require_once('../../auth/session.php');
require_once('../../includes/fonctions-produits.php');

verifierConnexion();
verifierRole(['manager', 'super_admin', 'caissier']);

//  2. VARIABLES
$code = $_GET['code'] ?? null;
$produit = null;
$erreurs = [];

//  3. RECHERCHE PRODUIT
if ($code) {
    $produit = trouverProduit($code);
}

//  4. TRAITEMENT FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code = $_POST['code'];
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $date = $_POST['date'];
    $quantite = $_POST['quantite'];

    // VALIDATION
    if (empty($nom)) $erreurs[] = "Nom obligatoire";
    if (!is_numeric($prix) || $prix <= 0) $erreurs[] = "Prix invalide";
    if (!is_numeric($quantite) || $quantite < 0) $erreurs[] = "Quantité invalide";
    if (empty($date)) $erreurs[] = "Date obligatoire";

    // ENREGISTREMENT
    if (empty($erreurs)) {

        $produits = lireProduits();

        $produits[] = [
            "code_barre" => $code,
            "nom" => $nom,
            "prix_unitaire_ht" => (int)$prix,
            "date_expiration" => $date,
            "quantite_stock" => (int)$quantite,
            "date_enregistrement" => date('Y-m-d')
        ];

        sauvegarderProduits($produits);

        $produit = trouverProduit($code);
    }
}
?>

<!--  5. INTERFACE HTML -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrement Produit</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<h1> Gestion des produits</h1>

<!-- ERREURS -->
<?php if (!empty($erreurs)): ?>
    <div style="color:red;">
        <ul>
            <?php foreach ($erreurs as $e): ?>
                <li><?= $e ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!--  PRODUIT EXISTANT -->
<?php if ($produit): ?>

    <h2>✔ Produit trouvé</h2>

    <p><strong>Code :</strong> <?= $produit['code_barre'] ?></p>
    <p><strong>Nom :</strong> <?= $produit['nom'] ?></p>
    <p><strong>Prix :</strong> <?= $produit['prix_unitaire_ht'] ?> CDF</p>
    <p><strong>Stock :</strong> <?= $produit['quantite_stock'] ?></p>
    <p><strong>Expiration :</strong> <?= $produit['date_expiration'] ?></p>

<?php endif; ?>

<!--  FORMULAIRE AJOUT -->
<?php if ($code && !$produit): ?>

    <h2> Ajouter un nouveau produit</h2>

    <form method="POST">

        <input type="hidden" name="code" value="<?= $code ?>">

        <label>Nom du produit</label><br>
        <input type="text" name="nom" required><br><br>

        <label>Prix unitaire Hors Taxes (CDF)</label><br>
        <input type="number" name="prix" required><br><br>

        <label>Date d’expiration</label><br>
        <input type="date" name="date" required><br><br>

        <label>Quantité initiale</label><br>
        <input type="number" name="quantite" required><br><br>

        <button type="submit"> Enregistrer ici </button>

    </form>

<?php endif; ?>

</body>
<hr>

<h2>Scanner produit</h2>

<button onclick="startScanner('../../modules/produits/enregistrer.php', 'produit')">
    Activer caméra
</button>

<video id="video" width="300"></video>
<button onclick="stopScanner()" style="background:red;color:white;">
    Arrêter la caméra
</button>
<p id="result"></p>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script src="../../assets/js/scanner.js"></script>
<script>
window.onload = function () {
    startScanner("../../modules/produits/enregistrer.php");
};
</script>

</html>
