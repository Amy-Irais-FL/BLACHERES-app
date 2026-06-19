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

include "../conexion.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = $data["id"] ?? null;

if(!$id){
    echo "ID_INVALIDO";
    exit;
}

// ===== BUSCAR REGISTRO =====
$res = pg_query_params($conn,
    "SELECT *,
        TO_CHAR(created_at AT TIME ZONE 'America/Mexico_City','DD/MM/YYYY') AS fecha,
        TO_CHAR(created_at AT TIME ZONE 'America/Mexico_City','HH24:MI') AS hora
     FROM registros
     WHERE id = $1",
    [$id]
);

if($row = pg_fetch_assoc($res)){

    $solicitante = $row["solicitante"];
    $urgencia = $row["urgencia"];
    $origen = $row["origen"];
    $observaciones = $row["observaciones"];

    $url = "https://script.google.com/macros/s/AKfycbx3jLuiA8jX7oMVipXgKgs5VhgKi7M_RnYm7edOYbJmUxN9mNKbtmQH7FJSqPYAx6NL/exec";

$datosCorreo = [
    "token" => "BLACHERE_2026",
    "tipo" => "no_realizada",
    "solicitante" => $solicitante,
    "urgencia" => $urgencia,
    "origen" => $origen,
    "observaciones" => $observaciones,
    "fecha" => $row["fecha"],
    "hora" => $row["hora"]
];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($datosCorreo)
    ]
];

$context = stream_context_create($options);

$respuesta = @file_get_contents(
    $url,
    false,
    $context
);

if(trim($respuesta) === "OK"){
    echo "CORREO_ENVIADO";
}else{
    echo "ERROR_CORREO";
}

} else {
    echo "NO_ENCONTRADO";
}

pg_close($conn);
?>