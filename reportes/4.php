<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

$fechaRef = $_GET['fecha_ref'] ?? '';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaRef)) {
    $fechaRef = date('Y-m-d');
}

$sql = "SELECT
            CASE
                WHEN TIMESTAMPDIFF(YEAR, birth_date, :fecha_ref1) < 30 THEN '<30'
                WHEN TIMESTAMPDIFF(YEAR, birth_date, :fecha_ref2) < 40 THEN '30-39'
                WHEN TIMESTAMPDIFF(YEAR, birth_date, :fecha_ref3) < 50 THEN '40-49'
                WHEN TIMESTAMPDIFF(YEAR, birth_date, :fecha_ref4) < 60 THEN '50-59'
                ELSE '>=60'
            END AS rango_edad,
            gender,
            COUNT(*) AS total_empleados
        FROM employees
        GROUP BY rango_edad, gender
        ORDER BY rango_edad";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'fecha_ref1' => $fechaRef,
    'fecha_ref2' => $fechaRef,
    'fecha_ref3' => $fechaRef,
    'fecha_ref4' => $fechaRef,
]);

$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$generoLabel = ['M' => 'Masculino', 'F' => 'Femenino'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados por edad y género</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>

    <h1>Empleados por rangos de edad y género</h1>
    <nav>
        <a href="../index.php">Volver al inicio</a>
    </nav>

    <p class="desc">
        Distribución de empleados según su rango de edad y género, calculada contra la fecha de referencia elegida.
    </p>

    <div style="text-align: center;">
        <form class="filter-form" method="GET">
            <label for="fecha_ref">Fecha de referencia:</label>
            <input type="date" name="fecha_ref" id="fecha_ref" value="<?= htmlspecialchars($fechaRef) ?>">
            <button type="submit">Actualizar</button>
        </form>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Rango de edad</th>
                        <th>Género</th>
                        <th>Total de empleados</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($datos as $fila): ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['rango_edad']) ?></td>
                            <td><?= htmlspecialchars($generoLabel[$fila['gender']] ?? $fila['gender']) ?></td>
                            <td><?= htmlspecialchars((string) $fila['total_empleados']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
