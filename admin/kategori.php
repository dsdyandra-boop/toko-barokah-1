<?php
ob_start();
include 'header_admin.php';
require_once '../config/database.php';

$error = '';
$sukses = '';

// 1. TAMBAH KATEGORI
if (isset($_POST['tambah_kategori'])) {
    $nama_kategori = mysqli_real_escape_string($conn, trim($_POST['nama_kategori']));

    if (empty($nama_kategori)) {
        $error = 'Nama kategori tidak boleh kosong!';
    } else {
        $cek = mysqli_query($conn, "SELECT id_kategori FROM categories WHERE nama_kategori = '$nama_kategori'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Nama kategori sudah ada!';
        } else {
            $query = "INSERT INTO categories (nama_kategori) VALUES ('$nama_kategori')";
            if (mysqli_query($conn, $query)) {
                $sukses = 'Kategori berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambah kategori: ' . mysqli_error($conn);
            }
        }
    }
}

// 2. EDIT KATEGORI
if (isset($_POST['edit_kategori'])) {
    $id_kategori   = (int)$_POST['id_kategori'];
    $nama_kategori = mysqli_real_escape_string($conn, trim($_POST['nama_kategori']));

    if (empty($nama_kategori)) {
        $error = 'Nama kategori tidak boleh kosong!';
    } else {
        $cek = mysqli_query($conn, "SELECT id_kategori FROM categories WHERE nama_kategori = '$nama_kategori' AND id_kategori != '$id_kategori'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Nama kategori sudah digunakan!';
        } else {
            $query = "UPDATE categories SET nama_kategori = '$nama_kategori' WHERE id_kategori = '$id_kategori'";
            if (mysqli_query($conn, $query)) {
                $sukses = 'Kategori berhasil diperbarui!';
            } else {
                $error = 'Gagal memperbarui kategori: ' . mysqli_error($conn);
            }
        }
    }
}

// 3. HAPUS KATEGORI
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    // Cek apakah ada produk yang terikat pada kategori ini
    $cek_produk = mysqli_query($conn, "SELECT id_produk FROM products WHERE id_kategori = '$id_hapus'");
    if (mysqli_num_rows($cek_produk) > 0) {
        $error = 'Kategori tidak bisa dihapus karena masih digunakan oleh beberapa produk!';
    } else {
        mysqli_query($conn, "DELETE FROM categories WHERE id_kategori = '$id_hapus'");
        header("Location: kategori.php?pesan=Kategori berhasil dihapus");
        exit;
    }
}

// Query Semua Kategori + Hitung Jumlah Produk
$query_kategori = "SELECT c.*, COUNT(p.id_produk) AS total_produk 
                   FROM categories c 
                   LEFT JOIN products p ON c.id_kategori = p.id_kategori 
                   GROUP BY c.id_kategori 
                   ORDER BY c.nama_kategori ASC";
$categories = mysqli_query($conn, $query_kategori);
?>

<h2>Kelola Kategori Produk</h2>
<p style="color: #666; font-size: 14px; margin-bottom: 20px;">Tambah, edit, dan pantau jumlah produk di setiap kategori.</p>

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

<!-- Form Tambah Kategori -->
<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 30px; max-width: 500px;">
    <h3>🏷️ Tambah Kategori Baru</h3>
    <form action="kategori.php" method="POST" style="margin-top: 15px; display: flex; gap: 10px; align-items: center;">
        <input type="text" name="nama_kategori" required placeholder="Contoh: Bumbu Dapur, Minuman, Snack" style="flex: 1; padding: 9px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
        <button type="submit" name="tambah_kategori" class="btn-add" style="border: none; cursor: pointer; height: 38px;">+ Tambah</button>
    </form>
</div>

<!-- Tabel Daftar Kategori -->
<h3>Daftar Kategori</h3>
<table class="table-admin" style="margin-top: 15px;">
    <thead>
        <tr>
            <th style="width: 60px;">No</th>
            <th>Nama Kategori</th>
            <th style="width: 160px;">Jumlah Produk</th>
            <th style="width: 150px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($categories) > 0) : ?>
            <?php $no = 1; while ($c = mysqli_fetch_assoc($categories)) : ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><strong>🏷️ <?php echo htmlspecialchars($c['nama_kategori']); ?></strong></td>
                    <td>
                        <span style="background: #eef2ff; color: #3b52d4; font-weight: bold; padding: 4px 10px; border-radius: 12px; font-size: 13px;">
                            📦 <?php echo number_format($c['total_produk']); ?> Produk
                        </span>
                    </td>
                    <td>
                        <button type="button" onclick='openEditCategoryModal(<?php echo json_encode($c); ?>)' class="btn-action btn-edit" style="border: none; cursor: pointer;">Edit</button>
                        <a href="kategori.php?hapus=<?php echo $c['id_kategori']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin hapus kategori ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px; color: #777;">Belum ada kategori terdaftar.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Modal Edit Kategori -->
<div id="modalEditKategori" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; padding: 25px; border-radius: 12px; width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h3>✏️ Edit Kategori</h3>
        <form action="kategori.php" method="POST" style="margin-top: 15px; display: flex; flex-direction: column; gap: 15px;">
            <input type="hidden" name="id_kategori" id="edit_kat_id">
            
            <div>
                <label style="font-size: 13px; font-weight: bold;">Nama Kategori</label>
                <input type="text" name="nama_kategori" id="edit_kat_nama" required style="width: 100%; padding: 9px; border: 1px solid #ccc; border-radius: 6px; margin-top: 5px;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeEditCategoryModal()" style="padding: 8px 15px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Batal</button>
                <button type="submit" name="edit_kategori" class="btn-add" style="margin: 0; border: none; cursor: pointer;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditCategoryModal(kategori) {
    document.getElementById('edit_kat_id').value = kategori.id_kategori;
    document.getElementById('edit_kat_nama').value = kategori.nama_kategori;
    document.getElementById('modalEditKategori').style.display = 'flex';
}

function closeEditCategoryModal() {
    document.getElementById('modalEditKategori').style.display = 'none';
}
</script>

<?php 
include 'footer_admin.php'; 
ob_end_flush();
?>