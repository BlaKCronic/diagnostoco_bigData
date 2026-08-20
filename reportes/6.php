<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

$sql = "SELECT
            d.dept_name AS departamento,

            ROUND(AVG(CASE WHEN e.gender = 'M' THEN s.salary END), 2) AS salario_promedio_hombres,

            ROUND(AVG(CASE WHEN e.gender = 'F' THEN s.salary END), 2) AS salario_promedio_mujeres,

            ROUND(AVG(CASE WHEN e.gender = 'M' THEN s.salary END) - AVG(CASE WHEN e.gender = 'F' THEN s.salary END),2
            ) AS diferencia_salarial,

            ROUND(((AVG(CASE WHEN e.gender = 'M' THEN s.salary END) -
                    AVG(CASE WHEN e.gender = 'F' THEN s.salary END))/
                    AVG(CASE WHEN e.gender = 'M' THEN s.salary END)) * 100,2
            ) AS porcentaje_brecha

        FROM employees e 
        JOIN salaries s ON e.emp_no = s.emp_no
        JOIN dept_emp de ON e.emp_no = de.emp_no
        JOIN departments d ON de.dept_no = d.dept_no

        WHERE s.to_date = '9999-01-01' AND de.to_date = '9999-01-01'

        GROUP BY d.dept_no, d.dept_name

        ORDER BY porcentaje_brecha DESC";

$resultado = $pdo->query($sql);

$datos = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Brecha salarial por departamento y género</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>

    <h1>Brecha salarial por departamento y género</h1>

    <nav>
        <a href="../index.php">Volver al inicio</a>
    </nav>

    <p class="desc">
        Comparación del salario promedio entre hombres y mujeres
        por departamento.
    </p>

    <?php if (count($datos) > 0): ?>

        <div class="card">
        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>Departamento</th>
                        <th>Salario promedio hombres</th>
                        <th>Salario promedio mujeres</th>
                        <th>Diferencia salarial</th>
                        <th>% de brecha</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($datos as $fila): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($fila['departamento']) ?>
                            </td>

                            <td>
                                $<?= number_format((float)$fila['salario_promedio_hombres'], 2) ?>
                            </td>

                            <td>
                                $<?= number_format((float)$fila['salario_promedio_mujeres'], 2) ?>
                            </td>

                            <td>
                                $<?= number_format((float)$fila['diferencia_salarial'], 2) ?>
                            </td>

                            <td>
                                <?= number_format((float)$fila['porcentaje_brecha'], 2) ?>%
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>
        </div>

    <?php else: ?>

        <div class="empty">
            No se encontraron datos para generar el reporte.
        </div>

    <?php endif; ?>

</body>

</html>