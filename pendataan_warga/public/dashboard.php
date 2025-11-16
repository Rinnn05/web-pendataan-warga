<?php
require_once __DIR__ . '/../app/helpers.php';
require_login();
include __DIR__ . '/../app/layout_header.php';
$totalWarga = $pdo->query('SELECT COUNT(*) FROM warga')->fetchColumn();
$totalKK = $pdo->query('SELECT COUNT(*) FROM keluarga')->fetchColumn();
$male = $pdo->query("SELECT COUNT(*) FROM warga WHERE jenis_kelamin='L'")->fetchColumn();
$female = $pdo->query("SELECT COUNT(*) FROM warga WHERE jenis_kelamin='P'")->fetchColumn();
$recent = $pdo->query('SELECT w.*, k.rt, k.rw FROM warga w LEFT JOIN keluarga k ON w.keluarga_id=k.id ORDER BY w.id DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
?>
<h3 class="mb-4">Dashboard</h3>
<div class="row">
  <div class="col-md-3"><div class="card p-3 mb-3"><small class="text-muted">Total Warga</small><h4 class="mt-2"><?= e($totalWarga) ?></h4></div></div>
  <div class="col-md-3"><div class="card p-3 mb-3"><small class="text-muted">Total KK</small><h4 class="mt-2"><?= e($totalKK) ?></h4></div></div>
  <div class="col-md-3"><div class="card p-3 mb-3"><small class="text-muted">Laki-laki</small><h4 class="mt-2"><?= e($male) ?></h4></div></div>
  <div class="col-md-3"><div class="card p-3 mb-3"><small class="text-muted">Perempuan</small><h4 class="mt-2"><?= e($female) ?></h4></div></div>
</div>
<div class="row">
  <div class="col-md-6"><div class="card p-3"><h6 class="card-title">Distribusi Jenis Kelamin</h6><canvas id="genderChart" height="200"></canvas></div></div>
  <div class="col-md-6"><div class="card p-3"><h6 class="card-title">Daftar Warga Terbaru</h6><table class="table table-sm mt-2"><thead><tr><th>NIK</th><th>Nama</th><th>TTL</th><th>RT/RW</th></tr></thead><tbody><?php foreach($recent as $r): ?><tr><td><?= e($r['nik']) ?></td><td><?= e($r['nama']) ?></td><td><?= e($r['tempat_lahir'].', '.$r['tanggal_lahir']) ?></td><td><?= e($r['rt'].'/'.$r['rw']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</div>
<script>
new Chart(document.getElementById('genderChart'), {type:'pie', data:{labels:['Laki-laki','Perempuan'], datasets:[{data:[<?= (int)$male ?>, <?= (int)$female ?>], backgroundColor:['#0d6efd','#6c757d']}]}, options:{}});
</script>
<?php include __DIR__ . '/../app/layout_footer.php'; ?>
