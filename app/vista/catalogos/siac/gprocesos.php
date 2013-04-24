<?php

	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../modelo/functions.php");
	include_once("../../includes/arreglos.php");	
	include_once("../../../../librerias/PHPMailer/class.phpmailer.php");
	include_once("../../../../librerias/PHPMailer/sendmails.php");
	
	$con= new DB_mysqli();
	
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	session_start();  
 	
	if($_POST['txtmotllamada']=="BAJASERVICIO"){
	
		$rowcan["STATUSASISTENCIA"]="CAN";
		$rowcan["IDUSUARIOMOD"]=$_SESSION["user"];
		$rowcan["FECHACANCELACION"]=date("Y-m-d H:i:s");		
		
		$con->update("$con->catalogo.catalogo_afiliado",$rowcan,"WHERE IDAFILIADO='".$_POST['idafiliado']."'");	
	}
	
	if($_POST['txtmotllamada']=="CAMBIOPROGRAMA")	$con->query("update $con->catalogo.catalogo_afiliado set IDPROGRAMA='".$_POST['cmboplans']."' ,IDUSUARIOMOD='".$_SESSION["user"]."' WHERE IDAFILIADO='".$_POST['idafiliado']."'");
	if($_POST['txtmotllamada']=="REACTIVACION")		$con->query("update $con->catalogo.catalogo_afiliado set STATUSASISTENCIA='ACT' ,IDUSUARIOMOD='".$_SESSION["user"]."' WHERE IDAFILIADO='".$_POST['idafiliado']."'");
	
		$row["IDRETENCION"]="";
		$row["FECHARETENCION"]=date("Y-m-d H:i:s");
		$row["IDCUENTA"]=$_POST['txtcodcuenta'];
		$row["IDPROGRAMA"]=$_POST['txtcodprograma'];
		$row["IDAFILIADO"]=$_POST['idafiliado'];
		$row["MOTIVOLLAMADA"]=$_POST['txtmotllamada'];
		$row["IDDETMOTIVOLLAMADA"]=$_POST['cmbopciones'];
		$row["MESREINTEGRO"]=$_POST["txtmessol"];
		$row["MONTOSOLICITADO"]=$_POST["txtmontosol"];
		$row["FECHAEJECUSION"]=$_POST["txtfechaejecuta"];
		$row["STATUS_RETENCION_AFILIADO"]="VALIDADO";
		$row["COMENTARIO"]=remplazar_enes_tildes(utf8_encode($_POST["txtacomentario"]));
		$row["STATUS_SEGUIMIENTO"]="REC";
		$row["IDGRUPO"]=($_POST["cmbasignacionc"])?$_POST["cmbasignacionc"]:"1";
		$row["ARRPROCEDENCIA"]=$_POST["cmbprocedencia"]; 
		$row["IDUSUARIO"]=$_SESSION["user"];
 
	//Inserta los datos

		if($_POST['idafiliado']!="")	$respuesta=$con->insert_reg("$con->temporal.retencion",$row);	
		if($respuesta)	$idreclamo=$con->reg_id();
	
		$rows["BLOQUEADO"]=$_POST["chkbloqueado"];
	
		//if($idreclamo and $_POST["cmbprocedencia"])		include_once("procesar_enviomail.php");

	echo "<script>";
	if(!$respuesta)	
	 {
		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
		echo "document.location.href='buscarafiliado.php' ";
	 }
	else
	 {		
		echo "document.location.href='gestionarafiliado.php?idafiliado=".$_POST['idafiliado']."' ";
	 }	 
    echo "</script>";
	
?>