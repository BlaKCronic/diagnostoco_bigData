<?php
require_once("../config.php");

$sql = "SELECT d.dept_name AS departamento, ROUND(AVG(s.salary), 2) AS salario_promedio
        FROM departments d
        INNER JOIN dept_emp de ON d.dept_no = de.dept_no
        INNER JOIN salaries s ON de.emp_no = s.emp_no
        GROUP BY d.dept_no, d.dept_name
        ORDER BY salario_promedio DESC";

$departamentos = [];
$salarios = [];

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($fila = $stmt->fetch()) {
        $departamentos[] = $fila['departamento'];
        $salarios[] = (float)$fila['salario_promedio'];
    }
} catch (PDOException $e) {
    // Manejo silencioso en caso de error de consulta
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfica 2 - Salario Promedio</title>
    <link rel="stylesheet" href="../css/estilo.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="card">
        <h2>Gráfica: Salario Promedio por Departamento</h2>
        
        <div style="position: relative; width: 100%; height: 400px; margin-top: 20px;">
            <canvas id="graficaSalarios"></canvas>
        </div>
    </div>

    <script>
        const labelsDeptos = <?php echo json_encode($departamentos); ?>;
        const dataSalarios = <?php echo json_encode($salarios); ?>;

        const ctx = document.getElementById('graficaSalarios').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelsDeptos,
                datasets: [{
                    label: 'Salario Promedio ($)',
                    data: dataSalarios,
                    backgroundColor: '#1f2937',
                    borderColor: '#111827',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { 
                        beginAtZero: true,
                        grid: { color: '#e5e7eb' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>

</body>
</html>