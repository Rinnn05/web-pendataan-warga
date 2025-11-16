<?php
require_once __DIR__ . '/../app/helpers.php';
require_login();
include __DIR__ . '/../app/layout_header.php';

$error = null;
$success = null;

// Proses tambah admin
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])){
  $username = trim($_POST['username'] ?? '');
  $name = trim($_POST['name'] ?? '');
  $password = $_POST['password'] ?? '';

  if(empty($username) || empty($password)){
    $error = 'Username dan password harus diisi!';
  } elseif(strlen($password) < 6){
    $error = 'Password minimal 6 karakter!';
  } else {
    $hp = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users (username, password, name, role) VALUES (:u, :p, :n, :r)');
    if($stmt->execute([':u' => $username, ':p' => $hp, ':n' => $name, ':r' => 'admin'])){
      $success = '✓ Admin berhasil ditambahkan!';
    } else {
      $error = 'Gagal menambahkan admin!';
    }
  }
}

// Proses hapus admin
if(isset($_GET['delete'])){
  $delete_id = (int)$_GET['delete'];
  $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$delete_id]);
  header('Location: admin.php');
  exit;
}

$admins = $pdo->query('SELECT id, username, name, role, created_at FROM users ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h3 class="mb-0">⚙️ Kelola Admin</h3>
    </div>
  </div>

  <div class="row">
    <div class="col-md-7">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0">Daftar Admin</h6>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Username</th>
                  <th>Nama</th>
                  <th>Role</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($admins as $a): ?>
                  <tr>
                    <td><strong><?= e($a['username']) ?></strong></td>
                    <td><?= e($a['name'] ?? '-') ?></td>
                    <td><span class="badge bg-primary"><?= e($a['role']) ?></span></td>
                    <td>
                      <a href="admin_edit.php?id=<?= (int)$a['id'] ?>" class="btn btn-sm btn-primary">✏️ Edit</a>
                      <a href="admin.php?delete=<?= (int)$a['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus admin ini?')">🗑️ Hapus</a>
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
          <h6 class="mb-0">📝 Tambah Admin Baru</h6>
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

          <form method="post" class="row g-3">
            <div class="col-md-12">
              <label class="form-label">Username <span class="text-danger">*</span></label>
              <input type="text" name="username" class="form-control" placeholder="Username unik" required>
            </div>

            <div class="col-md-12">
              <label class="form-label">Nama</label>
              <input type="text" name="name" class="form-control" placeholder="Nama lengkap">
            </div>

            <div class="col-md-12">
              <label class="form-label">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
            </div>

            <div class="col-12">
              <button type="submit" name="create_admin" class="btn btn-primary w-100">
                ➕ Buat Admin
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../app/layout_footer.php'; ?>