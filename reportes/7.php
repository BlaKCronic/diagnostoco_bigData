<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

$empleado = null;
$salarios = [];
$departamentos = [];
$puestos = [];

if (isset($_GET['emp_no']) && $_GET['emp_no'] !== '') {

    $emp_no = (int) $_GET['emp_no'];

    /*
     * DATOS GENERALES DEL EMPLEADO
     */
    $sql = "SELECT
                e.emp_no,
                e.birth_date,
                e.first_name,
                e.last_name,
                e.gender,
                e.hire_date,

                s.salary AS salario_actual,

                t.title AS puesto_actual,

                d.dept_name AS departamento_actual

            FROM employees e

            LEFT JOIN salaries s
                ON e.emp_no = s.emp_no
                AND s.to_date = '9999-01-01'

            LEFT JOIN titles t
                ON e.emp_no = t.emp_no
                AND t.to_date = '9999-01-01'

            LEFT JOIN dept_emp de
                ON e.emp_no = de.emp_no
                AND de.to_date = '9999-01-01'

            LEFT JOIN departments d
                ON de.dept_no = d.dept_no

            WHERE e.emp_no = :emp_no";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['emp_no' => $emp_no]);

    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);


    /*
     * HISTORIAL DE SALARIOS
     */
    if ($empleado) {

        $sql = "SELECT
                    salary,
                    from_date,
                    to_date
                FROM salaries
                WHERE emp_no = :emp_no
                ORDER BY from_date DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['emp_no' => $emp_no]);

        $salarios = $stmt->fetchAll(PDO::FETCH_ASSOC);


        /*
         * HISTORIAL DE DEPARTAMENTOS
         */
        $sql = "SELECT
                    d.dept_name,
                    de.from_date,
                    de.to_date
                FROM dept_emp de

                INNER JOIN departments d
                    ON de.dept_no = d.dept_no

                WHERE de.emp_no = :emp_no
                ORDER BY de.from_date DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['emp_no' => $emp_no]);

        $departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);


        /*
         * HISTORIAL DE PUESTOS
         */
        $sql = "SELECT
                    title,
                    from_date,
                    to_date
                FROM titles
                WHERE emp_no = :emp_no
                ORDER BY from_date DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['emp_no' => $emp_no]);

        $puestos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Consulta de empleados</title>

    <link rel="stylesheet" href="../css/estilo.css">

</head>
<style>
    .search-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .search-container input {
        width: 250px;
    }

    .employee-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-top: 20px;
    }

    .employee-info div {
        padding: 16px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }

    .employee-info strong {
        display: block;
        margin-bottom: 6px;
        color: #6b7280;
        font-size: 13px;
    }

    .employee-info span {
        color: #111827;
        font-size: 15px;
        font-weight: 600;
    }

    @media (max-width: 700px) {

        .employee-info {
            grid-template-columns: 1fr;
        }

        .search-container {
            flex-direction: column;
        }

        .search-container input {
            width: 100%;
        }

        .search-container button {
            width: 100%;
        }
    }
</style>

<body>

    <h1>Consulta de empleados</h1>

    <nav>
        <a href="../index.php">Volver al inicio</a>
    </nav>


    <!-- BUSCADOR -->

    <div class="card">

        <h2>Buscar empleado</h2>

        <p class="desc">
            Introduce el número de empleado para consultar toda su información.
        </p>

        <form method="GET">

            <div class="search-container">

                <input type="number" name="emp_no" placeholder="Número de empleado"
                    value="<?= isset($_GET['emp_no']) ? htmlspecialchars($_GET['emp_no']) : '' ?>" required>

                <button type="submit">
                    Buscar
                </button>

            </div>

        </form>

    </div>


    <?php if (isset($_GET['emp_no']) && !$empleado): ?>

        <div class="card">

            <p class="empty">
                No se encontró ningún empleado con el número
                <strong><?= htmlspecialchars($_GET['emp_no']) ?></strong>.
            </p>

        </div>

    <?php endif; ?>


    <?php if ($empleado): ?>

        <!-- DATOS GENERALES -->

        <div class="card">

            <h2>
                <?= htmlspecialchars($empleado['first_name']) ?>
                <?= htmlspecialchars($empleado['last_name']) ?>
            </h2>

            <div class="employee-info">

                <div>
                    <strong>Número de empleado</strong>
                    <span><?= $empleado['emp_no'] ?></span>
                </div>

                <div>
                    <strong>Fecha de nacimiento</strong>
                    <span><?= htmlspecialchars($empleado['birth_date']) ?></span>
                </div>

                <div>
                    <strong>Género</strong>
                    <span>
                        <?= $empleado['gender'] === 'M' ? 'Masculino' : 'Femenino' ?>
                    </span>
                </div>

                <div>
                    <strong>Fecha de contratación</strong>
                    <span><?= htmlspecialchars($empleado['hire_date']) ?></span>
                </div>

                <div>
                    <strong>Departamento actual</strong>
                    <span>
                        <?= htmlspecialchars($empleado['departamento_actual'] ?? 'Sin departamento') ?>
                    </span>
                </div>

                <div>
                    <strong>Puesto actual</strong>
                    <span>
                        <?= htmlspecialchars($empleado['puesto_actual'] ?? 'Sin puesto') ?>
                    </span>
                </div>

                <div>
                    <strong>Salario actual</strong>
                    <span>
                        $<?= number_format((float) $empleado['salario_actual'], 2) ?>
                    </span>
                </div>

            </div>

        </div>


        <!-- HISTORIAL DE SALARIOS -->

        <div class="card">

            <h2>Historial de salarios</h2>

            <div class="table-container">

                <table>

                    <thead>

                        <tr>
                            <th>Salario</th>
                            <th>Desde</th>
                            <th>Hasta</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($salarios as $salario): ?>

                            <tr>

                                <td>
                                    $<?= number_format((float) $salario['salary'], 2) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($salario['from_date']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($salario['to_date']) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- HISTORIAL DE DEPARTAMENTOS -->

        <div class="card">

            <h2>Historial de departamentos</h2>

            <div class="table-container">

                <table>

                    <thead>

                        <tr>
                            <th>Departamento</th>
                            <th>Desde</th>
                            <th>Hasta</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($departamentos as $departamento): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($departamento['dept_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($departamento['from_date']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($departamento['to_date']) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- HISTORIAL DE PUESTOS -->

        <div class="card">

            <h2>Historial de puestos</h2>

            <div class="table-container">

                <table>

                    <thead>

                        <tr>
                            <th>Puesto</th>
                            <th>Desde</th>
                            <th>Hasta</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($puestos as $puesto): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($puesto['title']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($puesto['from_date']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($puesto['to_date']) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    <?php endif; ?>


</body>

</html>