<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

$sql = "SELECT YEAR(hire_date) AS anio, gender, COUNT(*) AS total
        FROM employees
        GROUP BY YEAR(hire_date), gender
        ORDER BY anio, gender";
$rows = $pdo->query($sql)->fetchAll();

$generoLabel = ['M' => 'Masculino', 'F' => 'Femenino'];
$totalGeneral = array_sum(array_column($rows, 'total'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reporte 1 &middot; Evolución de contrataciones por año y género</title>
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
  table {
    border-collapse: collapse;
    width: 100%;
    max-width: 640px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
  }
  th, td {
    text-align: left;
    padding: 10px 16px;
    border-bottom: 1px solid var(--grid);
    font-variant-numeric: tabular-nums;
  }
  th {
    color: var(--ink-muted);
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }
  td:last-child, th:last-child { text-align: right; }
  tfoot td {
    font-weight: 700;
    border-bottom: none;
    border-top: 2px solid var(--grid);
  }
  tbody tr:last-child td { border-bottom: none; }
</style>
</head>
<body>
  <a class="back" href="../index.php">&larr; Volver</a>
  <h1>Reporte 1: Evolución de contrataciones por año y género</h1>
  <p class="desc">
    Identifica las tendencias de contratación y la evolución de la diversidad
    de género en la empresa a lo largo del tiempo.
  </p>
  <table>
    <thead>
      <tr>
        <th>Año</th>
        <th>Género</th>
        <th>Total de contrataciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars((string) $r['anio']) ?></td>
          <td><?= htmlspecialchars($generoLabel[$r['gender']] ?? $r['gender']) ?></td>
          <td><?= htmlspecialchars((string) $r['total']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">Total</td>
        <td><?= htmlspecialchars((string) $totalGeneral) ?></td>
      </tr>
    </tfoot>
  </table>
</body>
</html>