<?php
ob_start();
include 'header_admin.php';
require_once '../config/database.php';

// 1. HITUNG STATISTIK UTAMA
// Total Produk
$q_produk = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
$total_produk = mysqli_fetch_assoc($q_produk)['total'] ?? 0;

// Total Kategori
$q_kategori = mysqli_query($conn, "SELECT COUNT(*) as total FROM categories");
$total_kategori = mysqli_fetch_assoc($q_kategori)['total'] ?? 0;

// Produk Stok Menipis (Stok <= 5)
$q_stok_low = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE stok <= 5");
$total_stok_low = mysqli_fetch_assoc($q_stok_low)['total'] ?? 0;

// Ambil Produk Terbaru (5 data)
$query_terbaru = "SELECT p.*, c.nama_kategori 
                  FROM products p 
                  LEFT JOIN categories c ON p.id_kategori = c.id_kategori 
                  ORDER BY p.id_produk DESC LIMIT 5";
$products_terbaru = mysqli_query($conn, $query_terbaru);

// Ambil Produk Stok Menipis
$query_alert_stok = "SELECT * FROM products WHERE stok <= 5 ORDER BY stok ASC LIMIT 5";
$products_alert = mysqli_query($conn, $query_alert_stok);
?>

<div style="margin-bottom: 25px;">
    <h2>Dashboard Overview</h2>
    <p style="color: #666; font-size: 14px; margin-top: 4px;">Ringkasan data dan aktivitas toko online TokoHarian.</p>
</div>

<!-- KARTU STATISTIK -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <!-- Card Total Produk -->
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-left: 5px solid #004494; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase;">Total Produk</span>
            <h3 style="font-size: 28px; color: #1a1d2e; margin-top: 5px;"><?php echo number_format($total_produk); ?></h3>
        </div>
        <div style="font-size: 32px; opacity: 0.8;">📦</div>
    </div>

    <!-- Card Total Kategori -->
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-left: 5px solid #17a2b8; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase;">Kategori</span>
            <h3 style="font-size: 28px; color: #1a1d2e; margin-top: 5px;"><?php echo number_format($total_kategori); ?></h3>
        </div>
        <div style="font-size: 32px; opacity: 0.8;">🏷️</div>
    </div>

    <!-- Card Stok Menipis -->
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-left: 5px solid #ffc107; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase;">Stok Menipis</span>
            <h3 style="font-size: 28px; color: #d97706; margin-top: 5px;"><?php echo number_format($total_stok_low); ?></h3>
        </div>
        <div style="font-size: 32px; opacity: 0.8;">⚠️</div>
    </div>
</div>

<!-- SECTION KONTEN DUA KOLOM -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px;">
    <!-- Kolom Kiri: Produk Terbaru -->
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="font-size: 16px; color: #1a1d2e;">🆕 Produk Terbaru</h3>
            <a href="produk.php" style="font-size: 13px; color: #004494; text-decoration: none; font-weight: 600;">Lihat Semua &rarr;</a>
        </div>
        
        <table class="table-admin" style="margin-top: 0;">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($products_terbaru) > 0) : ?>
                    <?php while ($pt = mysqli_fetch_assoc($products_terbaru)) : ?>
                        <tr>
                            <td style="display: flex; align-items: center; gap: 10px;">
                                <img src="../assets/img/<?php echo htmlspecialchars($pt['gambar']); ?>" width="35" height="35" style="object-fit: cover; border-radius: 4px;">
                                <span><?php echo htmlspecialchars($pt['nama_produk']); ?></span>
                            </td>
                            <td>Rp <?php echo number_format($pt['harga'], 0, ',', '.'); ?></td>
                            <td><strong><?php echo $pt['stok']; ?></strong></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #777;">Belum ada produk.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Kolom Kanan: Perhatian Stok Menipis -->
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="font-size: 16px; color: #dc3545;">⚠️ Peringatan Stok (<= 5)</h3>
            <a href="produk.php" style="font-size: 13px; color: #004494; text-decoration: none; font-weight: 600;">Kelola Stok &rarr;</a>
        </div>

        <table class="table-admin" style="margin-top: 0;">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Barcode</th>
                    <th>Sisa Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($products_alert) > 0) : ?>
                    <?php while ($pa = mysqli_fetch_assoc($products_alert)) : ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($pa['nama_produk']); ?></strong></td>
                            <td><code style="background: #f0f4ff; padding: 2px 6px; border-radius: 4px;"><?php echo htmlspecialchars($pa['barcode'] ?? '-'); ?></code></td>
                            <td>
                                <span style="background: #f8d7da; color: #721c24; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">
                                    <?php echo $pa['stok']; ?> unit
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #28a745; font-weight: 500; padding: 15px;">
                            ✅ Semua stok produk dalam kondisi aman!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
include 'footer_admin.php'; 
ob_end_flush();
?>