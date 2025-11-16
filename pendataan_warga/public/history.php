<?php
require_once __DIR__ . '/../app/helpers.php';
require_login();
include __DIR__ . '/../app/layout_header.php';

$page = (int)($_GET['page'] ?? 1);
$per_page = 20;
$offset = ($page - 1) * $per_page;

$filter = $_GET['filter'] ?? 'semua';
$search = trim($_GET['search'] ?? '');

// Build query
$query = 'SELECT w.*, k.no_kk, k.kepala_keluarga, k.rt, k.rw, k.foto as foto_kk FROM warga w LEFT JOIN keluarga k ON w.keluarga_id=k.id WHERE 1=1';
$params = [];

if(!empty($search)){
  $query .= ' AND (w.nama LIKE :search OR w.nik LIKE :search OR k.no_kk LIKE :search)';
  $params[':search'] = '%' . $search . '%';
}

// Count total
$stmt = $pdo->prepare(str_replace('SELECT w.*, k.no_kk, k.kepala_keluarga, k.rt, k.rw, k.foto as foto_kk', 'SELECT COUNT(*) as total', $query));
$stmt->execute($params);
$total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$pages = ceil($total / $per_page);

// Get data
$query .= ' ORDER BY w.id DESC LIMIT :limit OFFSET :offset';
$stmt = $pdo->prepare($query);
foreach($params as $key => $value){
  $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$warga_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h3 class="mb-0">📜 Riwayat Data Warga</h3>
    </div>
  </div>

  <!-- Filter & Search -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="get" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Cari</label>
          <input type="text" name="search" class="form-control" placeholder="Cari nama, NIK, atau No. KK..." value="<?= e($search) ?>">
        </div>
        <div class="col-md-6 d-flex align-items-end gap-2">
          <button type="submit" class="btn btn-primary flex-grow-1">🔍 Cari</button>
          <a href="history.php" class="btn btn-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Data Table -->
  <div class="card">
    <div class="card-body">
      <?php if(empty($warga_history)): ?>
        <div class="alert alert-info text-center py-5">
          <p class="mb-0">📭 Tidak ada data yang ditemukan</p>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover table-striped">
            <thead>
              <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>No. KK</th>
                <th>RT/RW</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = $offset + 1; foreach($warga_history as $w): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= e($w['nik']) ?></td>
                <td><?= e($w['nama']) ?></td>
                <td><?= e($w['no_kk']) ?></td>
                <td><?= e($w['rt']) ?>/<?= e($w['rw']) ?></td>
                <td>
                  <a href="warga_detail.php?id=<?= (int)$w['id'] ?>" class="btn btn-sm btn-info">👁️ Lihat</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <?php if($pages > 1): ?>
          <nav class="mt-4" aria-label="Page navigation">
            <ul class="pagination justify-content-center flex-wrap">
              <?php if($page > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="?page=1&search=<?= urlencode($search) ?>">« Awal</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">‹ Sebelumnya</a>
                </li>
              <?php endif; ?>

              <?php for($i=max(1, $page-2); $i<=min($pages, $page+2); $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <?php if($page < $pages): ?>
                <li class="page-item">
                  <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Selanjutnya ›</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="?page=<?= $pages ?>&search=<?= urlencode($search) ?>">Akhir »</a>
                </li>
              <?php endif; ?>
            </ul>
          </nav>
        <?php endif; ?>

        <div class="mt-3 text-center text-muted small">
          <small>Total: <strong><?= (int)$total ?></strong> data | Halaman <strong><?= (int)$page ?></strong> dari <strong><?= (int)$pages ?></strong></small>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal Foto KK -->
<div class="modal fade" id="fotoModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">📸 Foto KK</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="fotoDetail" src="" alt="Foto KK" style="max-width: 100%; max-height: 500px; border-radius: 8px;">
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../app/layout_footer.php'; ?>