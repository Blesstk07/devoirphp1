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

body{
    margin:0;
    font-family:Arial;
    background:#000;
    color:#fff;
    padding:20px;
}

h1{
    color:red;
    text-align:center;
}

.card{
    background:#111;
    padding:20px;
    border-radius:10px;
    text-align:center;
    margin-bottom:20px;
    border:1px solid #222;
}

.total{
    font-size:28px;
    color:red;
    font-weight:bold;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th, td{
    border:1px solid #222;
    padding:10px;
    text-align:center;
}

th{
    background:#111;
    color:red;
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