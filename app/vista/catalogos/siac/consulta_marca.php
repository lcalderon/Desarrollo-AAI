<?
 
	include_once('../../../modelo/clase_mysqli.inc.php');
 
		
	$con= new DB_mysqli();
	 
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	if($_POST["marca"])
	 {	
		$sql="SELECT DISTINCT MARCA FROM $con->catalogo.catalogo_marca_modelo_vehiculo WHERE MARCA LIKE '".$_POST["marca"]."%' ORDER BY MARCA";
		
		$resul=$con->query($sql);

		$suggest='<ul>';
		
		while ($reg = $resul->fetch_object()){
			$suggest.="<li id ='".$reg->MARCA."'>";
			$suggest.=utf8_encode($reg->MARCA);
			$suggest.='</li>';
		}
	}
  else if($_POST["modelo"])
   {
		$sql="SELECT DISTINCT MODELO FROM $con->catalogo.catalogo_marca_modelo_vehiculo WHERE MARCA ='".$_POST["marca2"]."' AND MODELO LIKE '".$_POST["modelo"]."%' ORDER BY MODELO";
		
		$resul=$con->query($sql);

		$suggest='<ul>';
		
		while ($reg = $resul->fetch_object()){
			$suggest.="<li id ='".$reg->MODELO."'>";
			$suggest.=utf8_encode($reg->MODELO);
			$suggest.='</li>';
		}
  
   }
	
$suggest.='</ul>';
echo $suggest;


?>

