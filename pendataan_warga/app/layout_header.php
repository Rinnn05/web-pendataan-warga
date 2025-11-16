<?php if(session_status()==PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Pendataan Warga</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="dashboard.php">📋 Pendataan Warga</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <?php if(is_logged_in()): ?>
            <li class="nav-item">
              <span class="nav-link">Halo, <?= e($_SESSION['username']) ?></span>
            </li>
            <li class="nav-item">
              <a class="nav-link text-danger" href="logout.php">Logout</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar Offcanvas untuk Mobile -->
      <aside class="col-12 col-lg-2 sidebar bg-light p-3 d-none d-lg-block">
        <nav class="nav flex-column">
          <a class="nav-link" href="dashboard.php">📊 Dashboard</a>
          <a class="nav-link" href="keluarga.php">👨‍👩‍👧‍👦 Data KK</a>
          <a class="nav-link" href="warga.php">👤 Data Warga</a>
          <a class="nav-link" href="history.php">📜 Riwayat Data</a>
          <a class="nav-link" href="admin.php">⚙️ Kelola Admin</a>
          <a class="nav-link" href="export.php">📥 Export CSV</a>
          <hr>
          <span class="nav-link text-muted small">Halo, <?= e($_SESSION['username']) ?></span>
          <a class="nav-link text-danger" href="logout.php">🚪 Logout</a>
        </nav>
      </aside>

      <!-- Main Content -->
      <main class="col-12 col-lg-10 p-3 p-lg-4">
