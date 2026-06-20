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
$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo "No llegaron datos";
    exit;
}
$solicitante = $data['solicitante'] ?? "";
$urgencia = $data['urgencia'] ?? "";
$origen = $data['origen'] ?? "";
$observaciones = $data['observaciones'] ?? "";

function capitalizar($texto){
    return ucfirst(mb_strtolower($texto, "UTF-8"));
}
$solicitante = capitalizar($solicitante);
$observaciones = capitalizar($observaciones);

$observacion_general = "";
$fecha_manual = NULL;
$hora_manual = NULL;
$estado = "Sin empezar";

$result = pg_query_params($conn,
    "INSERT INTO registros 
    (solicitante, urgencia, origen, observaciones, observacion_general, fecha_manual, hora_manual, estado)
    VALUES ($1,$2,$3,$4,$5,$6,$7,$8)",
    [
        $solicitante,
        $urgencia,
        $origen,
        $observaciones,
        $observacion_general,
        $fecha_manual,
        $hora_manual,
        $estado
    ]
);

if ($result) {
        $url = "https://script.google.com/macros/s/AKfycbx3jLuiA8jX7oMVipXgKgs5VhgKi7M_RnYm7edOYbJmUxN9mNKbtmQH7FJSqPYAx6NL/exec";

    $datosCorreo = [
        "token" => "BLACHERE_2026",
        "solicitante" => $solicitante,
        "urgencia" => $urgencia,
        "origen" => $origen,
        "observaciones" => $observaciones
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
        echo "OK";
    }else{
        echo "ERROR_CORREO";
        
    }
} else {
    echo "Error BD";
}

pg_close($conn);
?>
