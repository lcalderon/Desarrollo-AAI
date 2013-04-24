<?
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

if (isset($_GET['term'])){
	$direccion =  strtolower($_GET['term']);


	$sql="SELECT
		ID,CVEENTIDAD1,CVEENTIDAD2,
		CVEENTIDAD3, CONCAT(CALLE,' ',CUADRA,' ',DESCRIPCION) DIRECCION, 
		LATITUD,LONGITUD 
	  FROM 
	  $con->catalogo.catalogo_guiacalle
	  WHERE 
	  
	  calle LIKE '%$direccion%'";

	  $result = $con->query($sql);


	  $direcciones = array();
	  while($reg= $result->fetch_object()) {
	  	if (strpos(strtolower($reg->DIRECCION), $direccion) !== false) {
	  		array_push($direcciones, array("id"=>$reg->ID, "label"=>$reg->DIRECCION,"latitud"=>$reg->LATITUD,"longitud"=>$reg->LONGITUD,'cveentidad1'=>$reg->CVEENTIDAD1,'cveentidad2'=>$reg->CVEENTIDAD2));
	  	}
	  	if (count($direcciones) > 100)
	  	break;
	  }
	  echo json_encode($direcciones);

}


?>