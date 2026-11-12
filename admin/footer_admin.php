</main>

    <footer style="text-align: center; padding: 20px; background: #fff; margin-top: 40px; border-top: 1px solid #eee;">
        <p>&copy; <?php echo date('Y'); ?> Toko Barokah 1Admin Panel. All rights reserved.</p>
    </footer>

    <!-- CDN SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Toast Notifikasi Pesan Sukses untuk Admin -->
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

    <!-- Script Animasi Transisi Halaman -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
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
                setTimeout(() => { window.location.href = href; }, 250);
            });
        });
    });
    </script>

</body>
</html>