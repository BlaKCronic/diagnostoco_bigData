<?php
require_once("../config.php");

$sql = "SELECT d.dept_name AS departamento, ROUND(AVG(s.salary), 2) AS salario_promedio
        FROM departments d
        INNER JOIN dept_emp de ON d.dept_no = de.dept_no
        INNER JOIN salaries s ON de.emp_no = s.emp_no
        GROUP BY d.dept_no, d.dept_name
        ORDER BY salario_promedio DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $reportes = $stmt->fetchAll();
} catch (PDOException $e) {
    $reportes = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte 2 - Salario Promedio</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

    <div class="card">
        <h2>Salario Promedio por Departamento</h2>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Departamento</th>
                        <th>Salario Promedio ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reportes)): ?>
                        <?php foreach ($reportes as $fila): ?>
                            <tr>
                                <td><?= htmlspecialchars($fila['departamento']) ?></td>
                                <td>$<?= number_format($fila['salario_promedio'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="2" class="empty">No se encontraron registros de salarios.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>