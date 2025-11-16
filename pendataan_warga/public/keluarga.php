<?php
require_once __DIR__ . '/../app/helpers.php';
require_login();
include __DIR__ . '/../app/layout_header.php';

$error = null;
$success = null;

// Proses tambah KK
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_kk'])){
  $no_kk = trim($_POST['no_kk'] ?? '');
  $kepala_keluarga = trim($_POST['kepala_keluarga'] ?? '');
  $alamat = trim($_POST['alamat'] ?? '');
  $rt = (int)($_POST['rt'] ?? 0);
  $rw = (int)($_POST['rw'] ?? 0);
  $foto = null;

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
      $stmt = $pdo->prepare('INSERT INTO keluarga (no_kk, kepala_keluarga, alamat, rt, rw, foto) VALUES (:no, :kep, :alamat, :rt, :rw, :foto)');
      if($stmt->execute([':no' => $no_kk, ':kep' => $kepala_keluarga, ':alamat' => $alamat, ':rt' => $rt, ':rw' => $rw, ':foto' => $foto])){
        $success = '✓ Data KK berhasil ditambahkan!';
      } else {
        $error = 'Gagal menambahkan data KK!';
      }
    }
  }
}

// Proses hapus KK
if(isset($_GET['delete'])){
  $delete_id = (int)$_GET['delete'];
  $stmt = $pdo->prepare('SELECT foto FROM keluarga WHERE id=?');
  $stmt->execute([$delete_id]);
  $k = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if($k && $k['foto']){
    $foto_path = __DIR__ . '/assets/uploads/kk/' . $k['foto'];
    if(file_exists($foto_path)){
      unlink($foto_path);
    }
  }
  
  $pdo->prepare('DELETE FROM keluarga WHERE id=?')->execute([$delete_id]);
  header('Location: keluarga.php');
  exit;
}

$rows = $pdo->query('SELECT * FROM keluarga ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h3 class="mb-0">👨‍👩‍👧‍👦 Data Kartu Keluarga</h3>
    </div>
  </div>

  <div class="row">
    <div class="col-md-7">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0">Daftar KK</h6>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Foto</th>
                  <th>No. KK</th>
                  <th>Kepala Keluarga</th>
                  <th>RT/RW</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($rows as $r): ?>
                  <tr>
                    <td>
                      <?php if($r['foto']): ?>
                        <img src="assets/uploads/kk/<?= e($r['foto']) ?>" alt="Foto KK" style="max-width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                      <?php else: ?>
                        <span class="badge bg-secondary">Tidak ada</span>
                      <?php endif; ?>
                    </td>
                    <td><strong><?= e($r['no_kk']) ?></strong></td>
                    <td><?= e($r['kepala_keluarga']) ?></td>
                    <td><?= (int)$r['rt'] ?>/<?= (int)$r['rw'] ?></td>
                    <td>
                      <a href="keluarga_edit.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-primary">✏️ Edit</a>
                      <a href="keluarga.php?delete=<?= (int)$r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')">🗑️ Hapus</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0">📝 Tambah KK Baru</h6>
        </div>
        <div class="card-body">
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

          <form method="post" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-12">
              <label class="form-label">Foto KK</label>
              <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/gif">
              <small class="text-muted">Format: JPG, PNG, GIF (Max 5MB)</small>
            </div>

            <div class="col-md-12">
              <label class="form-label">No. KK <span class="text-danger">*</span></label>
              <input type="text" name="no_kk" class="form-control" placeholder="16 digit No. KK" required>
            </div>

            <div class="col-md-12">
              <label class="form-label">Kepala Keluarga <span class="text-danger">*</span></label>
              <input type="text" name="kepala_keluarga" class="form-control" placeholder="Nama kepala keluarga" required>
            </div>

            <div class="col-md-12">
              <label class="form-label">Alamat</label>
              <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">RT</label>
              <input type="number" name="rt" class="form-control" min="0" placeholder="Nomor RT">
            </div>

            <div class="col-md-6">
              <label class="form-label">RW</label>
              <input type="number" name="rw" class="form-control" min="0" placeholder="Nomor RW">
            </div>

            <div class="col-12">
              <button type="submit" name="create_kk" class="btn btn-primary w-100">
                ➕ Simpan KK
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../app/layout_footer.php'; ?>