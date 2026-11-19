<?php
// controllers/AuthController.php
class AuthController {
    public static function showLogin() {
        if (is_logged_in()) {
            header("Location: index.php");
            exit;
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public static function processLogin() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = "Email dan kata sandi wajib diisi.";
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM `user` WHERE `email` = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active'] != 1) {
                $error = "Akun Anda dinonaktifkan oleh administrator.";
                require_once __DIR__ . '/../views/auth/login.php';
                return;
            }

            $_SESSION['user'] = [
                'id_user'   => $user['id_user'],
                'username'  => $user['username'],
                'email'     => $user['email'],
                'role_id'   => $user['role_id'],
                'nomor_telp'=> $user['nomor_telp']
            ];

            if ($user['role_id'] == 1) {
                header("Location: index.php?page=admin_dashboard");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Email atau kata sandi tidak cocok.";
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }

    public static function showRegister() {
        if (is_logged_in()) {
            header("Location: index.php");
            exit;
        }
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public static function processRegister() {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $nomor_telp = trim($_POST['nomor_telp'] ?? '');
        $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null;
        $alamat = trim($_POST['alamat'] ?? '');

        if (empty($username) || empty($email) || empty($password) || empty($nomor_telp) || empty($alamat)) {
            $error = "Semua bidang bertanda bintang (*) wajib diisi.";
            require_once __DIR__ . '/../views/auth/register.php';
            return;
        }

        $db = Database::connect();

        // Cek duplikasi email
        $stmt = $db->prepare("SELECT COUNT(*) FROM `user` WHERE `email` = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Email sudah terdaftar. Gunakan email lain.";
            require_once __DIR__ . '/../views/auth/register.php';
            return;
        }

        $id_user = generate_id('USR', 'user', 'id_user');
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO `user` (`id_user`, `username`, `email`, `password`, `nomor_telp`, `tanggal_lahir`, `foto_profil`, `role_id`, `is_active`) VALUES (?, ?, ?, ?, ?, ?, 'default.png', 2, 1)");
            $stmt->execute([$id_user, $username, $email, $hashPassword, $nomor_telp, $tanggal_lahir]);

            // Tambahkan alamat awal
            $stmtA = $db->prepare("INSERT INTO `alamat` (`alamat`, `penerima`, `telepon_penerima`, `id_user`) VALUES (?, ?, ?, ?)");
            $stmtA->execute([$alamat, $username, $nomor_telp, $id_user]);

            $db->commit();
            header("Location: index.php?page=login&msg=registered");
            exit;
        } catch (Exception $e) {
            $db->rollBack();
            $error = "Gagal mendaftar: " . $e->getMessage();
            require_once __DIR__ . '/../views/auth/register.php';
        }
    }

    public static function logout() {
        $_SESSION = [];
        if (session_id() != "" || isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 2592000, '/');
        }
        session_destroy();
        header("Location: index.php?page=login&msg=logout");
        exit;
    }
}

