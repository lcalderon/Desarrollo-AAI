<?
	include_once('../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();
	$con->select_db($con->catalogo);
		
	if ($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	 
	session_start();

	$rows["CONTRASENIA"]=sha1(strtoupper($_POST["idclave"]));
	
	$respuesta=$con->update("catalogo_usuario",$rows,"WHERE IDUSUARIO='".$_SESSION['user']."'");
	if($respuesta)	echo  _("SE CAMBIO CORRECTAMENTE LA CONTRASEÑA.");
?>
