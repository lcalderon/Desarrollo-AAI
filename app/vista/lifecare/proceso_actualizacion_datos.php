<?php
include_once('/var/www/html/soaang_pruebas/app/modelo/clase_lang.inc.php');
include_once('/var/www/html/soaang_pruebas/app/modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

date_default_timezone_set('America/Santiago');
echo 'Iniciando el proceso'.'<br>';

$sql ="select IDACTUALIZACION,IDPACIENTE,NOMBRETABLA,NOMBRECAMPO,VALOR from $con->temporal.actualizaciones_lifecare where ESTADO in ('','ERROR')";

$result_lectura = $con->query($sql);

while($reg= $result_lectura->fetch_object()){
	$idactualizacion=$reg->IDACTUALIZACION;
	$idPaciente=$reg->IDPACIENTE;
	$nombreTabla=$reg->NOMBRETABLA;
	$nombreCampo=$reg->NOMBRECAMPO;
	$valorCampo=$reg->VALOR;
	$fecha_encrypts=md5(date("HdmY"));

	$client = new  SoapClient("http://www.lifecare.cl/web_service/server_ws.php?wsdl");
	$response =$client->actualiza($idPaciente,$nombreTabla,$nombreCampo,$valorCampo,$fecha_encrypts);

	switch($response){
		case 1:
			// Actualizar la tabla actualizaciones lifecare
			$sql ="update $con->temporal.actualizaciones_lifecare set ESTADO='OK' where IDACTUALIZACION='$idactualizacion' ";
			//echo $sql;
			$con->query($sql);
			break;
		case 0: 
			// no hacer nada
			$sql ="update $con->temporal.actualizaciones_lifecare set ESTADO='ERROR' where IDACTUALIZACION='$idactualizacion' ";
			//echo $sql;
			$con->query($sql);
			break;

	};

}
echo 'Fin del proceso';
?>


