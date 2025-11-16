<?php
require_once __DIR__ . '/../app/helpers.php';
require_login();
include __DIR__ . '/../app/layout_header.php';

$id = (int)($_GET['id'] ?? 0);
$error = null;
$success = null;

// Get data admin
$stmt = $pdo->prepare('SELECT * FROM users WHERE id=?');
$stmt->execute([$id]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$u){
  header('Location: admin.php');
  exit;
}

// Proses update
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $username = trim($_POST['username'] ?? '');
  $name = trim($_POST['name'] ?? '');
  $pass = $_POST['password'] ?? null;

  if(empty($username)){
    $error = 'Username harus diisi!';
  } else {
    if($pass){
      $hp = password_hash($pass, PASSWORD_BCRYPT);
      $stmt = $pdo->prepare('UPDATE users SET username=?, name=?, password=? WHERE id=?');
      $result = $stmt->execute([$username, $name, $hp, $id]);
    } else {
      $stmt = $pdo->prepare('UPDATE users SET username=?, name=? WHERE id=?');
      $result = $stmt->execute([$username, $name, $id]);
    }

    if($result){
      $success = '✓ Data admin berhasil diperbarui!';
      $stmt = $pdo->prepare('SELECT * FROM users WHERE id=?');
      $stmt->execute([$id]);
      $u = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      $error = 'Gagal memperbarui data admin!';
    }
  }
}
?>

<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-8">
      <h3 class="mb-0">✏️ Edit Admin</h3>
    </div>
    <div class="col-md-4 text-end">
      <a href="admin.php" class="btn btn-secondary">← Kembali</a>
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
        <div class="col-md-12">
          <label class="form-label">Username <span class="text-danger">*</span></label>
          <input type="text" name="username" class="form-control" value="<?= e($u['username']) ?>" required>
        </div>

        <div class="col-md-12">
          <label class="form-label">Nama</label>
          <input type="text" name="name" class="form-control" value="<?= e($u['name'] ?? '') ?>">
        </div>

        <div class="col-md-12">
          <label class="form-label">Password <small class="text-muted">(kosongkan jika tidak ingin mengubah)</small></label>
          <input type="password" name="password" class="form-control">
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-primary btn-lg">
            ✓ Simpan Perubahan
          </button>
          <a href="admin.php" class="btn btn-secondary btn-lg">← Kembali Tanpa Simpan</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../app/layout_footer.php'; ?>