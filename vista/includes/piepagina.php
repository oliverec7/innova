<header>
    <link rel="stylesheet" href="vista/css/piepagina.css">
</header>
<footer class="bg-white sticky-footer">
    <div class="container my-auto">
        <div class="text-center my-auto copyright">
            <h6><b><span>Copyright © INNOVA <span id="year"></span>. Todos los derechos reservados.</span></b></h6>
        </div>
    </div>
    <script>
        document.getElementById("year").textContent = new Date().getFullYear();
    </script>
</footer>