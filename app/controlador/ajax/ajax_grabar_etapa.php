<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();

//  actualiza la etapa en la tabla ASISTENCIA
$asis[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis[IDETAPA]=$_POST[IDETAPA];
$con->update("$con->temporal.asistencia",$asis," WHERE IDASISTENCIA='$asis[IDASISTENCIA]'");


$asis_asig_proveedor[IDASIGPROV] = $_POST[IDASIGPROV];
$asis_asig_proveedor[STATUSPROVEEDOR] = $_POST[STATUSPROVEEDOR];
$asis_asig_proveedor[IDUSUARIOMOD] = $_POST[IDUSUARIOMOD];
$asis_asig_proveedor[FECHACONCLUIDO] = 'CURRENT_TIMESTAMP()';

$con->update("$con->temporal.asistencia_asig_proveedor",$asis_asig_proveedor," WHERE IDASIGPROV='$asis_asig_proveedor[IDASIGPROV]'");

$asis_usuario[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_usuario[IDUSUARIO]=$_POST[IDUSUARIOMOD];
$asis_usuario[IDETAPA]=$_POST[IDETAPA]-1;

$con->insert_reg("$con->temporal.asistencia_usuario",$asis_usuario);

$asis_asig_proveedor_tym[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_asig_proveedor_tym[IDASIGPROV]=$_POST[IDASIGPROV];
$asis_asig_proveedor_tym[MOVIMIENTO]='CONCLUIDO';
$asis_asig_proveedor_tym[IDUSUARIO] = $_POST[IDUSUARIOMOD];
$asis_asig_proveedor_tym[FECHAHORA] = 'CURRENT_TIMESTAMP()';
$con->insert_reg("$con->temporal.asistencia_asig_proveedor_tym",$asis_asig_proveedor_tym);


return;
?>