<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id  = $_SESSION['user_id'];
$id_order = intval($_GET['id'] ?? 0);

// Proteksi: Pastikan order ini benar milik user yang sedang login
$query_order = "SELECT * FROM orders WHERE id_order = '$id_order' AND id_user = '$user_id'";
$res_order   = mysqli_query($conn, $query_order);
$order       = mysqli_fetch_assoc($res_order);

if (!$order) {
    header("Location: pesanan_saya.php");
    exit;
}

$query_items = "SELECT od.*, p.nama_produk, p.gambar 
                FROM order_details od 
                JOIN products p ON od.id_produk = p.id_produk 
                WHERE od.id_order = '$id_order'";
$res_items   = mysqli_query($conn, $query_items);

include 'includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; margin-bottom: 20px;">
    <h2>Rincian Pesanan #TRX-<?php echo $order['id_order']; ?></h2>
    <a href="pesanan_saya.php" style="text-decoration: none; color: #0056b3; font-weight: bold;">&larr; Kembali ke Riwayat Pesanan</a>
</div>

<div style="display: flex; gap: 20px; flex-wrap: wrap;">
    <!-- Rincian Produk -->
    <div style="flex: 2; min-width: 300px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h3>Barang yang Dibeli</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; text-align: left;">
                    <th style="padding: 10px;">Gambar</th>
                    <th style="padding: 10px;">Produk</th>
                    <th style="padding: 10px;">Harga</th>
                    <th style="padding: 10px;">Qty</th>
                    <th style="padding: 10px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = mysqli_fetch_assoc($res_items)) : ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;">
                            <?php if ($item['gambar'] && file_exists("assets/img/" . $item['gambar'])) : ?>
                                <img src="assets/img/<?php echo htmlspecialchars($item['gambar']); ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                            <?php else : ?>
                                <div style="width: 50px; height: 50px; background: #eee; font-size: 10px; display: flex; align-items: center; justify-content: center;">No Image</div>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 10px;"><strong><?php echo htmlspecialchars($item['nama_produk']); ?></strong></td>
                        <td style="padding: 10px;">Rp <?php echo number_format($item['subtotal'] / $item['qty'], 0, ',', '.'); ?></td>
                        <td style="padding: 10px;"><?php echo $item['qty']; ?> pcs</td>
                        <td style="padding: 10px; font-weight: bold; color: #d32f2f;">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Info Status & Total -->
    <div style="flex: 1; min-width: 250px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); height: fit-content;">
        <h3>Status Pesanan</h3>
        <!-- Info Status & Total -->
    <div style="flex: 1; min-width: 250px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); height: fit-content;">
        <h3>Status Pesanan</h3>
        <div style="margin-top: 15px; margin-bottom: 15px;">
            <?php 
            $st = strtolower($order['status']);
            $bg = '#fff3cd'; $col = '#856404';
            if ($st === 'dikemas' || $st === 'diproses') { $bg = '#cce5ff'; $col = '#004085'; }
            if ($st === 'selesai') { $bg = '#d4edda'; $col = '#155724'; }
            if ($st === 'batal')   { $bg = '#f8d7da'; $col = '#721c24'; }
            ?>
            <span style="background: <?php echo $bg; ?>; color: <?php echo $col; ?>; padding: 6px 12px; border-radius: 4px; font-size: 14px; font-weight: bold; text-transform: uppercase;">
                <?php echo htmlspecialchars($order['status']); ?>
            </span>
        </div>

        <p style="font-size: 14px; color: #555;">Metode Bayar: <strong style="text-transform: uppercase; color: #0056b3;"><?php echo htmlspecialchars($order['metode_pembayaran'] ?? 'cash'); ?></strong></p>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">

        <span style="color: #666; font-size: 14px;">Total Tagihan:</span>
        <div style="font-size: 22px; font-weight: bold; color: #0056b3; margin-top: 5px;">
            Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?>
        </div>
    </div>
        <div style="margin-top: 15px; margin-bottom: 15px;">
            <?php 
            $st = strtolower($order['status']);
            $bg = '#fff3cd'; $col = '#856404';
            if ($st === 'dikemas' || $st === 'diproses') { $bg = '#cce5ff'; $col = '#004085'; }
            if ($st === 'selesai') { $bg = '#d4edda'; $col = '#155724'; }
            if ($st === 'batal')   { $bg = '#f8d7da'; $col = '#721c24'; }
            ?>
            <span style="background: <?php echo $bg; ?>; color: <?php echo $col; ?>; padding: 6px 12px; border-radius: 4px; font-size: 14px; font-weight: bold; text-transform: uppercase;">
                <?php echo htmlspecialchars($order['status']); ?>
            </span>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">

        <span style="color: #666; font-size: 14px;">Total Tagihan:</span>
        <div style="font-size: 22px; font-weight: bold; color: #0056b3; margin-top: 5px;">
            Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>