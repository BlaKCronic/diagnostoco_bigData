<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

$sql = "SELECT YEAR(hire_date) AS anio, gender, COUNT(*) AS total
        FROM employees
        GROUP BY YEAR(hire_date), gender
        ORDER BY anio, gender";
$rows = $pdo->query($sql)->fetchAll();

$porAnio = [];
foreach ($rows as $r) {
    $anio = (int) $r['anio'];
    $porAnio[$anio][$r['gender']] = (int) $r['total'];
}
ksort($porAnio);

$labels = array_map('strval', array_keys($porAnio));
$masculino = [];
$femenino = [];
foreach ($porAnio as $valores) {
    $masculino[] = $valores['M'] ?? 0;
    $femenino[] = $valores['F'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gráfica 1 &middot; Evolución de contrataciones por año y género</title>
<link rel="stylesheet" href="../css/estilo.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body>

    <h1>Evolución de contrataciones por año y género</h1>

    <nav>
        <a href="../index.php">Volver al inicio</a>
    </nav>

    <p class="desc">
        Contrataciones por año, apiladas y coloreadas por género, para visualizar
        la tendencia de contratación y la evolución de la diversidad de género.
    </p>

    <div class="card">
        <div class="chart-wrap">
            <canvas id="chartContrataciones"></canvas>
        </div>
    </div>

    <script>
        const labels = <?= json_encode($labels) ?>;
        const masculino = <?= json_encode($masculino) ?>;
        const femenino = <?= json_encode($femenino) ?>;

        new Chart(document.getElementById('chartContrataciones'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Masculino',
                        data: masculino,
                        backgroundColor: '#2a78d6',
                        borderRadius: 4,
                        borderSkipped: false,
                        stack: 'contrataciones'
                    },
                    {
                        label: 'Femenino',
                        data: femenino,
                        backgroundColor: '#eb6834',
                        borderRadius: 4,
                        borderSkipped: false,
                        stack: 'contrataciones'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'start',
                        labels: { color: '#111827', boxWidth: 12, boxHeight: 12 }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { color: '#6b7280' }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: '#e5e7eb' },
                        ticks: { color: '#6b7280', precision: 0 },
                        title: { display: true, text: 'Número de contrataciones', color: '#374151' }
                    }
                }
            }
        });
    </script>

</body>
</html>
