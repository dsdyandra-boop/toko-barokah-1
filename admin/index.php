<?php
require_once '../config/database.php';
include 'header_admin.php';

$total_produk   = mysqli_num_rows(mysqli_query($conn, "SELECT id_produk FROM products"));
$total_kategori = mysqli_num_rows(mysqli_query($conn, "SELECT id_kategori FROM categories"));
$total_users    = mysqli_num_rows(mysqli_query($conn, "SELECT id_user FROM users WHERE role = 'customer'"));
?>

<h2>Dashboard Admin</h2>
<p style="margin-bottom: 20px;">Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong>!</p>

<div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px;">
    <div style="flex: 1; min-width: 200px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-left: 5px solid #0056b3;">
        <h3 style="color: #666; font-size: 14px;">Total Produk</h3>
        <p style="font-size: 28px; font-weight: bold; color: #0056b3; margin-top: 5px;"><?php echo $total_produk; ?></p>
        <a href="produk.php" style="color: #0056b3; font-size: 13px; text-decoration: none; font-weight: bold;">Kelola Produk &rarr;</a>
    </div>
    <div style="flex: 1; min-width: 200px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-left: 5px solid #ffc107;">
        <h3 style="color: #666; font-size: 14px;">Total Kategori</h3>
        <p style="font-size: 28px; font-weight: bold; color: #d99b00; margin-top: 5px;"><?php echo $total_kategori; ?></p>
    </div>
    <div style="flex: 1; min-width: 200px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-left: 5px solid #28a745;">
        <h3 style="color: #666; font-size: 14px;">Total Customer</h3>
        <p style="font-size: 28px; font-weight: bold; color: #28a745; margin-top: 5px;"><?php echo $total_users; ?></p>
    </div>
</div>

<?php include 'footer_admin.php'; ?>