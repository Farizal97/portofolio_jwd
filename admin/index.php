<?php
session_start();
require_once '../koneksi.php';

// Proteksi Halaman Admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'overview';
$msg = '';
$msg_type = '';

/* =========================================================================
   LOGIKA BACKEND (CRUD PROSES)
   ========================================================================= */

// 1. UPDATE KONTEN PORTOFOLIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    $nama              = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $sub_role          = mysqli_real_escape_string($koneksi, trim($_POST['sub_role']));
    $ringkasan_summary = mysqli_real_escape_string($koneksi, trim($_POST['ringkasan_summary']));

    $query_update = "UPDATE tb_profil SET nama = '$nama', sub_role = '$sub_role', ringkasan_summary = '$ringkasan_summary' WHERE id = 1";
    if (mysqli_query($koneksi, $query_update)) {
        $msg = "Profil berhasil diperbarui!";
        $msg_type = "success";
    } else {
        $msg = "Gagal memperbarui profil: " . mysqli_error($koneksi);
        $msg_type = "danger";
    }
}

// 2. TAMBAH KATEGORI KEAHLIAN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_kategori'])) {
    $nama_kategori = mysqli_real_escape_string($koneksi, trim($_POST['nama_kategori']));
    if (!empty($nama_kategori)) {
        mysqli_query($koneksi, "INSERT INTO tb_kategori_keahlian (nama_kategori) VALUES ('$nama_kategori')");
        $msg = "Kategori berhasil ditambahkan!";
        $msg_type = "success";
    }
}

// 3. TAMBAH BADGE SKILL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_skill'])) {
    $kategori_id = (int)$_POST['kategori_id'];
    $nama_badge  = mysqli_real_escape_string($koneksi, trim($_POST['nama_badge']));
    if ($kategori_id > 0 && !empty($nama_badge)) {
        mysqli_query($koneksi, "INSERT INTO tb_keahlian (kategori_id, nama_badge) VALUES ('$kategori_id', '$nama_badge')");
        $msg = "Skill badge berhasil ditambahkan!";
        $msg_type = "success";
    }
}

// 4. HAPUS SKILL
if (isset($_GET['action']) && $_GET['action'] === 'delete_skill' && isset($_GET['id'])) {
    $id_skill = (int)$_GET['id'];
    mysqli_query($koneksi, "DELETE FROM tb_keahlian WHERE id = '$id_skill'");
    header("Location: index.php?page=keahlian");
    exit;
}

// 5. HAPUS PESAN MASUK
if (isset($_GET['action']) && $_GET['action'] === 'delete_pesan' && isset($_GET['id'])) {
    $id_pesan = (int)$_GET['id'];
    mysqli_query($koneksi, "DELETE FROM tb_pesan WHERE id = '$id_pesan'");
    header("Location: index.php?page=pesan");
    exit;
}

/* =========================================================================
   DATA FETCHING
   ========================================================================= */
