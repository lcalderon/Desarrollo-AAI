<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	
$con = new DB_mysqli();

if ($con->Errno)
{
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
}

	session_start();
	Auth::required($_POST["txturl"]);
	
$con->select_db($con->catalogo);

//variables POST

$rows["IDPLANTILLA"]=$_POST['cmbplantilla'];
$rows["DESCRIPCION"]=strtoupper($_POST['nombre']);
$rows["IDUSUARIOMOD"]=$_SESSION["user"];
$rows["FECHAINIVIGENCIA"]=$_POST['fechainiserv'];
$rows["FECHAFINVIGENCIA"]=$_POST['fechafinserv'];
$rows["IDFAMILIA"]=$_POST['cmbfamilia'];
$rows["DURACIONESTIMADA"]=$_POST['txtduracionestimada'];
$rows["VALIDACIONTIEMPO"]=($_POST['validaciontiempo']=='on')?1:0;
$rows["CONCLUCIONTEMPRANA"]=($_POST['concluciontemprana']=='on')?1:0;
$rows["CONCLUCIONCONPROVEEDOR"]=($_POST['concluciontemprana']=='on')?$_POST['conclucionconproveedor']:0;
$rows["PAGER_SERV"]=$_POST[PAGER_SERV];
$rows["MATNR"]=$_POST[MATNR];
//$rows["IDCOBERTURA"]=$_POST['cmbcobertura'];

//actualiza los datos

$resultado=$con->update("catalogo_servicio",$rows,"WHERE IDSERVICIO='".$_POST['idservicio']."'");

if($_POST['idservicio'])	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_servicio_log SELECT * FROM $con->catalogo.catalogo_servicio WHERE IDSERVICIO='".$_POST['idservicio']."'");	

$con->query("delete from catalogo_servicio_infraestructura");

//grabando infraestructura

foreach($_POST['cmbinfra'] as $indice => $infra){

	$row["IDSERVICIO"]=$_POST['idservicio'];
	$row["IDINFRAESTRUCTURA"]=$infra;
	$row["PRIORIDAD"]=$indice;

	//if($resultado and $infra!="")	$resp=$con->insert_reg("catalogo_servicio_infraestructura",$row);
}

if($resultado)	$resp=$con->query("delete from catalogo_servicio_costo where IDSERVICIO='".$_POST["idservicio"]."'");

foreach ($_POST['chkdesc'] as $idcosto){

	$rowc["IDCOSTO"]=$idcosto;
	$rowc["IDSERVICIO"]=$_POST["idservicio"];
	$rowc["MONTOLOCAL"]=$_POST["txtmonto".$idcosto];
	$rowc["IDMONEDA"]=$_POST["cmbmoneda".$idcosto];
	$rowc["UNIDAD"]=$_POST["txtunidad".$idcosto];
	$rowc["IDMEDIDA"]=$_POST["cmbmedida".$idcosto];
	$rowc["MONTOFORANEO"]=$_POST["txtforaneo".$idcosto];
	$rowc["PLUSNOCTURNO"]=$_POST["txtnocturno".$idcosto];
	$rowc["PLUSFESTIVO"]=$_POST["txtfestivo".$idcosto];
	$con->insert_reg("catalogo_servicio_costo",$rowc);
}

echo "<script>";
if(!$resultado)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
echo "document.location.href='edit_catalogo.php?codigo=".$_POST["idservicio"]."'";
echo "</script>";
?>