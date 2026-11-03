<?php
require_once '../config/database.php';
include 'header_admin.php';

$id = intval($_GET['id'] ?? 0);
$query_p = mysqli_query($conn, "SELECT * FROM products WHERE id_produk = '$id'");
$produk  = mysqli_fetch_assoc($query_p);

if (!$produk) {
    header("Location: produk.php");
    exit;
}

$error = '';
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY nama_kategori ASC");

if (isset($_POST['update'])) {
    $nama_produk = trim($_POST['nama_produk']);
    $id_kategori = intval($_POST['id_kategori']);
    $harga       = intval($_POST['harga']);
    $stok        = intval($_POST['stok']);
    $gambar_nama = $produk['gambar'];

    if (empty($nama_produk) || $harga <= 0) {
        $error = "Nama produk dan harga wajib diisi!";
    } else {
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['gambar']['tmp_name'];
            $file_name = $_FILES['gambar']['name'];
            $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $allowed)) {
                if ($gambar_nama && $gambar_nama !== 'default.jpg' && file_exists("../assets/img/" . $gambar_nama)) {
                    unlink("../assets/img/" . $gambar_nama);
                }
                $gambar_nama = time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($file_tmp, "../assets/img/" . $gambar_nama);
            } else {
                $error = "Format gambar harus JPG, JPEG, PNG, atau WEBP!";
            }
        }

        if (empty($error)) {
            $query = "UPDATE products SET 
                        id_kategori = '$id_kategori', 
                        nama_produk = '$nama_produk', 
                        harga = '$harga', 
                        stok = '$stok', 
                        gambar = '$gambar_nama' 
                      WHERE id_produk = '$id'";
            if (mysqli_query($conn, $query)) {
                header("Location: produk.php?pesan=Produk berhasil diperbarui!");
                exit;
            } else {
                $error = "Gagal memperbarui produk: " . mysqli_error($conn);
            }
        }
    }
}
?>

<h2>Edit Produk</h2>

<?php if ($error) : ?>
    <div class="alert-error" style="margin-top: 10px;"><?php echo $error; ?></div>
<?php endif; ?>

<form action="produk_edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data" style="background: white; padding: 20px; border-radius: 8px; margin-top: 15px; max-width: 600px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
    <div class="form-group">
        <label>Nama Produk</label>
        <input type="text" name="nama_produk" value="<?php echo htmlspecialchars($produk['nama_produk']); ?>" required>
    </div>

    <div class="form-group">
        <label>Kategori</label>
        <select name="id_kategori" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="">-- Pilih Kategori --</option>
            <?php while ($cat = mysqli_fetch_assoc($categories)) : ?>
                <option value="<?php echo $cat['id_kategori']; ?>" <?php echo ($cat['id_kategori'] == $produk['id_kategori']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Harga (Rp)</label>
        <input type="number" name="harga" value="<?php echo $produk['harga']; ?>" required>
    </div>

    <div class="form-group">
        <label>Stok</label>
        <input type="number" name="stok" value="<?php echo $produk['stok']; ?>" required>
    </div>

    <div class="form-group">
        <label>Gambar Produk Saat Ini</label><br>
        <?php if ($produk['gambar'] && file_exists("../assets/img/" . $produk['gambar'])) : ?>
            <img src="../assets/img/<?php echo htmlspecialchars($produk['gambar']); ?>" width="80" style="border-radius: 4px; margin-bottom: 10px;"><br>
        <?php else : ?>
            <p style="color: #888; font-size: 13px;">Belum ada gambar</p>
        <?php endif; ?>
        <input type="file" name="gambar" accept="image/*">
        <small style="color: #777;">Kosongkan jika tidak ingin mengganti gambar.</small>
    </div>

    <button type="submit" name="update" class="btn-auth" style="margin-top: 10px;">Update Produk</button>
    <a href="produk.php" style="display: inline-block; margin-top: 10px; margin-left: 10px; text-decoration: none; color: #666;">Batal</a>
</form>

<?php include 'footer_admin.php'; ?>