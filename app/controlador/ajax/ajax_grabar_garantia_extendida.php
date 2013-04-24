<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();


$con->insert_update("$con->temporal.garantia_extendida",$_POST);


?>