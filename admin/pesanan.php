<?php
ob_start();
include 'header_admin.php';
require_once '../config/database.php';

$error = '';
$sukses = '';

// 1. UPDATE STATUS PESANAN
if (isset($_POST['update_status'])) {
    $id_order = (int)$_POST['id_order'];
    $status   = mysqli_real_escape_string($conn, trim($_POST['status']));

    $query = "UPDATE orders SET status = '$status' WHERE id_order = '$id_order'";
    if (mysqli_query($conn, $query)) {
        $sukses = 'Status pesanan berhasil diperbarui!';
    } else {
        $error = 'Gagal memperbarui status: ' . mysqli_error($conn);
    }
}

// 2. HAPUS PESANAN
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM orders WHERE id_order = '$id_hapus'");
    header("Location: pesanan.php?pesan=Pesanan berhasil dihapus");
    exit;
}

// Query Ambil Semua Pesanan
$query_pesanan = "SELECT * FROM orders ORDER BY id_order DESC";
$result_pesanan = mysqli_query($conn, $query_pesanan);
?>

<h2>Kelola Pesanan & Transaksi</h2>
<p style="color: #666; font-size: 14px; margin-bottom: 20px;">Pantau pesanan masuk, verifikasi bukti pembayaran QRIS/COD, dan perbarui status transaksi.</p>

<?php if (isset($_GET['pesan'])) : ?>
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 15px;">
        ✅ <?php echo htmlspecialchars($_GET['pesan']); ?>
    </div>
<?php endif; ?>

<?php if ($sukses) : ?>
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 15px;">
        ✅ <?php echo htmlspecialchars($sukses); ?>
    </div>
<?php endif; ?>

<?php if ($error) : ?>
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 15px;">
        ⚠️ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Tabel Pesanan -->
<table class="table-admin">
    <thead>
        <tr>
            <th>ID / Tanggal</th>
            <th>Pemesan</th>
            <th>Total Bayar</th>
            <th>Metode</th>
            <th>Status</th>
            <th>Bukti Bayar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result_pesanan && mysqli_num_rows($result_pesanan) > 0) : ?>
            <?php while ($o = mysqli_fetch_assoc($result_pesanan)) : ?>
                <tr>
                    <td>
                        <strong>#ORD-<?php echo $o['id_order']; ?></strong><br>
                        <small style="color: #666;"><?php echo isset($o['tanggal_order']) ? date('d M Y H:i', strtotime($o['tanggal_order'])) : '-'; ?></small>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($o['nama_pemesan'] ?? $o['nama'] ?? 'Customer'); ?></strong><br>
                        <small style="color: #666;"><?php echo htmlspecialchars($o['no_hp'] ?? '-'); ?></small>
                    </td>
                    <td style="font-weight: bold; color: #004494;">
                        Rp <?php echo number_format($o['total_bayar'] ?? $o['total'] ?? 0, 0, ',', '.'); ?>
                    </td>
                    <td>
                        <span style="background: #eef2ff; color: #3b52d4; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                            💳 <?php echo strtoupper(htmlspecialchars($o['metode_pembayaran'] ?? 'COD')); ?>
                        </span>
                    </td>
                    <td>
                        <?php 
                        $st = strtolower($o['status'] ?? 'pending');
                        $badgeClass = 'badge-pending';
                        if ($st === 'proses') $badgeClass = 'badge-proses';
                        if ($st === 'selesai') $badgeClass = 'badge-selesai';
                        if ($st === 'batal') $badgeClass = 'badge-batal';
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>">
                            <?php echo ucfirst($st); ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($o['bukti_pembayaran'])) : ?>
                            <button type="button" onclick="openBuktiModal('../assets/img/bukti/<?php echo htmlspecialchars($o['bukti_pembayaran']); ?>')" class="btn-action btn-detail" style="border: none; cursor: pointer; font-size: 12px;">🖼️ Lihat</button>
                        <?php else : ?>
                            <span style="color: #999; font-size: 12px;">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="pesanan.php" method="POST" style="display: flex; gap: 5px; align-items: center;">
                            <input type="hidden" name="id_order" value="<?php echo $o['id_order']; ?>">
                            <select name="status" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc; font-size: 12px;">
                                <option value="pending" <?php echo ($st == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="proses" <?php echo ($st == 'proses') ? 'selected' : ''; ?>>Proses</option>
                                <option value="selesai" <?php echo ($st == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                                <option value="batal" <?php echo ($st == 'batal') ? 'selected' : ''; ?>>Batal</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-action btn-edit" style="border: none; cursor: pointer; padding: 5px 8px;">Ubah</button>
                            <a href="pesanan.php?hapus=<?php echo $o['id_order']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin hapus pesanan ini?')">✕</a>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 25px; color: #777;">
                    🧾 Belum ada pesanan / transaksi masuk.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Modal Preview Bukti Pembayaran -->
<div id="modalBukti" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); align-items: center; justify-content: center; z-index: 10000; padding: 15px;">
    <div style="background: white; padding: 20px; border-radius: 12px; max-width: 450px; width: 100%; text-align: center;">
        <h3 style="margin-bottom: 15px;">🖼️ Bukti Pembayaran</h3>
        <img id="imgBukti" src="" alt="Bukti Transfer" style="max-width: 100%; max-height: 350px; object-fit: contain; border-radius: 8px; border: 1px solid #ddd;">
        <div style="margin-top: 15px;">
            <button type="button" onclick="closeBuktiModal()" style="padding: 8px 18px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">Tutup</button>
        </div>
    </div>
</div>

<script>
function openBuktiModal(src) {
    document.getElementById('imgBukti').src = src;
    document.getElementById('modalBukti').style.display = 'flex';
}

function closeBuktiModal() {
    document.getElementById('modalBukti').style.display = 'none';
}
</script>

<?php 
include 'footer_admin.php'; 
ob_end_flush();
?>