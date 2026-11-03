<?php
require_once '../config/database.php';

$error = '';

// Proses Tambah Kategori (Wajib sebelum include header HTML)
if (isset($_POST['tambah_kategori'])) {
    $nama_kategori = trim($_POST['nama_kategori']);

    if (empty($nama_kategori)) {
        $error = "Nama kategori tidak boleh kosong!";
    } else {
        $nama_kategori_clean = mysqli_real_escape_string($conn, $nama_kategori);
        $query = "INSERT INTO categories (nama_kategori) VALUES ('$nama_kategori_clean')";
        if (mysqli_query($conn, $query)) {
            header("Location: kategori.php?pesan=Kategori berhasil ditambahkan!");
            exit;
        } else {
            $error = "Gagal menambahkan kategori: " . mysqli_error($conn);
        }
    }
}

// Ambil semua data kategori
$query  = "SELECT * FROM categories ORDER BY id_kategori DESC";
$result = mysqli_query($conn, $query);

include 'header_admin.php';
?>

<h2>Kelola Kategori Produk</h2>

<?php if (isset($_GET['pesan'])) : ?>
    <div class="alert-success" style="margin-top: 10px;">
        <?php echo htmlspecialchars($_GET['pesan']); ?>
    </div>
<?php endif; ?>

<?php if ($error) : ?>
    <div class="alert-error" style="margin-top: 10px;"><?php echo $error; ?></div>
<?php endif; ?>

<div style="display: flex; gap: 30px; flex-wrap: wrap; margin-top: 20px;">
    <!-- Form Tambah Kategori -->
    <div style="flex: 1; min-width: 280px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); height: fit-content;">
        <h3>Tambah Kategori Baru</h3>
        <form action="kategori.php" method="POST" style="margin-top: 15px;">
            <div class="form-group">
                <label for="nama_kategori">Nama Kategori</label>
                <input type="text" name="nama_kategori" id="nama_kategori" required placeholder="Contoh: Bumbu Dapur">
            </div>
            <button type="submit" name="tambah_kategori" class="btn-auth" style="margin-top: 10px; background: #28a745;">+ Simpan Kategori</button>
        </form>
    </div>

    <!-- Tabel Daftar Kategori -->
    <div style="flex: 2; min-width: 300px;">
        <table class="table-admin" style="margin-top: 0;">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Kategori</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0) : ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['nama_kategori']); ?></strong></td>
                            <td>
                                <a href="kategori_edit.php?id=<?php echo $row['id_kategori']; ?>" class="btn-action btn-edit">Edit</a>
                                <a href="kategori_hapus.php?id=<?php echo $row['id_kategori']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">Belum ada kategori.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer_admin.php'; ?>