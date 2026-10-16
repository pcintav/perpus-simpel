
<script>
    // Auto-detect active page and add active class
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            if (!link.getAttribute('href')) return;
            if (link.pathname === window.location.pathname) {
                link.classList.add('active');
            }
        });
    });
</script>


<div class="container-fluid">
            <!-- Membuat container penuh (full width) menggunakan Bootstrap -->

            <div class="row">
                <!-- Membuat satu baris di dalam container -->

                <div class="col-12 pt-2" style="background-color: rgba(218, 218, 221, 0.849);">
                    <!-- Membuat kolom penuh (12 grid) -->
                    <!-- Diberi border dengan warna abu-abu lembut (secondary-subtle) -->
                    <!-- Diberi border di bagian atas saja (border-top) -->
                    <!-- Padding atas 2 (pt-2) -->
                    <!-- Inline style: background warna abu-abu dengan transparansi -->

                    <p style="font-size: 12px; text-align: center; ">&#169;2025 HAK CIPTA DILINDUNGI UU</p>
                    <!-- Paragraf dengan ukuran font kecil (12px) -->
                    <!-- Teks ditengah (center) -->
                    <!-- Karakter @ ditulis menggunakan kode HTML &#169; -->
                    <!-- Isi teks: tahun 2025 Hak Cipta Dilindungi Undang-Undang -->
                </div>
            </div>
        </div>

        <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
        <!-- Memuat file JavaScript Bootstrap lokal untuk mendukung komponen interaktif (seperti nav, modal, dropdown) -->

        <script>
            // Memilih semua elemen dengan class "nav-link"
            document.querySelectorAll('.nav-link' ). forEach(link => {
                // Menambahkan event listener "click" pada setiap link
                link. addEventListener('click', function(event){
                    event.preventDefault();
                    // Mencegah aksi default (navigasi langsung) saat link diklik

                    document.querySelectorAll(' .nav-link' ) . forEach(nav => nav. classList. remove('active'));
                    // Menghapus class "active" dari semua link navigasi (agar hanya satu link aktif)

                    this.classList.add('active');
                    // Menambahkan class "active" ke link yang sedang diklik

                    window. location.href = this.href;
                    // Setelah class "active" ditambahkan, browser diarahkan ke URL link tersebut
                });
            });

            document.querySelectorAll('.nav-link').forEach(link => {
            if (link.pathname === window.location.pathname) {
                link.classList.add('active');
            }
        });
        </script>

    </body>
    <!-- Penutup tag body -->

</html>
<!-- Penutup tag html -->