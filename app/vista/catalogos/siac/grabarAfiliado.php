<?php

	session_start();
	include_once("../../../modelo/clase_mysqli.inc.php");
		
	$con = new DB_mysqli();		
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

	
	$rows["NOMBRE"]=$_POST['txtnombres'];	
	$rows["APPATERNO"]=$_POST['txtpaterno'];
	$rows["APMATERNO"]=$_POST['txtmaterno'];
	$rows["IDDOCUMENTO"]=$_POST['txtndocumento'];
	$rows["IDTIPODOCUMENTO"]=$_POST['cmbtipodoc'];
	$rows["GENERO"]=$_POST['cmbgenero'];
	$rows["EMAIL1"]=$_POST['txtemail'];
	$rows["EMAIL2"]=$_POST['txtemail2'];
	$rows["EMAIL3"]=$_POST['txtemail3'];
 

	//actualiza los datos
 
	$resultado= $con->update("$con->catalogo.catalogo_afiliado_persona",$rows,"WHERE IDAFILIADO='".$_POST["idafiliado"]."'");
	
	
	if($resultado) $rs_eliminatel=$con->query("DELETE from $con->catalogo.catalogo_afiliado_persona_telefono where IDAFILIADO='".$_POST["idafiliado"]."'");
	
	if($rs_eliminatel)
	 { 

		foreach($_POST["txttelefono"] as $indice => $numtelefono){

			if($numtelefono)
			 {
				$rowtele["IDAFILIADO"]=$_POST["idafiliado"];
				$rowtele["NUMEROTELEFONO"]=$numtelefono;
				$rowtele["IDUSUARIOMOD"]	=$_SESSION["user"]; 
				$rowtele["PRIORIDAD"]=$indice;
				
				$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_telefono",$rowtele);
				
			}
		}
	 }

		$row["IDRETENCION"]="";
		$row["FECHARETENCION"]=date("Y-m-d H:i:s");
		$row["IDCUENTA"]=$_POST['txtcodcuenta'];
		$row["IDPROGRAMA"]=$_POST['txtcodprograma'];
		$row["IDAFILIADO"]=$_POST['idafiliado'];
		$row["MOTIVOLLAMADA"]="GENERALIDAD";
		$row["IDDETMOTIVOLLAMADA"]=84;
		$row["COMENTARIO"]="CAMBIO DE INFORMACION DEL AFILIADO";
		$row["STATUS_RETENCION_AFILIADO"]="VALIDADO";
		$row["STATUS_SEGUIMIENTO"]="CON";
		$row["IDUSUARIO"]=$_SESSION["user"];
	
	//Inserta los datos

		if($resultado)	$con->insert_reg("$con->temporal.retencion",$row);		 
	
?>
