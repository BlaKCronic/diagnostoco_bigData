<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

$pageTitle = 'Gráfica 3 · Número de empleados por departamento';
$section = 'grafica';
$active = 3;

$sql = "SELECT d.dept_name AS departamento, COUNT(*) AS total_empleados
        FROM dept_emp de
        JOIN departments d ON d.dept_no = de.dept_no
        WHERE de.to_date = '9999-01-01'
        GROUP BY d.dept_name
        ORDER BY total_empleados DESC";

$filas = $pdo->query($sql)->fetchAll();
$maxValue = 0;
foreach ($filas as &$fila) {
    $fila['total_empleados'] = (int) $fila['total_empleados'];
    $maxValue = max($maxValue, $fila['total_empleados']);
}
unset($fila);

// --- Escala del eje X: paso "bonito" y máximo redondeado hacia arriba ---
function niceAxisScale(int $maxValue, int $targetTicks = 5): array
{
    if ($maxValue <= 0) {
        return ['step' => 1, 'max' => 1];
    }
    $roughStep = $maxValue / $targetTicks;
    $magnitude = 10 ** floor(log10($roughStep));
    $residual = $roughStep / $magnitude;

    if ($residual >= 7.5) {
        $niceResidual = 10;
    } elseif ($residual >= 3.5) {
        $niceResidual = 5;
    } elseif ($residual >= 1.5) {
        $niceResidual = 2;
    } else {
        $niceResidual = 1;
    }

    $step = $niceResidual * $magnitude;
    $axisMax = ceil($maxValue / $step) * $step;

    return ['step' => (int) $step, 'max' => (int) $axisMax];
}

$scale = niceAxisScale($maxValue);
$axisMax = $scale['max'];
$axisStep = $scale['step'];

// --- Geometría del gráfico (barras horizontales) ---
$barThickness = 22;
$barGap = 18;
$rowHeight = $barThickness + $barGap;
$marginLeft = 170;
$marginRight = 70;
$marginTop = 16;
$marginBottom = 32;
$plotWidth = 560;
$plotHeight = count($filas) * $rowHeight;
$svgWidth = $marginLeft + $plotWidth + $marginRight;
$svgHeight = $marginTop + $plotHeight + $marginBottom;

function xForValue(int $value, int $axisMax, int $plotWidth): float
{
    return $axisMax > 0 ? ($value / $axisMax) * $plotWidth : 0;
}

require __DIR__ . '/../includes/header.php';
?>

<div class="card">
    <h2>Número de empleados por departamento</h2>
    <p class="subtitle">Tamaño de cada departamento (empleados con asignación vigente) — útil para planificar recursos y espacio. <a href="../reportes/3.php">Ver como tabla</a>.</p>

    <div class="chart-wrap">
        <svg
            class="dept-chart"
            viewBox="0 0 <?php echo $svgWidth; ?> <?php echo $svgHeight; ?>"
            role="img"
            aria-label="Número de empleados por departamento"
        >
            <!-- Gridlines verticales -->
            <?php for ($tick = 0; $tick <= $axisMax; $tick += $axisStep):
                $x = $marginLeft + xForValue($tick, $axisMax, $plotWidth);
            ?>
                <line
                    x1="<?php echo $x; ?>" y1="<?php echo $marginTop; ?>"
                    x2="<?php echo $x; ?>" y2="<?php echo $marginTop + $plotHeight; ?>"
                    class="gridline"
                />
                <text
                    x="<?php echo $x; ?>" y="<?php echo $marginTop + $plotHeight + 20; ?>"
                    class="axis-label" text-anchor="middle"
                ><?php echo number_format($tick); ?></text>
            <?php endfor; ?>

            <!-- Barras -->
            <?php foreach ($filas as $i => $fila):
                $y = $marginTop + $i * $rowHeight;
                $barW = xForValue($fila['total_empleados'], $axisMax, $plotWidth);
                $labelText = $fila['departamento'] . ': ' . number_format($fila['total_empleados']) . ' empleados';
            ?>
                <text
                    x="<?php echo $marginLeft - 12; ?>" y="<?php echo $y + $barThickness / 2; ?>"
                    class="dept-label" text-anchor="end" dominant-baseline="middle"
                ><?php echo htmlspecialchars($fila['departamento']); ?></text>

                <rect
                    class="bar-hit"
                    x="<?php echo $marginLeft; ?>" y="<?php echo $y; ?>"
                    width="<?php echo $plotWidth; ?>" height="<?php echo $barThickness; ?>"
                    fill="transparent"
                    tabindex="0"
                    data-departamento="<?php echo htmlspecialchars($fila['departamento']); ?>"
                    data-valor="<?php echo (int) $fila['total_empleados']; ?>"
                    aria-label="<?php echo htmlspecialchars($labelText); ?>"
                ></rect>

                <rect
                    class="bar"
                    x="<?php echo $marginLeft; ?>" y="<?php echo $y; ?>"
                    width="<?php echo max($barW, 1); ?>" height="<?php echo $barThickness; ?>"
                    rx="4" ry="4"
                    data-departamento="<?php echo htmlspecialchars($fila['departamento']); ?>"
                    data-valor="<?php echo (int) $fila['total_empleados']; ?>"
                ></rect>

                <text
                    x="<?php echo $marginLeft + $barW + 8; ?>" y="<?php echo $y + $barThickness / 2; ?>"
                    class="value-label" dominant-baseline="middle"
                ><?php echo number_format($fila['total_empleados']); ?></text>
            <?php endforeach; ?>

            <!-- Línea base -->
            <line
                x1="<?php echo $marginLeft; ?>" y1="<?php echo $marginTop; ?>"
                x2="<?php echo $marginLeft; ?>" y2="<?php echo $marginTop + $plotHeight; ?>"
                class="baseline"
            />
        </svg>

        <div id="chart-tooltip" class="chart-tooltip" hidden></div>
    </div>
