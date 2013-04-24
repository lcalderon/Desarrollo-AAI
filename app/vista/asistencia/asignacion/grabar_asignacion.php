<?php
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

$con->select_db($con->temporal);
$db1=$con->catalogo;

$teatcp = $_POST[txtTEATCP].' '.$_POST[cbhoraTEATCP].':'.$_POST[cbminutoTEATCP].':00';
$teamcp = $_POST[txtTEAMCP].' '.$_POST[cbhoraTEAMCP].':'.$_POST[cbminutoTEAMCP].':00';
$idasistencia = $_POST[hid_idasistencia];
$idservicio = $_POST[hid_servicio];
$localforaneo = $_POST[hid_localforaneo];
$servicio = $_POST[txtservicio];
if($idservicio==''){
  $idservicio=$servicio;
}
//echo $teatcp.' '.$teamcp;
$sql_servicio = "SELECT MARGENTEAT,MARGENTEAM FROM $db1.catalogo_servicio WHERE IDSERVICIO = $idservicio";
//echo $sql_servicio;
$exec_servicio = $con->query($sql_servicio);
while($rset_servicio=$exec_servicio->fetch_object())
{
    $margenteat = $rset_servicio->MARGENTEAT;
    $margenteam = $rset_servicio->MARGENTEAM;
}

$sql_teat_team = "SELECT '$teatcp' XTEATCP,'$teamcp' XTEAMCP,SUBDATE('$teatcp', INTERVAL $margenteat MINUTE) TEAT,
ADDDATE('$teamcp', INTERVAL $margenteam MINUTE) TEAM";
//echo $sql_teat_team;
$exec_teat_team = $con->query($sql_teat_team);
while($rset_teat_team=$exec_teat_team->fetch_object()){
  $teat=$rset_teat_team->TEAT;
  $team=$rset_teat_team->TEAM;
}
$asigprov[IDUSUARIOMOD]=$_SESSION['user'];
//$asigprov[IDPROVEEDOR]=$_POST[hid_idprov];
$asigprov[IDPROVEEDOR]=$_GET[idproveedor];
$asigprov[IDASISTENCIA]=$idasistencia;
$asigprov[LOCALFORANEO]=$localforaneo;
$asigprov[TEATCP]=$teatcp;
$asigprov[TEAMCP]=$teamcp;
$asigprov[TEAT]=$teat;
$asigprov[TEAM]=$team;
$asigprov[IDTIPOMOVPROV]='1';

$rowsu['STATUSPROVEEDOR'] = "CA";
$update=$con->update("asistencia_asig_proveedor",$rowsu,"WHERE IDASISTENCIA=".$idasistencia);
$respuesta=$con->insert_reg('asistencia_asig_proveedor',$asigprov);
 
	echo "<script language='javascript'>";
	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');"; 
	    $asisuser[IDASISTENCIA]=$idasistencia;
	    $asisuser[IDUSUARIO]=$_SESSION['user'];
	    $asisuser[IDETAPA]=2;
	   $con->insert_reg('asistencia_usuario',$asisuser);

	    $rows[IDETAPA]=3;
	//actualiza los datos
 	$resultado=$con->update("asistencia",$rows,"WHERE IDASISTENCIA=".$idasistencia);
	 echo "</script>";
//	echo "document.location.href='../monitoreo/monitoreo_proveedor.php?idasist=$asigprov[IDASISTENCIA]'";
//echo "parent.top.document.location.href = '../monitoreo/monitoreo_proveedor.php?idasist=$asigprov[IDASISTENCIA]';window.close()";
$monprovext = $con->lee_parametro('INTERVALO_MONITOR_EX');
$monprovint = $con->lee_parametro('INTERVALO_MONITOR_IN');


$sql_asignacion="SELECT A.IDEXPEDIENTE,A.IDASISTENCIA,AP.FECHAHORA,AP.TEAT,AP.TEAM,
ADDDATE(AP.TEAM, INTERVAL 1 MINUTE) ARRIBO,
ADDDATE(AP.TEAM, INTERVAL 3 MINUTE) CONTACTACION,
ADDDATE(AP.TEAM, INTERVAL 5 MINUTE) MONAFIL,
ROUND(((HOUR(TIMEDIFF(AP.TEAM,AP.TEAT))*60)+MINUTE(TIMEDIFF(AP.TEAM,AP.TEAT)))/2,0) TIEMPOMEDIA,
 ADDDATE(AP.TEAT, INTERVAL ROUND(((HOUR(TIMEDIFF(AP.TEAM,AP.TEAT))*60)+MINUTE(TIMEDIFF(AP.TEAM,AP.TEAT)))/2,0)  MINUTE) MEDIA,
