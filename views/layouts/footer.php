<?php
// views/layouts/footer.php
?>
</main>

<footer class="py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="bg-warning text-dark p-2 rounded-3 d-inline-flex align-items-center justify-content-center">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </span>
                    <h5 class="text-white mb-0 fw-bold"><?= APP_NAME ?></h5>
                </div>
                <p class="text-secondary mb-3">
                    Sistem Informasi Pemesanan Instalasi Listrik Berbasis Web. Memberikan pelayanan instalasi listrik rumah tinggal, ruko, industri, sistem proteksi petir dan perawatan panel listrik secara profesional, aman, dan berstandar nasional PUIL.
                </p>
                <div class="d-flex gap-2 text-warning fs-5">
                    <i class="bi bi-shield-check"></i>
                    <span class="text-white small align-self-center">Teknisi Tersertifikasi & Bergaransi</span>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="text-white fw-bold mb-3">Layanan Populer</h6>
                <ul class="list-unstyled text-secondary small d-flex flex-column gap-2">
                    <li><i class="bi bi-check2 text-warning me-2"></i>Instalasi Rumah Baru / Renovasi</li>
                    <li><i class="bi bi-check2 text-warning me-2"></i>Instalasi Panel 1 Phase & 3 Phase</li>
                    <li><i class="bi bi-check2 text-warning me-2"></i>Grounding & Penangkal Petir</li>
                    <li><i class="bi bi-check2 text-warning me-2"></i>Penambahan Titik Stop Kontak & Saklar</li>
                    <li><i class="bi bi-check2 text-warning me-2"></i>Perbaikan Korsleting Listrik</li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6">
                <h6 class="text-white fw-bold mb-3">Kontak & Operasional</h6>
                <ul class="list-unstyled text-secondary small d-flex flex-column gap-2">
                    <li><i class="bi bi-geo-alt text-warning me-2"></i>Kota Metro, Lampung - Indonesia</li>
                    <li><i class="bi bi-telephone text-warning me-2"></i>(0725) 456-7890 / 0812-3456-7890</li>
                    <li><i class="bi bi-envelope text-warning me-2"></i>info@eksavindo.com</li>
                    <li><i class="bi bi-clock text-warning me-2"></i>Senin - Sabtu: 08.00 - 17.00 WIB</li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-4 opacity-25">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center text-secondary small">
            <div>
                &copy; <?= date('Y') ?> <?= APP_NAME ?>. Tugas Akhir D-III Sistem Informasi - UM Metro.
            </div>
            <div class="mt-2 mt-md-0">
                <span>Penulis: <strong>Afrizal Agung Nugraha</strong> (NPM: 21410014)</span>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

