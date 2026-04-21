<?php
// Ici, je gère l'enregistrement des produits
require_once('../../auth/session.php');
require_once('../../includes/fonctions-produits.php');

verifierRole(['manager', 'super_admin']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $produits = lireProduits();

    $produits[] = [
        "code_barre" => $_POST['code'],
        "nom" => $_POST['nom'],
        "prix_unitaire_" => (int)$_POST['prix'],
        "date_expiration" => $_POST['date'],
        "quantite" => (int)$_POST['quantite'],
        "date_enregistrement" => date('Y-m-d')
    ];

    sauvegarderProduits($produits);

    echo "Produit ajouté";
}
?>

<form method="POST">
    <input name="code" placeholder="Code barre">
    <input name="nom" placeholder="Nom">
    <input name="prix" type="number">
    <input name="date" type="date">
    <input name="quantite" type="number">
    <button>Enregistrer ici</button>
</form>

<?php
require_once('auth/session.php');
verifierConnexion();
?>