</div>

<style>
    .chart-wrap {
        position: relative;
        overflow-x: auto;
    }

    svg.dept-chart {
        width: 100%;
        height: auto;
        font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    }

    .gridline {
        stroke: var(--gridline);
        stroke-width: 1;
    }

    .baseline {
        stroke: var(--baseline);
        stroke-width: 1;
    }

    .axis-label {
        fill: var(--text-muted);
        font-size: 11px;
    }

    .dept-label {
        fill: var(--text-secondary);
        font-size: 13px;
    }

    .value-label {
        fill: var(--text-primary);
        font-size: 12px;
        font-variant-numeric: tabular-nums;
    }

    .bar {
        fill: var(--series-1);
        transition: fill 0.1s ease;
        pointer-events: none;
    }

    .bar-hit {
        cursor: pointer;
    }

    .bar-hit:hover + .bar,
    .bar-hit:focus + .bar {
        fill: var(--series-1);
        filter: brightness(1.12);
    }

    .bar-hit:focus {
        outline: 2px solid var(--series-1);
        outline-offset: 2px;
    }

    .chart-tooltip {
        position: absolute;
        pointer-events: none;
        background: var(--surface-1);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 13px;
        color: var(--text-secondary);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transform: translate(-50%, -110%);
        white-space: nowrap;
    }

    .chart-tooltip strong {
        color: var(--text-primary);
        font-variant-numeric: tabular-nums;
    }
</style>

<script>
(function () {
    var svg = document.querySelector('svg.dept-chart');
    var tooltip = document.getElementById('chart-tooltip');
    var wrap = document.querySelector('.chart-wrap');

    function showTooltip(target, clientX, clientY) {
        var dept = target.getAttribute('data-departamento');
        var valor = Number(target.getAttribute('data-valor')).toLocaleString('es-MX');

        tooltip.innerHTML = '';
        var deptNode = document.createTextNode(dept + ': ');
        var strong = document.createElement('strong');
        strong.textContent = valor + ' empleados';
        tooltip.appendChild(deptNode);
        tooltip.appendChild(strong);

        var wrapRect = wrap.getBoundingClientRect();
        tooltip.style.left = (clientX - wrapRect.left) + 'px';
        tooltip.style.top = (clientY - wrapRect.top) + 'px';
        tooltip.hidden = false;
    }

    function hideTooltip() {
        tooltip.hidden = true;
    }

    svg.querySelectorAll('.bar-hit').forEach(function (el) {
        el.addEventListener('pointermove', function (e) {
            showTooltip(el, e.clientX, e.clientY);
        });
        el.addEventListener('pointerenter', function (e) {
            showTooltip(el, e.clientX, e.clientY);
        });
        el.addEventListener('pointerleave', hideTooltip);
        el.addEventListener('focus', function () {
            var rect = el.getBoundingClientRect();
            showTooltip(el, rect.left + rect.width / 2, rect.top);
        });
        el.addEventListener('blur', hideTooltip);
    });
})();
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
