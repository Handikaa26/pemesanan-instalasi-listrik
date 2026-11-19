<?php
// config/app.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'PT. EKSAVINDO MITRA MANDIRI');
define('APP_TAGLINE', 'Layanan Pemesanan Instalasi Listrik Profesional & Terstandar');

function base_url($path = '') {
    // Dynamic base url detection
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    $base = rtrim($protocol . $host . $script, '/\\');
    return $base . '/' . ltrim($path, '/');
}

function rupiah($nominal) {
    return 'Rp ' . number_format($nominal, 0, ',', '.');
}

function get_auth_user() {
    return $_SESSION['user'] ?? null;
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function is_admin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 1;
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: index.php?page=login&msg=harus_login");
        exit;
    }
}

function require_admin() {
    if (!is_admin()) {
        header("Location: index.php?page=login&msg=akses_ditolak");
        exit;
    }
}

function generate_id($prefix, $table, $column) {
    $db = Database::connect();
    $stmt = $db->query("SELECT $column FROM `$table` ORDER BY $column DESC LIMIT 1");
    $last = $stmt->fetchColumn();
    if (!$last) {
        return $prefix . '00001';
    }
    $num = intval(substr($last, strlen($prefix)));
    $num++;
    return $prefix . str_pad($num, 5, '0', STR_PAD_LEFT);
}

function generate_order_id() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
}

function generate_qris_payload($amount) {
    // Template Midtrans GoPay QRIS Standar Pembayaran Nasional
    $prefix = "00020101021226620014COM.GO-JEK.WWW011993600914393526165350210M9352616530303UKE51440014ID.CO.QRIS.WWW0215AID6465362943570303UKE520435835303360";
    
    // Tag 54: Transaction Amount (Dynamic sesuai nominal tagihan yang dipesan)
    $amountStr = (string)intval($amount);
    $amountLen = str_pad(strlen($amountStr), 2, '0', STR_PAD_LEFT);
    $tag54 = "54" . $amountLen . $amountStr;

    // Data Merchant: uji coba instalasi listri, KARANGANYAR, Postcode, Midtrans Ref
    $suffix = "5802ID5925uji coba instalasi listri6011KARANGANYAR61055771262395028A1202611130201361ouzVqkbTgID0703A016304";

    $payloadWithoutCrc = $prefix . $tag54 . $suffix;

    // Hitung Checksum CRC16-CCITT Standar EMVCo (Polinom 0x1021, Init 0xFFFF)
    $crc = 0xFFFF;
    $len = strlen($payloadWithoutCrc);
    for ($i = 0; $i < $len; $i++) {
        $crc ^= (ord($payloadWithoutCrc[$i]) << 8);
        for ($j = 0; $j < 8; $j++) {
            if ($crc & 0x8000) {
                $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
            } else {
                $crc = ($crc << 1) & 0xFFFF;
            }
        }
    }

    $crcHex = strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    return $payloadWithoutCrc . $crcHex;
}

