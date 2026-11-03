<?php
require_once '../config/database.php';
include 'header_admin.php';

$query  = "SELECT p.*, k.nama_kategori FROM products p 
           LEFT JOIN categories k ON p.id_kategori = k.id_kategori 
           ORDER BY p.id_produk DESC";
$result = mysqli_query($conn, $query);
?>

<h2>Daftar Produk</h2>

<?php if (isset($_GET['pesan'])) : ?>
    <div class="alert-success" style="margin-top: 10px;">
        <?php echo htmlspecialchars($_GET['pesan']); ?>
    </div>
<?php endif; ?>

<div style="margin-top: 15px;">
    <a href="produk_tambah.php" class="btn-add">+ Tambah Produk Baru</a>
</div>

<table class="table-admin">
    <thead>
        <tr>
            <th>No</th>
            <th>Gambar</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0) : ?>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td>
                        <?php if ($row['gambar'] && file_exists("../assets/img/" . $row['gambar'])) : ?>
                            <img src="../assets/img/<?php echo htmlspecialchars($row['gambar']); ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                        <?php else : ?>
                            <span style="color: #888; font-size: 12px;">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($row['nama_produk']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['nama_kategori'] ?? 'Tanpa Kategori'); ?></td>
                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td><?php echo $row['stok']; ?> pcs</td>
                    <td>
                        <a href="produk_edit.php?id=<?php echo $row['id_produk']; ?>" class="btn-action btn-edit">Edit</a>
                        <a href="produk_hapus.php?id=<?php echo $row['id_produk']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr>
                <td colspan="7" style="text-align: center;">Belum ada produk.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer_admin.php'; ?>