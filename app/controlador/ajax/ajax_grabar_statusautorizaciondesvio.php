<?
/*  cambia a activo el STATUSAUTORIZACIONDESVIO si no hay proveedores pendientes */
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_asistencia.inc.php');

$asis = new asistencia();

if ($asis->validar_statusautorizaciondesvio($_POST[IDASISTENCIA]))
{
	$asis->activa_statusautorizaciondesvio($_POST[IDASISTENCIA]);
}
else echo "Error";

?>