<?
include_once('../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$db =$con->catalogo;
$con->select_db($db);
if ($con->Errno) {
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
}

if (strlen($_GET[calle]))
{
	if ($_GET[cveentidad7]!='') $ext =" AND CVEENTIDAD7 ='$_GET[cveentidad7]'";
	if ($_GET[cveentidad6]!='') $ext.=" AND CVEENTIDAD6 ='$_GET[cveentidad6]'";
	if ($_GET[cveentidad5]!='') $ext.=" AND CVEENTIDAD5 ='$_GET[cveentidad5]'";
	if ($_GET[cveentidad4]!='') $ext.=" AND CVEENTIDAD4 ='$_GET[cveentidad4]'";
	if ($_GET[cveentidad3]!='') $ext.=" AND CVEENTIDAD3 ='$_GET[cveentidad3]'";
	if ($_GET[cveentidad2]!='') $ext.=" AND CVEENTIDAD2 ='$_GET[cveentidad2]'";
	if ($_GET[cveentidad1]!='') $ext.=" AND CVEENTIDAD1 ='$_GET[cveentidad1]'";
	
	if ($_GET[tv]!='') $ext.=" AND IDTIPOVIA ='$_GET[tv]'";
	
	$ext.='';

	$sql="
	select
		ID, CONCAT(CALLE,' ',CUADRA,' ',DESCRIPCION) DIRECCION, LATITUD, LONGITUD, CVEENTIDAD3, CVETIPOVIA, DESCRIPCION
	from 
		catalogo_guiacalle
	where 
	CALLE like '%$_GET[calle]%' $ext  ;";
	
	$resul=$con->query($sql);

	$suggest='<ul>';
	
	while ($reg = $resul->fetch_object()){
		$geodesia=$reg->LATITUD.','.$reg->LONGITUD.','.$reg->CVETIPOVIA;
		$suggest.="<li id = $geodesia>";
		$suggest.=$reg->DIRECCION;
		$suggest.='</li>';
	}
}
$suggest.='</ul>';
echo $suggest;

?>

