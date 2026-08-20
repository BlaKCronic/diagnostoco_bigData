<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

$sql = "SELECT d.dept_name AS departamento, COUNT(*) AS total_empleados
        FROM dept_emp de
        JOIN departments d ON d.dept_no = de.dept_no
        WHERE de.to_date = '9999-01-01'
        GROUP BY d.dept_name
        ORDER BY total_empleados DESC";

$filas = $pdo->query($sql)->fetchAll();

$totalGeneral = 0;
foreach ($filas as $fila) {
    $totalGeneral += (int) $fila['total_empleados'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte 3 &middot; Número de empleados por departamento</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

    <h1>Número de empleados por departamento</h1>

    <nav>
        <a href="../index.php">Volver al inicio</a>
    </nav>

    <p class="desc">
        Dimensiona el tamaño de cada departamento para planificación de recursos y espacio.
        Se consideran únicamente asignaciones vigentes (empleados activos en el departamento).
    </p>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Departamento</th>
                        <th>Total de empleados</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filas as $fila): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['departamento']) ?></td>
                        <td><?= number_format((int) $fila['total_empleados']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td><?= number_format($totalGeneral) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</body>
</html>
