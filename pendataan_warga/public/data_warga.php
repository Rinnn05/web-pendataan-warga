<?php
<?php
require_once __DIR__ . '/../app/helpers.php';
require_login();
include __DIR__ . '/../app/layout_header.php';

$page = $_GET['page'] ?? 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$total = $pdo->query('SELECT COUNT(*) FROM warga')->fetchColumn();
$warga = $pdo->query("SELECT w.*, k.rt, k.rw FROM warga w LEFT JOIN keluarga k ON w.keluarga_id=k.id ORDER BY w.id DESC LIMIT $per_page OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);
$pages = ceil($total / $per_page);
?>

<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-8">
      <h3 class="mb-0">Data Warga</h3>
    </div>
    <div class="col-md-4 text-end">
      <a href="tambah_warga.php" class="btn btn-primary btn-lg">
        <i class="icon">➕</i> Tambah Warga
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <?php if(empty($warga)): ?>
        <div class="alert alert-info text-center py-5">
          <p class="mb-0">📋 Belum ada data warga. <a href="tambah_warga.php" class="alert-link">Tambah data warga sekarang</a></p>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>NIK</th>
                <th>Nama</th>
                <th>TTL</th>
                <th>Hubungan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($warga as $w): ?>
                <tr>
                  <td><strong><?= e($w['nik']) ?></strong></td>
                  <td><?= e($w['nama']) ?></td>
                  <td><?= e($w['tempat_lahir']) ?>, <?= e($w['tanggal_lahir']) ?></td>
                  <td><?= e($w['hubungan_keluarga'] ?? '-') ?></td>
                  <td>
                    <a href="edit_warga.php?id=<?= (int)$w['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="hapus_warga.php?id=<?= (int)$w['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <?php if($pages > 1): ?>
          <nav class="mt-4">
            <ul class="pagination justify-content-center">
              <?php for($i=1; $i<=$pages; $i++): ?>
                <li class="page-item <?= $i==$page ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= (int)$i ?>"><?= (int)$i ?></a>
                </li>
              <?php endfor; ?>
            </ul>
          </nav>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../app/layout_footer.php'; ?>