<?php
require_once '../config/database.php';

$id = intval($_GET['id'] ?? 0);
$query_cat = mysqli_query($conn, "SELECT * FROM categories WHERE id_kategori = '$id'");
$kategori  = mysqli_fetch_assoc($query_cat);

if (!$kategori) {
    header("Location: kategori.php");
    exit;
}

$error = '';

// Proses Update Kategori
if (isset($_POST['update_kategori'])) {
    $nama_kategori = trim($_POST['nama_kategori']);

    if (empty($nama_kategori)) {
        $error = "Nama kategori tidak boleh kosong!";
    } else {
        $nama_kategori_clean = mysqli_real_escape_string($conn, $nama_kategori);
        $query = "UPDATE categories SET nama_kategori = '$nama_kategori_clean' WHERE id_kategori = '$id'";
        if (mysqli_query($conn, $query)) {
            header("Location: kategori.php?pesan=Kategori berhasil diperbarui!");
            exit;
        } else {
            $error = "Gagal memperbarui kategori: " . mysqli_error($conn);
        }
    }
}

include 'header_admin.php';
?>

<h2>Edit Kategori</h2>

<?php if ($error) : ?>
    <div class="alert-error" style="margin-top: 10px;"><?php echo $error; ?></div>
<?php endif; ?>

<form action="kategori_edit.php?id=<?php echo $id; ?>" method="POST" style="background: white; padding: 20px; border-radius: 8px; margin-top: 15px; max-width: 500px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
    <div class="form-group">
        <label for="nama_kategori">Nama Kategori</label>
        <input type="text" name="nama_kategori" id="nama_kategori" value="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>" required>
    </div>

    <button type="submit" name="update_kategori" class="btn-auth" style="margin-top: 10px; background: #0056b3;">Update Kategori</button>
    <a href="kategori.php" style="display: inline-block; margin-top: 10px; margin-left: 10px; text-decoration: none; color: #666;">Batal</a>
</form>

<?php include 'footer_admin.php'; ?>