<?
 
	include_once("../../../modelo/clase_mysqli.inc.php");
		
	$con= new DB_mysqli();
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	 $con->select_db($con->catalogo);
	
	if($_POST["marca"])
	 {	
		$sql="SELECT DISTINCT MARCA FROM catalogo_marca_modelo_vehiculo WHERE MARCA LIKE '".$_POST["marca"]."%' ORDER BY MARCA";
		
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
		//$sql="SELECT DISTINCT MODELO FROM catalogo_marca_modelo_vehiculo WHERE MARCA ='".$_POST["marca"]."' AND MODELO LIKE '".$_POST["modelo"]."%' ORDER BY MODELO";
		$sql="SELECT DISTINCT MODELO FROM catalogo_marca_modelo_vehiculo WHERE MARCA ='".$_POST["marca2"]."' AND MODELO LIKE '".$_POST["modelo"]."%' ORDER BY MODELO";
		
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

