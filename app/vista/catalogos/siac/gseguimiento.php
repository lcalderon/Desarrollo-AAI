<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/functions.php');
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

	list($fechahora,$fecha,$hora)=fechahora();
	
	$idreclamo=$_POST["idcaso"];

	
	if($_POST['desafiliacionOk']==1 and $_POST["cmbstatus"] =="CON"){
	
		$rowcan["STATUSASISTENCIA"]="CAN";
		$rowcan["IDUSUARIOMOD"]=$_SESSION["user"];
		$rowcan["FECHACANCELACION"]=date("Y-m-d H:i:s");		
		
		$con->update("$con->catalogo.catalogo_afiliado",$rowcan,"WHERE IDAFILIADO='".$_POST['idafiliado']."'");	
	}
	
	$row["IDSEGUIMIENTO"]="";
	$row["IDRETENCION"]=$idreclamo;
	$row["FECHARETENCION"]=$fechahora;
	$row["COMENTARIO"]=remplazar_enes_tildes(utf8_encode(trim($_POST["txtsegimiento"])));
	$row["ARRVALIDEZ"]=$_POST["cmbvalidez"];
	$row["IDGRUPO"]=$_POST["cmbasignacionc"];
	$row["FECHADISPOSICION"]=$_POST["txtfecha"];
	$row["IDUSUARIO"]=$_SESSION["user"];
	$row["DEFENSACONSUMIDOR"]=$_POST["chkdefensacon"];
	$row["RESPUESTAFORMAL"]=$_POST["chkfromal"];
	$row["MESREINTEGRO"]=$_POST["txtmessolicitado"];
	$row["MONTOSOLICITADO"]=$_POST["txtmontosolicitado"];
	$row["FECHAEJECUSION"]=$_POST["txtfechaejecuta"];

	//Inserta los datos

	$respuesta=$con->insert_reg("$con->temporal.retencion_seguimiento",$row);		

	$rowasig["IDGRUPO"]=$_POST["cmbasignacionc"];	
	$rowasig["DEFENSACONSUMIDOR"]=($_POST["chkdefensacon2"])?$_POST["chkdefensacon2"]:$_POST["chkdefensacon"];
	$rowasig["ARRVALIDEZ"]=$_POST["cmbvalidez"];
	$rowasig["STATUS_SEGUIMIENTO"]=strtoupper($_POST["cmbstatus"]);
	$rowasig["RESPUESTAFORMAL"]=($_POST["chkfromal2"])?$_POST["chkfromal2"]:$_POST["chkfromal"];
	$rowasig["FECHADISPOSICION"]=$_POST["txtfecha"];
	$rowasig["MESREINTEGRO"]=$_POST["txtmessolicitado"];
	$rowasig["MONTOSOLICITADO"]=$_POST["txtmontosolicitado"];
	$rowasig["FECHAEJECUSION"]=$_POST["txtfechaejecuta"];
	
	if($respuesta)	$con->update("$con->temporal.retencion",$rowasig,"WHERE IDRETENCION='".$idreclamo."'");
	if($_POST['desafiliacionOk']==1 and $_POST["cmbstatus"] =="CON") $con->update("$con->temporal.retencion",$rowasig,"WHERE MOTIVOLLAMADA='DESAFILIACION' AND STATUS_SEGUIMIENTO IN('PRO','REC') AND IDAFILIADO='".$_POST['idafiliado']."'");
//verificando para envial mails	
	
	//if($respuesta and ($_POST["txtareaori"]!=$_POST["cmbasignacionc"]) and $_POST["cmbasignacionc"]!="")	include_once("procesar_enviomail.php");
	if($_POST["desafiliacionOk"])	$urlextra="&desafiliacionOk=1";
	
	echo "<script>";
	if(!$respuesta)
	 {
		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
		echo "self.close();";
	 }
	else
	 {	  
	 	echo "document.location.href='detalle.php?idcaso=".$_POST['idcaso']."$urlextra'";
	 }
    echo "</script>";
	
?>
