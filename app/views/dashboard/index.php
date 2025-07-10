<?php
// app/views/dashboard/index.php
// Vista del dashboard genérico (se redirigirá a uno específico por rol)
?>
<div class="p-5 mb-4 bg-light rounded-3 shadow-sm">
    <div class="container-fluid py-5 text-center">
        <h1 class="display-5 fw-bold">Bienvenido a tu Dashboard, <?php echo Session::get('user_name'); ?>!</h1>
        <p class="col-md-8 fs-4 mx-auto">Tu rol es: **<?php echo ucfirst(Session::get('user_role')); ?>**</p>
        <p>Estás en el dashboard general. Serás redirigido a tu dashboard específico.</p>
    </div>
</div>