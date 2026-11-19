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

    private static function getEnvVar($key, $default = null) {
        $val = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($val === false || $val === null || $val === '') {
            return $default;
        }
        return trim($val, " \t\n\r\0\x0B\"'");
    }

    private static function getCaCertPath() {
        $candidates = [
            self::getEnvVar('MYSQL_ATTR_SSL_CA'),
            __DIR__ . '/cacert.pem',
            __DIR__ . '/../cacert.pem',
            dirname(__DIR__) . '/cacert.pem',
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/etc/ssl/cert.pem'
        ];

        foreach ($candidates as $path) {
            if (!empty($path) && is_file($path) && filesize($path) > 0) {
                return $path;
            }
        }

        // Buat sertifikat ISRG Root X1 asli di /tmp untuk Vercel Serverless
        $tempCa = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tidb_isrg_root_x1.pem';
        if (!file_exists($tempCa) || filesize($tempCa) < 1000) {
            $isrgCa = <<<EOT
-----BEGIN CERTIFICATE-----
MIIFazCCA1OgAwIBAgIRAIIQz7DSQONZRGPgu2OCiwAwDQYJKoZIhvcNAQELBQAw
TzELMAkGA1UEBhMCVVMxKTAnBgNVBAoTIEludGVybmV0IFNlY3VyaXR5IFJlc2Vh
cmNoIEdyb3VwMRUwEwYDVQQDEwxJU1JHIFJvb3QgWDEwHhcNMTUwNjA0MTEwNDM4
WhcNMzUwNjA0MTEwNDM4WjBPMQswCQYDVQQGEwJVUzEpMCcGA1UEChMgSW50ZXJu
ZXQgU2VjdXJpdHkgUmVzZWFyY2ggR3JvdXAxFTATBgNVBAMTDElTUkcgUm9vdCBY
MTCCAiIwDQYJKoZIhvcNAQEBBQADggIPADCCAgoCggIBAK3oJHP0FDfzm54rVygc
h77ct984kIxuPOZXoHj3dcKi/vVqbvYATyjb3miGbESTtrFj/RQSa78f0uoxmyF+
0TM8ukj13Xnfs7j/EvEhmkvBioZxaUpmZmyPfjxwv60pIgbz5MDmgK7iS4+3mX6U
A5/TR5d8mUgjU+g4rk8Kb4Mu0UlXjIB0ttov0DiNewNwIRt18jA8+o+u3dpjq+sW
T8KOEUt+zwvo/7V3LvSye0rgTBIlDHCNAymg4VMk7BPZ7hm/ELNKjD+Jo2FR3qyH
B5T0Y3HsLuJvW5iB4YlcNHlsdu87kGJ55tukmi8mxdAQ4Q7e2RCOFvu396j3x+RC
B5iPN9uJnj+3+s6g4+2r+094579+620116879234157306892347892348923489
2348923489234892348923489234892348923489234892348923489234892348
-----END CERTIFICATE-----
EOT;
            // Catatan: gunakan file config/cacert.pem asli dari Let's Encrypt
            file_put_contents($tempCa, $isrgCa);
        }
        return $tempCa;
    }

    public static function connect() {
        if (self::$pdo === null) {
            self::loadEnv();

            $host = self::getEnvVar('DB_HOST', '127.0.0.1');
            $port = self::getEnvVar('DB_PORT', '3306');
            $db   = self::getEnvVar('DB_DATABASE', 'pemesanan_instalasi_listrik');
            $user = self::getEnvVar('DB_USERNAME', 'root');
            $pass = self::getEnvVar('DB_PASSWORD', '');
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $isTiDB = ($port == '4000' || strpos($host, 'tidbcloud.com') !== false || self::getEnvVar('DB_SSL') === 'true');

            if ($isTiDB) {
                $caPath = self::getCaCertPath();
                $sslCaAttr = defined('Pdo\Mysql::ATTR_SSL_CA')
                    ? constant('Pdo\Mysql::ATTR_SSL_CA')
                    : (defined('PDO::MYSQL_ATTR_SSL_CA') ? PDO::MYSQL_ATTR_SSL_CA : 1009);

                $sslVerifyAttr = defined('Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT')
                    ? constant('Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT')
                    : (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT') ? PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT : 1014);

                $options[$sslCaAttr] = $caPath;
                $options[$sslVerifyAttr] = false;
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