$query_profil   = mysqli_query($koneksi, "SELECT * FROM tb_profil WHERE id = 1");
$profil         = mysqli_fetch_assoc($query_profil);
$query_kategori = mysqli_query($koneksi, "SELECT * FROM tb_kategori_keahlian ORDER BY id ASC");
$total_pesan    = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM tb_pesan"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Workspace - Control Center</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        :root {
            --bg-workspace: #090a0f;
            --bg-sidebar: #0d0e14;
            --bg-card: #13161f;
            --bg-card-hover: #191d29;
            --border-color: #1e2330;
            --accent-orange: #ff5e28;
            --accent-orange-hover: #e04e1b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-workspace);
            color: #94a3b8;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .user-dropdown {
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            color: #fff;
            font-size: 0.82rem;
        }

        .sidebar-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: #475569;
            text-transform: uppercase;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            padding-left: 0.75rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.85rem;
            border-radius: 0.4rem;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover, .sidebar-link.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .sidebar-link.active {
            border-left: 3px solid var(--accent-orange);
            background-color: rgba(255, 94, 40, 0.05);
        }

        /* Main Workspace Area */
        .main-content {
            margin-left: 260px;
            padding: 1.5rem 2.5rem;
        }

        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 1.5rem;
        }

        .breadcrumb-custom {
            font-size: 0.78rem;
            color: #475569;
        }

        .breadcrumb-custom span {
            color: #94a3b8;
        }

        /* Hero Banner Card */
        .hero-banner {
            background: linear-gradient(135deg, #13161f 70%, rgba(255, 94, 40, 0.08) 100%);
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            padding: 2.25rem;
            position: relative;
            overflow: hidden;
        }

        .badge-system {
            background-color: rgba(255, 94, 40, 0.1);
            color: var(--accent-orange);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.3rem 0.75rem;
            border-radius: 50rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            background-color: var(--accent-orange);
            border-radius: 50%;
        }

        .hero-banner h1 {
            color: #ffffff;
            font-size: 2rem;
            font-weight: 800;
            margin-top: 0.75rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .hero-banner p {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 1.25rem;
        }

        .hero-meta {
            font-size: 0.75rem;
            color: #64748b;
            display: flex;
            gap: 1.25rem;
        }

        .hero-meta i {
            color: #10b981;
        }

        /* Quick Actions Card List */
        .action-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 0.85rem;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
            margin-bottom: 0.75rem;
        }

        .action-card:hover {
            background-color: var(--bg-card-hover);
            border-color: #334155;
            color: inherit;
            transform: translateY(-2px);
        }

        .action-icon {
            width: 44px;
            height: 44px;
            border-radius: 0.65rem;
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-orange);
            font-size: 1.2rem;
        }

        .action-number {
            font-size: 0.7rem;
            font-family: monospace;
            color: #475569;
            margin-right: 1rem;
        }

        /* Status Sidebar Card */
        .status-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            padding: 1.5rem;
        }

        .badge-healthy {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: 50rem;
            text-transform: uppercase;
        }

        .status-item {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.78rem;
        }

        .status-item i {
            color: #10b981;
        }

        .status-item h6 {
            color: #ffffff;
            font-size: 0.82rem;
            margin-bottom: 0.15rem;
            font-weight: 600;
        }

        .status-item p {
            color: #64748b;
            margin: 0;
            font-size: 0.72rem;
        }

        /* Form Custom */
        .form-control, .form-select {
            background-color: #0d1017;
            border: 1px solid var(--border-color);
            color: #ffffff;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
        }

        .form-control:focus, .form-select:focus {
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
            border: none;
            padding: 0.65rem 1.5rem;
        }

        .btn-accent:hover {
            background-color: var(--accent-orange-hover);
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR LEFT -->
    <aside class="sidebar p-3 d-flex flex-column justify-content-between">
        <div>
            <!-- User Header -->
            <div class="user-dropdown d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2 text-truncate">
                    <i class="bi bi-person-circle text-secondary"></i>
                    <span class="fw-medium text-truncate"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin'); ?>@gmail.com</span>
                </div>
                <i class="bi bi-chevron-down text-secondary" style="font-size: 0.75rem;"></i>
            </div>

            <!-- Tab Switcher Icons -->
            <div class="d-flex gap-1 p-1 rounded-2 mb-3" style="background-color: rgba(255,255,255,0.02); border: 1px solid var(--border-color);">
                <a href="index.php?page=overview" class="btn btn-sm w-100 py-1 <?= $page == 'overview' ? 'text-white' : 'text-secondary'; ?>" style="<?= $page == 'overview' ? 'background-color: rgba(255,255,255,0.06);' : ''; ?> font-size: 0.75rem;"><i class="bi bi-sliders"></i></a>
                <a href="index.php?page=keahlian" class="btn btn-sm w-100 py-1 <?= $page == 'keahlian' ? 'text-white' : 'text-secondary'; ?>" style="<?= $page == 'keahlian' ? 'background-color: rgba(255,255,255,0.06);' : ''; ?> font-size: 0.75rem;"><i class="bi bi-folder"></i></a>
                <a href="index.php?page=pesan" class="btn btn-sm w-100 py-1 <?= $page == 'pesan' ? 'text-white' : 'text-secondary'; ?>" style="<?= $page == 'pesan' ? 'background-color: rgba(255,255,255,0.06);' : ''; ?> font-size: 0.75rem;"><i class="bi bi-tag"></i></a>
            </div>

            <!-- Navigation Collections -->
            <div class="sidebar-section-label">Collections</div>
            <a href="index.php?page=admins" class="sidebar-link <?= $page == 'admins' ? 'active' : ''; ?> mb-1">
                <i class="bi bi-shield-lock me-2 text-secondary"></i> Admins
            </a>
            <a href="index.php?page=media" class="sidebar-link <?= $page == 'media' ? 'active' : ''; ?> mb-1">
                <i class="bi bi-images me-2 text-secondary"></i> Media
            </a>

            <!-- Navigation Portfolio -->
            <div class="sidebar-section-label">Portofolio</div>
            <a href="index.php?page=keahlian" class="sidebar-link <?= $page == 'keahlian' ? 'active' : ''; ?> mb-1">
                <i class="bi bi-award me-2 text-secondary"></i> Keahlian
            </a>
            <a href="index.php?page=pesan" class="sidebar-link <?= $page == 'pesan' ? 'active' : ''; ?> mb-1">
                <i class="bi bi-envelope me-2 text-secondary"></i> Pesan Masuk
                <?php if ($total_pesan > 0) : ?>
                    <span class="badge bg-danger ms-auto" style="font-size: 0.65rem;"><?= $total_pesan; ?></span>
                <?php endif; ?>
            </a>
            <a href="index.php?page=konten" class="sidebar-link <?= $page == 'konten' ? 'active' : ''; ?> mb-1">
                <i class="bi bi-file-text me-2 text-secondary"></i> Konten Portofolio
            </a>
        </div>

        <!-- Logout Bottom Link -->
        <div>
            <a href="logout.php" class="sidebar-link text-danger">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar / Logout
            </a>
        </div>
    </aside>

    <!-- MAIN WORKSPACE CONTENT -->
    <main class="main-content">
        
        <!-- Top Bar Navigation -->
        <div class="top-bar">
            <div class="breadcrumb-custom">
                Workspace / <span><?= ucfirst($page); ?></span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="../index.php" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill text-white" style="font-size: 0.75rem;"><i class="bi bi-box-arrow-up-right me-1"></i> Buka Website</a>
                <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3" style="font-size: 0.75rem;"><i class="bi bi-power me-1"></i> Logout</a>
            </div>
        </div>

        <?php if (!empty($msg)) : ?>
            <div class="alert alert-<?= $msg_type; ?> alert-dismissible fade show border-0 text-white mb-4" role="alert" style="background-color: <?= $msg_type === 'success' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)'; ?>;">
                <i class="bi bi-info-circle me-1"></i> <?= $msg; ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- DINAMIS TAB SWITCHER -->
        <?php if ($page === 'overview') : ?>
            <!-- HERO CONTROL CENTER BANNER -->
            <div class="hero-banner mb-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="badge-system">
                            <span class="badge-dot"></span> SYSTEM OPERATIONAL
                        </div>
                        <h1>Portfolio control center</h1>
                        <p>Kelola profil, keahlian, dan aset digital dari satu workspace yang terorganisir.</p>
                        
                        <div class="hero-meta">
                            <span><i class="bi bi-check-lg me-1"></i> Payload connected</span>
                            <span><i class="bi bi-check-lg me-1"></i> Auto cache refresh</span>
                        </div>
                    </div>

                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <a href="../index.php" target="_blank" class="d-inline-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background-color: rgba(13, 16, 23, 0.8); border: 1px solid var(--border-color);">
                            <div class="action-icon" style="width:36px; height:36px; font-size: 1rem;">
                                <i class="bi bi-globe"></i>
                            </div>
                            <div class="text-start pe-3">
                                <span class="d-block text-uppercase text-secondary" style="font-size: 0.62rem; letter-spacing: 0.08em;">LIVE PORTFOLIO</span>
                                <strong class="text-white" style="font-size: 0.85rem;">Buka website <i class="bi bi-arrow-up-right ms-1 text-secondary"></i></strong>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- CONTENT MANAGEMENT SECTION & STATUS -->
            <div class="row g-4">
                <!-- Left Main Column (Quick Actions) -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.1em; color: var(--accent-orange) !important;">CONTENT MANAGEMENT</span>
                            <h5 class="text-white fw-bold mb-0">Quick actions</h5>
                        </div>
                        <span class="text-secondary" style="font-size: 0.75rem;">Pilih area yang ingin Anda kelola</span>
                    </div>

                    <!-- Action Item 01: Konten Portofolio -->
                    <a href="index.php?page=konten" class="action-card">
                        <div class="d-flex align-items-center">
                            <span class="action-number">01</span>
                            <div class="action-icon me-3">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <h6 class="text-white fw-bold mb-1" style="font-size: 0.9rem;">Konten Portofolio</h6>
                                <p class="text-secondary mb-0" style="font-size: 0.78rem;">Identitas, headline, kontak dan call to action.</p>
                            </div>
                        </div>
                        <i class="bi bi-arrow-right text-secondary"></i>
                    </a>

                    <!-- Action Item 02: Keahlian & Skill -->
                    <a href="index.php?page=keahlian" class="action-card">
                        <div class="d-flex align-items-center">
                            <span class="action-number">02</span>
                            <div class="action-icon me-3">
                                <i class="bi bi-code-slash"></i>
                            </div>
                            <div>
                                <h6 class="text-white fw-bold mb-1" style="font-size: 0.9rem;">Keahlian & Skill</h6>
                                <p class="text-secondary mb-0" style="font-size: 0.78rem;">Kategori kompetensi dan daftar teknologi.</p>
                            </div>
                        </div>
                        <i class="bi bi-arrow-right text-secondary"></i>
                    </a>

                    <!-- Action Item 03: Media Library -->
                    <a href="index.php?page=media" class="action-card">
                        <div class="d-flex align-items-center">
                            <span class="action-number">03</span>
                            <div class="action-icon me-3">
                                <i class="bi bi-image"></i>
                            </div>
                            <div>
                                <h6 class="text-white fw-bold mb-1" style="font-size: 0.9rem;">Media Library</h6>
                                <p class="text-secondary mb-0" style="font-size: 0.78rem;">Kelola gambar dan file untuk kebutuhan konten.</p>
                            </div>
                        </div>
                        <i class="bi bi-arrow-right text-secondary"></i>
                    </a>

                    <!-- Action Item 04: Pesan Masuk -->
                    <a href="index.php?page=pesan" class="action-card">
                        <div class="d-flex align-items-center">
                            <span class="action-number">04</span>
                            <div class="action-icon me-3">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div>
                                <h6 class="text-white fw-bold mb-1" style="font-size: 0.9rem;">Pesan Masuk</h6>
                                <p class="text-secondary mb-0" style="font-size: 0.78rem;">Baca dan tindak lanjuti pesan dari pengunjung.</p>
                            </div>
                        </div>
                        <i class="bi bi-arrow-right text-secondary"></i>
                    </a>
                </div>

                <!-- Right Column (Workspace Status Panel) -->
                <div class="col-lg-4">
                    <div class="status-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.65rem; letter-spacing: 0.08em;">WORKSPACE STATUS</span>
                            <span class="badge-healthy">HEALTHY</span>
                        </div>

                        <h3 class="text-white fw-bold mb-1">Ready</h3>
                        <p class="text-secondary mb-4" style="font-size: 0.78rem;">Semua modul konten siap digunakan.</p>

                        <div class="status-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>
                                <h6>Profil utama</h6>
                                <p>Connected to portfolio</p>
                            </div>
                        </div>

                        <div class="status-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>
                                <h6>Expertise collection</h6>
                                <p>Editable and sortable</p>
                            </div>
                        </div>

                        <div class="status-item mb-4">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>
                                <h6>Public website</h6>
                                <p>Cache invalidation active</p>
                            </div>
                        </div>

                        <p class="text-secondary border-top border-secondary border-opacity-25 pt-3 mb-0" style="font-size: 0.72rem; line-height: 1.5;">
                            Perubahan yang disimpan akan tersedia pada website secara otomatis.
                        </p>
                    </div>
                </div>
            </div>

        <?php elseif ($page === 'konten') : ?>
            <!-- TAB KONTEN PORTOFOLIO -->
            <div class="card status-card p-4">
                <h4 class="text-white fw-bold mb-4"><i class="bi bi-person-gear me-2" style="color: var(--accent-orange);"></i>Kelola Konten Portofolio</h4>
                <form action="" method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-white small">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($profil['nama'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white small">Sub Role / Pekerjaan</label>
                            <input type="text" name="sub_role" class="form-control" value="<?= htmlspecialchars($profil['sub_role'] ?? ''); ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-white small">Ringkasan Summary</label>
                            <textarea name="ringkasan_summary" class="form-control" rows="4" required><?= htmlspecialchars($profil['ringkasan_summary'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="update_profil" class="btn btn-accent">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        <?php elseif ($page === 'keahlian') : ?>
            <!-- TAB KEAHLIAN & SKILL -->
            <div class="row g-4 mb-4">
                <!-- Form Tambah Kategori -->
                <div class="col-md-6">
                    <div class="card status-card p-4">
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-plus-circle me-1 text-warning"></i> Tambah Kategori Keahlian</h6>
                        <form action="" method="POST">
                            <div class="input-group">
                                <input type="text" name="nama_kategori" class="form-control" placeholder="Nama Kategori Baru" required>
                                <button type="submit" name="tambah_kategori" class="btn btn-accent">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Form Tambah Badge Skill -->
                <div class="col-md-6">
                    <div class="card status-card p-4">
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-plus-circle me-1 text-warning"></i> Tambah Badge Skill</h6>
                        <form action="" method="POST">
                            <div class="row g-2">
                                <div class="col-5">
                                    <select name="kategori_id" class="form-select" required>
                                        <option value="">-- Kategori --</option>
                                        <?php 
                                        $q_kat_opt = mysqli_query($koneksi, "SELECT * FROM tb_kategori_keahlian");
                                        while ($k_opt = mysqli_fetch_assoc($q_kat_opt)) :
                                        ?>
                                            <option value="<?= $k_opt['id']; ?>"><?= htmlspecialchars($k_opt['nama_kategori']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-5">
                                    <input type="text" name="nama_badge" class="form-control" placeholder="Skill Badge" required>
                                </div>
                                <div class="col-2">
                                    <button type="submit" name="tambah_skill" class="btn btn-accent w-100">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar Keahlian -->
            <div class="card status-card p-4">
                <h4 class="text-white fw-bold mb-3"><i class="bi bi-award me-2" style="color: var(--accent-orange);"></i>Daftar Kategori & Badge Skill</h4>
                <div class="table-responsive">
                    <table class="table table-dark table-hover border-secondary align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Kategori</th>
                                <th>Daftar Badge Technology</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($kat = mysqli_fetch_assoc($query_kategori)) : 
                                $kat_id = $kat['id'];
                                $q_badge = mysqli_query($koneksi, "SELECT * FROM tb_keahlian WHERE kategori_id = '$kat_id'");
                            ?>
                            <tr>
                                <td>0<?= $no++; ?></td>
                                <td class="fw-bold text-white"><?= htmlspecialchars($kat['nama_kategori']); ?></td>
                                <td>
                                    <?php while ($b = mysqli_fetch_assoc($q_badge)) : ?>
                                        <span class="badge bg-secondary me-1 mb-1 p-2">
                                            <?= htmlspecialchars($b['nama_badge']); ?>
                                            <a href="index.php?page=keahlian&action=delete_skill&id=<?= $b['id']; ?>" class="text-danger ms-1 text-decoration-none" onclick="return confirm('Hapus badge ini?')">&times;</a>
                                        </span>
                                    <?php endwhile; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page === 'pesan') : ?>
            <!-- TAB PESAN MASUK -->
            <div class="card status-card p-4">
                <h4 class="text-white fw-bold mb-3"><i class="bi bi-envelope me-2" style="color: var(--accent-orange);"></i>Pesan Masuk</h4>
                <p class="text-secondary small mb-4">Daftar pesan yang dikirim oleh pengunjung melalui form kontak website.</p>
                
                <div class="table-responsive">
                    <table class="table table-dark table-hover border-secondary align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pengirim</th>
                                <th>Email</th>
                                <th>Subjek</th>
                                <th>Pesan</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query_pesan = mysqli_query($koneksi, "SELECT * FROM tb_pesan ORDER BY id DESC");
                            if ($query_pesan && mysqli_num_rows($query_pesan) > 0) :
                                $no_p = 1;
                                while ($p = mysqli_fetch_assoc($query_pesan)) :
                            ?>
                                <tr>
                                    <td><?= $no_p++; ?></td>
                                    <td class="fw-bold text-white"><?= htmlspecialchars($p['nama']); ?></td>
                                    <td><a href="mailto:<?= htmlspecialchars($p['email']); ?>" class="text-orange text-decoration-none"><?= htmlspecialchars($p['email']); ?></a></td>
                                    <td><?= htmlspecialchars($p['subjek']); ?></td>
                                    <td style="max-width: 280px;"><?= htmlspecialchars($p['pesan']); ?></td>
                                    <td class="small text-secondary"><?= htmlspecialchars($p['created_at'] ?? '-'); ?></td>
                                    <td>
                                        <a href="index.php?page=pesan&action=delete_pesan&id=<?= $p['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus pesan ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else : 
                            ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-secondary">Belum ada pesan masuk.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page === 'media' || $page === 'admins') : ?>
            <!-- TAB MEDIA / ADMINS PLACEHOLDER -->
            <div class="card status-card p-5 text-center">
                <i class="bi bi-tools display-4 text-secondary mb-3"></i>
                <h4 class="text-white fw-bold">Modul <?= ucfirst($page); ?> Ready</h4>
                <p class="text-secondary small">Area ini sudah terhubung ke database workspace.</p>
            </div>
        <?php endif; ?>

    </main>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>