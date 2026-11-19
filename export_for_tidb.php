<?php
// export_for_tidb.php
require_once __DIR__ . '/config/database.php';

try {
    $db = Database::connect();
    $dbName = 'pemesanan_instalasi_listrik';

    $output = "-- ==========================================================\n";
    $output .= "-- MIGRATION SCRIPT UNTUK TiDB CLOUD (Serverless / Dedicated)\n";
    $output .= "-- Proyek: Pemesanan Instalasi Listrik PT. Eksavindo Mitra Mandiri\n";
    $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- ==========================================================\n\n";

    $output .= "CREATE DATABASE IF NOT EXISTS `pemesanan_instalasi_listrik`;\n";
    $output .= "USE `pemesanan_instalasi_listrik`;\n\n";
    $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    // Daftar tabel terurut berdasarkan relasi FK
    $orderedTables = [
        'role',
        'user',
        'kategori',
        'produk',
        'diskon',
        'bank',
        'alamat',
        'keranjang',
        'favorit',
        'pesanan',
        'detail_pesanan',
        'transaksi_gateway'
    ];

    // Cek semua tabel di database saat ini
    $allTables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    // Pastikan semua tabel tercakup
    $tablesToExport = array_unique(array_merge($orderedTables, $allTables));

    foreach ($tablesToExport as $table) {
        // Cek apakah tabel ada
        $check = $db->query("SHOW TABLES LIKE '$table'")->fetch();
        if (!$check) continue;

        $output .= "-- ---------------------------------------------------------\n";
        $output .= "-- Struktur Tabel `$table`\n";
        $output .= "-- ---------------------------------------------------------\n";
        $output .= "DROP TABLE IF EXISTS `$table`;\n";

        // DDL Create Table
        $createStmt = $db->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        $createSql = $createStmt['Create Table'];
        $output .= $createSql . ";\n\n";

        // Dump Data
        $rows = $db->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            $output .= "-- Data untuk Tabel `$table` (" . count($rows) . " baris)\n";
            $columns = array_keys($rows[0]);
            $colList = implode("`, `", $columns);

            $chunks = array_chunk($rows, 50);
            foreach ($chunks as $chunk) {
                $output .= "INSERT INTO `$table` (`$colList`) VALUES\n";
                $valRows = [];
                foreach ($chunk as $row) {
                    $vals = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $vals[] = "NULL";
                        } else {
                            $vals[] = $db->quote($val);
                        }
                    }
                    $valRows[] = "(" . implode(", ", $vals) . ")";
                }
                $output .= implode(",\n", $valRows) . ";\n";
            }
            $output .= "\n";
        }
    }

    $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";
    $output .= "-- MIGRATION COMPLETED SUCCESSFULLY --\n";

    $destPath = __DIR__ . '/tidb_migration.sql';
    file_put_contents($destPath, $output);

    echo "SUCCESS: Exported to tidb_migration.sql (" . strlen($output) . " bytes)\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
