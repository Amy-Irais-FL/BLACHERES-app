<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');

include "conexion.php";

// consulta 
$result = pg_query($conn, "
SELECT *,
  created_at::date AS fecha,
  created_at::time AS hora
FROM registros
");
$datos = [];
while($row = pg_fetch_assoc($result)){
    $datos[] = $row;
}

echo json_encode($datos);

pg_close($conn);
?>