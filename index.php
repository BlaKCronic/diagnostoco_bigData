<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>diagnostico</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <h1>Diagnóstico Big Data</h1>
    <nav>
        <?php for ($i = 1; $i <= 7; $i++): ?>
            <a href="reportes/<?php echo $i; ?>.php">Reporte <?php echo $i; ?></a>
        <?php endfor; ?>
    </nav>

    <nav>
        <?php for ($i = 1; $i <= 6; $i++): ?>
            <a href="grafica/<?php echo $i; ?>.php">Grafica <?php echo $i; ?></a>
        <?php endfor; ?>
    </nav>
</body>
</html>