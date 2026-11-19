<?php
// views/konsumen/checkout.php
require_login();
$pageTitle = 'Form Checkout Multi-Alamat - PT. Eksavindo Mitra Mandiri';
require_once __DIR__ . '/../layouts/header.php';
$db = Database::connect();
$currentUser = get_auth_user();

// Ambil item keranjang
$sql = "SELECT k.*, p.nama_produk, p.harga, p.ukuran AS ukuran_produk, p.daya, p.foto_produk, d.besar_diskon,
               (p.harga - COALESCE(d.besar_diskon, 0)) AS harga_final
        FROM keranjang k
        JOIN produk p ON k.id_produk = p.id_produk
        LEFT JOIN diskon d ON p.id_produk = d.id_produk AND d.is_active = 1
        WHERE k.id_user = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$currentUser['id_user']]);
$items = $stmt->fetchAll();

if (empty($items)) {
    header("Location: index.php?page=keranjang");
    exit;
}

$grandTotal = 0;
foreach ($items as $item) {
    $grandTotal += ($item['harga_final'] * $item['jml_beli']);
}

// Ambil daftar alamat tersimpan user (Urutkan dari yang terbaru)
$stmtA = $db->prepare("SELECT * FROM alamat WHERE id_user = ? ORDER BY id_alamat DESC");
$stmtA->execute([$currentUser['id_user']]);
$daftarAlamat = $stmtA->fetchAll();

// Ambil daftar bank untuk opsi transfer manual
$daftarBank = $db->query("SELECT * FROM bank")->fetchAll();

$hasSavedAddress = !empty($daftarAlamat);
?>

<style>
/* Checkout Stepper UI */
.checkout-stepper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 520px;
    margin: 0 auto 2.5rem;
    position: relative;
}
.checkout-stepper::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 40px;
    right: 40px;
    height: 3px;
    background: #e2e8f0;
    z-index: 1;
    transform: translateY(-50%);
}
.step-item {
    position: relative;
    z-index: 2;
    background: #fff;
    padding: 0 10px;
    text-align: center;
}
.step-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
    margin: 0 auto 6px;
    border: 2px solid #cbd5e1;
    color: #64748b;
    background: #fff;
    transition: all 0.25s ease;
}
.step-item.completed .step-circle {
    background: #10b981;
    border-color: #10b981;
    color: #fff;
}
.step-item.active .step-circle {
    background: #2563eb;
    border-color: #2563eb;
    color: #fff;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2);
}
.step-title {
    font-size: 0.78rem;
    font-weight: 600;
    color: #64748b;
}
.step-item.active .step-title {
    color: #1e293b;
    font-weight: 700;
}

