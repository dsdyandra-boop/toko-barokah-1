<?php
session_start();
require_once 'config/database.php';

// Wajib login untuk akses
if (!isset($_SESSION['login'])) {
    header("Location: login.php?pesan=Silakan login untuk melihat riwayat pesanan!");
    exit;
}

$user_id = $_SESSION['user_id'];
$query   = "SELECT * FROM orders WHERE id_user = '$user_id' ORDER BY id_order DESC";
$result  = mysqli_query($conn, $query);

include 'includes/header.php';
?>

<h2 class="section-title" style="margin-top: 30px;">Riwayat Pesanan Saya</h2>

<div style="margin-top: 20px;">
    <?php if (mysqli_num_rows($result) > 0) : ?>
        <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <thead>
                <tr style="background: #0056b3; color: white; text-align: left;">
                    <th style="padding: 12px 15px;">ID Transaksi</th>
                    <th style="padding: 12px 15px;">Total Bayar</th>
                    <th style="padding: 12px 15px;">Status Pesanan</th>
                    <th style="padding: 12px 15px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;"><strong>#TRX-<?php echo $row['id_order']; ?></strong></td>
                        <td style="padding: 15px; font-weight: bold; color: #d32f2f;">Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                        <td style="padding: 15px;">
                            <?php 
                            $st = strtolower($row['status']);
                            $bg = '#fff3cd'; $col = '#856404'; // Pending
                            if ($st === 'dikemas' || $st === 'diproses') { $bg = '#cce5ff'; $col = '#004085'; }
                            if ($st === 'selesai') { $bg = '#d4edda'; $col = '#155724'; }
                            if ($st === 'batal')   { $bg = '#f8d7da'; $col = '#721c24'; }
                            ?>
                            <span style="background: <?php echo $bg; ?>; color: <?php echo $col; ?>; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase;">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="pesanan_saya_detail.php?id=<?php echo $row['id_order']; ?>" class="btn-auth" style="padding: 6px 12px; font-size: 13px; text-decoration: none; display: inline-block; width: auto; background: #17a2b8;">Lihat Rincian</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div style="background: white; padding: 40px; text-align: center; border-radius: 8px;">
            <p style="color: #666; font-size: 16px;">Anda belum pernah membuat pesanan.</p>
            <a href="index.php" class="btn-auth" style="display: inline-block; width: auto; text-decoration: none; margin-top: 15px;">&larr; Belanja Sekarang</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>