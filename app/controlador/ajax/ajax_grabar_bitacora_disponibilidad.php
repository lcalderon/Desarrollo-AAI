<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();

if($_POST[COMENTARIO]!='')
{
$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_bitacora[ARRCLASIFICACION]=$_POST[ARRCLASIFICACION];
$asis_bitacora[COMENTARIO]=$_POST[COMENTARIO];
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

$con->insert_reg("$con->temporal.asistencia_bitacora_etapa3",$asis_bitacora);
}

/*  GRABAR  LA ETAPA DE MONITOREO */
$asis_usuario[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_usuario[IDUSUARIO]=$_POST[IDUSUARIOMOD];
$asis_usuario[IDETAPA]=3;
$con->insert_reg("$con->temporal.asistencia_usuario",$asis_usuario);

//  actualiza la etapa en la tabla ASISTENCIA
$asis[IDETAPA]=6;
$con->update("$con->temporal.asistencia",$asis," WHERE IDASISTENCIA='$_POST[IDASISTENCIA]'");

/* CAMBIO DE STATUS LA TAREA */

$sql="
update 
	$con->temporal.monitor_tarea 
set 
	STATUSTAREA='ATENDIDA' 
WHERE 
 	IDASISTENCIA='$_POST[IDASISTENCIA]' 
  	AND STATUSTAREA IN ('PENDIENTE','INVISIBLE' )
  	AND IDTAREA ='CONF_SERV'
  LIMIT 1
	  	";

$con->query($sql);

return;
?>