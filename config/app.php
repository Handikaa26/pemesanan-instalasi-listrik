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

