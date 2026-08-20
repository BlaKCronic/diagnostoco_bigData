<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

$pageTitle = 'Reporte 3 · Número de empleados por departamento';
$section = 'reportes';
$active = 3;

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

require __DIR__ . '/../includes/header.php';
?>

<div class="card">
    <h2>Número de empleados por departamento</h2>
    <p class="subtitle">Dimensiona el tamaño de cada departamento para planificación de recursos y espacio. Se consideran únicamente asignaciones vigentes (empleados activos en el departamento).</p>

    <table class="report-table">
        <thead>
            <tr>
                <th>Departamento</th>
                <th class="numeric">Total de empleados</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filas as $fila): ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['departamento']); ?></td>
                <td class="numeric"><?php echo number_format((int) $fila['total_empleados']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="numeric"><?php echo number_format($totalGeneral); ?></td>
            </tr>
        </tfoot>
    </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
