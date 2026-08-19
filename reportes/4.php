<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

$sql = "SELECT
            CASE
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 30 THEN '<30'
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 40 THEN '30-39'
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 50 THEN '40-49'
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 60 THEN '50-59'
                ELSE '>=60'
            END AS rango_edad,
            gender,
            COUNT(*) AS total_empleados
        FROM employees
        GROUP BY rango_edad, gender
        ORDER BY rango_edad";

$resultado = $pdo->query($sql);

$datos = $resultado->fetchAll(PDO::FETCH_ASSOC);
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
                    <td><?= $fila['rango_edad'] ?></td>
                    <td><?= $fila['gender'] ?></td>
                    <td><?= $fila['total_empleados'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>