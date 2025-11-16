<?php
require_once __DIR__ . '/../app/helpers.php';
require_login();
include __DIR__ . '/../app/layout_header.php';

$id = (int)($_GET['id'] ?? 0);
$error = null;
$success = null;

// Get data keluarga
$stmt = $pdo->prepare('SELECT * FROM keluarga WHERE id=?');
$stmt->execute([$id]);
$k = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$k){
  header('Location: keluarga.php');
  exit;
}

// Proses update
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $no_kk = trim($_POST['no_kk'] ?? '');
  $kepala_keluarga = trim($_POST['kepala_keluarga'] ?? '');
  $alamat = trim($_POST['alamat'] ?? '');
  $rt = (int)($_POST['rt'] ?? 0);
  $rw = (int)($_POST['rw'] ?? 0);
  $foto = $k['foto'];

  if(empty($no_kk) || empty($kepala_keluarga)){
    $error = 'No. KK dan Kepala Keluarga harus diisi!';
  } else {
    // Proses upload foto
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK){
      $foto_tmp = $_FILES['foto']['tmp_name'];
      $foto_name = $_FILES['foto']['name'];
      $foto_ext = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));
      $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

      if(in_array($foto_ext, $allowed_ext)){
        // Hapus foto lama
        if($k['foto']){
          $old_foto = __DIR__ . '/assets/uploads/kk/' . $k['foto'];
          if(file_exists($old_foto)){
            unlink($old_foto);
          }
        }

        $foto_new = 'kk_' . time() . '.' . $foto_ext;
        $foto_path = __DIR__ . '/assets/uploads/kk/' . $foto_new;

        if(!is_dir(__DIR__ . '/assets/uploads/kk/')){
          mkdir(__DIR__ . '/assets/uploads/kk/', 0755, true);
        }

        if(move_uploaded_file($foto_tmp, $foto_path)){
          $foto = $foto_new;
        } else {
          $error = 'Gagal upload foto!';
        }
      } else {
        $error = 'Format foto tidak didukung! Gunakan: JPG, PNG, GIF';
      }
    }

    if(!$error){
      $stmt = $pdo->prepare('UPDATE keluarga SET no_kk=?, kepala_keluarga=?, alamat=?, rt=?, rw=?, foto=? WHERE id=?');
      if($stmt->execute([$no_kk, $kepala_keluarga, $alamat, $rt, $rw, $foto, $id])){
        $success = '✓ Data KK berhasil diperbarui!';
        // Refresh data
        $stmt = $pdo->prepare('SELECT * FROM keluarga WHERE id=?');
        $stmt->execute([$id]);
        $k = $stmt->fetch(PDO::FETCH_ASSOC);
      } else {
        $error = 'Gagal memperbarui data KK!';
      }
    }
  }
}
?>

<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-8">
      <h3 class="mb-0">✏️ Edit KK</h3>
    </div>
    <div class="col-md-4 text-end">
      <a href="keluarga.php" class="btn btn-secondary">← Kembali</a>
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
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-12">
          <label class="form-label">Foto KK</label>
          <?php if($k['foto']): ?>
            <div class="mb-3">
              <img src="assets/uploads/kk/<?= e($k['foto']) ?>" alt="Foto KK" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
              <p class="text-muted mt-2"><small>Foto saat ini</small></p>
            </div>
          <?php endif; ?>
          <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/gif">
          <small class="text-muted">Kosongkan jika tidak ingin mengubah foto. Format: JPG, PNG, GIF (Max 5MB)</small>
        </div>

        <div class="col-md-12">
          <label class="form-label">No KK <span class="text-danger">*</span></label>
          <input type="text" name="no_kk" class="form-control" value="<?= e($k['no_kk']) ?>" placeholder="16 digit No. KK" required>
        </div>

        <div class="col-md-12">
          <label class="form-label">Kepala Keluarga <span class="text-danger">*</span></label>
          <input type="text" name="kepala_keluarga" class="form-control" value="<?= e($k['kepala_keluarga']) ?>" placeholder="Nama kepala keluarga" required>
        </div>

        <div class="col-md-12">
          <label class="form-label">Alamat</label>
          <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap"><?= e($k['alamat']) ?></textarea>
        </div>

        <div class="col-md-6">
          <label class="form-label">RT</label>
          <input type="number" name="rt" class="form-control" value="<?= (int)$k['rt'] ?>" min="0" placeholder="Nomor RT">
        </div>

        <div class="col-md-6">
          <label class="form-label">RW</label>
          <input type="number" name="rw" class="form-control" value="<?= (int)$k['rw'] ?>" min="0" placeholder="Nomor RW">
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-primary btn-lg">
            ✓ Simpan Perubahan
          </button>
          <a href="keluarga.php" class="btn btn-secondary btn-lg">← Kembali Tanpa Simpan</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../app/layout_footer.php'; ?>