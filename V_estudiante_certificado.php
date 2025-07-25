<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo'] ?? 'Certificado'); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Enlace a la hoja de estilos del certificado -->
    <link rel="stylesheet" href="views/css/certificado_estilo.css">
</head>
<body>

    <div class="no-print" style="text-align: center; padding: 1rem;">
        <a href="index.php?c=estudiante&a=misCursos" style="display: inline-block; background-color: #555; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; margin-right: 10px;">
            <i class="fas fa-arrow-left"></i> Volver a Mis Cursos
        </a>
        <button onclick="window.print()" style="background-color: #8BC34A; color: white; padding: 10px 15px; border-radius: 5px; border: none; cursor: pointer;">
            <i class="fas fa-print"></i> Imprimir / Guardar como PDF
        </button>
    </div>

    <!-- Contenedor principal del certificado -->
    <div id="certificate-container" style="margin: 2rem auto;">
        
        <!-- Contenido del certificado -->
        <div class="certificate-content">
            <header class="certificate-header">
                <img src="../img/Frame-2.png" alt="hola">                
            </header>

            <main class="certificate-body">
                <p class="company-name">CADENA MONSALVE SAS</p>
                <p class="certifies-text">CERTIFICA QUE</p>
                
                <h1 class="student-name">
                    <?php echo htmlspecialchars(strtoupper($data['certificado']['nombre_estudiante'] ?? 'NOMBRE DEL ESTUDIANTE')); ?>
                </h1>
                
                <p class="student-id">
                    Cédula: <?php echo htmlspecialchars($data['certificado']['numero_documento'] ?? '00000000'); ?>
                </p>

                <p class="main-text">
                    ES PERSONA COMPETENTE PARA REVISIÓN E INSPECCIÓN DE LOS EQUIPOS DE PROTECCIÓN CONTRA CAÍDAS DE LA MARCA VERTICAL SAFE
                </p>
            </main>

            <footer class="certificate-footer">
                <div class="signature-area">
                    <p class="signature-font">Marcela Monsalve</p>
                    <div class="signature-line">
                        <p class="signer-name">MARCELA MONSALVE</p>
                        <p class="signer-title">GERENTE GENERAL</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

</body>
</html>
