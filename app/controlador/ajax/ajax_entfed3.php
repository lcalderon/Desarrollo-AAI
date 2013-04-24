<?
include_once('../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
if ($con->Errno) {
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
	
}

$sql="
	select 
	CVEENTIDAD3, DESCRIPCION 
	FROM
	$con->catalogo.catalogo_entidad
	WHERE 
	CVEENTIDAD1='$_POST[ent1]'
	AND CVEENTIDAD2='$_POST[ent2]'
	AND CVEENTIDAD3<>'0' 
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
	echo "<option value='$reg->CVEENTIDAD3'>".$reg->DESCRIPCION."</option>";

 
?>