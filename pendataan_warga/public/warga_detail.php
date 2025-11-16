<?php
require_once __DIR__ . '/../app/helpers.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT w.*, k.no_kk, k.kepala_keluarga, k.rt, k.rw, k.alamat, k.foto as foto_kk FROM warga w LEFT JOIN keluarga k ON w.keluarga_id=k.id WHERE w.id=?');
$stmt->execute([$id]);
$w = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$w){
  header('Location: history.php');
  exit;
}

include __DIR__ . '/../app/layout_header.php';
?>

<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-8">
      <h3 class="mb-0">📋 Detail Data Warga</h3>
    </div>
    <div class="col-md-4 text-end">
      <a href="history.php" class="btn btn-secondary">← Kembali</a>
      <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak</button>
    </div>
  </div>

  <!-- Invoice/Detail Card -->
  <div class="card invoice-card">
    <div class="card-header">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h4 class="mb-0">DATA WARGA</h4>
          <small class="text-white-50">ID: <?= (int)$w['id'] ?></small>
        </div>
        <div class="col-md-6 text-end">
          <small class="text-white-50">Tanggal:</small><br>
          <strong class="text-white"><?= date('d/m/Y') ?></strong>
        </div>
      </div>
    </div>
    <div class="card-body">
      <!-- Foto KK Section -->
      <?php if($w['foto_kk']): ?>
        <div class="row mb-4">
          <div class="col-md-12">
            <h6 class="text-muted mb-3">📸 Foto Kartu Keluarga</h6>
            <img src="assets/uploads/kk/<?= e($w['foto_kk']) ?>" alt="Foto KK" 
                 style="max-width: 300px; max-height: 300px; border-radius: 8px; border: 1px solid #e5e7eb; cursor: pointer;" 
                 data-bs-toggle="modal" data-bs-target="#fotoModal"
                 onclick="document.getElementById('fotoDetail').src='assets/uploads/kk/<?= e($w['foto_kk']) ?>'">
          </div>
        </div>
        <hr>
      <?php endif; ?>

      <div class="row mb-4">
        <div class="col-md-6">
          <h6 class="text-muted mb-3">Informasi Pribadi</h6>
          <table class="table table-borderless table-sm">
            <tr>
              <td class="text-muted" style="width: 35%;">NIK</td>
              <td><strong><?= e($w['nik'] ?? '-') ?></strong></td>
            </tr>
            <tr>
              <td class="text-muted">Nama Lengkap</td>
              <td><strong><?= e($w['nama']) ?></strong></td>
            </tr>
            <tr>
              <td class="text-muted">Jenis Kelamin</td>
              <td><?= $w['jenis_kelamin'] === 'L' ? 'Laki-laki' : ($w['jenis_kelamin'] === 'P' ? 'Perempuan' : '-') ?></td>
            </tr>
            <tr>
              <td class="text-muted">Tempat Lahir</td>
              <td><?= e($w['tempat_lahir'] ?? '-') ?></td>
            </tr>
            <tr>
              <td class="text-muted">Tanggal Lahir</td>
              <td><?= e($w['tanggal_lahir'] ?? '-') ?></td>
            </tr>
            <tr>
              <td class="text-muted">Nomor HP</td>
              <td><?= e($w['nomor_hp'] ?? '-') ?></td>
            </tr>
          </table>
        </div>
        <div class="col-md-6">
          <h6 class="text-muted mb-3">Informasi Keluarga</h6>
          <table class="table table-borderless table-sm">
            <tr>
              <td class="text-muted" style="width: 35%;">No. KK</td>
              <td><strong><?= e($w['no_kk'] ?? '-') ?></strong></td>
            </tr>
            <tr>
              <td class="text-muted">Kepala Keluarga</td>
              <td><?= e($w['kepala_keluarga'] ?? '-') ?></td>
            </tr>
            <tr>
              <td class="text-muted">RT/RW</td>
              <td><?= isset($w['rt'], $w['rw']) ? "RT {$w['rt']}/RW {$w['rw']}" : '-' ?></td>
            </tr>
            <tr>
              <td class="text-muted">Hubungan Keluarga</td>
              <td><?= e($w['hubungan_keluarga'] ?? '-') ?></td>
            </tr>
            <tr>
              <td class="text-muted">Alamat</td>
              <td><?= e($w['alamat'] ?? '-') ?></td>
            </tr>
          </table>
        </div>
      </div>

      <hr>

      <h6 class="text-muted mb-3">Alamat Lengkap</h6>
      <p><?= e($w['alamat_text'] ?? '-') ?></p>

      <hr>

      <div class="row text-center text-muted small">
        <div class="col-md-4">
          <small>Tanggal Cetak: <?= date('d/m/Y H:i') ?></small>
        </div>
        <div class="col-md-4">
          <small>Operator: <?= e($_SESSION['username']) ?></small>
        </div>
        <div class="col-md-4">
          <small>Status: <span class="badge bg-success">Tersimpan</span></small>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  @media print {
    .navbar, .sidebar, .btn-secondary, .btn-primary {
      display: none !important;
    }
    .invoice-card {
      box-shadow: none !important;
    }
  }
</style>


<!-- Modal Foto KK -->
<div class="modal fade" id="fotoModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">📸 Foto Kartu Keluarga</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="fotoDetail" src="" alt="Foto KK" style="max-width: 100%; max-height: 600px; border-radius: 8px;">
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../app/layout_footer.php'; ?>