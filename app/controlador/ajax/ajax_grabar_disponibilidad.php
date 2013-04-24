<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();

$opcion=$_POST[OPCION];

if($opcion=='Agregar'){
$asis_dispo[IDASISTENCIA]=$_POST[IDASISTENCIA];
//$asis_bitacora[IDETAPA]=$_POST[IDETAPA];
$asis_dispo[FECHAINI]=$_POST[FECHA];
$asis_dispo[FECHAFIN]=$_POST[FECHA2];

$asis_dispo[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

$con->insert_reg("$con->temporal.asistencia_disponibilidad_afiliado",$asis_dispo);
}
elseif($opcion=='Guardar Edicion'){
$asis_dispo[IDASISTENCIA]=$_POST[IDASISTENCIA];
//$asis_bitacora[IDETAPA]=$_POST[IDETAPA];
$asis_dispo[FECHAINI]=$_POST[FECHA];
$asis_dispo[FECHAFIN]=$_POST[FECHA2];
$con->update("$con->temporal.asistencia_disponibilidad_afiliado",$asis_dispo," WHERE ID =$_POST[IDDISPO]");

}
return;
?>
