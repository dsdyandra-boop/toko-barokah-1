<?php
require_once '../config/database.php';

$id_order = intval($_GET['id'] ?? 0);

// 1. PROSES UPDATE STATUS (Wajib dijalankan sebelum include header/output HTML)
if (isset($_POST['update_status'])) {
    $status_baru = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status = '$status_baru' WHERE id_order = '$id_order'");
    header("Location: pesanan.php?pesan=Status pesanan #TRX-$id_order berhasil diperbarui!");
    exit;
}

// 2. Ambil Data Header Order & User
$query_order = "SELECT o.*, u.nama, u.email 
                FROM orders o 
                JOIN users u ON o.id_user = u.id_user 
                WHERE o.id_order = '$id_order'";
$res_order   = mysqli_query($conn, $query_order);
$order       = mysqli_fetch_assoc($res_order);

// 3. Ambil Item Produk
$query_items = "SELECT od.*, p.nama_produk, p.gambar 
                FROM order_details od 
                JOIN products p ON od.id_produk = p.id_produk 
                WHERE od.id_order = '$id_order'";
$res_items   = mysqli_query($conn, $query_items);

// 4. BARU INCLUDE HEADER DI SINI
include 'header_admin.php';
?>

<?php if (!$order) : ?>
    <div style="background: white; padding: 30px; text-align: center; border-radius: 8px;">
        <p>Pesanan tidak ditemukan!</p>
        <a href="pesanan.php" style="color: #0056b3; font-weight: bold;">&larr; Kembali ke Daftar Pesanan</a>
    </div>
<?php else : ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Detail Pesanan #TRX-<?php echo $order['id_order']; ?></h2>
        <a href="pesanan.php" style="text-decoration: none; color: #0056b3; font-weight: bold;">&larr; Kembali ke Daftar Pesanan</a>
    </div>

    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        <!-- Detail Barang -->
        <div style="flex: 2; min-width: 300px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h3>Item Produk Yang Dibelanja</h3>
            <table class="table-admin" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_assoc($res_items)) : ?>
                        <tr>
                            <td>
                                <?php if ($item['gambar'] && file_exists("../assets/img/" . $item['gambar'])) : ?>
                                    <img src="../assets/img/<?php echo htmlspecialchars($item['gambar']); ?>" width="40" height="40" style="object-fit: cover; border-radius: 4px;">
                                <?php else : ?>
                                    <span style="font-size: 11px; color: #888;">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($item['nama_produk']); ?></strong></td>
                            <td>Rp <?php echo number_format($item['subtotal'] / $item['qty'], 0, ',', '.'); ?></td>
                            <td><?php echo $item['qty']; ?> pcs</td>
                            <td><strong style="color: #d32f2f;">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></strong></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div style="text-align: right; margin-top: 15px; font-size: 18px; font-weight: bold;">
                Total Pembayaran: <span style="color: #0056b3;">Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></span>
            </div>
        </div>

        <!-- Informasi Pemesan & Form Ubah Status -->
        <div style="flex: 1; min-width: 250px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); height: fit-content;">
            <h3>Informasi Pemesan</h3>
            <p style="margin-top: 10px; font-size: 14px;">Nama: <strong><?php echo htmlspecialchars($order['nama']); ?></strong></p>
            <p style="font-size: 14px; margin-top: 5px;">Email: <strong><?php echo htmlspecialchars($order['email']); ?></strong></p>
            
            <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">

            <h3>Ubah Status Pesanan</h3>
            <!-- Informasi Pemesan & Form Ubah Status -->
        <div style="flex: 1; min-width: 250px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); height: fit-content;">
            <h3>Informasi Pemesan</h3>
            <p style="margin-top: 10px; font-size: 14px;">Nama: <strong><?php echo htmlspecialchars($order['nama']); ?></strong></p>
            <p style="font-size: 14px; margin-top: 5px;">Email: <strong><?php echo htmlspecialchars($order['email']); ?></strong></p>
            <p style="font-size: 14px; margin-top: 5px;">Metode Pembayaran: <strong style="text-transform: uppercase; color: #0056b3;"><?php echo htmlspecialchars($order['metode_pembayaran'] ?? 'cash'); ?></strong></p>
            
            <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">

            <h3>Ubah Status Pesanan</h3>
            ...
            <form action="pesanan_detail.php?id=<?php echo $id_order; ?>" method="POST" style="margin-top: 10px;">
                <div class="form-group">
                    <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-weight: bold;">
                        <option value="pending" <?php echo ($order['status'] === 'pending') ? 'selected' : ''; ?>>PENDING (Menunggu)</option>
                        <option value="diproses" <?php echo ($order['status'] === 'diproses') ? 'selected' : ''; ?>>DIPROSES (Dikemas/Dikirim)</option>
                        <option value="selesai" <?php echo ($order['status'] === 'selesai') ? 'selected' : ''; ?>>SELESAI</option>
                        <option value="batal" <?php echo ($order['status'] === 'batal') ? 'selected' : ''; ?>>BATAL</option>
                    </select>
                </div>
                <button type="submit" name="update_status" class="btn-auth" style="margin-top: 10px; background: #0056b3;">Simpan Status</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php include 'footer_admin.php'; ?>