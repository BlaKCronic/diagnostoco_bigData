<?php
// Plantilla de configuración de base de datos.
// Copia este archivo como config.php (ignorado por git, ver .gitignore) y
// coloca ahí tus credenciales reales -- locales o del servidor de despliegue.
declare(strict_types=1);
$DB_HOST = 'localhost';
$DB_NAME = 'employeesdb';
$DB_USER = 'root';
$DB_PASS = '';
$DB_PORT = '3306';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('Error de conexión a la base de datos: ' . htmlspecialchars($e->getMessage()));
}