/* Address Card Selection */
.address-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    transition: all 0.2s ease;
    cursor: pointer;
    background: #ffffff;
    position: relative;
}
.address-card:hover {
    border-color: #93c5fd;
    background: #f8fafc;
}
.address-card.selected {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
}
.address-type-pill {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Radio Address Type Pill Button Group */
.type-radio-btn {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.65rem 1.25rem;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 600;
    font-size: 0.88rem;
    background: #fff;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.type-radio-btn:hover {
    border-color: #94a3b8;
}
input[type="radio"]:checked + .type-radio-btn {
    border-color: #ea580c;
    background: #fff7ed;
    color: #c2410c;
    box-shadow: 0 2px 8px rgba(234, 88, 12, 0.15);
}

/* Order Summary Box */
.order-summary-card {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}
</style>

<div class="container py-4">
    <!-- Stepper Navigation (Mirip Gambar Gokwik / Multi-Step) -->
    <div class="checkout-stepper">
        <div class="step-item completed">
            <div class="step-circle"><i class="bi bi-check-lg"></i></div>
            <div class="step-title">1. Kontak</div>
        </div>
        <div class="step-item active">
            <div class="step-circle">2</div>
            <div class="step-title">2. Alamat Lokasi</div>
        </div>
        <div class="step-item">
            <div class="step-circle">3</div>
            <div class="step-title">3. Pembayaran</div>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger py-3 px-4 rounded-3 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                <div>
                    <h6 class="fw-bold mb-0">Terjadi Kesalahan</h6>
                    <small><?= htmlspecialchars($error) ?></small>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'alamat_added'): ?>
        <div class="alert alert-success py-2 px-3 rounded-3 small mb-4">
            <i class="bi bi-check-circle-fill me-1 text-success"></i> Alamat baru berhasil disimpan ke daftar alamat Anda!
        </div>
    <?php endif; ?>

    <form action="index.php?page=checkout_process" method="POST" enctype="multipart/form-data" id="checkoutForm">
        <!-- Hidden input mode alamat: 'saved' atau 'new' -->
        <input type="hidden" name="address_mode" id="addressMode" value="<?= $hasSavedAddress ? 'saved' : 'new' ?>">

        <div class="row g-4">
            <!-- KOLOM KIRI: FORM MULTI-ALAMAT & METODE PEMBAYARAN -->
            <div class="col-lg-7">
                
                <!-- KARTU 1: LOKASI PEMASANGAN LISTRIK (MULTI-ALAMAT) -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 border-bottom mb-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">
                                <i class="bi bi-geo-alt-fill text-danger me-2"></i>Lokasi Pemasangan Instalasi
                            </h5>
                            <span class="text-muted small">Pilih alamat tersimpan atau tambahkan alamat lokasi baru</span>
                        </div>

                        <!-- Toggle Tab: Alamat Tersimpan vs Alamat Baru -->
                        <div class="btn-group btn-group-sm mt-2 mt-sm-0" role="group">
                            <?php if ($hasSavedAddress): ?>
                            <button type="button" class="btn btn-primary fw-semibold" id="tabSavedBtn" onclick="switchAddressMode('saved')">
                                <i class="bi bi-journal-bookmark-fill me-1"></i> Alamat Tersimpan (<?= count($daftarAlamat) ?>)
                            </button>
                            <button type="button" class="btn btn-outline-primary fw-semibold" id="tabNewBtn" onclick="switchAddressMode('new')">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Baru
                            </button>
                            <?php else: ?>
                            <span class="badge bg-primary px-3 py-2">Tambah Alamat Pertama</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- PENGATURAN MODE A: ALAMAT TERSIMPAN -->
                    <div id="sectionSavedAddresses" style="<?= $hasSavedAddress ? 'display: block;' : 'display: none;' ?>">
                        <?php if ($hasSavedAddress): ?>
                        <div class="d-flex flex-column gap-3 mb-3" id="savedAddressList">
                            <?php foreach ($daftarAlamat as $idx => $alm): 
                                $tipe = $alm['tipe_alamat'] ?? 'Rumah';
                                $isHome = (stripos($tipe, 'Rumah') !== false);
                                $kota = $alm['kota'] ?? 'Kota Metro';
                                $prov = $alm['provinsi'] ?? 'Lampung';
                                $kodepos = $alm['kode_pos'] ?? '34111';
                                $catatan = $alm['catatan_patokan'] ?? '';
                            ?>
                            <div class="address-card <?= ($idx === 0) ? 'selected' : '' ?>" id="card-addr-<?= $alm['id_alamat'] ?>" onclick="selectSavedAddress(<?= $alm['id_alamat'] ?>)">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="pt-1">
                                        <input type="radio" name="id_alamat" value="<?= $alm['id_alamat'] ?>" id="radio-addr-<?= $alm['id_alamat'] ?>" class="form-check-input" <?= ($idx === 0) ? 'checked' : '' ?> onclick="event.stopPropagation(); selectSavedAddress(<?= $alm['id_alamat'] ?>)">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <div class="fw-bold text-dark fs-6">
                                                <?= htmlspecialchars($alm['penerima']) ?>
                                                <span class="text-muted fw-normal small ms-1">(<?= htmlspecialchars($alm['telepon_penerima']) ?>)</span>
                                            </div>
                                            <span class="address-type-pill <?= $isHome ? 'bg-primary-subtle text-primary border border-primary' : 'bg-warning-subtle text-warning-emphasis border border-warning' ?>">
                                                <i class="bi <?= $isHome ? 'bi-house-door-fill' : 'bi-building' ?> me-1"></i><?= htmlspecialchars($tipe) ?>
                                            </span>
                                        </div>
                                        <div class="text-secondary small mb-1">
                                            <?= htmlspecialchars($alm['alamat']) ?>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="bi bi-pin-map-fill text-danger me-1"></i><?= htmlspecialchars($kota) ?>, <?= htmlspecialchars($prov) ?> <?= !empty($kodepos) ? '- ' . htmlspecialchars($kodepos) : '' ?>
                                        </div>
                                        <?php if (!empty($catatan)): ?>
                                        <div class="mt-2 p-2 bg-light rounded small text-dark border-start border-warning border-3">
                                            <i class="bi bi-info-circle me-1 text-primary"></i><strong>Patokan/Catatan:</strong> <?= htmlspecialchars($catatan) ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="text-center pt-2">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-semibold" onclick="switchAddressMode('new')">
                                <i class="bi bi-plus-lg me-1"></i> Ingin Kirim Teknisi ke Alamat Baru? Klik Disini
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- PENGATURAN MODE B: TAMBAH ALAMAT BARU (FORM SESUAI FOTO GOKWIK CHECKOUT) -->
                    <div id="sectionNewAddress" style="<?= $hasSavedAddress ? 'display: none;' : 'display: block;' ?>">
                        <div class="p-3 bg-light rounded-4 border">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-dark mb-0">
                                    <i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Alamat Pemasangan Baru
                                </h6>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">* Kolom Wajib Diisi</span>
                            </div>

                            <!-- Pilihan Tipe Bangunan / Address Type (Rumah vs Komersial) -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Tipe Alamat Pemasangan (Address Type)</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <label>
                                        <input type="radio" name="new_tipe_alamat" value="Rumah" class="d-none" checked>
                                        <div class="type-radio-btn">
                                            <i class="bi bi-house-door-fill text-danger"></i> Rumah (Home)
                                        </div>
                                    </label>
                                    <label>
                                        <input type="radio" name="new_tipe_alamat" value="Komersial / Usaha" class="d-none">
                                        <div class="type-radio-btn">
                                            <i class="bi bi-building text-primary"></i> Komersial / Toko / Kantor
                                        </div>
                                    </label>
                                    <label>
                                        <input type="radio" name="new_tipe_alamat" value="Industri / Gedung" class="d-none">
                                        <div class="type-radio-btn">
                                            <i class="bi bi-gear-wide-connected text-secondary"></i> Industri / Proyek
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Baris 1: Kode Pos, Kota, Provinsi -->
                            <div class="row g-2 mb-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary mb-1">Kode Pos *</label>
                                    <input type="text" name="new_kode_pos" id="inputKodePos" class="form-control form-control-sm" placeholder="Contoh: 34111" value="34111">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary mb-1">Kota / Kabupaten *</label>
                                    <input type="text" name="new_kota" id="inputKota" class="form-control form-control-sm" placeholder="Contoh: Kota Metro" value="Kota Metro">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary mb-1">Provinsi *</label>
                                    <input type="text" name="new_provinsi" id="inputProvinsi" class="form-control form-control-sm" placeholder="Contoh: Lampung" value="Lampung">
                                </div>
                            </div>

                            <!-- Baris 2: Nama Penerima & WhatsApp -->
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary mb-1">Nama Lengkap Pemilik / Penerima di Lokasi *</label>
                                    <input type="text" name="new_penerima" id="inputPenerima" class="form-control form-control-sm" placeholder="Nama Pemilik Rumah / Gedung" value="<?= htmlspecialchars($currentUser['username'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary mb-1">Nomor WhatsApp / Telepon Aktif *</label>
                                    <input type="text" name="new_telepon" id="inputTelepon" class="form-control form-control-sm" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($currentUser['nomor_telp'] ?? '') ?>">
                                </div>
                            </div>

                            <!-- Baris 3: Alamat Lengkap -->
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-secondary mb-1">Alamat Lengkap Lokasi Pemasangan Listrik *</label>
                                <textarea name="new_alamat" id="inputAlamatLengkap" class="form-control form-control-sm" rows="3" placeholder="Masukkan nama jalan, nomor bangunan/rumah, RT/RW, kelurahan, dan kecamatan secara detail..."></textarea>
                            </div>

                            <!-- Baris 4: Catatan & Patokan -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary mb-1">Patokan Lokasi & Catatan Tambahan (Opsional)</label>
                                <input type="text" name="new_catatan" id="inputCatatan" class="form-control form-control-sm" placeholder="Contoh: Sebelah Masjid Nurul Iman, pagar warna abu-abu, KWH meter di teras.">
                            </div>

                            <div class="alert alert-info py-2 px-3 small mb-0 rounded-3 d-flex align-items-center">
                                <i class="bi bi-shield-check fs-5 me-2 text-primary"></i>
                                <div>Alamat baru ini akan otomatis tersimpan di buku alamat akun Anda untuk pemesanan berikutnya.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping / Teknisi Dispatch Card Sesuai Gambar -->
                    <div class="p-3 mt-3 bg-white rounded-3 border d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                <i class="bi bi-tools me-1"></i> Teknisi Resmi
                            </span>
                            <span class="small fw-semibold text-dark">Survei Lokasi & Pengerjaan Standar PT. Eksavindo Mitra Mandiri</span>
                        </div>
                        <span class="badge bg-success px-2 py-1 fw-bold">Gratis / Termasuk</span>
                    </div>
                </div>

                <!-- KARTU 2: METODE PEMBAYARAN -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>3. Metode Pembayaran Resmi
                    </h5>

                    <div class="d-flex flex-column gap-3 mb-3">
                        <!-- OPSI A: Payment Gateway Midtrans (Rekomendasi) -->
                        <label class="p-3 border border-primary bg-primary bg-opacity-10 rounded-3 d-flex align-items-center justify-content-between cursor-pointer">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="metode_bayar" value="payment_gateway" class="form-check-input mt-0" checked id="radioGateway" onchange="togglePaymentMethod()">
                                <div>
                                    <div class="fw-bold text-primary">
                                        <i class="bi bi-lightning-charge-fill me-1 text-warning"></i> Online Payment Gateway QRIS & VA (Instan & Otomatis)
                                        <span class="badge bg-success ms-2">Auto-Verified</span>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        Scan otomatis via <strong>GoPay QRIS, BCA Mobile, Livin, OVO, Dana</strong>, atau Virtual Account. Tanpa perlu input nominal manual.
                                    </div>
                                </div>
                            </div>
                            <span class="fs-4 text-primary"><i class="bi bi-qr-code-scan"></i></span>
                        </label>

                        <!-- OPSI B: Transfer Rekening Manual -->
                        <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between cursor-pointer">
                            <div class="d-flex align-items-center gap-3">
                                <input type="radio" name="metode_bayar" value="manual_transfer" class="form-check-input mt-0" id="radioManual" onchange="togglePaymentMethod()">
                                <div>
                                    <div class="fw-bold text-dark">Transfer Rekening Bank Manual PT. Eksavindo</div>
                                    <div class="text-muted small mt-1">Transfer langsung ke rekening kantor dan unggah struk bukti transfer.</div>
                                </div>
                            </div>
                            <span class="fs-4 text-secondary"><i class="bi bi-bank"></i></span>
                        </label>
                    </div>

                    <!-- Bagian Form Transfer Manual -->
                    <div id="sectionManual" style="display: none;" class="p-3 bg-light rounded-3 border mb-2">
                        <h6 class="fw-bold small text-dark mb-2">Pilih Rekening Bank Tujuan:</h6>
                        <div class="d-flex flex-column gap-2 mb-3">
                            <?php foreach ($daftarBank as $bIdx => $bank): ?>
                            <label class="p-2 border bg-white rounded-3 d-flex align-items-center gap-2 small cursor-pointer">
                                <input type="radio" name="id_bank" value="<?= $bank['id_bank'] ?>" <?= ($bIdx === 0) ? 'checked' : '' ?>>
                                <div>
                                    <strong><?= htmlspecialchars($bank['nama_bank']) ?></strong> - 
                                    <span class="font-monospace text-primary fw-bold"><?= htmlspecialchars($bank['norek']) ?></span>
                                    (a/n <?= htmlspecialchars($bank['atas_nama']) ?>)
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>

                        <div>
                            <label class="form-label fw-bold small text-dark">Unggah Foto Struk / Bukti Transfer</label>
                            <input type="file" name="bukti_transfer" id="inputBukti" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg">
                            <div class="form-text small">Wajib upload gambar JPG / PNG jika memilih transfer bank manual.</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- KOLOM KANAN: ORDER SUMMARY (PERSIS DESAIN FOTO CHECKOUT GOKWIK) -->
            <div class="col-lg-5">
                <div class="order-summary-card p-4 sticky-top" style="top: 90px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <h5 class="fw-bold mb-0 text-dark">Ringkasan Pemesanan</h5>
                        <span class="badge bg-primary-subtle text-primary fw-bold"><?= count($items) ?> Item</span>
                    </div>

                    <!-- Daftar Item Pemesanan -->
                    <div class="d-flex flex-column gap-3 mb-3">
                        <?php foreach ($items as $item): ?>
                        <div class="d-flex align-items-center gap-3 border-bottom pb-3">
                            <div class="rounded-3 bg-light d-flex align-items-center justify-content-center border" style="width: 52px; height: 52px; overflow: hidden; flex-shrink: 0;">
                                <?php if (!empty($item['foto_produk']) && file_exists(__DIR__ . '/../../uploads/' . $item['foto_produk'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($item['foto_produk']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <i class="bi bi-lightning-charge-fill text-warning fs-4"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold small text-dark mb-0 line-clamp-1"><?= htmlspecialchars($item['nama_produk']) ?></div>
                                <div class="text-muted small">
                                    Qty: <strong class="text-dark"><?= $item['jml_beli'] ?></strong> 
                                    <?= !empty($item['daya']) ? '&bull; ' . htmlspecialchars($item['daya']) : '' ?>
                                </div>
                            </div>
                            <div class="fw-bold text-dark small text-end">
                                <?= rupiah($item['harga_final'] * $item['jml_beli']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Kode Promo / Voucher Kupon Sesuai Foto -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-warning"><i class="bi bi-tag-fill"></i></span>
                            <input type="text" id="inputPromoCode" class="form-control border-start-0" placeholder="Kode Promo / Kupon?">
                            <button class="btn btn-outline-primary fw-semibold px-3" type="button" onclick="applyPromoCode()">Terapkan</button>
                        </div>
                        <div id="promoFeedback" class="small mt-1" style="display: none;"></div>
                    </div>

                    <!-- Rincian Biaya -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Subtotal</span>
                        <span class="fw-semibold text-dark small"><?= rupiah($grandTotal) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" id="rowPromoDiscount" style="display: none;">
                        <span class="text-success small fw-semibold">Diskon Kupon Promo</span>
                        <span class="text-success small fw-bold" id="valPromoDiscount">- Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Biaya Survei & Teknisi</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold">Gratis (Free)</span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                        <div>
                            <div class="fw-bold text-dark fs-6">Total Pembayaran:</div>
                            <small class="text-muted">Sudah termasuk PPN & jasa instalasi</small>
                        </div>
                        <div class="text-end">
                            <span class="fs-4 fw-bold text-danger" id="displayTotalBayar"><?= rupiah($grandTotal) ?></span>
                        </div>
                    </div>

                    <!-- Tombol Lanjut Pembayaran (Continue -> Sesuai Gambar) -->
                    <button type="submit" id="btnSubmitOrder" class="btn btn-warning w-100 py-3 rounded-pill fw-bold text-dark shadow fs-6">
                        Lanjut ke Pembayaran QRIS <i class="bi bi-arrow-right ms-1"></i>
                    </button>

                    <div class="text-center mt-3">
                        <a href="index.php?page=keranjang" class="text-decoration-none text-muted small">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Keranjang Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Switch antara Mode Alamat Tersimpan dan Alamat Baru
function switchAddressMode(mode) {
    const addressModeInput = document.getElementById('addressMode');
    const secSaved = document.getElementById('sectionSavedAddresses');
    const secNew = document.getElementById('sectionNewAddress');
    const tabSavedBtn = document.getElementById('tabSavedBtn');
    const tabNewBtn = document.getElementById('tabNewBtn');

    addressModeInput.value = mode;

    if (mode === 'saved') {
        if (secSaved) secSaved.style.display = 'block';
        if (secNew) secNew.style.display = 'none';
        if (tabSavedBtn) {
            tabSavedBtn.className = 'btn btn-primary fw-semibold';
            tabNewBtn.className = 'btn btn-outline-primary fw-semibold';
        }
    } else {
        if (secSaved) secSaved.style.display = 'none';
        if (secNew) secNew.style.display = 'block';
        if (tabSavedBtn && tabNewBtn) {
            tabSavedBtn.className = 'btn btn-outline-primary fw-semibold';
            tabNewBtn.className = 'btn btn-primary fw-semibold';
        }
    }
}

// Memilih Alamat Tersimpan
function selectSavedAddress(id) {
    // Uncheck all & remove class selected
    document.querySelectorAll('.address-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Check radio & add selected class
    const radio = document.getElementById('radio-addr-' + id);
    const card = document.getElementById('card-addr-' + id);
    if (radio) radio.checked = true;
    if (card) card.classList.add('selected');

    // Pastikan mode adalah saved
    document.getElementById('addressMode').value = 'saved';
}

// Toggle Metode Pembayaran (Gateway vs Manual)
function togglePaymentMethod() {
    const isGateway = document.getElementById('radioGateway').checked;
    const sectionManual = document.getElementById('sectionManual');
    const inputBukti = document.getElementById('inputBukti');
    const btnSubmit = document.getElementById('btnSubmitOrder');

    if (isGateway) {
        sectionManual.style.display = 'none';
        inputBukti.removeAttribute('required');
        btnSubmit.innerHTML = 'Lanjut ke Pembayaran QRIS <i class="bi bi-arrow-right ms-1"></i>';
        btnSubmit.className = 'btn btn-warning w-100 py-3 rounded-pill fw-bold text-dark shadow fs-6';
    } else {
        sectionManual.style.display = 'block';
        inputBukti.setAttribute('required', 'required');
        btnSubmit.innerHTML = 'Kirim Pesanan & Bukti Transfer <i class="bi bi-arrow-right ms-1"></i>';
        btnSubmit.className = 'btn btn-primary w-100 py-3 rounded-pill fw-bold text-white shadow fs-6';
    }
}

// Simulasi Kupon Promo Sesuai Gambar
function applyPromoCode() {
    const code = document.getElementById('inputPromoCode').value.trim().toUpperCase();
    const feedback = document.getElementById('promoFeedback');
    const rowDisc = document.getElementById('rowPromoDiscount');
    const valDisc = document.getElementById('valPromoDiscount');
    const displayTotal = document.getElementById('displayTotalBayar');
    const originalTotal = <?= $grandTotal ?>;

    if (!code) {
        feedback.style.display = 'block';
        feedback.className = 'small mt-1 text-danger';
        feedback.innerHTML = '<i class="bi bi-x-circle me-1"></i> Masukkan kode promo terlebih dahulu.';
        return;
    }

    if (code === 'EKSAVINDO2026' || code === 'HEMATLISTRIK' || code === 'GOPAYQRIS') {
        const potongan = 25000;
        const totalAfter = Math.max(0, originalTotal - potongan);
        feedback.style.display = 'block';
        feedback.className = 'small mt-1 text-success fw-semibold';
        feedback.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Kupon ' + code + ' aktif! Hemat Rp 25.000';
        rowDisc.style.display = 'flex';
        valDisc.innerText = '- Rp 25.000';
        displayTotal.innerText = 'Rp ' + totalAfter.toLocaleString('id-ID');
    } else {
        feedback.style.display = 'block';
        feedback.className = 'small mt-1 text-danger';
        feedback.innerHTML = '<i class="bi bi-x-circle me-1"></i> Kode promo "' + code + '" tidak valid atau telah kedaluwarsa.';
        rowDisc.style.display = 'none';
        displayTotal.innerText = 'Rp ' + originalTotal.toLocaleString('id-ID');
    }
}

// Validasi Form sebelum submit
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const mode = document.getElementById('addressMode').value;
    if (mode === 'new') {
        const penerima = document.getElementById('inputPenerima').value.trim();
        const telp = document.getElementById('inputTelepon').value.trim();
        const alamat = document.getElementById('inputAlamatLengkap').value.trim();

        if (!penerima || !telp || !alamat) {
            e.preventDefault();
            alert('Mohon lengkapi Nama Penerima, Nomor Telepon/WhatsApp, dan Alamat Lengkap Pemasangan!');
            document.getElementById('inputAlamatLengkap').focus();
            return false;
        }
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
