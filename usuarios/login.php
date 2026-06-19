<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

session_start();

include "../conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$usuario = trim($data["usuario"] ?? "");
$password = trim($data["password"] ?? "");

function login($conn, $tabla, $tipo, $usuario, $password){

    $res = pg_query_params($conn,
        "SELECT * FROM $tabla WHERE usuario = $1",
        [$usuario]
    );

    if($row = pg_fetch_assoc($res)){

        if($password === $row["password"]){

            $_SESSION["usuario"] = $row["usuario"];
            $_SESSION["tipo"] = $tipo;
            $_SESSION["id"] = $row["id"];

            echo json_encode([
                "estado" => "OK",
                "usuario" => $row["usuario"],
                "tipo" => $tipo,
                "id" => $row["id"]
            ]);

            exit;
        }
    }
}

login($conn, "usuarios", "admin", $usuario, $password);

login($conn, "usuarios2", "usuario2", $usuario, $password);

echo json_encode([
    "estado" => "ERROR"
]);

?>