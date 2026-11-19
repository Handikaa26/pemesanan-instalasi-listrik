<?php
// run_tidb_migration_auto.php

$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = 4000;
$user = '2UcDyhDLXTAhFB5.root';
$pass = 'sN8A0EdoIePnE5Ib';
$targetDb = 'pemesanan_instalasi_listrik';

echo "1. Menghubungkan ke TiDB Cloud ({$host}:{$port}) dengan SSL...\n";

$caPath = __DIR__ . '/cacert.pem';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_SSL_CA       => $caPath,
];

try {
    // 1. Hubungkan ke server TiDB Cloud
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "[OK] Berhasil terhubung ke TiDB Cloud via SSL Enkripsi!\n";

    // 2. Buat database jika belum ada
    echo "2. Membuat database `{$targetDb}` di TiDB Cloud...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$targetDb}`;");
    $pdo->exec("USE `{$targetDb}`;");
    echo "[OK] Database `{$targetDb}` berhasil dibuat dan aktif!\n";

    // 3. Matikan FK Check sementara
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // 4. Eksekusi tabel dan data dari tidb_migration.sql
    echo "3. Mengeksekusi tabel dan data migrasi ke TiDB Cloud...\n";
    $sqlFile = __DIR__ . '/tidb_migration.sql';
    $sqlContent = file_get_contents($sqlFile);

    // Jalankan per statement SQL
    $pdo->exec($sqlContent);
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    echo "[OK] Seluruh DDL dan Data berhasil diunggah ke TiDB Cloud!\n";

    // 5. Verifikasi semua tabel
    echo "\n=== VERIFIKASI TABEL DI TIDB CLOUD ===\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Total tabel berhasil dimigrasi: " . count($tables) . "\n";
    foreach ($tables as $t) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        echo "   - Tabel `$t`: $count baris data\n";
    }

    echo "\n[SUKSES] MIGRASI OTOMATIS KE TIDB CLOUD SELESAI 100%!\n";

} catch (PDOException $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}
