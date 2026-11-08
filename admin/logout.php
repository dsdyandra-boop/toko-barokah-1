<?php
session_start();
session_unset();
session_destroy();

// Redirect ke halaman login admin (atau ke katalog toko jika login admin belum dibuat)
if (file_exists('login.php')) {
    header("Location: login.php?pesan=Anda telah berhasil logout");
} else {
    header("Location: ../index.php");
}
exit;
?>