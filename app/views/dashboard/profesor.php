<?php
// app/views/dashboard/profesor.php
// Vista del dashboard para el rol 'profesor'
?>
<div class="p-5 mb-4 bg-success text-white rounded-3 shadow-sm">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Panel de Profesor</h1>
        <p class="fs-4">Bienvenido, **<?php echo Session::get('user_name'); ?>** (Profesor).</p>
        <p>Aquí puedes gestionar tus clases, proyectos y calificaciones.</p>
        <a class="btn btn-light btn-lg rounded-pill px-4" href="<?php echo BASE_URL; ?>/logout" role="button">
            <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-chalkboard-teacher me-2"></i> Mis Clases</h5>
                <p class="card-text">Gestiona las clases que impartes y su contenido.</p>
                <a href="#" class="btn btn-outline-success rounded-pill">Ver Mis Clases</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-tasks me-2"></i> Proyectos y Entregas</h5>
                <p class="card-text">Revisa y califica las entregas de proyectos de tus estudiantes.</p>
                <a href="#" class="btn btn-outline-success rounded-pill">Revisar Entregas</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-clipboard-check me-2"></i> Calificaciones</h5>
                <p class="card-text">Administra las calificaciones de tus estudiantes.</p>
                <a href="#" class="btn btn-outline-success rounded-pill">Ver Calificaciones</a>
            </div>
        </div>
    </div>
</div>
