<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();

$columnsToAdd = [
    'tipe_alamat' => "VARCHAR(50) DEFAULT 'Rumah'",
    'kota' => "VARCHAR(100) DEFAULT 'Kota Metro'",
    'provinsi' => "VARCHAR(100) DEFAULT 'Lampung'",
    'kode_pos' => "VARCHAR(20) DEFAULT '34111'",
    'catatan_patokan' => "VARCHAR(255) DEFAULT NULL"
];

$existingCols = [];
$q = $db->query("SHOW COLUMNS FROM alamat");
while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
    $existingCols[] = $r['Field'];
}

foreach ($columnsToAdd as $col => $def) {
    if (!in_array($col, $existingCols)) {
        $db->exec("ALTER TABLE alamat ADD COLUMN $col $def");
        echo "Column $col added.\n";
    } else {
        echo "Column $col already exists.\n";
    }
}

echo "Migration finished successfully.\n";

