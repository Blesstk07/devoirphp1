<?php

require_once(__DIR__ . '/../../auth/session.php');
verifierConnexion();

$file = __DIR__ . '/../../data/produits.json';

if (!file_exists($file)) {
    file_put_contents($file, "[]");
}

$produits = json_decode(file_get_contents($file), true);

if (!is_array($produits)) $produits = [];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Produits</title>

<style>
/* =========================
   CYBERPUNK LISTE PRODUITS
========================= */

body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#050505;
    color:#fff;
    min-height:100vh;
    overflow-x:hidden;
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
    animation:moveGrid 8s linear infinite;
    z-index:-2;
}

@keyframes moveGrid{
    from{transform:translateY(0);}
    to{transform:translateY(50px);}
}

/* ===== GLOW ===== */
body::after{
    content:"";
    position:fixed;
    width:600px;
    height:600px;
    top:15%;
    left:50%;
    transform:translateX(-50%);
    background:radial-gradient(circle, rgba(255,0,120,0.25), transparent 60%);
    filter:blur(90px);
    z-index:-1;
    animation:pulse 5s infinite alternate;
}

@keyframes pulse{
    from{transform:translateX(-50%) scale(1); opacity:0.5;}
    to{transform:translateX(-50%) scale(1.3); opacity:1;}
}

/* ===== TITLE ===== */
h1{
    text-align:center;
    margin-top:25px;
    color:#00fff2;
    text-shadow:0 0 12px #00fff2;
    letter-spacing:2px;
}

/* ===== TABLE CONTAINER EFFECT ===== */
table{
    width:95%;
    margin:30px auto;
    border-collapse:collapse;
    background:rgba(20,20,20,0.75);
    backdrop-filter:blur(10px);
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 0 25px rgba(255,0,120,0.15);
    animation:fadeIn 0.6s ease;
}

/* ===== HEAD ===== */
th{
    background:linear-gradient(90deg,#ff0077,#ff2e93);
    color:white;
    padding:14px;
    text-transform:uppercase;
    letter-spacing:1px;
    font-size:13px;
}

/* ===== CELLS ===== */
td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid rgba(255,255,255,0.05);
    transition:0.2s;
}

tr:hover td{
    background:rgba(255,0,120,0.08);
    transform:scale(1.01);
}

/* ===== STOCK COLORS ===== */
.low{
    color:#ffb000;
    font-weight:bold;
    text-shadow:0 0 6px #ffb000;
}

.ok{
    color:#00ff88;
    font-weight:bold;
    text-shadow:0 0 6px #00ff88;
}

/* ===== ANIMATION ===== */
@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(15px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* ===== RESPONSIVE ===== */
@media (max-width:700px){
    table{
        font-size:12px;
    }

    th, td{
        padding:8px;
    }
}
</style>

</head>

<body>

<h1>📦 Stock Produits</h1>

<table>

<tr>
    <th>Code</th>
    <th>Nom</th>
    <th>Prix</th>
    <th>Stock</th>
    <th>Enregistrement</th>
    <th>Expiration</th>
</tr>

<?php foreach ($produits as $p): ?>

<?php $class = ($p['quantite_stock'] <= 10) ? 'low' : 'ok'; ?>

<tr>
    <td><?= $p['code_barre'] ?></td>
    <td><?= $p['nom'] ?></td>
    <td><?= $p['prix_unitaire_ht'] ?> FC</td>
    <td class="<?= $class ?>"><?= $p['quantite_stock'] ?></td>
    <td><?= $p['date_enregistrement'] ?></td>
    <td><?= $p['date_expiration'] ?></td>
</tr>

<?php endforeach; ?>

</table>

</body>
</html>