<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bersihkan seluruh variabel session
$_SESSION = array();

// Hapus cookie session dari browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session total
session_destroy();

header("Location: index.php?pesan=" . urlencode("Berhasil logout"));
exit;
?>