<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('America/Mexico_City');

$conn = pg_connect("host=aws-1-us-east-1.pooler.supabase.com port=6543 dbname=postgres user=postgres.qhqdxviaqpyzpgrxizol password=Blachere123! sslmode=require");

if (!$conn) {
    echo "Error de conexión";
    exit;
}

pg_query($conn, "SET TIME ZONE 'America/Mexico_City'");
?>