<?
include_once('../../modelo/clase_mysqli.inc.php');

$idasistencia= $_POST[IDASISTENCIA];

$con = new DB_mysqli();

/* busca los campos que son obligatorios en este servicio*/
$sql="
	SELECT 
		cdo.IDSERVICIO,
		cdo.NOMBRETABLA,
		cdo.CAMPOOBLIGATORIO
	FROM
 		$con->temporal.asistencia a,
 		$con->catalogo.catalogo_datosobligatorios cdo 
	WHERE
		a.IDSERVICIO = cdo.IDSERVICIO
		AND a.IDASISTENCIA ='$idasistencia'
		";

$result = $con->query($sql);

if ($result->num_rows)
{ // SI EXISTE  CAMPO OBLIGATORIOS
	while($reg=$result->fetch_object())
	{
		$tabla=$reg->NOMBRETABLA;
		$campos[] = $reg->CAMPOOBLIGATORIO;
	}

	foreach ($campos as $valor)
	$condicion.= " AND ".$valor. "<>''";

	/*luego busca si estos campos estan llenos en la tabla respectiva */
	$sql="
			SELECT 
				*
			FROM 
 				$con->temporal.$tabla 
			WHERE 
 				IDASISTENCIA ='$idasistencia'
 				$condicion
 			 ";
	$result = $con->query($sql);
	if ($result->num_rows) echo "TRUE";
}
else {  // SI NO EXISTE UN CAMPO OBLIGATORIO
	echo "TRUE";
}


?>