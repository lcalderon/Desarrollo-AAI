<?

include_once('../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();


if ($con->Errno) {
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
	
}


$sql="
select 
	CVEENTIDAD1, DESCRIPCION 
	FROM 
	$con->catalogo.catalogo_entidad 
	WHERE 
	CVEPAIS ='$_POST[pais]'
	AND CVEENTIDAD1<>'0'
	AND CVEENTIDAD2='0'
	AND CVEENTIDAD3='0' 
	AND CVEENTIDAD4='0'
	AND CVEENTIDAD5='0'
	AND CVEENTIDAD6='0'
	AND CVEENTIDAD7='0'
	ORDER BY 
	DESCRIPCION
";

$resul= $con->query($sql);
echo "<option value=''>TODOS</option>";
while ($reg= $resul->fetch_object())
	echo "<option value='$reg->CVEENTIDAD1'>".$reg->DESCRIPCION."</option>";



 
?>