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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<style>
  :root {
    color-scheme: light;
    --surface: #fcfcfb;
    --page: #f9f9f7;
    --ink: #0b0b0b;
    --ink-secondary: #52514e;
    --ink-muted: #898781;
    --grid: #e1e0d9;
    --border: rgba(11,11,11,0.10);
    --series-masculino: #2a78d6;
    --series-femenino: #eb6834;
  }
  @media (prefers-color-scheme: dark) {
    :root {
      color-scheme: dark;
      --surface: #1a1a19;
      --page: #0d0d0d;
      --ink: #ffffff;
      --ink-secondary: #c3c2b7;
      --ink-muted: #898781;
      --grid: #2c2c2a;
      --border: rgba(255,255,255,0.10);
      --series-masculino: #3987e5;
      --series-femenino: #d95926;
    }
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    padding: 32px;
    background: var(--page);
    color: var(--ink);
    font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
  }
  a.back { color: var(--ink-secondary); text-decoration: none; font-size: 0.9rem; }
  a.back:hover { text-decoration: underline; }
  h1 { font-size: 1.4rem; margin: 12px 0 4px; }
  p.desc { color: var(--ink-secondary); margin: 0 0 24px; max-width: 60ch; }
  .chart-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 24px;
    max-width: 780px;
  }
  .chart-wrap { position: relative; height: 380px; }
</style>
</head>
<body>
  <a class="back" href="../index.php">&larr; Volver</a>
  <h1>Gráfica 1: Evolución de contrataciones por año y género</h1>
  <p class="desc">
    Contrataciones por año, apiladas y coloreadas por género, para visualizar
    la tendencia de contratación y la evolución de la diversidad de género.
  </p>

  <div class="chart-card">
    <div class="chart-wrap">
      <canvas id="chartContrataciones"></canvas>
    </div>
  </div>

  <script>
    const labels = <?= json_encode($labels) ?>;
    const masculino = <?= json_encode($masculino) ?>;
    const femenino = <?= json_encode($femenino) ?>;

    const style = getComputedStyle(document.documentElement);
    const colorMasculino = style.getPropertyValue('--series-masculino').trim();
    const colorFemenino = style.getPropertyValue('--series-femenino').trim();
    const colorGrid = style.getPropertyValue('--grid').trim();
    const colorMuted = style.getPropertyValue('--ink-muted').trim();
    const colorInk = style.getPropertyValue('--ink').trim();

    new Chart(document.getElementById('chartContrataciones'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Masculino',
            data: masculino,
            backgroundColor: colorMasculino,
            borderRadius: 4,
            borderSkipped: false,
            stack: 'contrataciones'
          },
          {
            label: 'Femenino',
            data: femenino,
            backgroundColor: colorFemenino,
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
            labels: { color: colorInk, boxWidth: 12, boxHeight: 12 }
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
            ticks: { color: colorMuted }
          },
          y: {
            stacked: true,
            beginAtZero: true,
            grid: { color: colorGrid },
            ticks: { color: colorMuted, precision: 0 },
            title: { display: true, text: 'Número de contrataciones', color: colorMuted }
          }
        }
      }
    });
  </script>
</body>
</html>