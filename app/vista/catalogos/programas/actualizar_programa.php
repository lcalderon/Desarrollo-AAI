<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("aleatorio.php");

	$con = new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
	session_start();
	Auth::required($_POST["txturl"]);
	
	$con->select_db($con->catalogo);



//EDITAR PROGRAMA

if(!$_POST["editarservicio"]  and !$_POST["frmagregaserv"])
{

	$codigo=$_POST['idprograma'];
	$rows["NOMBRE"]=$_POST['nombre'];
	$rows["FECHAINIVIGENCIA"]=$_POST['fechainiserv'];
	$rows["FECHAFINVIGENCIA"]=$_POST['fechafinserv'];
	$rows["ACTIVO"]=$_POST['chkactivo'];
	$rows["PILOTO"]=$_POST['piloto'];
	$rows["IDUSUARIOMOD"]=$_SESSION['user'];
	$rows["PROGRAMAVIP"]=$_POST['chkvip'];
	$rows["VALIDA_ACTIVACION"]=$_POST['chkgestionar'];
	$rows["VALIDACIONEXTERNA"]=$_POST['ckbvalidacion'];		

	//Update datos

	$respuesta=$con->update("catalogo_programa",$rows,"WHERE IDPROGRAMA='$codigo'");
	
	if($codigo)	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_programa_log SELECT * FROM $con->catalogo.catalogo_programa WHERE IDPROGRAMA='".$codigo."'");	
 		
}

//agrega servicio

if($_POST["frmagregaserv"])
{

	if($_POST['cmbmoneda'] == "")	$_POST['cmbmoneda'] =3;

	$codigo=$_POST["idprograma"];
	$row["IDPROGRAMASERVICIO"]="";
	$row["IDPROGRAMA"]=$_POST["idprograma"];
	$row["IDSERVICIO"]=$_POST['cmbservicio'];
	$row["EVENTOS"]=$_POST['txtevento'];
	$row["IDTIPOFRECUENCIA"]=$_POST['cbmtfrecuencia'];
	$row["MONTOXSERV"]=$_POST['txtmonto'];
	$row["IDMONEDA"]=$_POST['cmbmoneda'];
	$row["TIPOCOBERTURA"]=$_POST['cbmtcobertura'];
	$row["COMENTARIO_EXCLUSION"]=$_POST['txtarexclusion'];
	$row["ETIQUETA"]=$_POST['txtetiqueta'];

	$respuesta=$con->insert_reg('catalogo_programa_servicio',$row);
 
	$benef="ok";
}



// Update programa

if($_POST["editarservicio"])
{
	if($_POST['cmbmoneda'] == "")	$_POST['cmbmoneda'] =3;

	$codigo=$_POST['idprograma'];
	$rows["IDPROGRAMA"]=$_POST['idprograma'];
	$rows["IDSERVICIO"]=$_POST['cmbservicio'];
	$rows["EVENTOS"]=$_POST['txtevento'];
	$rows["IDTIPOFRECUENCIA"]=$_POST['cbmtfrecuencia'];
	$rows["MONTOXSERV"]=$_POST['txtmonto'];
	$rows["IDMONEDA"]=$_POST['cmbmoneda'];
	$rows["TIPOCOBERTURA"]=$_POST['cbmtcobertura'];
	$rows["COMENTARIO_EXCLUSION"]=$_POST['txtarexclusion'];
	$rows["ETIQUETA"]=$_POST['txtetiqueta'];

	//Update datos

	$respuesta=$con->update("catalogo_programa_servicio",$rows,"WHERE IDPROGRAMASERVICIO='".$_POST['idprogservicio']."'");

}


//var_dump($_FILES);
if($respuesta)
{
	$uploaddir = 'tmp/';

	$file_prefix = 'id_' . substr(md5(microtime() . mt_rand(1, 1000000)), 0, 16) . '_';
	$uploadfile = $uploaddir . basename($file_prefix . $_FILES['userfile']['name']);
	$upload_file_name = basename($file_prefix . $_FILES['userfile']['name']);

	if(move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
	{
		$file_content_type = $_FILES['userfile']['type'];
		$permited_mimes[] = 'application/pdf';

		if(in_array($file_content_type, $permited_mimes)){
			$mime= $_FILES['userfile']['type'];
			$nombrearchivo= $_FILES['userfile']['name'];
			$fp = fopen($uploadfile,"rb");
			$contenido = fread($fp, filesize($uploadfile));
			$contenido = addslashes($contenido);
			fclose($fp);
			$sql="update $con->catalogo.catalogo_programa set MIME='$mime', NOMBREARCHIVO = '$nombrearchivo', CONTENIDOARCHIVO ='$contenido' WHERE IDPROGRAMA ='$_POST[idprograma]' ";
			$con->query($sql);		
			unlink($uploadfile);
		} else {
			unlink('tmp/' . $upload_file_name);
			$upload_file_name = '';

		}


	}

}

echo "<script>";
if(!$respuesta)
{
	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	echo "document.location.href='general.php'";
}
else
{
	echo "document.location.href='edit_catalogo.php?codigo=".$_POST['idprograma']."&benef=$benef&opc=".$_POST['opc']."' ";
}
echo "</script>";

?>