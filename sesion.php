<?php
header("Access-Control-Allow-Origin: https://blacheres-app.onrender.com");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

session_start();
header('Content-Type: application/json');

if(isset($_SESSION["usuario"])){

    echo json_encode([
        "estado" => "OK",
        "usuario" => $_SESSION["usuario"],
        "tipo" => $_SESSION["tipo"],
        "id" => $_SESSION["id"]
    ]);

}else{

    echo json_encode([
        "estado" => "NO"
    ]);
}
?>