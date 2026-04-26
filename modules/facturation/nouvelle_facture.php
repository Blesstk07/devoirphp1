<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');
require_once('../../includes/fonctions-produits.php');
require_once('../../includes/fonctions-factures.php');

verifierConnexion();
verifierRole(['caissier', 'manager', 'super_admin']);

if (!isset($_SESSION['facture'])) {
    $_SESSION['facture'] = [];
}

/* ================= INIT ANTI DOUBLE SCAN ================= */
if (!isset($_SESSION['last_scan'])) {
    $_SESSION['last_scan'] = '';
}

/* ================= SCAN ================= */
if (!empty($_GET['code'])) {

    $code = trim($_GET['code']);

    // 🔒 sécurité anti double passage HTTP
    if ($_SESSION['last_scan'] !== $code) {

        $_SESSION['last_scan'] = $code;

        $produit = trouverProduit($code);

        if ($produit) {

            $found = false;

            foreach ($_SESSION['facture'] as &$item) {

                if ($item['code_barre'] === $produit['code_barre']) {
                    $item['quantite'] += 1;
                    $found = true;
                    break;
                }
            }

            unset($item);

            if (!$found) {
                $_SESSION['facture'][] = [
                    "code_barre" => $produit['code_barre'],
                    "nom" => $produit['nom'],
                    "prix_unitaire_ht" => (float)$produit['prix_unitaire_ht'],
                    "quantite" => 1
                ];
            }
        }
    }
}

/* ================= UPDATE ================= */
if (isset($_POST['update'])) {

    $i = $_POST['index'];
    $q = (int)$_POST['quantite'];

    if ($q > 0) {
        $_SESSION['facture'][$i]['quantite'] = $q;
    } else {
        unset($_SESSION['facture'][$i]);
        $_SESSION['facture'] = array_values($_SESSION['facture']);
    }
}

/* ================= DELETE ================= */
if (isset($_GET['delete'])) {
    unset($_SESSION['facture'][$_GET['delete']]);
    $_SESSION['facture'] = array_values($_SESSION['facture']);
}

/* ================= VIDER ================= */
if (isset($_POST['vider'])) {
    $_SESSION['facture'] = [];
    $_SESSION['last_scan'] = '';
}

/* ================= CALCUL ================= */
$result = calculerFacture($_SESSION['facture']);

$total_ht = $result['total_ht'] ?? 0;
$tva = $result['tva'] ?? 0;
$total_ttc = $result['total_ttc'] ?? 0;

/* ================= VALIDATION ================= */
if (isset($_POST['valider_facture'])) {

    $file = '../../data/factures.json';

    $factures = file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : [];

    // 🔥 ID propre + unique + lisible
    $idFacture = "FAC-" . date("Ymd-His") . "-" . rand(100, 999);

    foreach ($result['articles'] as $item) {
        mettreAJourStock($item['code_barre'], $item['quantite']);
    }

    $factures[] = [
        "id" => $idFacture,
        "date" => date("Y-m-d H:i:s"),
        "articles" => $result['articles'],
        "total_ht" => $total_ht,
        "tva" => $tva,
        "total_ttc" => $total_ttc,
        "caissier" => $_SESSION['user']['identifiant']
    ];

    file_put_contents($file, json_encode($factures, JSON_PRETTY_PRINT));

    $_SESSION['facture'] = [];
    $_SESSION['last_scan'] = '';

    header("Location: /TP/modules/facturation/afficher_facture.php?id=" . $idFacture);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Nouvelle facture</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: #0b0b0b;
    color: white;
}

.container {
    display: flex;
    max-width: 1200px;
    margin: auto;
    padding: 20px;
    gap: 20px;
}

.left { flex: 2; }
.right { flex: 1; }

.camera-box {
    text-align: center;
    margin-bottom: 10px;
}

#video {
    width: 320px;
    height: 240px;
    border: 2px solid #ff6600;
    border-radius: 10px;
}

.btn {
    background: #ff6600;
    border: none;
    padding: 10px 15px;
    margin: 5px;
    border-radius: 8px;
    color: white;
    cursor: pointer;
}

.btn-danger {
    background: red;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #111;
}

th {
    background: #ff6600;
}

td, th {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #333;
}

.ticket {
    background: white;
    color: black;
    padding: 15px;
    border-radius: 10px;
}
</style>
</head>

<body>

<div class="container">

<div class="left">

<h2>🧾 Nouvelle facture</h2>

<div class="camera-box">

    <video id="video"></video>

    <button type="button" class="btn" onclick="startScanner('/TP/modules/facturation/nouvelle_facture.php')">
        🎥 Activer caméra
    </button>

    <button type="button" class="btn btn-danger" onclick="stopScanner()">
        ⛔ Stop caméra
    </button>

</div>

<table>
<tr>
    <th>Produit</th>
    <th>Prix</th>
    <th>Qté</th>
    <th>Total</th>
    <th></th>
</tr>

<?php foreach ($_SESSION['facture'] as $i => $item): ?>
<tr>
    <td><?= htmlspecialchars($item['nom']) ?></td>
    <td><?= $item['prix_unitaire_ht'] ?></td>
    <td>
        <form method="POST">
            <input type="hidden" name="index" value="<?= $i ?>">
            <input type="number" name="quantite" value="<?= $item['quantite'] ?>">
            <button name="update">✔</button>
        </form>
    </td>
    <td><?= $item['prix_unitaire_ht'] * $item['quantite'] ?></td>
    <td><a href="?delete=<?= $i ?>" style="color:red;">❌</a></td>
</tr>
<?php endforeach; ?>

</table>

<br>

<form method="POST">
    <button class="btn" name="valider_facture">💾 Valider</button>
    <button class="btn btn-danger" name="vider">🗑 Vider</button>
</form>

</div>

<div class="right">

<div class="ticket">

<h3>Super Marché CodeRunner</h3>
<p><?= date("d/m/Y H:i") ?></p>

<hr>

<?php foreach ($_SESSION['facture'] as $item): ?>
<div>
<?= $item['nom'] ?> — <?= $item['quantite'] ?> x <?= $item['prix_unitaire_ht'] ?>
</div>
<?php endforeach; ?>

<hr>

<strong>Total : <?= $total_ttc ?> CDF</strong>

</div>

</div>

</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script src="/TP/assets/js/scanner.js"></script>

</body>
</html>