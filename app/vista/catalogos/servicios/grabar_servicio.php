<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
		
	$con = new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
 	session_start();
	Auth::required($_POST["txturl"]);
	
	$con->select_db($con->catalogo);

//variables POST	 

	$rows["IDSERVICIO"]="";
	$rows["IDPLANTILLA"]=$_POST['cmbplantilla'];
	$rows["DESCRIPCION"]=strtoupper($_POST['nombre']);
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];
	$rows["FECHAINIVIGENCIA"]=$_POST['fechainiserv'];
	$rows["FECHAFINVIGENCIA"]=$_POST['fechafinserv'];
	$rows["DURACIONESTIMADA"]=$_POST['txtduracionestimada'];
	$rows["IDFAMILIA"]=$_POST['cmbfamilia'];
	$rows["VALIDACIONTIEMPO"]=($_POST['validaciontiempo']=='on')?1:0;
	$rows["CONCLUCIONTEMPRANA"]=($_POST['concluciontemprana']=='on')?1:0;
	$rows["CONCLUCIONCONPROVEEDOR"]=($_POST['concluciontemprana']=='on')?$_POST['conclucionconproveedor']:0;
	$rows["PAGER_SERV"]=$_POST[PAGER_SERV];
	$rows["MATNR"]=$_POST[MATNR];
	

//Inserta los datos

	$respuesta=$con->insert_reg('catalogo_servicio',$rows);
	$idserv=$con->reg_id();

	if($respuesta and $idserv)	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_servicio_log SELECT * FROM $con->catalogo.catalogo_servicio WHERE IDSERVICIO='".$idserv."'");
	
	$data=$con->consultation("SELECT LAST_INSERT_ID()");
		
	echo "<script>";
	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
    echo "document.location.href='edit_catalogo.php?codigo=".$data[0][0]."'";
    echo "</script>";	
?>