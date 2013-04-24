<?

/*
AJAX BORRA UNA TAREA SI SE LE PASA EL ID, O BORRA TODAS LAS TAREAS DE UNA ASISTENCIA
*/


include_once('../../modelo/clase_mysqli.inc.php');
$con = 	new  DB_mysqli();
$sql="
UPDATE
  $con->temporal.monitor_tarea
SET
  STATUSTAREA ='INVISIBLE'
WHERE
  ID = '$_POST[ID]'
";
$con->query($sql);



//include_once('../../modelo/clase_monitortarea.inc.php');
//$tarea= new monitortarea();
//
//if (isset($_POST[ID])) $tarea->borrar_tarea($_POST[ID]);
//if (isset($_POST[IDASISTENCIA])) $tarea->borrar_tarea_asistencia($_POST[IDASISTENCIA],$_POST[IDTAREA]);



?>
