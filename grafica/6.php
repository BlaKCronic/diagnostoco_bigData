<?php 
declare(strict_types=1); 
 
require_once __DIR__ . '/../config.php'; 
 
$sql = "SELECT 
            d.dept_name AS departamento, 
 
            ROUND(AVG(CASE WHEN e.gender = 'M' THEN s.salary END), 2) AS salario_promedio_hombres, 
 
            ROUND(AVG(CASE WHEN e.gender = 'F' THEN s.salary END), 2) AS salario_promedio_mujeres, 
 
            ROUND(AVG(CASE WHEN e.gender = 'M' THEN s.salary END) - AVG(CASE WHEN e.gender = 'F' THEN s.salary END), 2) 
            AS diferencia_salarial, 
 
            ROUND(((AVG(CASE WHEN e.gender = 'M' THEN s.salary END) - 
                    AVG(CASE WHEN e.gender = 'F' THEN s.salary END)) / 
                    AVG(CASE WHEN e.gender = 'M' THEN s.salary END)) * 100, 2) 
            AS porcentaje_brecha 
 
        FROM employees e  
        JOIN salaries s ON e.emp_no = s.emp_no 
        JOIN dept_emp de ON e.emp_no = de.emp_no 
        JOIN departments d ON de.dept_no = d.dept_no 
 
        WHERE s.to_date = '9999-01-01' 
        AND de.to_date = '9999-01-01' 
 
        GROUP BY d.dept_no, d.dept_name 
 
        ORDER BY porcentaje_brecha DESC"; 
 
$resultado = $pdo->query($sql); 
 
$datos = $resultado->fetchAll(PDO::FETCH_ASSOC); 

$departamentos = [];
$salariosHombres = [];
$salariosMujeres = [];
$brechas = [];

foreach ($datos as $fila) {
    $departamentos[] = $fila['departamento'];
    $salariosHombres[] = (float)$fila['salario_promedio_hombres'];
    $salariosMujeres[] = (float)$fila['salario_promedio_mujeres'];
    $brechas[] = (float)$fila['porcentaje_brecha'];
}
?> 
 
<!DOCTYPE html> 
<html lang="es"> 
 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
 
    <title>Brecha salarial por departamento y género</title> 
 
    <link rel="stylesheet" href="../css/estilo.css"> 

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

            <div class="chart-wrap">
                <canvas id="graficaBrecha"></canvas>
            </div>

        </div>

    <?php else: ?>

        <div class="empty">
            No se encontraron datos para generar el reporte.
        </div>

    <?php endif; ?> 


    <?php if (count($datos) > 0): ?>


    <script>

        const departamentos = <?= json_encode($departamentos) ?>;
        const salariosHombres = <?= json_encode($salariosHombres) ?>;
        const salariosMujeres = <?= json_encode($salariosMujeres) ?>;
        const brechas = <?= json_encode($brechas) ?>;

        const ctx = document.getElementById('graficaBrecha');

        new Chart(ctx, {

            data: {

                labels: departamentos,

                datasets: [

                    {
                        type: 'bar',
                        label: 'Salario promedio hombres',
                        data: salariosHombres,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },

                    {
                        type: 'bar',
                        label: 'Salario promedio mujeres',
                        data: salariosMujeres,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgb(255, 99, 132)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },

                    {
                        type: 'line',
                        label: 'Porcentaje de brecha',
                        data: brechas,
                        borderColor: 'rgb(255, 159, 64)',
                        backgroundColor: 'rgb(255, 159, 64)',
                        borderWidth: 3,
                        pointRadius: 5,
                        tension: 0.3,
                        yAxisID: 'y1'
                    }

                ]
            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                interaction: {
                    mode: 'index',
                    intersect: false
                },

                plugins: {

                    legend: {
                        position: 'bottom'
                    },

                    title: {
                        display: true,
                        text: 'Salario promedio y brecha salarial por departamento',
                        font: {
                            size: 18
                        }
                    },

                    tooltip: {

                        callbacks: {

                            label: function(context) {

                                if (context.dataset.label === 'Porcentaje de brecha') {

                                    return context.dataset.label + ': ' +
                                        context.raw.toFixed(2) + '%';

                                }

                                return context.dataset.label + ': $' +
                                    context.raw.toLocaleString('es-MX', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                            }

                        }

                    }

                },

                scales: {

                    y: {

                        beginAtZero: false,

                        title: {
                            display: true,
                            text: 'Salario promedio ($)'
                        },

                        ticks: {

                            callback: function(value) {

                                return '$' + value.toLocaleString('es-MX');

                            }

                        }

                    },

                    y1: {

                        position: 'right',

                        title: {
                            display: true,
                            text: 'Brecha salarial (%)'
                        },

                        grid: {
                            drawOnChartArea: false
                        },

                        ticks: {

                            callback: function(value) {

                                return value + '%';

                            }

                        }

                    }

                }

            }

        });

    </script>

    <?php endif; ?>

</body> 
 
</html>