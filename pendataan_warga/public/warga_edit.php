<?php
require_once __DIR__ . '/../app/helpers.php';
require_login();
include __DIR__ . '/../app/layout_header.php';

$id = (int)($_GET['id'] ?? 0);
$error = null;
$success = null;

// Get data warga
$stmt = $pdo->prepare('SELECT w.*, k.no_kk, k.kepala_keluarga, k.rt, k.rw FROM warga w LEFT JOIN keluarga k ON w.keluarga_id=k.id WHERE w.id=?');
$stmt->execute([$id]);
$w = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$w){
  header('Location: warga.php');
  exit;
}

// Proses update
if($_SERVER['REQUEST_METHOD']==='POST'){
  $nik = trim($_POST['nik'] ?? '');
  $nama = trim($_POST['nama'] ?? '');
  $keluarga_id = $_POST['keluarga_id'] ?? null;
  $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
  $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
  $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
  $hubungan_keluarga = trim($_POST['hubungan_keluarga'] ?? '');
  $alamat_text = trim($_POST['alamat_text'] ?? '');
  $nomor_hp = trim($_POST['nomor_hp'] ?? '');

  if(empty($nama)){
    $error = 'Nama warga harus diisi!';
  } else {
    // Update tanpa foto dulu
    $stmt = $pdo->prepare('UPDATE warga SET keluarga_id=?, nik=?, nama=?, jenis_kelamin=?, tempat_lahir=?, tanggal_lahir=?, hubungan_keluarga=?, alamat_text=?, nomor_hp=? WHERE id=?');
    if($stmt->execute([$keluarga_id ?: null, $nik, $nama, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $hubungan_keluarga, $alamat_text, $nomor_hp, $id])){
      $success = '✓ Data warga berhasil diperbarui!';
      // Refresh data
      $stmt = $pdo->prepare('SELECT w.*, k.no_kk, k.kepala_keluarga, k.rt, k.rw FROM warga w LEFT JOIN keluarga k ON w.keluarga_id=k.id WHERE w.id=?');
      $stmt->execute([$id]);
      $w = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      $error = 'Gagal memperbarui data warga!';
    }
  }
}

$keluarga = $pdo->query('SELECT id, no_kk, kepala_keluarga, rt, rw FROM keluarga ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-8">
      <h3 class="mb-0">✏️ Edit Data Warga</h3>
    </div>
    <div class="col-md-4 text-end">
      <a href="history.php" class="btn btn-secondary">← Kembali</a>
    </div>
  </div>

  <?php if($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Sukses!</strong> <?= $success ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error!</strong> <?= $error ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="post" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">KK (pilih)</label>
          <select name="keluarga_id" class="form-control">
            <option value="">-- Pilih KK --</option>
            <?php foreach($keluarga as $k): ?>
              <option value="<?= (int)$k['id'] ?>" <?= $k['id'] == $w['keluarga_id'] ? 'selected' : '' ?>>
                <?= e($k['no_kk'] . ' — ' . $k['kepala_keluarga'] . ' (RT' . (int)$k['rt'] . '/' . (int)$k['rw'] . ')') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">NIK</label>
          <input type="text" name="nik" class="form-control" value="<?= e($w['nik'] ?? '') ?>" placeholder="16 digit NIK">
        </div>

        <div class="col-md-12">
          <label class="form-label">Nama <span class="text-danger">*</span></label>
          <input type="text" name="nama" class="form-control" value="<?= e($w['nama']) ?>" placeholder="Nama lengkap" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Jenis Kelamin</label>
          <select name="jenis_kelamin" class="form-control">
            <option value="">-- Pilih --</option>
            <option value="L" <?= ($w['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="P" <?= ($w['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Tempat Lahir</label>
          <input type="text" name="tempat_lahir" class="form-control" value="<?= e($w['tempat_lahir'] ?? '') ?>" placeholder="Tempat lahir">
        </div>

        <div class="col-md-6">
          <label class="form-label">Tanggal Lahir</label>
          <input type="date" name="tanggal_lahir" class="form-control" value="<?= e($w['tanggal_lahir'] ?? '') ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Hubungan Keluarga</label>
          <input type="text" name="hubungan_keluarga" class="form-control" value="<?= e($w['hubungan_keluarga'] ?? '') ?>" placeholder="Misal: Kepala Keluarga">
        </div>

        <div class="col-md-12">
          <label class="form-label">Alamat</label>
          <textarea name="alamat_text" class="form-control" rows="3" placeholder="Alamat lengkap"><?= e($w['alamat_text'] ?? '') ?></textarea>
        </div>

        <div class="col-md-12">
          <label class="form-label">Nomor HP</label>
          <input type="text" name="nomor_hp" class="form-control" value="<?= e($w['nomor_hp'] ?? '') ?>" placeholder="08xxxxxxxxxx">
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-primary btn-lg">
            ✓ Simpan Perubahan
          </button>
          <a href="history.php" class="btn btn-secondary btn-lg">← Kembali Tanpa Simpan</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../app/layout_footer.php'; ?>