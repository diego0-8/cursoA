<?php
// app/views/common/footer.php
?>
    </div> <!-- Cierre del div de contenido principal del header -->
  </main>
</div> <!-- Cierre del div min-h-full del header -->

<!-- Pie de página con información de copyright -->
<footer class="bg-white border-t">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
        &copy; <?= date('Y') ?> Mi Proyecto de Alturas. Todos los derechos reservados.
    </div>
</footer>

<!-- Inclusión del archivo JavaScript principal -->
<!-- Esta línea es crucial para que tu página sea interactiva -->
<script src="<?= BASE_URL ?>/js/script.js"></script>

</body>
</html>