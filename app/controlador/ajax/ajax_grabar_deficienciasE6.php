<?
include_once('../../modelo/clase_mysqli.inc.php');

$idasigprov = $_POST[IDASIGPROV];
$idproveedor = $_POST[IDPROVEEDOR];
$idasistencia= $_POST[IDASISTENCIA];
$idexpediente= $_POST[IDEXPEDIENTE];


$con = new DB_mysqli();

$idasistencia = $_POST[IDASISTENCIA];
$idjustificacion = $_POST[JUSTIFICACION];
$idproveedor = $_POST[IDPROVEEDOR];

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

/* CALCULA DEFICIENCIAS DE TAREAS PENDIENTES */
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
	AND mt.STATUSTAREA IN ('PENDIENTE','INVISIBLE')
";

$result=$con->query($sql);

while($reg=$result->fetch_object())
{
	$tiempo = $reg->MINUTOS;
	$deficiencia ='';
	$servicio = $reg->SERVICIO;
	switch ($reg->IDTAREA){
		
		case 'MON_PROV':  // MONITOREO AL PROVEEDOR
		{
			/* DETERMINA LA DEFICIENCIA */
			if ($tiempo >=6 && $tiempo<11) $deficiencia='CP6';
			elseif ($tiempo>=11) $deficiencia='CP5';

			if ($deficiencia!='')
			{

				$def[CVEDEFICIENCIA]=$deficiencia;
				$def[IDSUPERVISOR]=$responsable;  //QUE ES EL COORDINADOR
				$def[IDCOORDINADOR]=$responsable;  //QUE ES EL COORDINADOR
				$def[IDETAPA]=4;

				$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
			}

			break;
		}
		case 'MON_AFIL':
			{
				/* DETERMINAR LA DEFICIENCIA */
				if ($tiempo >=6 && $tiempo<11) $deficiencia='CP6';
				elseif ($tiempo>=11) $deficiencia='CA5';

				if ($deficiencia!='')
				{
					$def[CVEDEFICIENCIA]=$deficiencia;
					$def[IDSUPERVISOR]=$responsable;
					$def[IDCOORDINADOR]=$responsable;
					$def[ORIGEN]='AUTOMATICA';
					$def[IDETAPA]=5;
					$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
				}
				break;
			}
		case 'ULT_MON': 
			{   /* ULTIMO MONITOREO AL PROVEEDOR */
				if ($tiempo>=6) $deficiencia='CP5';
				if ($deficiencia!='')
				{
					$def[CVEDEFICIENCIA]=$deficiencia;  // TIEMPO MAYOR AL ESTABLECIDO SIN AVISO
					$def[IDSUPERVISOR]=$responsable;
					$def[IDCOORDINADOR]=$responsable;
					$def[IDETAPA]=6;
					$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
				}
				break;
			}
		
	}

} // fin del while

/*  DEFICICENCIA DE ARRIBO */
$deficiencia='';
$sql="
SELECT 
(
  SELECT 
	 UNIX_TIMESTAMP(FECHAARRIBO) - UNIX_TIMESTAMP(TEAM)
  FROM 
  	$con->temporal.asistencia_asig_proveedor 
  WHERE 
  	IDASIGPROV='$idasigprov' 
)SEGUNDOS,
(SELECT IDUSUARIORESPONSABLE FROM $con->temporal.asistencia WHERE IDASISTENCIA='$idasistencia') RESPONSABLE ,
(SELECT IDSERVICIO FROM $con->temporal.asistencia WHERE IDASISTENCIA='$idasistencia' ) IDSERVICIO
";
$result = $con->query($sql);
while ($reg=$result->fetch_object())
{
	$tiempo = intval($reg->SEGUNDOS/60)+(($reg->SEGUNDOS % 60)/100);
	$responsable = $reg->RESPONSABLE;
	$servicio = $reg->IDSERVICIO;
}

/* DETERMINAR LA DEFICIENCIA */
if ($servicio==18) 
{ 
	if ($tiempo>0) $deficiencia='PA2';  
} 
elseif ($tiempo>6) $deficiencia='PA2';

/* CASOS DE AMBULANCIA NO HAY TOLERANCIA */
if  ($deficiencia !='')
{
	$def[CVEDEFICIENCIA]=$deficiencia;  // TIEMPO MAYOR AL ESTABLECIDO SIN AVISO
	$def[IDSUPERVISOR]=$responsable;
	$def[IDCOORDINADOR]=$responsable;  //QUE ES EL COORDINADOR
	$def[IDPROVEEDOR]=$idproveedor;
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
  	IDASISTENCIA='$_POST[IDASISTENCIA]' 
  	AND STATUSTAREA in ('PENDIENTE','INVISIBLE')
  	AND IDTAREA NOT IN ('CONT_AFIL','ARR_PROV')
  	";

$con->query($sql);



?>



