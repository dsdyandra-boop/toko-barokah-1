<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

// Wajib login untuk akses
if (!isset($_SESSION['user']) || !is_array($_SESSION['user']) || empty($_SESSION['user']['id_user'])) {
    header("Location: login.php?error=" . urlencode("Silakan login untuk melihat riwayat pesanan!"));
    exit;
}

$user_id = (int)$_SESSION['user']['id_user'];
$query   = "SELECT * FROM orders WHERE id_user = '$user_id' ORDER BY id_order DESC";
$result  = mysqli_query($conn, $query);

include 'includes/header.php';
?>

<div style="max-width: 1000px; margin: 0 auto; padding: 28px 20px 50px;">
    <h2 style="font-size: 24px; color: #132a21; margin-bottom: 5px;">📦 Riwayat Pesanan Saya</h2>
    <p style="color: #536b60; font-size: 14px; margin-bottom: 24px;">Pantau status dan rincian pesanan kamu di Toko Barokah 1.</p>

    <?php if ($result && mysqli_num_rows($result) > 0) : ?>
        <div style="background: #ffffff; border: 1px solid #e2ece7; border-radius: 16px; overflow-x: auto; box-shadow: 0 4px 14px rgba(19, 42, 33, 0.04);">
            <table style="width: 100%; border-collapse: collapse; min-width: 560px;">
                <thead>
                    <tr style="background: #eaf6f0; color: #132a21; text-align: left; border-bottom: 1px solid #d3ede1;">
                        <th style="padding: 14px 16px; font-size: 13px;">ID Transaksi</th>
                        <th style="padding: 14px 16px; font-size: 13px;">Metode</th>
                        <th style="padding: 14px 16px; font-size: 13px;">Total Bayar</th>
                        <th style="padding: 14px 16px; font-size: 13px;">Status Pesanan</th>
                        <th style="padding: 14px 16px; font-size: 13px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) : 
                        $total_tampil = $row['total_bayar'] ?? $row['total_harga'] ?? 0;
                        $st = strtolower($row['status'] ?? 'pending');
                        $bg = '#fef3c7'; $col = '#92400e';
                        if ($st === 'dikemas' || $st === 'proses' || $st === 'diproses') { $bg = '#dbeafe'; $col = '#1e40af'; }
                        if ($st === 'selesai') { $bg = '#dcfce7'; $col = '#166534'; }
                        if ($st === 'batal')   { $bg = '#fee2e2'; $col = '#991b1b'; }
                    ?>
                        <tr style="border-bottom: 1px solid #edf4f0;">
                            <td style="padding: 15px 16px;"><strong>#ORD-<?php echo (int)$row['id_order']; ?></strong></td>
                            <td style="padding: 15px 16px; text-transform: uppercase; font-size: 13px; color: #536b60; font-weight: 600;">
                                <?php echo htmlspecialchars($row['metode_pembayaran'] ?? 'COD'); ?>
                            </td>
                            <td style="padding: 15px 16px; font-weight: 800; color: #156145;">
                                Rp <?php echo number_format($total_tampil, 0, ',', '.'); ?>
                            </td>
                            <td style="padding: 15px 16px;">
                                <span style="background: <?php echo $bg; ?>; color: <?php echo $col; ?>; padding: 4px 11px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                                    <?php echo htmlspecialchars($row['status'] ?? 'pending'); ?>
                                </span>
                            </td>
                            <td style="padding: 15px 16px; text-align: center;">
                                <a href="pesanan_saya_detail.php?id=<?php echo (int)$row['id_order']; ?>" class="btn-press" style="padding: 7px 14px; font-size: 13px; text-decoration: none; display: inline-block; background: #eaf6f0; color: #156145; border: 1px solid #cde4d9; border-radius: 9px; font-weight: 700;">Lihat Rincian</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <div style="background: #ffffff; border: 1px solid #e2ece7; padding: 48px 24px; text-align: center; border-radius: 16px;">
            <div style="font-size: 46px; margin-bottom: 10px;">📦</div>
            <p style="color: #536b60; font-size: 15px; margin-bottom: 18px;">Kamu belum pernah membuat pesanan.</p>
            <a href="index.php" class="btn-lift" style="display: inline-block; padding: 11px 22px; background: #156145; color: #ffffff; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 14px;">&larr; Belanja Sekarang</a>
        </div>
    <?php endif; ?>
</div>

<?php 
include 'includes/footer.php'; 
ob_end_flush();
?>