TIMEDIFF(ADDDATE(AP.TEAT, INTERVAL ROUND(((HOUR(TIMEDIFF(AP.TEAM,AP.TEAT))*60)+MINUTE(TIMEDIFF(AP.TEAM,AP.TEAT)))/2,0)  MINUTE),AP.FECHAHORA) TIEMPOMON,
ROUND(((HOUR(TIMEDIFF(ADDDATE(AP.TEAT, INTERVAL ROUND(((HOUR(TIMEDIFF(AP.TEAM,AP.TEAT))*60)+MINUTE(TIMEDIFF(AP.TEAM,AP.TEAT)))/2,0)  MINUTE),AP.FECHAHORA))*60) +
MINUTE(TIMEDIFF(ADDDATE(AP.TEAT, INTERVAL ROUND(((HOUR(TIMEDIFF(AP.TEAM,AP.TEAT))*60)+MINUTE(TIMEDIFF(AP.TEAM,AP.TEAT)))/2,0)  MINUTE),AP.FECHAHORA)))/IF(P.INTERNO=0,$monprovext,$monprovint),0) MINMONIT ,
P.INTERNO
 FROM asistencia_asig_proveedor AP INNER JOIN 
 $db1.catalogo_proveedor P
 ON AP.IDPROVEEDOR = P.IDPROVEEDOR 
INNER JOIN asistencia A
ON A.IDASISTENCIA = AP.IDASISTENCIA 
WHERE AP.IDASISTENCIA = $idasistencia
AND AP.STATUSPROVEEDOR IN ('AC')";
//echo $sql_asignacion;
$exec_asignacion = $con->query($sql_asignacion);
while($rset_asignacion=$exec_asignacion->fetch_object()){
    $idexpediente = $rset_asignacion->IDEXPEDIENTE;
    $idasistencia = $rset_asignacion->IDASISTENCIA;
    $fechaasignacion = $rset_asignacion->FECHAHORA;
    $fechaarribo = $rset_asignacion->ARRIBO;
    $fechacontactacion = $rset_asignacion->CONTACTACION;
    $fechamonafil = $rset_asignacion->MONAFIL;
    $minutomonitoreo = $rset_asignacion->MINMONIT;
    $interno = $rset_asignacion->INTERNO;
}

if($interno==0){
  $nmonitoreo=$monprovext;
}else
{
  $nmonitoreo=$monprovint;
}
    for($i=1;$i<=$nmonitoreo;$i++){
	$sql_monitoreo="SELECT ADDDATE(FECHAHORA, INTERVAL $minutomonitoreo*$i MINUTE) MONITOREO FROM asistencia_asig_proveedor
	WHERE IDASISTENCIA = $idasistencia AND STATUSPROVEEDOR = 'AC'";

	$exec_monitoreo = $con->query($sql_monitoreo);
	if($rset_monitoreo=$exec_monitoreo->fetch_object()){
	    $rowtareaprov[IDTAREA]='MON_PROV';
	    $rowtareaprov[FECHATAREA]=$rset_monitoreo->MONITOREO;
	    $rowtareaprov[IDEXPEDIENTE]=$idexpediente;
	    $rowtareaprov[IDASISTENCIA]=$idasistencia;
	    $rowtareaprov[RECORDATORIO]=1;
	    $rowtareaprov[NUMMON]=$i;
	    $rowtareaprov[STATUSTAREA]='PENDIENTE';
	    $rowtareaprov[IDUSUARIO]=$_SESSION['user'];
	    $con->insert_reg('monitor_tarea',$rowtareaprov);
	}
	
    }
    



  $rowtarea[IDTAREA]='ARR_PROV';
  $rowtarea[FECHATAREA]=$fechaarribo;
  $rowtarea[IDEXPEDIENTE]=$idexpediente;
  $rowtarea[IDASISTENCIA]=$idasistencia;
  $rowtarea[STATUSTAREA]='PENDIENTE';
  $rowtarea[IDUSUARIO]=$_SESSION['user'];
  $con->insert_reg('monitor_tarea',$rowtarea);

  $rowtarea2[IDTAREA]='CON_PROV';
  $rowtarea2[FECHATAREA]=$fechacontactacion;
  $rowtarea2[IDEXPEDIENTE]=$idexpediente;
  $rowtarea2[IDASISTENCIA]=$idasistencia;
  $rowtarea2[RECORDATORIO]=1;
  $rowtarea2[STATUSTAREA]='PENDIENTE';
  $rowtarea2[IDUSUARIO]=$_SESSION['user'];
  $con->insert_reg('monitor_tarea',$rowtarea2);

  $rowtarea3[IDTAREA]='MON_AFIL';
  $rowtarea3[FECHATAREA]=$fechamonafil;
  $rowtarea3[IDEXPEDIENTE]=$idexpediente;
  $rowtarea3[IDASISTENCIA]=$idasistencia;
  $rowtarea3[RECORDATORIO]=1;
  $rowtarea3[STATUSTAREA]='PENDIENTE';
  $rowtarea3[IDUSUARIO]=$_SESSION['user'];
  $con->insert_reg('monitor_tarea',$rowtarea3);



echo "<script language='javascript'>";

echo "parent.top.document.location.href = '../../plantillas/etapa3.php?idasistencia=$asigprov[IDASISTENCIA]';";
	
	 echo "</script>";	

//agregamos monitoreos como tareas.






/*
echo "<script language='javascript'>alert('Proveedor Asignado con Exito'); window.location.href= 'proveedor_asignado.php?idasist='+$asigprov[IDASISTENCIA];</script>";	
*/

//header('location: proveedor_asignado.php?idasist='.$asigprov[NUMASISTENCIA]);

?>

