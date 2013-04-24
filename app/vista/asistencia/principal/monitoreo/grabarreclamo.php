<?php

	include_once('../../../../modelo/clase_mysqli.inc.php');
	include_once("../../../../../librerias/PHPMailer/class.phpmailer.php");
	include_once("../../../../../librerias/PHPMailer/sendmails.php");
	include_once("../../../includes/arreglos.php");	
	
	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	session_start();

	$id_expediente=$_POST["id_expediente"];
	
	$resp=$con->consultation("SELECT COUNT(*) FROM  $con->temporal.expediente WHERE IDEXPEDIENTE='$id_expediente' AND IDAFILIADO!=0");
	
	if($resp[0][0]==0)
	 {	
		$res_afi=$con->query("insert into $con->catalogo.catalogo_afiliado(ARRORIGENTABLA,IDCUENTA,IDPROGRAMA,STATUSASISTENCIA,AFILIADO_SISTEMA,IDUSUARIOMOD,CVEAFILIADO) SELECT 'PERSONAS',IDCUENTA,IDPROGRAMA,'CAN','SINVALIDAR', '".$_SESSION["user"]."' as usuario,CVEAFILIADO from $con->temporal.expediente where IDEXPEDIENTE='$id_expediente' ") ;
		
		if($res_afi)	$idafiliado=$con->reg_id();
		
		if($idafiliado)		
		 {
			$res_afi_per=$con->query("insert into $con->catalogo.catalogo_afiliado_persona(IDAFILIADO,NOMBRE,APPATERNO,APMATERNO,IDTIPODOCUMENTO,IDDOCUMENTO,DIGITOVERIFICADOR,IDUSUARIOMOD) SELECT '".$idafiliado."' as afiliado,NOMBRE,APPATERNO,APMATERNO,IDTIPODOCUMENTO,IDDOCUMENTO,DIGITOVERIFICADOR,'".$_SESSION["user"]."' as usuariomod from $con->temporal.expediente_persona where IDEXPEDIENTE='$id_expediente' and ARRTIPOPERSONA='TITULAR' ") ;
			
			if($res_afi_per)
			 {
				$res_persona=$con->consultation("SELECT IDPERSONA FROM  $con->temporal.expediente_persona WHERE IDEXPEDIENTE='$id_expediente' LIMIT 1");
			 	 
				if($res_persona[0][0])	$con->query("insert into $con->catalogo.catalogo_afiliado_persona_telefono(IDAFILIADO,IDTIPOTELEFONO,CODIGOAREA,NUMEROTELEFONO,EXTENSION,IDTSP,IDUSUARIOMOD) SELECT '".$idafiliado."' as afiliado,IDTIPOTELEFONO,CODIGOAREA,NUMEROTELEFONO,EXTENSION,IDTSP,'".$_SESSION["user"]."' as usuariomod from $con->temporal.expediente_persona_telefono where IDPERSONA='".$res_persona[0][0]."' ") ;
				 
				$con->query("UPDATE $con->temporal.expediente SET IDAFILIADO='$idafiliado' WHERE IDEXPEDIENTE='$id_expediente'");
				
			 }
		 }	
	 
	 }

		$inforeclamo=$con->consultation("SELECT IDAFILIADO,IDCUENTA,IDPROGRAMA FROM  $con->temporal.expediente WHERE IDEXPEDIENTE='$id_expediente' ");
 
		$row["IDRETENCION"]="";
		$row["FECHARETENCION"]=date("Y-m-d H:i:s");
		$row["IDCUENTA"]=$inforeclamo[0][1];
		$row["IDPROGRAMA"]=$inforeclamo[0][2];
		$row["IDAFILIADO"]=$inforeclamo[0][0];
		$row["MOTIVOLLAMADA"]=$_POST['txtmotllamada'];
		$row["IDDETMOTIVOLLAMADA"]=$_POST['cmbopciones'];
		$row["STATUS_RETENCION_AFILIADO"]="VALIDADO";
		$row["COMENTARIO"]=$_POST["txtacomentario"];
		$row["STATUS_SEGUIMIENTO"]="REC";
		$row["IDGRUPO"]=$_POST["cmbasignacionc"];
		$row["ARRPROCEDENCIA"]=$_POST["cmbprocedencia"]; 
		$row["IDEXPEDIENTE"]=$id_expediente; 
		$row["IDASISTENCIA"]=$_POST["id_asistencia"]; 
		$row["IDUSUARIO"]=$_SESSION["user"];
	
	//Inserta los datos reclamo

		if($inforeclamo[0][0] >0)	$respreclamo=$con->insert_reg("$con->temporal.retencion",$row);	
		if($respreclamo)	$idreclamo=$con->reg_id();
		
	//registrar bitacora
 
		$asis_bitacora[IDASISTENCIA]=$_POST["id_asistencia"];
		$asis_bitacora[COMENTARIO]="*** SE GENERO EL RECLAMO NRO $idreclamo";
		$asis_bitacora[IDUSUARIOMOD]=$_SESSION["user"];

		if($idreclamo)
		 {
			$con->insert_reg("$con->temporal.asistencia_bitacora_etapa8",$asis_bitacora);
			
			//enviar mail
				//include_once("../../../catalogos/siac/procesar_enviomail.php");			 
		 }
	 

	
	echo "<script>";
	if(!$idreclamo)	echo "alert('"._("HUBO UN PROBLEMA, NO SE REGISTRO EL RECLAMO").".');";	else echo "alert('"._("SE GENERO EL RECLAMO ")."NRO $idreclamo.');";
    echo "parent.win.close();";
    echo "</script>";
	
?>