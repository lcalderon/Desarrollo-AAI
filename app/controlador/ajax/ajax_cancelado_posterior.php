<?

include_once('../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();


$idasigprov = $_POST[IDASIGPROV];
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
		case 'CONF_SERV' :    //CONFIRMACION DEL SERVICIO
		{
			/* DETERMINA LA DEFICIENCIA */
			if ($tiempo >=2 && $tiempo<4) $deficiencia='CP6';
			elseif ($tiempo>=4) $deficiencia='CA5';

			if ($deficiencia!='')
			{
				$def[CVEDEFICIENCIA]=$deficiencia;
				$def[IDSUPERVISOR]=$responsable;
				$def[IDCOORDINADOR]=$responsable;
				$def[IDETAPA]=3;

				$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
			}
			break;
		}
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
			{  //ULTIMO MONITOREO
				
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
		case 'CONT_AFIL':
			{
				/* DETERMINA LOS RANGOS DE TIEMPO PARA LAS DEFICIENCIAS DEL CONTACTO*/

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
				break;
			}
		case 'LLAM_CON':
			{
				/* DETERMINA LOS RANGOS DE TIEMPO PARA LAS DEFICIENCIAS DE LA LLAMADA*/
				$sql="
					SELECT 
						ENROJO 
					FROM 
						$con->catalogo.catalogo_tarea 
					WHERE 
						IDTAREA='LLAM_CON' 
					";
				$result = $con->query($sql);
				while ($reg=$result->fetch_object()){
					$enrojo = $reg->ENROJO;
				}

				if ($tiempo >=$enrojo && $tiempo<11) $deficiencia='CP6';
				elseif ($tiempo>=11) $deficiencia='CA5';

				/* grabar las deficiencias */
				if ($deficiencia!='')
				{
				
					$def[CVEDEFICIENCIA]=$deficiencia;
					$def[IDSUPERVISOR]=$responsable;  
					$def[IDCOORDINADOR]=$responsable; 
					$def[IDETAPA]=8;
					
					$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
				}
				break;
			}
	}

} // fin del while



/* CAMBIA DE ESTADO A LA ASISTENCIA*/
$asis[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis[ARRSTATUSASISTENCIA]='CP';
$con->update("$con->temporal.asistencia",$asis," WHERE IDASISTENCIA='$asis[IDASISTENCIA]'");


/*  CANCELA LAS TAREAS PENDIENTES E INVISIBLES DE LA ASISTENCIA */

$sql="
UPDATE
 $con->temporal.monitor_tarea
SET 
	STATUSTAREA='CANCELADA'
WHERE
  IDASISTENCIA='$_POST[IDASISTENCIA]'
  AND STATUSTAREA in ('PENDIENTE','INVISIBLE');
";
$con->query($sql);
?>