<?php
// auto_update_orders.php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=pemesanan_instalasi_listrik", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    $besokTime = strtotime('+1 day');
    $namaHari = $hariIndo[date('w', $besokTime)];
    $tgl = date('j', $besokTime);
    $namaBulan = $bulanIndo[(int)date('n', $besokTime)];
    $tahun = date('Y', $besokTime);
    $jadwalOtomatis = "$namaHari, $tgl $namaBulan $tahun - Pukul 09.00 WIB";
    $timTeknisiOtomatis = "Tim Armada 1 (Teknisi Tersertifikasi PT. Eksavindo)";

    // Update semua pesanan yang sudah lunas tetapi jadwalnya masih kosong
    $stmt = $pdo->prepare("UPDATE pesanan SET 
        jadwal_pemasangan = ?,
        kurir = ?,
        status_keberangkatan = 1,
        status_pemesanan = 1
        WHERE status_pembayaran = 1 AND (jadwal_pemasangan IS NULL OR jadwal_pemasangan = '')");
    $stmt->execute([$jadwalOtomatis, $timTeknisiOtomatis]);

    echo "Update otomatis berhasil! Baris terpengaruh: " . $stmt->rowCount() . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

