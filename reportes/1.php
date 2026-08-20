<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

$sql = "SELECT YEAR(hire_date) AS anio, gender, COUNT(*) AS total
        FROM employees
        GROUP BY YEAR(hire_date), gender
        ORDER BY anio, gender";
$rows = $pdo->query($sql)->fetchAll();

$generoLabel = ['M' => 'Masculino', 'F' => 'Femenino'];
$totalGeneral = array_sum(array_column($rows, 'total'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte 1 &middot; Evolución de contrataciones por año y género</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

    <h1>Evolución de contrataciones por año y género</h1>

    <nav>
        <a href="../index.php">Volver al inicio</a>
    </nav>

    <p class="desc">
        Identifica las tendencias de contratación y la evolución de la diversidad
        de género en la empresa a lo largo del tiempo.
    </p>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Año</th>
                        <th>Género</th>
                        <th>Total de contrataciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) $r['anio']) ?></td>
                            <td><?= htmlspecialchars($generoLabel[$r['gender']] ?? $r['gender']) ?></td>
                            <td><?= htmlspecialchars((string) $r['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">Total</td>
                        <td><?= htmlspecialchars((string) $totalGeneral) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</body>
</html>
