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
        GROUP BY e.emp_no, e.first_name, e.last_name
        HAVING anios_carrera > 0
        ORDER BY incremento_pct DESC
        LIMIT :limite";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Formatear datos para Chart.js Scatter
$chartData = [];
foreach ($datos as $fila) {
    $chartData[] = [
        'x' => (float)$fila['anios_carrera'],
        'y' => (float)$fila['incremento_pct'],
        'empleado' => $fila['empleado'],
        'min' => (float)$fila['salario_minimo'],
        'max' => (float)$fila['salario_maximo']
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfica - Top Incremento Salarial</title>
    <link rel="stylesheet" href="../css/estilo.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        p.desc {
            color: #6b7280;
            margin: 0 auto 24px;
            max-width: 60ch;
            text-align: center;
            line-height: 1.6;
        }

        .chart-card {
            width: 100%;
            max-width: 900px;
            margin: 0 auto 25px;
            padding: 24px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        }

        .chart-wrap {
            position: relative;
            width: 100%;
            height: 450px;
        }

        .filter-form {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            display: inline-block;
        }
        .filter-form label {
            font-weight: bold;
            margin-right: 10px;
        }
        .filter-form input[type="number"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 80px;
        }
        .filter-form button {
            padding: 6px 12px;
            background-color: #1f2937;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .filter-form button:hover {
            background-color: #374151;
        }
    </style>
</head>
<body>

    <h1>Gráfica - Top Empleados con Mayor Incremento Salarial</h1>

    <nav>
        <a href="../index.php">Volver al inicio</a>
    </nav>
    
    <div style="text-align: center;">
        <form class="filter-form" method="GET">
            <label for="limite">Top N Empleados:</label>
            <input type="number" name="limite" id="limite" value="<?= htmlspecialchars((string)$limite) ?>" min="1" max="100">
            <button type="submit">Actualizar Gráfica</button>
        </form>
    </div>

    <p class="desc">
        Relación entre los Años de Carrera (Eje X) y el Porcentaje de Incremento Salarial (Eje Y). Pasa el mouse sobre los puntos para ver los detalles.
    </p>

    <div class="chart-card">
        <div class="chart-wrap">
            <canvas id="graficoDisc"></canvas>
        </div>
    </div>

    <script>
        const scatterData = <?= json_encode($chartData) ?>;

        // Extraer nombres y porcentajes para el gráfico de barras horizontales
        const nombres = scatterData.map(d => d.empleado + ' (' + d.x + ' años)');
        const incrementos = scatterData.map(d => d.y);

        new Chart(document.getElementById('graficoDisc'), {
            type: 'bar',
            data: {
                labels: nombres,
                datasets: [{
                    label: '% de Incremento Salarial',
                    data: incrementos,
                    backgroundColor: 'rgba(124, 58, 237, 0.7)',
                    borderColor: 'rgba(124, 58, 237, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y', // Esto lo convierte en barras horizontales (barras de progreso)
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Top Incremento Salarial',
                        color: '#111827',
                        font: { size: 16, weight: '600' },
                        padding: { bottom: 20 }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const punto = scatterData[index];
                                return [
                                    `Aumento: +${punto.y}%`,
                                    `Salario Inicial: $${punto.min.toLocaleString('en-US', {minimumFractionDigits: 2})}`,
                                    `Salario Máximo: $${punto.max.toLocaleString('en-US', {minimumFractionDigits: 2})}`
                                ];
                            }
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: '% de Incremento Salarial',
                            color: '#374151',
                            font: { weight: 'bold' }
                        },
                        grid: { color: '#e5e7eb' },
                        beginAtZero: true
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
