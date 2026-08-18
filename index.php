<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>diagnostico</title>
</head>
<body>
    <nav>
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <a href="reportes/<?php echo $i; ?>.php">Reporte <?php echo $i; ?></a>
        <?php endfor; ?>
    </nav>

    <nav>
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <a href="grafica/<?php echo $i; ?>.php">Grafica <?php echo $i; ?></a>
        <?php endfor; ?>
    </nav>
</body>
</html>