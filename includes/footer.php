</main>
    <footer class="footer">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> TokoHarian. All rights reserved.</p>
        </div>
    </footer>
  <script>
document.addEventListener('DOMContentLoaded', () => {
    // Tangkap semua tombol link <a>
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');

            // Abaikan jika link kosong, anchor #, logout/hapus yang pakai confirm, atau tab baru
            if (
                !href || 
                href.startsWith('#') || 
                href.startsWith('javascript') || 
                this.getAttribute('target') === '_blank' ||
                this.getAttribute('onclick')
            ) {
                return;
            }

            e.preventDefault();
            document.body.classList.add('page-out');

            setTimeout(() => {
                window.location.href = href;
            }, 250);
        });
    });
});
</script>
<!-- CDN SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['pesan'])) : ?>
<script>
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true
});

Toast.fire({
  icon: 'success',
  title: '<?php echo htmlspecialchars($_GET['pesan']); ?>'
});
</script>
<?php endif; ?>
</body>
</html>