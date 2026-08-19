<?php
/**
 * Espera (opcionalmente) definidas antes del include:
 * $pageTitle - título mostrado en el encabezado
 * $section   - 'reportes' o 'grafica', para resaltar el menú activo
 * $active    - número de reporte/gráfica activo (1-6)
 */
$pageTitle = $pageTitle ?? 'Diagnóstico Big Data';
$section = $section ?? null;
$active = $active ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header class="site-header">
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
        <a class="back-link" href="../index.php">&larr; Volver al inicio</a>
    </header>

    <?php if ($section === 'reportes' || $section === 'grafica'): ?>
    <nav class="menu-links">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <a class="<?php echo ($section === 'reportes' && $active === $i) ? 'active' : ''; ?>"
               href="../reportes/<?php echo $i; ?>.php">Reporte <?php echo $i; ?></a>
        <?php endfor; ?>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <a class="<?php echo ($section === 'grafica' && $active === $i) ? 'active' : ''; ?>"
               href="../grafica/<?php echo $i; ?>.php">Gráfica <?php echo $i; ?></a>
        <?php endfor; ?>
    </nav>
    <?php endif; ?>

    <main>
