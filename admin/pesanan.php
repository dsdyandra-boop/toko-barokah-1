<?php
require_once '../config/database.php';
include 'header_admin.php';

$query  = "SELECT o.*, u.nama, u.email 
           FROM orders o 
           JOIN users u ON o.id_user = u.id_user 
           ORDER BY o.id_order DESC";
$result = mysqli_query($conn, $query);
?>

<h2>Kelola Pesanan Customer</h2>

<?php if (isset($_GET['pesan'])) : ?>
    <div class="alert-success" style="margin-top: 10px;">
        <?php echo htmlspecialchars($_GET['pesan']); ?>
    </div>
<?php endif; ?>

<table class="table-admin">
    <thead>
        <tr>
            <th>ID Pesanan</th>
            <th>Nama Customer</th>
            <th>Email</th>
            <th>Total Bayar</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0) : ?>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><strong>#TRX-<?php echo $row['id_order']; ?></strong></td>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><strong style="color: #d32f2f;">Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></strong></td>
                    <td>
                        <?php 
                        $st = strtolower($row['status']);
                        $badge_class = 'badge-pending';
                        if ($st === 'diproses') $badge_class = 'badge-proses';
                        if ($st === 'selesai')  $badge_class = 'badge-selesai';
                        if ($st === 'batal')    $badge_class = 'badge-batal';
                        ?>
                        <span class="badge <?php echo $badge_class; ?>"><?php echo strtoupper($row['status']); ?></span>
                    </td>
                    <td>
                        <a href="pesanan_detail.php?id=<?php echo $row['id_order']; ?>" class="btn-action btn-detail">🔎 Detail & Status</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr>
                <td colspan="6" style="text-align: center;">Belum ada pesanan masuk.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer_admin.php'; ?>