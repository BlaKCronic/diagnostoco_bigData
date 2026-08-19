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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
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
        height: 380px;
    }
</style>

<body>

    <h1>Empleados por rangos de edad y género</h1>

    <nav>
        <a href="../index.php">Volver al inicio</a>
    </nav>
    <p class="desc">
        Distribución de empleados según su rango de edad y género.
    </p>

    <div class="chart-card">
        <div class="chart-wrap">
            <canvas id="graficoEdades"></canvas>
        </div>
    </div>

    <script>
        const datos = <?= json_encode($datos) ?>;

        const rangos = ['<30', '30-39', '40-49', '50-59', '>=60'];

        const hombres = [];
        const mujeres = [];

        rangos.forEach(rango => {
            let hombre = 0;
            let mujer = 0;

            datos.forEach(fila => {
                if (fila.rango_edad === rango) {

                    if (fila.gender === 'M') {
                        hombre = parseInt(fila.total_empleados);
                    }

                    if (fila.gender === 'F') {
                        mujer = parseInt(fila.total_empleados);
                    }
                }
            });

            hombres.push(hombre);
            mujeres.push(mujer);
        });

        new Chart(document.getElementById('graficoEdades'), {
            type: 'bar',

            data: {
                labels: rangos,

                datasets: [
                    {
                        label: 'Hombres',
                        data: hombres,
                        backgroundColor: '#1f2937',
                        borderRadius: 6
                    },
                    {
                        label: 'Mujeres',
                        data: mujeres,
                        backgroundColor: '#6b7280',
                        borderRadius: 6
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    title: {
                        display: true,
                        text: 'Distribución de empleados por edad y género',
                        color: '#111827',
                        font: {
                            size: 16,
                            weight: '600'
                        },
                        padding: {
                            bottom: 20
                        }
                    },

                    legend: {
                        labels: {
                            color: '#374151',
                            usePointStyle: true,
                            padding: 18
                        }
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#6b7280'
                        },
                        grid: {
                            color: '#e5e7eb'
                        },
                        title: {
                            display: true,
                            text: 'Total de empleados',
                            color: '#374151'
                        }
                    },

                    x: {
                        ticks: {
                            color: '#6b7280'
                        },
                        grid: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Rango de edad',
                            color: '#374151'
                        }
                    }
                }
            }
        }); 
    </script>

</body>

</html>