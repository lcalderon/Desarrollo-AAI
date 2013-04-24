<?
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');

$idusuariomod = $_SESSION['user'];
$con = new DB_mysqli();

/*   SI EL POLIGONO YA EXISTE DEBE ACTUALIZARSE */
$sql = "SELECT ID,IDPOLIGONO FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1='$_POST[CVEENTIDAD1]' AND CVEENTIDAD2='$_POST[CVEENTIDAD2]' LIMIT 1 " ;
$result = $con->query($sql);
$reg = $result->fetch_object();

$idPoligono =  $reg->IDPOLIGONO;
$idEntidad = $reg->ID;
$msg ="Error no se pudo grabar el poligono";

if ($idPoligono != 0){
	/* BORRAR LOS VERTICES ACTUALES*/
		
	$sql="delete from $con->catalogo.catalogo_vertices_poligono where IDPOLIGONO='$reg->IDPOLIGONO'";
	$con->query($sql);
	
	/*   Graba los vertices del poligono en catalogo_vertice_poligono*/
	for($i=0;$i<count($_POST['LATITUD']);$i++)
	{
		$datos_vertice['IDPOLIGONO']=$reg->IDPOLIGONO;
		$datos_vertice['ORDEN']= $i;
		$datos_vertice['LATITUD']= $_POST['LATITUD'][$i];
		$datos_vertice['LONGITUD']= $_POST['LONGITUD'][$i];
		$datos_vertice['IDUSUARIOMOD']= $idusuariomod;
		$con->insert_reg("$con->catalogo.catalogo_vertices_poligono",$datos_vertice);
	}
	
	/* actualizar el catalogo_poligono */
	$datos_poligono['IDPOLIGONO']=$reg->IDPOLIGONO;
	$datos_poligono['NOMBRE']='';
	$datos_poligono['ZOOMMAP']=$_POST['ZOOMMAP'];
	$con->insert_update("$con->catalogo.catalogo_poligono",$datos_poligono);
	$msg ="Poigono actualizado";

}
else
{
	/*   Grabar en catalogo_poligono y obtener el IDPOLIGONO */
	$datos_poligono['ZOOMMAP']=$_POST['ZOOMMAP'];
	$datos_poligono['IDUSUARIOMOD']=$idusuariomod;
	$con->insert_reg("$con->catalogo.catalogo_poligono",$datos_poligono);
	$idPoligono = $con->reg_id();
	
	/*   Graba los vertices del poligono en catalogo_vertice_poligono*/
	for($i=0;$i<count($_POST['LATITUD']);$i++)
	{
		$datos_vertice['IDPOLIGONO']=$idPoligono;
		$datos_vertice['ORDEN']= $i;
		$datos_vertice['LATITUD']= $_POST['LATITUD'][$i];
		$datos_vertice['LONGITUD']= $_POST['LONGITUD'][$i];
		$datos_vertice['IDUSUARIOMOD']= $idusuariomod;
		$con->insert_reg("$con->catalogo.catalogo_vertices_poligono",$datos_vertice);
	}

	/*   Graba el IDPOLIGONO  en el catalogo_entidad */
	$datos_entidad['IDPOLIGONO']=$idPoligono;
	$datos_entidad['LATITUD']=$_POST['CEN_LAT'];
	$datos_entidad['LONGITUD']=$_POST['CEN_LNG'];
	$con->update("$con->catalogo.catalogo_entidad",$datos_entidad," where ID='$idEntidad'");
	$msg ="Poligono grabado";

}
echo $msg;
?>

