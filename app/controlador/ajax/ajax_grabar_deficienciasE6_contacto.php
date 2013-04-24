<?
include_once('../../modelo/clase_mysqli.inc.php');

$idasigprov = $_POST[IDASIGPROV];
$idasistencia= $_POST[IDASISTENCIA];
$idexpediente= $_POST[IDEXPEDIENTE];
$deficiencia ='';


$con = new DB_mysqli();

/* DETERMINA EXPEDIENTE, RESPONSABLE y SERVICIO  */
$sql="
SELECT 
	IDEXPEDIENTE,
	IDUSUARIORESPONSABLE,
	IDSERVICIO 
FROM 
	$con->temporal.asistencia 
WHERE 
	IDASISTENCIA = '$idasistencia'
";

$result = $con->query($sql);
if($reg=$result->fetch_object())
{
	$idexpediente=$reg->IDEXPEDIENTE;
	$responsable = $reg->IDUSUARIORESPONSABLE;
	$servicio =$reg->IDSERVICIO;
}

/*  datos para las deficiencias */
$def[IDEXPEDIENTE]=$idexpediente;
$def[IDASISTENCIA]=$idasistencia;
$def[ORIGEN]='AUTOMATICA';

/* CALCULA DEFICIENCIA DE TAREA DE CONTACTO */
$sql="
SELECT 
 (( UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(mt.FECHATAREA)) DIV 60 )  + 
 ((( UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(mt.FECHATAREA)) % 60 ) / 100) MINUTOS,     
 mt.IDTAREA,
 mt.STATUSTAREA, 
 ct.ENROJO
FROM 
	$con->temporal.monitor_tarea mt,
	$con->catalogo.catalogo_tarea ct
WHERE
	mt.idasistencia = '$_POST[IDASISTENCIA]'
	AND ct.IDTAREA = mt.IDTAREA
	AND ct.IDTAREA ='CONT_AFIL'
	AND mt.STATUSTAREA IN ('PENDIENTE','INVISIBLE')
";
//echo $sql;
$result=$con->query($sql);

while($reg=$result->fetch_object())
{
	$tiempo = $reg->MINUTOS;
	$servicio = $reg->SERVICIO;
}

if ($tiempo > 6) $deficiencia='CA5';

if ($deficiencia!='')
{
	$def[CVEDEFICIENCIA]=$deficiencia;
	$def[IDSUPERVISOR]=$responsable;
	$def[IDCOORDINADOR]=$responsable;
	$def[ORIGEN]='AUTOMATICA';
	$def[IDETAPA]=6;
	$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
}

/* CAMBIA DE STATUS LAS TAREAS */
$sql="
update
	$con->temporal.monitor_tarea 
set
   STATUSTAREA='ATENDIDA' 
WHERE 
  	IDASISTENCIA='$idasistencia' 
  	AND STATUSTAREA in ('PENDIENTE','INVISIBLE')
  	";

$con->query($sql);






?>



