<?php
// migrate_gateway.php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=pemesanan_instalasi_listrik", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 1. Alter pesanan table to add metode_bayar and snap_token if not exists
    $columns = $pdo->query("SHOW COLUMNS FROM pesanan")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('metode_bayar', $columns)) {
        $pdo->exec("ALTER TABLE pesanan ADD COLUMN `metode_bayar` ENUM('manual_transfer', 'payment_gateway') NOT NULL DEFAULT 'payment_gateway' AFTER `status_keberangkatan`");
        echo "Kolom metode_bayar berhasil ditambahkan ke tabel pesanan.\n";
    }
    if (!in_array('snap_token', $columns)) {
        $pdo->exec("ALTER TABLE pesanan ADD COLUMN `snap_token` VARCHAR(255) DEFAULT NULL AFTER `status_pemesanan`");
        echo "Kolom snap_token berhasil ditambahkan ke tabel pesanan.\n";
    }

    // 2. Create detail_pesanan table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `detail_pesanan` (
        `id_detail_pesanan` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `id_pesanan` VARCHAR(50) NOT NULL,
        `id_produk` INT NOT NULL,
        `ukuran_produk` VARCHAR(50) DEFAULT NULL,
        `jml_beli` INT NOT NULL DEFAULT 1,
        `harga_satuan` INT NOT NULL,
        `subtotal` INT NOT NULL,
        CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
        CONSTRAINT `fk_detail_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Tabel detail_pesanan berhasil dibuat.\n";

    // 3. Create transaksi_gateway table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `transaksi_gateway` (
        `id_transaksi_gateway` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `id_pesanan` VARCHAR(50) NOT NULL,
        `order_id` VARCHAR(64) NOT NULL UNIQUE,
        `gross_amount` INT NOT NULL,
        `snap_token` VARCHAR(255) DEFAULT NULL,
        `payment_type` VARCHAR(50) DEFAULT NULL,
        `transaction_status` VARCHAR(50) NOT NULL DEFAULT 'pending',
        `fraud_status` VARCHAR(50) DEFAULT NULL,
        `settlement_time` DATETIME DEFAULT NULL,
        `raw_response` JSON DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT `fk_gateway_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Tabel transaksi_gateway berhasil dibuat.\n";

    echo "MIGRASI DATABASE PAYMENT GATEWAY BERHASIL!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

