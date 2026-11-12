<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

if (!isset($_SESSION['user']) || !is_array($_SESSION['user']) || empty($_SESSION['user']['id_user'])) {
    header("Location: login.php");
    exit;
}

$user_id  = (int)$_SESSION['user']['id_user'];
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

$total_tampil = $order['total_bayar'] ?? $order['total_harga'] ?? 0;

include 'includes/header.php';
?>

<div style="max-width: 1000px; margin: 0 auto; padding: 28px 20px 50px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h2 style="font-size: 22px; color: #132a21;">Rincian Pesanan #ORD-<?php echo (int)$order['id_order']; ?></h2>
        <a href="pesanan_saya.php" style="text-decoration: none; color: #156145; font-weight: 700; font-size: 14px;">&larr; Kembali ke Riwayat Pesanan</a>
    </div>

    <div style="display: flex; gap: 22px; flex-wrap: wrap; align-items: start;">
        <!-- Rincian Produk -->
        <div style="flex: 2; min-width: 300px; background: #ffffff; border: 1px solid #e2ece7; padding: 22px; border-radius: 16px; box-shadow: 0 4px 14px rgba(19, 42, 33, 0.04); overflow-x: auto;">
            <h3 style="font-size: 16px; color: #132a21; margin-bottom: 12px;">Barang yang Dibeli</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #ebf3ef; text-align: left; color: #536b60; font-size: 13px;">
                        <th style="padding: 10px;">Produk</th>
                        <th style="padding: 10px;">Harga</th>
                        <th style="padding: 10px;">Qty</th>
                        <th style="padding: 10px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($res_items && mysqli_num_rows($res_items) > 0) : ?>
                        <?php while ($item = mysqli_fetch_assoc($res_items)) : 
                            $harga_satuan = ($item['qty'] > 0) ? ($item['subtotal'] / $item['qty']) : 0;
                        ?>
                            <tr style="border-bottom: 1px solid #f1f6f4;">
                                <td style="padding: 12px 10px; display: flex; align-items: center; gap: 12px;">
                                    <?php if (!empty($item['gambar']) && file_exists("assets/img/" . $item['gambar'])) : ?>
                                        <img src="assets/img/<?php echo htmlspecialchars($item['gambar']); ?>" width="50" height="50" style="object-fit: cover; border-radius: 8px; border: 1px solid #e2ece7;">
                                    <?php else : ?>
                                        <div style="width: 50px; height: 50px; background: #f1f6f4; border-radius: 8px; font-size: 18px; display: flex; align-items: center; justify-content: center;">🛍️</div>
                                    <?php endif; ?>
                                    <strong style="color: #132a21; font-size: 14px;"><?php echo htmlspecialchars($item['nama_produk']); ?></strong>
                                </td>
                                <td style="padding: 12px 10px; font-size: 13px;">Rp <?php echo number_format($harga_satuan, 0, ',', '.'); ?></td>
                                <td style="padding: 12px 10px; font-size: 13px;"><?php echo (int)$item['qty']; ?> pcs</td>
                                <td style="padding: 12px 10px; font-weight: 700; color: #156145; font-size: 14px;">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" style="padding: 16px; text-align: center; color: #536b60; font-size: 13px;">Rincian item tidak tersedia untuk pesanan lama ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Info Status & Total (Sudah dibersihkan dari duplikasi) -->
        <div style="flex: 1; min-width: 260px; background: #ffffff; border: 1px solid #e2ece7; padding: 22px; border-radius: 16px; box-shadow: 0 4px 14px rgba(19, 42, 33, 0.04);">
            <h3 style="font-size: 16px; color: #132a21; margin-bottom: 14px;">Status Pesanan</h3>
            <div style="margin-bottom: 16px;">
                <?php 
                $st = strtolower($order['status'] ?? 'pending');
                $bg = '#fef3c7'; $col = '#92400e';
                if ($st === 'dikemas' || $st === 'proses' || $st === 'diproses') { $bg = '#dbeafe'; $col = '#1e40af'; }
                if ($st === 'selesai') { $bg = '#dcfce7'; $col = '#166534'; }
                if ($st === 'batal')   { $bg = '#fee2e2'; $col = '#991b1b'; }
                ?>
                <span style="background: <?php echo $bg; ?>; color: <?php echo $col; ?>; padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                    <?php echo htmlspecialchars($order['status'] ?? 'pending'); ?>
                </span>
            </div>

            <p style="font-size: 14px; color: #536b60; margin-bottom: 8px;">
                Metode Bayar: <strong style="text-transform: uppercase; color: #132a21;"><?php echo htmlspecialchars($order['metode_pembayaran'] ?? 'COD'); ?></strong>
            </p>

            <?php if (!empty($order['alamat'])) : ?>
                <p style="font-size: 13px; color: #536b60; line-height: 1.5;">
                    Alamat: <span style="color: #132a21;"><?php echo htmlspecialchars($order['alamat']); ?></span>
                </p>
            <?php endif; ?>

            <hr style="border: 0; border-top: 1px solid #ebf3ef; margin: 16px 0;">

            <span style="color: #536b60; font-size: 13px;">Total Tagihan:</span>
            <div style="font-size: 22px; font-weight: 800; color: #156145; margin-top: 4px;">
                Rp <?php echo number_format($total_tampil, 0, ',', '.'); ?>
            </div>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
ob_end_flush();
?>