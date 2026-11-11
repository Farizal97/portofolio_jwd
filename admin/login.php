<?php
session_start();
require_once '../koneksi.php';

// Jika sudah dalam keadaan login, alihkan langsung ke index admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $query  = "SELECT * FROM tb_users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($koneksi, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Verifikasi ganda: mendukung password teks biasa (plaintext) ATAU password hash
            if ($password === $user['password'] || password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id']        = $user['id'];
                $_SESSION['admin_name']      = $user['nama_lengkap'];
                $_SESSION['admin_username']  = $user['username'];

                header("Location: index.php");
                exit;
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    } else {
        $error = "Silakan isi username dan password!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Workspace Control Center</title>
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

        .login-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }

        .form-control {
            background-color: #0d1017;
            border: 1px solid var(--border-color);
            color: #ffffff;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }

        .form-control:focus {
            background-color: #0d1017;
            border-color: var(--accent-orange);
            color: #ffffff;
            box-shadow: none;
        }

        .btn-accent {
            background-color: var(--accent-orange);
            color: #ffffff;
            font-weight: 600;
            border-radius: 0.75rem;
            padding: 0.75rem;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-accent:hover {
            background-color: var(--accent-orange-hover);
            color: #ffffff;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="container" style="max-width: 420px;">
        <div class="card login-card p-4 p-sm-5">
            <!-- Header Logo & Brand -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3" style="width:48px; height:48px; background-color: rgba(255,94,40,0.15); border: 1px solid rgba(255,94,40,0.3); color: var(--accent-orange); font-weight: bold; font-size: 1.1rem;">
                    FK
                </div>
                <h4 class="text-white fw-bold mb-1">Portfolio Workspace</h4>
                <p class="text-secondary small mb-0">Masuk ke control center untuk mengelola sistem</p>
            </div>

            <!-- Alert Notifikasi Error -->
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger border-0 small py-2 mb-4" role="alert" style="background-color: rgba(239, 68, 68, 0.15); color: #f87171;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $error; ?>
                </div>
            <?php endif; ?>

            <!-- Form Login Admin -->
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label text-white small fw-medium">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label text-white small fw-medium">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-accent w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </button>
            </form>

            <div class="text-center">
                <a href="../index.php" class="text-secondary text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> Kembali ke Website</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>