<?php
// Conexión a la base de datos employeesdb
// En el servidor de despliegue, sobreescribir estos valores o usar variables de entorno.

$DB_HOST = 'localhost';
$DB_NAME = 'employeesdb';
$DB_USER = 'root';
$DB_PASS = 'America14';

function getConnection(): mysqli
{
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;

    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

    if ($conn->connect_error) {
        die('Error de conexión a la base de datos: ' . $conn->connect_error);
    }

    $conn->set_charset('utf8mb4');

    return $conn;
}
