<div class="container-fluid">
    <div class="row border border-secondary-subtle border-top pt-2" style="background-color: rgba(218, 218, 221, 0.849);">
        <p class="text-center mb-0" style="font-size: 12px;">&#169;2025 HAK CIPTA DILINDUNGI UU</p>
    </div>
</div>

<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script>
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
        });
    });
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') && link.getAttribute('href').includes(currentPage)) {
            link.classList.add('active');
        }
    });
</script>
</body>
</html>
