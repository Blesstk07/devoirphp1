<?php

require_once('../../auth/session.php');
verifierConnexion();

$file = '../../data/produits.json';
$produits = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

if (!is_array($produits)) $produits = [];

$code = $_POST['code_barre'] ?? '';

$produit = null;

foreach ($produits as $p) {
    if ($p['code_barre'] === $code) {
        $produit = $p;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Scanner Produit</title>

<style>
/* =======================
   CYBERPUNK SCANNER UI
======================= */

body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #000;
    color: #fff;
    overflow-x: hidden;
}

/* GRID NEON */
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background:
        linear-gradient(rgba(0,255,255,0.07) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,0,120,0.07) 1px, transparent 1px);
    background-size: 45px 45px;
    animation: gridMove 6s linear infinite;
    z-index: -2;
}

@keyframes gridMove {
    from { transform: translateY(0); }
    to { transform: translateY(45px); }
}

/* GLOW */
body::after {
    content: "";
    position: fixed;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(255,0,120,0.25), transparent 60%);
    top: 20%;
    left: 50%;
    filter: blur(70px);
    z-index: -1;
    animation: pulse 5s infinite alternate;
}

@keyframes pulse {
    from { transform: scale(1); opacity: 0.6; }
    to { transform: scale(1.2); opacity: 1; }
}

/* CONTAINER */
.container {
    width: 90%;
    max-width: 520px;
    margin: 40px auto;
    padding: 25px;
    background: rgba(10,10,20,0.85);
    backdrop-filter: blur(12px);
    border-radius: 16px;
    border: 1px solid rgba(255,0,120,0.3);
}

/* TITRE */
h2 {
    text-align: center;
    color: #00fff2;
    text-shadow: 0 0 10px #00fff2;
}

/* VIDEO */
video {
    width: 100%;
    border-radius: 12px;
    border: 2px solid #ff0077;
}

/* BUTTONS */
button {
    width: 48%;
    padding: 12px;
    margin-top: 10px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
}

.start {
    background: linear-gradient(45deg,#00ff88,#00ffee);
}

.stop {
    background: linear-gradient(45deg,#ff003c,#ff0077);
    color: white;
}

/* FORM */
form input {
    width: 100%;
    padding: 12px;
    margin: 6px 0;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.05);
    color: white;
}

/* RESULT */
.result {
    margin-top: 20px;
    padding: 15px;
    border-left: 3px solid #ff0077;
    background: rgba(255,255,255,0.05);
}
</style>
</head>

<body>

<div class="container">

<h2>📷 Scanner Produit</h2>

<!-- CAMERA -->
<video id="video"></video>

<button class="start" onclick="startScan()">Démarrer</button>
<button class="stop" onclick="stopScan()">Arrêter</button>

<!-- FORM SCAN -->
<form id="scanForm" method="POST">
    <input type="hidden" name="code_barre" id="code_barre">
</form>

<?php if ($code && $produit): ?>

<div class="result">
    <h3>✔ Produit trouvé</h3>
    <p>Nom : <?= $produit['nom'] ?></p>
    <p>Stock : <?= $produit['quantite_stock'] ?></p>
    <p>Code : <?= $produit['code_barre'] ?></p>
</div>

<?php elseif ($code && !$produit): ?>

<div class="result">
    <h3>❌ Produit introuvable</h3>

    <!--  FORMULAIRE ENREGISTREMENT -->
    <form method="POST" action="./facturation/enregistrer.php">

        <input type="hidden" name="code_barre" value="<?= $code ?>">

        <input name="nom" placeholder="Nom produit" required>
        <input name="prix_unitaire_ht" placeholder="Prix" required>
        <input name="quantite_stock" placeholder="Stock" required>

        <button type="submit" style="width:100%; background:#ff0077; color:white;">
            Enregistrer
        </button>

    </form>
</div>

<?php endif; ?>

</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>

<script>

let codeReader;

function startScan() {

    codeReader = new ZXing.BrowserBarcodeReader();

    codeReader.getVideoInputDevices()
    .then(devices => {

        const deviceId = devices[0].deviceId;

        codeReader.decodeFromVideoDevice(deviceId, 'video', (result) => {

            if (result) {
                document.getElementById('code_barre').value = result.text;
                document.getElementById('scanForm').submit();
                stopScan();
            }

        });

    });
}

function stopScan() {
    if (codeReader) codeReader.reset();
}

</script>

</body>
</html>