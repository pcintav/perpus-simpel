<div class="container-fluid">
    <div class="row border border-secondary-subtle border-top pt-2" style="background-color: rgba(218, 218, 221, 0.849);">
        <p style="font-size: 12px; text-align: center;">&#169;2024 HAK CIPTA DLINDUNGI UU</p>
    </div>
</div>

    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script>
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(event){
                event.preventDefault();
                document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
                window.location.href = this.href;
            });
        });

        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.pathname === window.location.pathname) {
                link.classList.add('active');
            }
        });
    </script>
    </body>
</html>