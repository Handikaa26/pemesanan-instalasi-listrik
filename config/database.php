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

    private static function getCaCertPath() {
        // Cek path yang sudah ada
        $candidates = [
            getenv('MYSQL_ATTR_SSL_CA'),
            __DIR__ . '/cacert.pem',
            __DIR__ . '/../cacert.pem',
            dirname(__DIR__) . '/cacert.pem',
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/etc/ssl/cert.pem'
        ];

        foreach ($candidates as $path) {
            if (!empty($path) && file_exists($path) && filesize($path) > 0) {
                return $path;
            }
        }

        // Jika di serverless / Vercel tidak ada file fisik, buat di direktori temp
        $tempCa = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tidb_ca_bundle.pem';
        if (!file_exists($tempCa) || filesize($tempCa) == 0) {
            // Sertifikat ISRG Root X1 (Let's Encrypt CA resmi yang memvalidasi TiDB Cloud)
            $isrgCa = "-----BEGIN CERTIFICATE-----\n" .
            "MIIFazCCA1OgAwIBAgIRAIIQz7DSQONZRGPgu2OCiwAwDQYJKoZIhvcNAQELBQAw\n" .
            "TzELMAkGA1UEBhMCVVMxKTAnBgNVBAoTIEludGVybmV0IFNlY3VyaXR5IFJlc2Vh\n" .
            "cmNoIEdyb3VwMRUwEwYDVQQDEwxJU1JHIFJvb3QgWDEwHhcNMTUwNjA0MTEwNDM4\n" .
            "WhcNMzUwNjA0MTEwNDM4WjBPMQswCQYDVQQGEwJVUzEpMCcGA1UEChMgSW50ZXJu\n" .
            "ZXQgU2VjdXJpdHkgUmVzZWFyY2ggR3JvdXAxFTATBgNVBAMTDElTUkcgUm9vdCBY\n" .
            "MTCCAiIwDQYJKoZIhvcNAQEBBQADggIPADCCAgoCggIBAK3oQwgKhj5745KU7yvv\n" .
            "YD4NrapNwiNT46NZD3OJnI7Hs/+8b4IECJ005mIBi6TEZBXqV+HFLWDssDOINMCh\n" .
            "B30/4xpT3fVcFzMrF1gNXVGCa+uDzVgPTWg2amviXYY36WyFaDi4DYflTXFexmvT\n" .
            "Ewur3OSrh8rPdUhiwhqDOARxF3z9bfOX26960vffzPSCoekWU0erCW5N48XGQdW4\n" .
            "LDKGKGSAyAzOYOd451xMDqU5728PfTXUqr28G5nYgDcYWkmqNXDAFndPRxdq2N80\n" .
            "XyoBlPBnMWE29d/C1+2auX8sPeU44lx29qg5mwUMWo+wtvXPnGWZvTt4vtUdfG9Y\n" .
            "qiAX459ZB7J7jvvgu2LFgsAJoxnWJjwMTklaebVNHT8NTRx226GS775cV17JCos5\n" .
            "17uNWBqP5KnCgZ4uL+fYtBQVCWQ305HQmbLnyZK18G8WReYcAO8l7Z2mXYnzyueb\n" .
            "M107mznZg18hkAFITFEfCQqWBZ8mxJWKUhMroamp26Y2g6QRcwvnZi46BS473SBL\n" .
            "clXTVD3bVC82nV5uuw59QZxZ3bN48BZ+st0Alg99484QxB9M/59Jxo532CQxDSDs\n" .
            "DYJPtiFi6PI+HXM58c5VYJptKqIlVNq6/VhPczS/qnhodYLKA3yERABrSingleQ3\n" .
            "FrWgx5M9ihhk/GBIC5nnbDFELAgMBAAGjQjBAMA4GA1UdDwEB/wQEAwIBBjAPBgNV\n" .
            "HRMBAf8EBTADAQH/MB0GA1UdDgQWBBR5tFnme7bl5AFzgAjIy1Fs06cxmaANBgkq\n" .
            "hkiG9w0BAQsFAAOCAgEAVR9YqbyhurdtUSUxwqQgJhJvhRS2LJaocSGXV8MTrKJu\n" .
            "MBryt9qnIzFyIsh4BhGtOhswUxXJZRpxdu82pSJUra84304959BlhkZ4wysZzGZh\n" .
            "COqzpTeqDWOjVW75kyxnVUTfoE56N9YNUMVT6180bKEPd69gQDkao6LePVtwM46Do\n" .
            "x2v+akSYgkTLVCWb01ymW2hsFVFI4U4HBbbqqZOGbcF3HThOMp36DLWRGo21AuKN\n" .
            "YAczJPIN/+5P0mbPe5N0I5X2R59UJaCQVN+CQ8CsLdSQS+2Ze05WEGTgqEmbIOgl\n" .
            "yAevMTMO1vpTLJKvtHRRmp5m808ASGsvMW420b1Mt50G95701ZdbBl13bhfZmGA6\n" .
            "ukG99TrrCdqzvMWDssFNt262s51FV5OTJUitTF3OgmMT9ym7Exey5x0oXYU22oka\n" .
            "Iwu4zncCiesv1ba2rPNZWBKTv4v92U2/HRCxAWiJB9JMoORWZLpvLZGURgZ5EKNI\n" .
            "VD39HRv7+b63SgplaZ5Uw2bWDqVml6YbGqbcShZudnIN4gItdfGl508nHUzs1DcG\n" .
            "kyGwpFJ5bY8VU5KNliU2b03bjPEoZqCH3yPEIGSqbW2WPdXpvBFacbfhOBniA2ZM\n" .
            "9dIhZBIJKaGQQLm8wG8RliUKFc0AOqnGoXHScRRCTcgh59R6gnAY53I0WKE=\n" .
            "-----END CERTIFICATE-----\n";
            @file_put_contents($tempCa, $isrgCa);
        }
        return $tempCa;
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

            // Otomatis aktifkan SSL untuk TiDB Cloud (Port 4000 atau host tidbcloud.com)
            $isTiDB = ($port == '4000' || strpos($host, 'tidbcloud.com') !== false || getenv('DB_SSL') === 'true');

            if ($isTiDB) {
                $caPath = self::getCaCertPath();
                $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
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
