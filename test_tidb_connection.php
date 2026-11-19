<?php
// test_tidb_connection.php
require_once __DIR__ . '/config/database.php';

echo "=== PENGUJIAN KONEKSI DATABASE ===\n";
try {
    $db = Database::connect();
    echo "[OK] Berhasil terhubung ke database!\n";

    $version = $db->query("SELECT VERSION()")->fetchColumn();
    echo "[INFO] Versi Database Engine: " . $version . "\n";

    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "[INFO] Jumlah tabel terdeteksi: " . count($tables) . "\n";
    foreach ($tables as $t) {
        $count = $db->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        echo "   - $t ($count baris)\n";
    }

    echo "\n=== STATUS: SIAP DIGUNAKAN ===\n";
} catch (Exception $e) {
    echo "[GAGAL] Error koneksi: " . $e->getMessage() . "\n";
}
