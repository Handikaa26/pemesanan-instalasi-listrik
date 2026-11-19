<?php
// views/layouts/header.php
$currentUser = get_auth_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?><?= APP_NAME ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0b4f9c;
            --primary-dark: #07356c;
            --accent-yellow: #f59e0b;
            --accent-glow: #e0f2fe;
            --bg-light: #f8fafc;
            --text-dark: #0f172a;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #07356c 0%, #0b4f9c 100%);
            box-shadow: 0 4px 20px rgba(7, 53, 108, 0.15);
        }
        .navbar-brand-badge {
            background: #fef08a;
            color: #854d0e;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 999px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .card-custom {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.25s ease-in-out;
            background: #ffffff;
        }
        .card-custom:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.09);
        }
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            font-weight: 600;
            border-radius: 0.6rem;
            padding: 0.6rem 1.4rem;
            transition: all 0.2s;
        }
        .btn-primary-custom:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            color: white;
            box-shadow: 0 4px 12px rgba(11, 79, 156, 0.3);
        }
        .badge-status-0 { background-color: #fef3c7; color: #92400e; } /* Menunggu */
        .badge-status-1 { background-color: #dbeafe; color: #1e40af; } /* Diproses / Lunas */
        .badge-status-2 { background-color: #dcfce7; color: #166534; } /* Selesai */
        .badge-status-3 { background-color: #fee2e2; color: #991b1b; } /* Batal / Ditolak */
        .hero-banner {
            background: linear-gradient(135deg, #07356c 0%, #0369a1 100%);
            color: white;
            border-radius: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        footer {
            margin-top: auto;
            background: #0f172a;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-5" href="index.php">
            <span class="bg-warning text-dark p-2 rounded-3 d-inline-flex align-items-center justify-content-center shadow-sm">
                <i class="bi bi-lightning-charge-fill"></i>
            </span>
            <span>PT. EKSAVINDO <span class="text-warning">LISTRIK</span></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item">
                    <a class="nav-link <?= (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active fw-bold' : '' ?>" href="index.php">
                        <i class="bi bi-house-door me-1"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'katalog') ? 'active fw-bold' : '' ?>" href="index.php?page=katalog">
                        <i class="bi bi-grid me-1"></i> Layanan Instalasi
                    </a>
                </li>
                <?php if (is_logged_in() && !is_admin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'pesanan_saya') ? 'active fw-bold' : '' ?>" href="index.php?page=pesanan_saya">
                        <i class="bi bi-receipt me-1"></i> Pesanan Saya
                    </a>
                </li>
                <?php endif; ?>
                <?php if (is_admin()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active text-warning fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-shield-check me-1"></i> Panel Admin
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="index.php?page=admin_dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                        <li><a class="dropdown-item" href="index.php?page=admin_pesanan"><i class="bi bi-card-checklist me-2"></i> Kelola Pesanan & Jadwal</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="index.php?page=admin_produk"><i class="bi bi-box-seam me-2"></i> Kelola Produk/Paket</a></li>
                        <li><a class="dropdown-item" href="index.php?page=admin_kategori"><i class="bi bi-tags me-2"></i> Kelola Kategori</a></li>
                        <li><a class="dropdown-item" href="index.php?page=admin_bank"><i class="bi bi-bank me-2"></i> Kelola Rekening Bank</a></li>
                        <li><a class="dropdown-item" href="index.php?page=admin_diskon"><i class="bi bi-percent me-2"></i> Kelola Diskon</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <?php if (is_logged_in() && !is_admin()): 
                    $db = Database::connect();
                    $stmtC = $db->prepare("SELECT SUM(jml_beli) FROM keranjang WHERE id_user = ?");
                    $stmtC->execute([$currentUser['id_user']]);
                    $cartCount = $stmtC->fetchColumn() ?: 0;
                ?>
                <a href="index.php?page=keranjang" class="btn btn-outline-light position-relative rounded-pill px-3 py-2">
                    <i class="bi bi-cart3"></i>
                    <?php if ($cartCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $cartCount ?>
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>

                <?php if (is_logged_in()): ?>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5 text-primary"></i>
                        <span><?= htmlspecialchars($currentUser['username']) ?></span>
                        <span class="badge <?= is_admin() ? 'bg-danger' : 'bg-primary' ?>"><?= is_admin() ? 'Admin' : 'Konsumen' ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li class="px-3 py-2 text-muted small">ID: <?= htmlspecialchars($currentUser['id_user']) ?></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="index.php?page=profil"><i class="bi bi-person me-2"></i> Profil & Alamat</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="index.php?page=logout"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    </ul>
                </div>
                <?php else: ?>
                <div class="d-flex gap-2">
                    <a href="index.php?page=login" class="btn btn-outline-light rounded-pill px-3">Masuk</a>
                    <a href="index.php?page=register" class="btn btn-warning rounded-pill px-3 fw-bold text-dark">Daftar Akun</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="flex-grow-1">

