<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Opsional: Set NULL / hapus referensi id_kategori pada produk terkait agar tidak orphan
    mysqli_query($conn, "UPDATE products SET id_kategori = NULL WHERE id_kategori = '$id'");
    
    // Hapus data kategori
    mysqli_query($conn, "DELETE FROM categories WHERE id_kategori = '$id'");
}

header("Location: kategori.php?pesan=Kategori berhasil dihapus!");
exit;