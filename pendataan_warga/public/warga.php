<?php
require_once __DIR__ . '/../app/helpers.php';
require_login();
include __DIR__ . '/../app/layout_header.php';

$error = null;
$success = null;

// Proses Tambah Warga
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['create_warga'])){
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
    $stmt = $pdo->prepare('INSERT INTO warga (keluarga_id,nik,nama,jenis_kelamin,tempat_lahir,tanggal_lahir,hubungan_keluarga,alamat_text,nomor_hp) VALUES (:kel,:nik,:nama,:jk,:temp,:tgl,:hub,:alamat,:hp)');
    if($stmt->execute([
      ':kel' => $keluarga_id ?: null,
      ':nik' => $nik ?: null,
      ':nama' => $nama,
      ':jk' => $jenis_kelamin,
      ':temp' => $tempat_lahir,
      ':tgl' => $tanggal_lahir ?: null,
      ':hub' => $hubungan_keluarga,
      ':alamat' => $alamat_text,
      ':hp' => $nomor_hp ?: null
    ])){
      $success = '✓ Data warga berhasil ditambahkan!';
      header('Location: warga.php');
      exit;
    } else {
      $error = 'Gagal menambahkan data warga!';
    }
  }
}

// Proses Hapus Warga
if(isset($_GET['delete'])){
  $delete_id = (int)$_GET['delete'];
  $pdo->prepare('DELETE FROM warga WHERE id=?')->execute([$delete_id]);
  header('Location: warga.php');
  exit;
}

$keluarga = $pdo->query('SELECT id, no_kk, kepala_keluarga, rt, rw FROM keluarga ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
$rows = $pdo->query('SELECT w.*, k.no_kk, k.rt, k.rw FROM warga w LEFT JOIN keluarga k ON w.keluarga_id=k.id ORDER BY w.id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-8">
      <h3 class="mb-0">📋 Data Warga</h3>
    </div>
    <div class="col-md-4 text-end">
      <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#tambahWarngaModal">
        ➕ Tambah Warga
      </button>
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

  <!-- Tabel Data Warga -->
  <div class="card">
    <div class="card-body">
      <?php if(empty($rows)): ?>
        <div class="alert alert-info text-center py-5">
          <p class="mb-0">📭 Belum ada data warga. <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#tambahWarngaModal">Tambah data warga sekarang</button></p>
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
              <?php foreach($rows as $r): ?>
                <tr>
                  <td><strong><?= e($r['nik']) ?></strong></td>
                  <td><?= e($r['nama']) ?></td>
                  <td><?= e($r['tempat_lahir']) ?>, <?= e($r['tanggal_lahir']) ?></td>
                  <td><?= e($r['hubungan_keluarga']) ?></td>
                  <td>
                    <a class="btn btn-sm btn-primary" href="warga_edit.php?id=<?= (int)$r['id'] ?>">Edit</a>
                    <a class="btn btn-sm btn-danger" href="warga.php?delete=<?= (int)$r['id'] ?>" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal Tambah Warga -->
<div class="modal fade" id="tambahWarngaModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">📝 Tambah Data Warga</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">KK (pilih)</label>
              <select name="keluarga_id" class="form-control">
                <option value="">-- Pilih KK --</option>
                <?php foreach($keluarga as $k): ?>
                  <option value="<?= (int)$k['id'] ?>">
                    <?= e($k['no_kk'] . ' — ' . $k['kepala_keluarga'] . ' (RT' . (int)$k['rt'] . '/' . (int)$k['rw'] . ')') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">NIK</label>
              <input type="text" name="nik" class="form-control" placeholder="16 digit NIK">
            </div>

            <div class="col-md-12">
              <label class="form-label">Nama <span class="text-danger">*</span></label>
              <input type="text" name="nama" class="form-control" placeholder="Nama lengkap" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Jenis Kelamin</label>
              <select name="jenis_kelamin" class="form-control">
                <option value="">-- Pilih --</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Tempat Lahir</label>
              <input type="text" name="tempat_lahir" class="form-control" placeholder="Tempat lahir">
            </div>

            <div class="col-md-6">
              <label class="form-label">Tanggal Lahir</label>
              <input type="date" name="tanggal_lahir" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Hubungan Keluarga</label>
              <select name="hubungan_keluarga" class="form-control">
                <option value="">-- Pilih Hubungan --</option>
                <option value="Ayah">Ayah</option>
                <option value="Ibu">Ibu</option>
                <option value="Anak">Anak</option>
              </select>
            </div>

            <div class="col-md-12">
              <label class="form-label">Alamat</label>
              <textarea name="alamat_text" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
            </div>

            <div class="col-md-12">
              <label class="form-label">Nomor HP</label>
              <input type="text" name="nomor_hp" class="form-control" placeholder="08xxxxxxxxxx">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="create_warga" class="btn btn-primary">✓ Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../app/layout_footer.php'; ?>