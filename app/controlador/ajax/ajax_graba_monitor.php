<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();
$con->insert_reg("$con->temporal.monitor_tarea",$_POST);

return;
?>
