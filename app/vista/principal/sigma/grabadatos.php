<?php 
session_start();
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
$idusuario=$_SESSION['user'];

$idasistencia = $_POST['idasistencia'];
$fecha = $_POST['fecha'];
$comentario =$_POST['comentario'];
$citas =$_POST['citas'];
$monitoreo =$_POST['monitoreo'];
$lugar =$_POST['lugar'];
$clinica =$_POST['clinica'];

$con = new DB_mysqli();


$sql="update  $con->temporal.asistencia_medica_controlcitas  set FECHACITA='$fecha', IDUSUARIOMOD='SIGMA'  where IDASISTENCIA='$idasistencia'";
$con->query($sql);

	if($citas =="checked"){
			
		$resumen="Ingresar Cita \nLugar: ".$lugar."\nClinica: ".$clinica."\nComentario: ".$comentario;
		
	} else if($monitoreo =="checked"){
			
		$resumen="Ingresar Monitoreo :\n".$fecha."\n\n".$comentario;
		
	}

$sql="insert into  $con->temporal.asistencia_bitacora_etapa2
		set 
		IDASISTENCIA ='$idasistencia',             
		ARRCLASIFICACION ='BIT',          
		FECHAMOD = NOW(),         
		IDUSUARIOMOD = 'SIGMA',             
		COMENTARIO  ='$resumen',               
		IDPROVEEDOR =''"
		;               

$con->query($sql);


if ($con->errno==0) echo json_encode(array("status"=>true));
else echo json_encode(array("status"=>false));
?>