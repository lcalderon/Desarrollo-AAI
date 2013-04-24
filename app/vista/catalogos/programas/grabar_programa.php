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

	$idplan=$_POST["txtcodigo"];

// if (!$_POST["agregarplan"])
// {

$rows["IDPROGRAMA"]=$idplan;
$rows["IDCUENTA"]=$_POST["cmbcuenta"];
$rows["NOMBRE"]=strtoupper($_POST["nombre"]);
$rows["FECHAINIVIGENCIA"]=$_POST['fechainiserv'];
$rows["FECHAFINVIGENCIA"]=$_POST['fechafinserv'];
$rows["ACTIVO"]=$_POST['chkactivo'];
$rows["PILOTO"]=$_POST['piloto'];
$rows["IDUSUARIOMOD"]=$_SESSION["user"];
$rows["PROGRAMAVIP"]=$_POST['chkvip'];
$rows["VALIDACIONEXTERNA"]=$_POST['ckbvalidacion'];	

//Inserta los datos
$respuesta=$con->insert_reg("catalogo_programa",$rows);

if($respuesta and $idplan)	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_programa_log SELECT * FROM $con->catalogo.catalogo_programa WHERE IDPROGRAMA='".$idplan."'");	
 		
		
/* if($respuesta)
{
$rsbeneficiario=$con->query("select IDTIPOBENEFICIARIO from catalogo_tipo_beneficiario");

while($rsben = $rsbeneficiario->fetch_object())
{
$rowb["IDPROGRAMA"]=$idplan;
$rowb["IDTIPOBENEFICIARIO"]=$rsben->IDTIPOBENEFICIARIO;

$con->insert_reg('catalogo_programa_beneficiario',$rowb);
}

$conforme=$con->consultation("select count(*) as cantidad from catalogo_programa_conformidad where IDPROGRAMA='$idplan'");
if($conforme[0][0] == 0)
{
$rnd=new Aleatorios();


$dato[0]="FINANZAS";
$dato[1]="OPERACIONES";
$dato[2]="CALIDAD";
$dato[3]="SISTEMAS";

for($i = 0 ; $i <= 3 ; $i ++) {
$clave=$rnd->getAleatorio("hex",16,FALSE);

$row1["IDPROGRAMA"]=$idplan;
$row1["NOMBRE"]=$dato[$i];
$row1["STATUSCONFIRMA"]="PENDIENTE";
$row1["CLAVE"]=$clave;
$row1["IDUSUARIO"]=$_SESSION['user'];

$con->insert_reg('catalogo_programa_conformidad',$row1);

}
}
} */

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
				$sql="update $con->catalogo.catalogo_programa set MIME='$mime', NOMBREARCHIVO = '$nombrearchivo', CONTENIDOARCHIVO ='$contenido' WHERE IDPROGRAMA ='$rows[IDPROGRAMA]' ";
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
	if (!$_POST["agregarplan"]) $idplan="";
	echo "document.location.href='add_catalogo.php'";

}
else
{
	echo "document.location.href='edit_catalogo.php?codigo=$idplan&opc=".$_POST["opc"]."' ";
}
echo "</script>";
?>