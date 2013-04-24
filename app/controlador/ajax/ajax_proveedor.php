<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_proveedor.inc.php');
$prov = new proveedor();




echo $prov->grabar($_POST);


?>