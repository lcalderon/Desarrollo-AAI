<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_proveedor.inc.php');

$prov= new proveedor();

$prov->grabar_horario($_POST);
//var_dump($_POST);
return;
?>