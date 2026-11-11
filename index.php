<?php
require_once 'koneksi.php';

// PROSES SIMPAN PESAN DARI FORM KONTAK KE DATABASE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_pesan'])) {
    $nama   = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email  = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $subjek = mysqli_real_escape_string($koneksi, trim($_POST['subjek']));
    $pesan  = mysqli_real_escape_string($koneksi, trim($_POST['pesan']));

    if (!empty($nama) && !empty($email) && !empty($pesan)) {
        $query_insert = "INSERT INTO tb_pesan (nama, email, subjek, pesan) VALUES ('$nama', '$email', '$subjek', '$pesan')";
        if (mysqli_query($koneksi, $query_insert)) {
            echo "<script>alert('Pesan Anda berhasil terkirim dan masuk ke Pesan Masuk Admin!'); window.location.href='index.php#kontak';</script>";
        } else {
            echo "<script>alert('Gagal mengirim pesan: " . mysqli_error($koneksi) . "');</script>";
        }
    } else {
        echo "<script>alert('Harap isi semua kolom inputan!');</script>";
    }
}

// Fetch Profil Data
$query_profil = mysqli_query($koneksi, "SELECT * FROM tb_profil LIMIT 1");
$profil = mysqli_fetch_assoc($query_profil);

// Fetch Kategori & Keahlian Data
$query_kategori = mysqli_query($koneksi, "SELECT * FROM tb_kategori_keahlian ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profil['nama'] ?? 'Farizal Hendra Putra Kusuma'); ?> - Portofolio</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap');
        
        :root {
            --bg-dark: #090a0f;
            --bg-https://github.com/Farizal97/portofolio_jwd/tree/portofoliocard: #11141c;
            --border-card: #1e2330;
            --accent-orange: #ff5e28;
            --accent-orange-hover: #e04e1b;
            --text-gray: #8e9aaf;
        }

        html, body {
            background-color: var(--bg-dark) !important;
            color: var(--text-gray) !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .navbar-custom {
            background-color: rgba(9, 10, 15, 0.85) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .nav-link-custom {
            color: var(--text-gray) !important;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            color: #ffffff !important;
        }

        .badge-status {
            background-color: rgba(255, 94, 40, 0.08);
            border: 1px solid rgba(255, 94, 40, 0.3);
            color: var(--accent-orange);
            border-radius: 50rem;
            padding: 0.35rem 0.9rem;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero-title {
            font-size: 3.8rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.03em;
            color: #ffffff;
        }

        .hero-title .text-orange {
            color: var(--accent-orange) !important;
        }

        .hero-card-frame {
            background-color: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 1.75rem;
            padding: 2.25rem 2rem 1.75rem 2rem;
            position: relative;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }

        .dashed-box {
            position: absolute;
            inset: 12px;
            border: 1px dashed rgba(255, 255, 255, 0.07);
            border-radius: 1.25rem;
            pointer-events: none;
        }

        .watermark-fhpk {
            font-size: 5.5rem;
            font-weight: 900;
            color: rgba(255, 255, 255, 0.04);
            letter-spacing: 0.08em;
            user-select: none;
            text-align: center;
            margin: 2.5rem 0;
        }

        .floating-badge {
            position: absolute;
            top: 2rem;
            right: -0.5rem;
            background-color: #0d1017;
            border: 1px solid var(--border-card);
            border-radius: 0.75rem;
            padding: 0.4rem 0.85rem;
            font-size: 0.68rem;
            color: #d1d5db;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        }

        .card-inner-box {
            background-color: #0d1017;
            border: 1px solid var(--border-card);
            border-radius: 1.25rem;
            padding: 1.25rem;
        }

        .card-custom {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border-card) !important;
            border-radius: 1.5rem !important;
        }

        .btn-accent {
            background-color: var(--accent-orange) !important;
            color: #ffffff !important;
            border-radius: 50rem !important;
            padding: 0.65rem 1.5rem !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            border: none !important;
            transition: all 0.3s ease !important;
        }

        .btn-accent:hover {
            background-color: var(--accent-orange-hover) !important;
            box-shadow: 0 8px 20px rgba(255, 94, 40, 0.25) !important;
        }

        .btn-outline-custom {
            background-color: rgba(255, 255, 255, 0.02) !important;
            border: 1px solid var(--border-card) !important;
            color: #ffffff !important;
            border-radius: 50rem !important;
            padding: 0.65rem 1.5rem !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            transition: all 0.3s ease !important;
        }

        .btn-outline-custom:hover {
            border-color: #334155 !important;
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        .badge-skill {
            background-color: #0d1017 !important;
            border: 1px solid var(--border-card) !important;
            color: #cbd5e1 !important;
            font-size: 0.75rem;
            padding: 0.4rem 0.85rem;
            border-radius: 0.5rem;
        }

        .section-tag {
            color: var(--accent-orange);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        .contact-banner-card {
            background: linear-gradient(135deg, #11141c 60%, rgba(255, 94, 40, 0.08) 100%) !important;
            border: 1px solid var(--border-card) !important;
            border-radius: 1.75rem !important;
        }

        .contact-form-card {
            background-color: #0b0d13 !important;
            border: 1px solid rgba(255, 255, 255, 0.06) !important;
            border-radius: 1.25rem !important;
        }

        .form-label-custom {
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .input-custom {
            background-color: #131620 !important;
            border: 1px solid #1e2330 !important;
            color: #ffffff !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            font-size: 0.85rem !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-custom::placeholder {
            color: #475569 !important;
        }

        .input-custom:focus {
            border-color: var(--accent-orange) !important;
            box-shadow: 0 0 0 3px rgba(255, 94, 40, 0.15) !important;
        }

        .btn-submit-orange {
            background-color: var(--accent-orange) !important;
            color: #ffffff !important;
            border-radius: 0.75rem !important;
            padding: 0.8rem 1.5rem !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            border: none !important;
            transition: all 0.3s ease !important;
        }

        .btn-submit-orange:hover {
            background-color: var(--accent-orange-hover) !important;
            box-shadow: 0 8px 25px rgba(255, 94, 40, 0.3) !important;
        }

        .icon-box-email {
            width: 38px;
            height: 38px;
            border-radius: 0.65rem;
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-card);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-orange);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 justify-content-between">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-md navbar-dark navbar-custom fixed-top py-3">
        <div class="container" style="max-width: 1100px;">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <span class="d-inline-flex justify-content-center align-items-center rounded-3" style="width:32px; height:32px; background-color: rgba(255,94,40,0.15); border: 1px solid rgba(255,94,40,0.3); color: var(--accent-orange); font-size: 0.8rem; font-weight: bold;">FK</span>
                <span class="text-white fw-bold" style="font-size: 1rem;">FHPK<span style="color: var(--accent-orange);">.dev</span></span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto gap-md-4 text-center my-2 my-md-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom active" href="#profil">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#keahlian">Keahlian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#kontak">Kontak</a>
                    </li>
                </ul>
                <div class="text-center text-md-end">
                    <a href="#kontak" class="btn btn-outline-custom text-decoration-none py-2 px-3">
                        Mari terhubung <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="pt-5 mt-4">
        
        <!-- HERO SECTION -->
        <section id="profil" class="py-5">
            <div class="container py-4 py-lg-5" style="max-width: 1100px;">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-7">
                        <div class="mb-3">
                            <span class="badge-status d-inline-flex align-items-center gap-2">
                                <span class="spinner-grow spinner-grow-sm" style="width: 0.35rem; height: 0.35rem; color: var(--accent-orange);" role="status"></span>
                                Available for collaboration
                            </span>
                        </div>

                        <p class="text-secondary mb-2" style="font-size: 0.95rem;">Halo, saya <?= htmlspecialchars($profil['nama'] ?? 'Farizal Hendra Putra Kusuma'); ?>.</p>
                        <h1 class="hero-title mb-4">
                            Saya merancang <br>
                            <span class="text-orange">pengalaman digital.</span>
                        </h1>

                        <p class="text-secondary mb-4 pe-lg-4" style="font-size: 0.95rem; line-height: 1.7;">
                            <?= htmlspecialchars($profil['ringkasan_summary'] ?? ''); ?>
                        </p>

                        <div class="d-flex flex-wrap gap-3 mb-5">
                            <a href="#keahlian" class="btn btn-accent text-decoration-none">
                                Eksplor keahlian <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                            <a href="#kontak" class="btn btn-outline-custom text-decoration-none">
                                <i class="bi bi-envelope me-1 text-secondary"></i> Hubungi saya
                            </a>
                        </div>

                        <div class="pt-3 border-top border-secondary border-opacity-25 text-secondary" style="font-size: 0.8rem;">
                            <span class="me-3"><i class="bi bi-geo-alt-fill me-1" style="color: var(--accent-orange);"></i> Bekasi, Indonesia</span>
                            <span class="me-3">|</span>
                            <span>Web Development • Product Design</span>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="hero-card-frame">
                            <div class="dashed-box"></div>
                            
                            <div class="d-flex justify-content-between align-items-center text-secondary mb-4" style="font-size: 0.7rem;">
                                <span class="font-monospace text-uppercase" style="letter-spacing: 0.1em; color: #64748b;">PORTFOLIO / <?= date('Y'); ?></span>
                                <i class="bi bi-sparkles" style="color: var(--accent-orange); font-size: 0.9rem;"></i>
                            </div>

                            <div class="floating-badge d-flex align-items-center gap-2">
                                <span style="width: 5px; height: 5px; background-color: #10b981; border-radius: 50%;"></span>
                                <span><strong class="text-white" style="font-weight:600;">CRAFTING</strong> Clean Interfaces</span>
                            </div>

                            <div class="watermark-fhpk">FHPK</div>

                            <div class="card-inner-box mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex justify-content-center align-items-center rounded-3" style="width:38px; height:38px; background-color: rgba(255,94,40,0.15); border: 1px solid rgba(255,94,40,0.3); color: var(--accent-orange); font-weight: bold; font-size: 0.85rem;">
                                            FK
                                        </div>
                                        <div>
                                            <h6 class="text-white mb-0 fw-bold" style="font-size: 0.85rem;"><?= htmlspecialchars($profil['nama'] ?? 'Farizal Hendra Putra Kusuma'); ?></h6>
                                            <small class="text-secondary" style="font-size: 0.72rem;"><?= htmlspecialchars($profil['sub_role'] ?? 'Web Developer & UI/UX Designer'); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-inner-box d-flex justify-content-between align-items-center py-2 px-3">
                                <div>
                                    <span class="d-block text-uppercase text-secondary" style="font-size: 0.62rem; letter-spacing: 0.08em;">FOCUS</span>
                                    <strong class="text-white" style="font-size: 0.78rem;">Web experiences</strong>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px; height:28px; background-color: rgba(255,94,40,0.1); border: 1px solid rgba(255,94,40,0.2);">
                                    <i class="bi bi-arrow-right" style="color: var(--accent-orange); font-size: 0.75rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- KEAHLIAN SECTION -->
        <section id="keahlian" class="py-5 border-top border-secondary border-opacity-25">
            <div class="container py-4" style="max-width: 1100px;">
                <div class="row align-items-end mb-5">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <span class="section-tag d-block mb-2">— KEAHLIAN UTAMA</span>
                        <h2 class="h1 fw-bold text-white mb-0">Dibangun dengan <br>detail dan tujuan.</h2>
                    </div>
                    <div class="col-md-5">
                        <p class="text-secondary mb-0" style="font-size: 0.88rem; line-height: 1.6;">
                            Dari konsep visual hingga implementasi teknis, setiap solusi dibuat untuk menjawab kebutuhan pengguna dan mendukung tujuan produk.
                        </p>
                    </div>
                </div>

                <div class="row g-4">
                    <?php 
                    $icon_list = ['bi-code-slash', 'bi-database', 'bi-layers'];
                    $index = 0;
                    while ($kategori = mysqli_fetch_assoc($query_kategori)) : 
                        $kat_id = $kategori['id'];
                        $query_badge = mysqli_query($koneksi, "SELECT * FROM tb_keahlian WHERE kategori_id = '$kat_id'");
                        $current_icon = $icon_list[$index % count($icon_list)];
                        $index++;
                    ?>
                    <div class="col-md-4">
                        <div class="card card-custom h-100 p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-white bg-opacity-10" style="width:38px; height:38px; color: var(--accent-orange);">
                                        <i class="bi <?= $current_icon; ?> fs-5"></i>
                                    </div>
                                    <span class="text-secondary font-monospace small">0<?= $index; ?></span>
                                </div>
                                <h4 class="text-white h5 fw-bold mb-2"><?= htmlspecialchars($kategori['nama_kategori']); ?></h4>
                                <p class="text-secondary mb-4" style="font-size: 0.82rem; line-height: 1.6;">
                                    Pengembangan solusi teknis dan visual yang efisien, terstruktur, serta siap diimplementasikan.
                                </p>
                            </div>

                            <div class="pt-3 border-top border-secondary border-opacity-25 d-flex flex-wrap gap-2">
                                <?php while ($badge = mysqli_fetch_assoc($query_badge)) : ?>
                                    <span class="badge-skill">
                                        <?= htmlspecialchars($badge['nama_badge']); ?>
                                    </span>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>

        <!-- KONTAK SECTION -->
        <section id="kontak" class="py-5">
            <div class="container py-4" style="max-width: 1100px;">
                <div class="card contact-banner-card p-4 p-md-5 overflow-hidden">
                    <div class="row gy-5 align-items-stretch">
                        
                        <!-- LEFT COLUMN: Content Info & Sosmed -->
                        <div class="col-lg-5 d-flex flex-column justify-content-between">
                            <div>
                                <span class="section-tag d-block mb-3">— MARI BERKOLABORASI</span>
                                <h2 class="display-5 fw-extrabold text-white mb-4" style="line-height: 1.15;">
                                    Punya ide menarik?<br>
                                    Mari wujudkan<br>
                                    bersama.
                                </h2>
                                <p class="text-secondary mb-4" style="font-size: 0.9rem; line-height: 1.7; max-width: 420px;">
                                    Terbuka untuk diskusi proyek, kolaborasi, dan peluang baru di bidang pengembangan web serta desain produk digital.
                                </p>
                            </div>

                            <div class="pt-4 border-top border-secondary border-opacity-25 mt-4">
                                <span class="d-block text-uppercase text-secondary fw-bold mb-3" style="font-size: 0.65rem; letter-spacing: 0.12em;">
                                    ATAU HUBUNGI MELALUI
                                </span>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php 
                                    $query_sosmed_contact = mysqli_query($koneksi, "SELECT * FROM tb_media_sosial");
                                    if ($query_sosmed_contact) :
                                        while ($sosmed = mysqli_fetch_assoc($query_sosmed_contact)) : 
                                    ?>
                                            <a href="<?= htmlspecialchars($sosmed['url_link']); ?>" target="_blank" class="btn btn-outline-custom text-decoration-none py-2 px-3 d-inline-flex align-items-center gap-2" style="font-size: 0.8rem;">
                                                <i class="bi bi-link-45deg" style="color: var(--accent-orange);"></i> <?= htmlspecialchars($sosmed['platform']); ?>
                                            </a>
                                    <?php 
                                        endwhile;
                                    endif; 
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Form Ceritakan Proyek (POST) -->
                        <div class="col-lg-7">
                            <div class="card contact-form-card p-4 p-sm-4 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div>
                                        <span class="text-uppercase fw-bold d-block mb-1" style="font-size: 0.68rem; letter-spacing: 0.12em; color: var(--accent-orange) !important;">
                                            KIRIM PESAN
                                        </span>
                                        <h4 class="text-white fw-bold mb-0">Ceritakan proyek Anda</h4>
                                    </div>
                                    <div class="icon-box-email">
                                        <i class="bi bi-envelope fs-5"></i>
                                    </div>
                                </div>

                                <form action="index.php" method="POST">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label form-label-custom">Nama</label>
                                            <input type="text" name="nama" class="form-control input-custom" placeholder="Nama lengkap" required>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label class="form-label form-label-custom">Email</label>
                                            <input type="email" name="email" class="form-control input-custom" placeholder="nama@email.com" required>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label form-label-custom">Subjek</label>
                                            <input type="text" name="subjek" class="form-control input-custom" placeholder="Contoh: Website company profile" required>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label form-label-custom">Pesan</label>
                                            <textarea name="pesan" rows="4" class="form-control input-custom" placeholder="Ceritakan tujuan, kebutuhan, dan lingkup proyek Anda..." required></textarea>
                                        </div>

                                        <div class="col-12 pt-2">
                                            <button type="submit" name="kirim_pesan" class="btn btn-submit-orange w-100 d-flex align-items-center justify-content-center gap-2">
                                                Kirim pesan <i class="bi bi-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <div class="text-center mt-3">
                                    <small class="text-secondary" style="font-size: 0.72rem;">
                                        Informasi Anda hanya digunakan untuk menanggapi pesan ini.
                                    </small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="py-4 border-top border-secondary border-opacity-25">
        <div class="container d-flex flex-column flex-sm-row align-items-center justify-between text-secondary" style="max-width: 1100px; font-size: 0.75rem;">
            <p class="mb-2 mb-sm-0">&copy; <?= date('Y'); ?> <?= htmlspecialchars($profil['nama'] ?? 'Farizal Hendra Putra Kusuma'); ?>. All rights reserved.</p>
            <p class="mb-0">Designed & Built with</p>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- External JavaScript -->
    <script src="script.js"></script>
</body>
</html>