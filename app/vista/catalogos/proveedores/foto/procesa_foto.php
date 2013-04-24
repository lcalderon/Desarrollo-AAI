<?
include_once('../../../../modelo/clase_mysqli.inc.php');

if (isset($_FILES['foto'])){

	$nombrearchivo= basename($_FILES['foto']['name']);

	$destino_archivo = "../temp/".$nombrearchivo ;

	if	(in_array($_FILES['foto']['error'],array(1,2,3,5))) $error="Error al cargar el archivo";

	if(move_uploaded_file($_FILES['foto']['tmp_name'],$destino_archivo))
	{
		/* array de tipos de permitidos */
		$mimes_permitidos = array('image/jpeg','image/jpg','image/png');
		$mime_foto = $_FILES['foto']['type'];

		if (in_array($mime_foto, $mimes_permitidos))
		{
			$fp = fopen($destino_archivo,"rb");
			$contenido = fread($fp, filesize($destino_archivo));
			$contenido = addslashes($contenido);
			fclose($fp);

			$con = new DB_mysqli();

			if ($con->exist("$con->catalogo.catalogo_proveedor_foto",'IDPROVEEDOR',"  WHERE IDPROVEEDOR = '$_POST[IDPROVEEDOR]'"))
			{

				$sql="UPDATE $con->catalogo.catalogo_proveedor_foto SET MIME='$mime_foto', FOTO='$contenido' WHERE IDPROVEEDOR = '$_POST[IDPROVEEDOR]' ";
			}
			else
			{
				$sql="INSERT INTO $con->catalogo.catalogo_proveedor_foto SET IDPROVEEDOR ='$_POST[IDPROVEEDOR]', MIME='$mime_foto', FOTO='$contenido' ";
			}

			$con->query($sql);
			//$error = "Imagen cargada";
		}
		else
		$error = "La foto no tiene el formato adecuado";

		unlink($destino_archivo);


	}

	header("location: form_foto.php?idproveedor=$_POST[IDPROVEEDOR]&error=$error");
}

?>
