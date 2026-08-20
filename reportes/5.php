<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

$limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;
if ($limite <= 0) $limite = 10;

$sql = "SELECT 
            e.emp_no,
            CONCAT(e.first_name, ' ', e.last_name) AS empleado,
            MIN(s.salary) AS salario_minimo,
            MAX(s.salary) AS salario_maximo,
            ROUND(((MAX(s.salary) - MIN(s.salary)) / MIN(s.salary)) * 100, 2) AS incremento_pct,
            TIMESTAMPDIFF(YEAR, MIN(s.from_date), LEAST(MAX(s.to_date), CURDATE())) AS anios_carrera
        FROM employees e
        JOIN salaries s ON e.emp_no = s.emp_no
        GROUP BY e.emp_no
        HAVING anios_carrera > 0
        ORDER BY incremento_pct DESC
        LIMIT :limite";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Empleados - Incremento Salarial</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

    <h1>Top Empleados con Mayor Incremento Salarial</h1>
    <nav>
        <a href="../index.php">Volver al inicio</a>
    </nav>

    <div style="text-align: center;">
        <form class="filter-form" method="GET">
            <label for="limite">Cantidad a visualizar (Top N):</label>
            <input type="number" name="limite" id="limite" value="<?= htmlspecialchars((string)$limite) ?>" min="1" max="1000">
            <button type="submit">Filtrar</button>
        </form>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No. Emp</th>
                        <th>Empleado</th>
                        <th>Salario Inicial (Min)</th>
                        <th>Salario Final (Max)</th>
                        <th>% Incremento</th>
                        <th>Años de Carrera</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($datos) > 0): ?>
                        <?php foreach ($datos as $fila): ?>
                            <tr>
                                <td><?= $fila['emp_no'] ?></td>
                                <td><?= htmlspecialchars($fila['empleado']) ?></td>
                                <td>$<?= number_format((float)$fila['salario_minimo'], 2) ?></td>
                                <td>$<?= number_format((float)$fila['salario_maximo'], 2) ?></td>
                                <td style="color: green; font-weight: bold;">+<?= $fila['incremento_pct'] ?>%</td>
                                <td><?= $fila['anios_carrera'] ?> años</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty">No se encontraron resultados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
