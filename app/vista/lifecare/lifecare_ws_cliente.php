<?php

  $client = new  SoapClient("http://www.lifecare.cl/web_service/server_ws.php?wsdl");

date_default_timezone_set('America/Santiago');

$idPaciente='6';
$nombreTabla='tbl_paciente';
$nombreCampo='apellido_paterno_paciente';
$valorCampo= 'SANTIS1'; 
$fecha_encrypts=md5(date("HdmY"));  

 

 $response =$client->actualiza($idPaciente,$nombreTabla,$nombreCampo,$valorCampo,$fecha_encrypts);

echo $response;

?>
