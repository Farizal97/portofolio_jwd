<?php
session_start();
require_once '../koneksi.php';

// Jika tombol konfirmasi keluar diklik
if (isset($_POST['confirm_logout'])) {
    // Hapus semua session
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Workspace Control Center</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        :root {
            --bg-workspace: #090a0f;
            --bg-card: #13161f;
            --border-color: #1e2330;
            --accent-orange: #ff5e28;
            --accent-orange-hover: #e04e1b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-workspace);
            color: #94a3b8;
        }

        .logout-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }

        .btn-accent-danger {
            background-color: #ef4444;
            color: #ffffff;
            font-weight: 600;
            border-radius: 0.75rem;
            padding: 0.75rem;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-accent-danger:hover {
            background-color: #dc2626;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.25);
        }

        .btn-outline-custom {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            color: #ffffff;
            border-radius: 0.75rem;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            border-color: #475569;
            background-color: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="container" style="max-width: 420px;">
        <div class="card logout-card p-4 p-sm-5 text-center">
            <!-- Icon Logout Glow -->
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 mx-auto" style="width:64px; height:64px; background-color: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.25); color: #ef4444; font-size: 1.5rem;">
                <i class="bi bi-box-arrow-right"></i>
            </div>

            <!-- Title & Description -->
            <h4 class="text-white fw-bold mb-2">Konfirmasi Keluar</h4>
            <p class="text-secondary small mb-4">Apakah Anda yakin ingin mengakhiri sesi admin dan keluar dari workspace control center?</p>

            <!-- Action Buttons -->
            <form action="" method="POST" class="d-flex flex-column gap-2 mb-3">
                <button type="submit" name="confirm_logout" class="btn btn-accent-danger w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Ya, Keluar Sesi
                </button>
                <a href="index.php" class="btn btn-outline-custom w-100 text-decoration-none">
                    Batal & Kembali
                </a>
            </form>

            <div class="text-center">
                <small class="text-secondary" style="font-size: 0.75rem;">System status: <span class="text-success"><i class="bi bi-circle-fill" style="font-size:0.5rem;"></i> Active</span></small>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>