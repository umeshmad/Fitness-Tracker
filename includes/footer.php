</main>
<footer class="site-footer">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
        <span class="brand">Stride Log</span>
        <span>Track the work. See the progress.</span>
    </div>
</footer>
<script src="<?= $base ?>js/bootstrap.bundle.min.js"></script>
<script src="<?= $base ?>js/validation.js"></script>
<script src="<?= $base ?>js/main.js"></script>
<?php // Pages can load extra scripts (slider, chart) by setting $extraScripts before including the header. ?>
<?php if (isset($extraScripts)): foreach ($extraScripts as $script): ?>
<script src="<?= $base . e($script) ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
