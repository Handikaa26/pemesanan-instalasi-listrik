<?php
// views/konsumen/katalog.php
$pageTitle = 'Katalog Layanan & Produk Instalasi Listrik';
$db = Database::connect();

$selectedKat = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

$sql = "SELECT p.*, k.nama_kategori, d.besar_diskon 
        FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
        LEFT JOIN diskon d ON p.id_produk = d.id_produk AND d.is_active = 1
        WHERE 1=1";
$params = [];

if ($selectedKat > 0) {
    $sql .= " AND p.id_kategori = ?";
    $params[] = $selectedKat;
}

if (!empty($keyword)) {
    $sql .= " AND (p.nama_produk LIKE ? OR p.deskripsi_produk LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

$sql .= " ORDER BY p.id_produk DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Jika permintaan AJAX, kembalikan hanya list HTML produk
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    if (empty($products)) {
        echo '<div class="col-12"><div class="card card-custom p-5 text-center my-4">
                <i class="bi bi-inbox text-muted display-4 mb-3"></i>
                <h5 class="fw-bold">Layanan Tidak Ditemukan</h5>
                <p class="text-muted small">Coba gunakan kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
              </div></div>';
    } else {
        foreach ($products as $item) {
            require __DIR__ . '/katalog_item.php';
        }
    }
    exit;
}

$categories = $db->query("SELECT * FROM kategori")->fetchAll();
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container py-4">
    <div class="bg-white p-4 rounded-4 shadow-sm mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <h4 class="fw-bold mb-1">Daftar Paket & Layanan</h4>
                <p class="text-muted small mb-0"><span id="productCount"><?= count($products) ?></span> paket instalasi listrik tersedia</p>
            </div>
            <div class="col-md-8">
                <form id="filterForm" onsubmit="event.preventDefault();" class="row g-2">
                    <div class="col-md-5">
                        <select name="kategori" id="filterKategori" class="form-select">
                            <option value="0">Semua Kategori Layanan</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id_kategori'] ?>" <?= ($selectedKat == $cat['id_kategori']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nama_kategori']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search" id="searchIcon"></i>
                                <span class="spinner-border spinner-border-sm text-primary d-none" id="searchSpinner" role="status"></span>
                            </span>
                            <input type="text" name="q" id="searchQuery" class="form-control border-start-0" placeholder="Ketik langsung (AJAX live search)..." value="<?= htmlspecialchars($keyword) ?>" autocomplete="off">
                            <button class="btn btn-outline-secondary d-none" type="button" id="btnResetSearch" title="Hapus"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Container Grid Produk (Di-update oleh AJAX) -->
    <div class="row g-4" id="produkContainer">
        <?php if (empty($products)): ?>
        <div class="col-12">
            <div class="card card-custom p-5 text-center my-4">
                <i class="bi bi-inbox text-muted display-4 mb-3"></i>
                <h5 class="fw-bold">Layanan Tidak Ditemukan</h5>
                <p class="text-muted small">Coba gunakan kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
                <div>
                    <a href="index.php?page=katalog" class="btn btn-outline-primary rounded-pill px-4">Lihat Semua Layanan</a>
                </div>
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($products as $item): ?>
                <?php require __DIR__ . '/katalog_item.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchQuery');
    const kategoriSelect = document.getElementById('filterKategori');
    const produkContainer = document.getElementById('produkContainer');
    const searchSpinner = document.getElementById('searchSpinner');
    const searchIcon = document.getElementById('searchIcon');
    const btnReset = document.getElementById('btnResetSearch');
    const productCount = document.getElementById('productCount');
    let debounceTimer;

    function fetchProdukAjax() {
        const query = searchInput.value.trim();
        const kategori = kategoriSelect.value;

        // Tampilkan/sembunyikan tombol reset
        if (query.length > 0) {
            btnReset.classList.remove('d-none');
        } else {
            btnReset.classList.add('d-none');
        }

        // Tampilkan loading spinner
        searchIcon.classList.add('d-none');
        searchSpinner.classList.remove('d-none');

        const params = new URLSearchParams({
            page: 'katalog',
            ajax: '1',
            q: query,
            kategori: kategori
        });

        fetch(`index.php?${params.toString()}`)
            .then(res => res.text())
            .then(html => {
                produkContainer.innerHTML = html;
                // Update counter produk
                const count = produkContainer.querySelectorAll('.product-card-col').length;
                if (productCount) {
                    productCount.textContent = count;
                }
            })
            .catch(err => {
                console.error("AJAX Search Error:", err);
            })
            .finally(() => {
                searchSpinner.classList.add('d-none');
                searchIcon.classList.remove('d-none');
            });
    }

    // Event input dengan debounce 250ms (responsif & hemat request)
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchProdukAjax, 250);
    });

    kategoriSelect.addEventListener('change', fetchProdukAjax);

    btnReset.addEventListener('click', function() {
        searchInput.value = '';
        btnReset.classList.add('d-none');
        fetchProdukAjax();
        searchInput.focus();
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
