#!/usr/bin/php -q
<?
require_once("/var/lib/asterisk/agi-bin/phpagi/phpagi.php");

$dnid=$argv[1];

error_reporting(E_ALL);

$agi = new AGI();
$any = addslashes($agi->request[agi_callerid]);
$channel = addslashes($agi->request[agi_channel]);
//$dnid = addslashes($agi->request[agi_dnid]);


$conexion = new mysqli("192.168.0.188","pquispe","pquispe159","soaa_ng_temporal");
if (mysqli_connect_errno()) {
    printf("Fallo de conexion: %s\n", mysqli_connect_error());
    exit();
}

$query = "insert into soaa_ng_temporal.monitor_llamadas set TELEFONO ='$any',CHANNEL = '$channel', DNID= '$dnid', STATUS='ANSWER'" ;
$result =$conexion->query($query); 

//$query2 = "insert into dev_soaa_ng_temporal.monitor_llamadas set TELEFONO ='$any',CHANNEL = '$channel', DNID= '$dnid'" ;
//$result2 =$conexion->query($query2); 



///////// bucle de espera de la llamada


$query = "select CHANNEL, EXTENSION, FECHA+0 FECHAINI,FECHA+15 FECHAFIN from soaa_ng_temporal.monitor_llamadas  order by FECHA desc  limit 1";
$result =$conexion->query($query);
$reg =$result->fetch_object();
$contador=$reg->fechaini;

while ($reg->extension=='unknown')
{
        $query = "select CHANNEL,EXTENSION, FECHA+0 FECHAINI,FECHA+15 FECHAFIN from monitor_llamadas order by FECHA desc limit 1"; 
        $result =$conexion->query($query);
        $reg =$result->fetch_object();
        $contador++;
}

$agi->exec("DIAL IAX2/$reg->extension|30|to");


$mysqli->close();
?>

