<?php
require_once __DIR__ . '/../app/helpers.php';

// Jika user sudah klik konfirmasi logout
if(isset($_GET['confirm']) && $_GET['confirm'] === 'yes'){
  session_destroy();
  header('Location: login.php');
  exit;
}

// Jika belum, tampilkan halaman konfirmasi
include __DIR__ . '/../app/layout_header.php';
?>

<div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 100px);">
  <div class="card shadow-lg" style="max-width: 450px; width: 100%; border: none; border-radius: 12px;">
    <div class="card-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border-radius: 12px 12px 0 0;">
      <div class="text-center py-4">
        <div style="font-size: 2.5rem; margin-bottom: 10px;">🚪</div>
        <h4 class="mb-0" style="color: white; font-weight: bold;">Konfirmasi Logout</h4>
      </div>
    </div>

    <div class="card-body p-4">
      <p class="text-center text-muted mb-4">
        Apakah Anda yakin ingin keluar dari aplikasi?
      </p>
      
      <div class="alert alert-info border-1" style="background-color: #e7f3ff; border-color: #0c63e4;">
        <div class="d-flex align-items-center">
          <span style="font-size: 1.5rem; margin-right: 10px;">👤</span>
          <div>
            <small class="d-block text-muted">Sedang login sebagai:</small>
            <strong class="text-dark"><?= e($_SESSION['username'] ?? 'Admin') ?></strong>
          </div>
        </div>
      </div>

      <div class="d-grid gap-3 mt-4">
        <a href="logout.php?confirm=yes" class="btn btn-danger btn-lg" style="font-weight: 600; padding: 12px 20px;">
          ✓ Ya, Logout Sekarang
        </a>
        <a href="dashboard.php" class="btn btn-secondary btn-lg" style="font-weight: 600; padding: 12px 20px; background-color: #6c757d; border: none;">
          ← Batal & Kembali
        </a>
      </div>

      <hr class="my-4" style="border-color: #e9ecef;">
      
      <p class="text-center text-muted small mb-0" style="font-size: 0.85rem;">
        💡 Pastikan Anda sudah menyimpan semua data sebelum logout
      </p>
    </div>
  </div>
</div>

<style>
  body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
  }

  .card {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
  }

  .btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
    border: none !important;
    transition: all 0.3s ease !important;
  }

  .btn-danger:hover {
    background: linear-gradient(135deg, #c82333 0%, #bd2130 100%) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3) !important;
  }

  .btn-secondary {
    transition: all 0.3s ease !important;
  }

  .btn-secondary:hover {
    background-color: #5a6268 !important;
    transform: translateY(-2px);
  }

  .alert-info {
    border-radius: 8px !important;
  }

  @media (max-width: 768px) {
    .card {
      margin: 20px !important;
    }
  }
</style>

<?php include __DIR__ . '/../app/layout_footer.php'; ?>