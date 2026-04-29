<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

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
        created_at::date AS fecha,
        created_at::time AS hora
     FROM registros
     WHERE id = $1",
    [$id]
);

if($row = pg_fetch_assoc($res)){

    $solicitante = $row["solicitante"];
    $urgencia = $row["urgencia"];
    $origen = $row["origen"];
    $observaciones = $row["observaciones"];

    // ===== CORREO =====
    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'amy644224@gmail.com';
        $mail->Password = 'esbq htuq bmtj vubh';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('amy644224@gmail.com', 'Sistema Solicitudes');
        $mail->addAddress('amy644224@gmail.com');

        $mail->isHTML(true);
        $mail->Subject = 'Solicitud NO realizada';

        $mail->Body = "
            <h3>⚠️ Solicitud pendiente</h3>
            <p>La siguiente solicitud NO fue realizada:</p>
            <b>Solicitante:</b> $solicitante <br>
            <b>Urgencia:</b> $urgencia <br>
            <b>Tema:</b> $origen <br>
            <b>Observaciones:</b> $observaciones <br>
            <b>Fecha original:</b> {$row["fecha"]} {$row["hora"]}
        ";

        $mail->send();
        echo "OK";

    } catch (Exception $e) {
        echo "ERROR_MAIL";
    }

} else {
    echo "NO_ENCONTRADO";
}

pg_close($conn);
?>