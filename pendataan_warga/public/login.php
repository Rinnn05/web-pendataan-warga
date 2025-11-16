<?php
require_once __DIR__ . '/../app/helpers.php';

// Jika sudah login, arahkan ke dashboard
if(is_logged_in()) {
  header('Location: dashboard.php');
  exit;
}

$error = null;
$success = null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'daftar';

// Proses Daftar
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='daftar'){
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if(empty($username) || empty($password) || empty($password_confirm)){
        $error = 'Semua field harus diisi.';
    } elseif(strlen($username) < 3){
        $error = 'Username minimal 3 karakter.';
    } elseif(strlen($password) < 5){
        $error = 'Password minimal 5 karakter.';
    } elseif($password !== $password_confirm){
        $error = 'Password tidak sesuai.';
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :u');
        $stmt->execute([':u'=>$username]);
        if($stmt->fetchColumn() > 0){
            $error = 'Username sudah terdaftar.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (:u, :p)');
            if($stmt->execute([':u'=>$username, ':p'=>$hashed])){
                $success = 'Pendaftaran berhasil! Silakan login dengan akun Anda.';
                $tab = 'login';
            } else {
                $error = 'Gagal mendaftar. Silakan coba lagi.';
            }
        }
    }
}

// Proses Login
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='login'){
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :u LIMIT 1');
    $stmt->execute([':u'=>$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($user && password_verify($password, $user['password'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
        $tab = 'login';
    }
}

// Jangan include layout_header.php karena halaman ini tidak punya sidebar
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pendataan Warga - Login & Daftar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
  <!-- Navbar Simple -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Pendataan Warga</a>
    </div>
  </nav>

  <div class="login-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card shadow-lg mt-5 mb-5">
            <!-- Tab Navigation -->
            <div class="card-header-tabs">
              <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <a class="nav-link <?= $tab === 'daftar' ? 'active' : '' ?>" href="#daftar" id="daftar-tab" data-bs-toggle="tab" role="tab">
                    📝 Daftar
                  </a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link <?= $tab === 'login' ? 'active' : '' ?>" href="#login" id="login-tab" data-bs-toggle="tab" role="tab">
                    🔐 Login
                  </a>
                </li>
              </ul>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
              <!-- Tab Daftar -->
              <div class="tab-pane fade <?= $tab === 'daftar' ? 'show active' : '' ?>" id="daftar" role="tabpanel">
                <div class="card-body">
                  <h5 class="card-title mb-3">Pendaftaran Akun Warga</h5>
                  <p class="text-muted mb-4">Daftar akun baru untuk melanjutkan</p>
                  
                  <?php if($error && $tab === 'daftar'): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>⚠️ Error!</strong> <?= e($error) ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                  <?php endif; ?>

                  <form method="post">
                    <input type="hidden" name="action" value="daftar">
                    
                    <div class="mb-3">
                      <label class="form-label">Username</label>
                      <input type="text" name="username" class="form-control" placeholder="Minimal 3 karakter" required>
                      <small class="text-muted">Username yang mudah diingat</small>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" placeholder="Minimal 5 karakter" required>
                      <small class="text-muted">Gunakan password yang kuat</small>
                    </div>

                    <div class="mb-4">
                      <label class="form-label">Konfirmasi Password</label>
                      <input type="password" name="password_confirm" class="form-control" placeholder="Ulangi password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                      ✓ Daftar Sekarang
                    </button>
                  </form>

                  <hr class="my-4">
                  <p class="text-center text-muted mb-0">
                    Sudah punya akun? <a href="#login" id="link-to-login" class="text-primary fw-bold">Login di sini</a>
                  </p>
                </div>
              </div>

              <!-- Tab Login -->
              <div class="tab-pane fade <?= $tab === 'login' ? 'show active' : '' ?>" id="login" role="tabpanel">
                <div class="card-body">
                  <h5 class="card-title mb-3">Login Akun Warga</h5>
                  <p class="text-muted mb-4">Masuk dengan akun Anda</p>
                  
                  <?php if($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <strong>✓ Sukses!</strong> <?= e($success) ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                  <?php endif; ?>

                  <?php if($error && $tab === 'login'): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>⚠️ Error!</strong> <?= e($error) ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                  <?php endif; ?>

                  <form method="post">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="mb-3">
                      <label class="form-label">Username</label>
                      <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                    </div>

                    <div class="mb-4">
                      <label class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                      🔓 Login
                    </button>
                  </form>

                  <hr class="my-4">
                  <p class="text-center text-muted mb-0">
                    Belum punya akun? <a href="#daftar" id="link-to-daftar" class="text-primary fw-bold">Daftar di sini</a>
                  </p>

                  <div class="mt-4 p-3 bg-light rounded">
                    <small class="text-muted">
                      <strong>ℹ️ Demo Admin:</strong><br>
                      Username: <code>admin</code><br>
                      Password: <code>lukman05</code>
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('link-to-login')?.addEventListener('click', function(e){
      e.preventDefault();
      document.getElementById('login-tab')?.click();
    });

    document.getElementById('link-to-daftar')?.addEventListener('click', function(e){
      e.preventDefault();
      document.getElementById('daftar-tab')?.click();
    });
  </script>
</body>
</html>
