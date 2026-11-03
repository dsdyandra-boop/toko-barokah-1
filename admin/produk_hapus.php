<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$query_p = mysqli_query($conn, "SELECT gambar FROM products WHERE id_produk = '$id'");
$produk  = mysqli_fetch_assoc($query_p);

if ($produk) {
    if ($produk['gambar'] && $produk['gambar'] !== 'default.jpg' && file_exists("../assets/img/" . $produk['gambar'])) {
        unlink("../assets/img/" . $produk['gambar']);
    }
    mysqli_query($conn, "DELETE FROM products WHERE id_produk = '$id'");
}

header("Location: produk.php?pesan=Produk berhasil dihapus!");
exit;