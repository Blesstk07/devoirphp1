<?php

$file = __DIR__ . '/../data/factures.json';

$factures = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

if (!is_array($factures)) $factures = [];

$today = date('Y-m-d');
$total = 0;

$ventes = [];

foreach ($factures as $f) {

    if (!isset($f['date'], $f['total'])) continue;

    $date = substr($f['date'], 0, 10);

    if ($date === $today) {
        $total += (float)$f['total'];
        $ventes[] = $f;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Rapport journalier</title>

<style>
/* =========================
   CYBERPUNK RAPPORTS
========================= */

body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#050505;
    color:#fff;
    padding:20px;
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
    top:15%;
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
    text-align:center;
    color:#00fff2;
    text-shadow:0 0 12px #00fff2;
    letter-spacing:2px;
    margin-bottom:20px;
}

/* ===== CARD TOTAL ===== */
.card{
    background:rgba(20,20,20,0.8);
    padding:20px;
    border-radius:12px;
    border:1px solid rgba(255,0,120,0.2);
    box-shadow:0 0 20px rgba(255,0,120,0.1);
    text-align:center;
    margin-bottom:20px;
    backdrop-filter:blur(10px);
    animation:fadeIn 0.6s ease;
}

/* TOTAL NUMBER */
.total{
    font-size:32px;
    font-weight:bold;
    color:#ff0077;
    text-shadow:0 0 10px #ff0077;
}

/* ===== TABLE ===== */
table{
    width:100%;
    border-collapse:collapse;
    background:rgba(20,20,20,0.8);
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 0 20px rgba(255,0,120,0.1);
}

/* HEADER */
th{
    background:linear-gradient(90deg,#ff0077,#ff2e93);
    color:white;
    padding:12px;
    text-transform:uppercase;
    letter-spacing:1px;
}

/* CELLS */
td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid rgba(255,255,255,0.05);
}

/* HOVER EFFECT */
tr:hover td{
    background:rgba(255,0,120,0.08);
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
}
</style>
</head>

<body>

<h1>📊 Rapport journalier</h1>

<div class="card">
    <p>Total des ventes du jour</p>
    <div class="total"><?= number_format($total, 0, ',', ' ') ?> FC</div>
</div>

<table>
<tr>
<th>Date</th>
<th>ID Facture</th>
<th>Total</th>
</tr>

<?php foreach ($ventes as $v): ?>
<tr>
<td><?= $v['date'] ?? '' ?></td>
<td><?= $v['id'] ?? '---' ?></td>
<td><?= number_format($v['total'], 0, ',', ' ') ?> FC</td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>