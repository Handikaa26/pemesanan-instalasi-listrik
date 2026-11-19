<?php
// config/database.php
class Database {
    private static $pdo = null;

    private static function loadEnv() {
        $envPath = __DIR__ . '/../.env';
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value, " \t\n\r\0\x0B\"'");
                    if (!isset($_SERVER[$name]) && !isset($_ENV[$name])) {
                        putenv("$name=$value");
                        $_ENV[$name] = $value;
                        $_SERVER[$name] = $value;
                    }
                }
            }
        }
    }

    public static function connect() {
        if (self::$pdo === null) {
            self::loadEnv();

            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $port = getenv('DB_PORT') ?: '3306';
            $db   = getenv('DB_DATABASE') ?: 'pemesanan_instalasi_listrik';
            $user = getenv('DB_USERNAME') ?: 'root';
            $pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            // Konfigurasi SSL untuk TiDB Cloud (Port 4000, host tidbcloud.com, atau DB_SSL=true)
            $isTiDB = ($port == '4000' || strpos($host, 'tidbcloud.com') !== false || getenv('DB_SSL') === 'true');

            if ($isTiDB) {
                $caCandidates = [
                    getenv('MYSQL_ATTR_SSL_CA'),
                    __DIR__ . '/cacert.pem',
                    __DIR__ . '/../cacert.pem',
                    dirname(__DIR__) . '/cacert.pem',
                    '/etc/ssl/certs/ca-certificates.crt', // Vercel / Debian / Ubuntu
                    '/etc/pki/tls/certs/ca-bundle.crt',   // Amazon Linux 2 / CentOS
                    '/etc/ssl/cert.pem'
                ];

                $caFile = null;
                foreach ($caCandidates as $candidate) {
                    if (!empty($candidate) && file_exists($candidate)) {
                        $caFile = $candidate;
                        break;
                    }
                }

                if ($caFile) {
                    $options[PDO::MYSQL_ATTR_SSL_CA] = $caFile;
                } else {
                    // Fallback ke bundled cacert.pem
                    $options[PDO::MYSQL_ATTR_SSL_CA] = __DIR__ . '/cacert.pem';
                }

                // Jangan verifikasi nama hostname jika sertifikat self-signed / cloud gateway
                if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
                    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
                }
            }

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                die("Koneksi database gagal [Host: {$host}:{$port}]: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
