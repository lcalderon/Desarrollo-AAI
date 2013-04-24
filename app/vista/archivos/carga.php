<?php
include_once "../../modelo/clase_mysqli.inc.php";
$idasistencia = $_POST['IDASISTENCIA'];

$msg= '';
if (isset($_FILES['foto'])){
	
	$nombrearchivo= basename($_FILES['foto']['name']);
	$destino_archivo = "temp/".$nombrearchivo ;
	
	if(move_uploaded_file($_FILES['foto']['tmp_name'],$destino_archivo))
	{
		/* array de tipos de permitidos */
		$mimes_permitidos = array('image/jpeg','image/jpg','image/png');
		$mime = $_FILES['foto']['type'];
			
		if (in_array($mime, $mimes_permitidos))
		{
			$fp = fopen($destino_archivo,"rb");
			$contenido = fread($fp, filesize($destino_archivo));
			$contenido = addslashes($contenido);
			fclose($fp);
			
			$con = new DB_mysqli;
			$sql="insert into $con->temporal.asistencia_imagenes set IDASISTENCIA='$idasistencia', MIME='$mime',IMAGEN ='$contenido'";
			$con->query($sql);
			
		}
		//else{
		//$msg = "Archivo no tiene el formato adecuado<br>";	
		//}
		unlink($destino_archivo);
	}
	//else
	//$msg = "La carpeta Temp no tiene derechos <br>";
}
//else 
  // $msg = "No subio ningun Archivo.<br>";
  
header("location: index.php?idasistencia=$idasistencia");

?>
