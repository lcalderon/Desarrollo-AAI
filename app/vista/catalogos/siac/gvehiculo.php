<?php

	include_once('../../../../app/modelo/clase_mysqli.inc.php');
	include_once('../../../../app/modelo/functions.php');

	$con= new DB_mysqli();
	
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	$con->select_db($con->catalogo);	

 	session_start(); 	
 
		$row["IDAFILIADO"]=strtoupper($_POST["idafiliado"]);
		$row["MARCA"]=strtoupper($_POST["txtmarca"]);
		$row["SUBMARCA"]=strtoupper($_POST["txtsubmarca"]);
		$row["ANIO"]=strtoupper($_POST["cmbanio"]);
		$row["COLOR"]=$_POST["txtcolor"];
		$row["PLACA"]=$_POST["txtplaca"];
		$row["NUMVIN"]=$_POST["txtvin"];
		$row["NUMSERIEMOTOR"]=$_POST["txtmotor"];
		$row["NUMSERIECHASIS"]=$_POST["txtserie"];
		$row["IDFAMILIAVEH"]=$_POST["cmbfamilia"];
		$row["USO"]=$_POST["cmbuso"];
		$row["ARRCOMBUSTIBLE"]=$_POST["cmbcombustible"];
		$row["ARRTRANSMISION"]=$_POST["cmbtrasmision"];
		$row["ARRPESO"]=$_POST["cmbpeso"];
		$row["NEWREGISTRO"]=1;
		$row["IDUSUARIOMOD"]=$_SESSION["user"];

		if($_POST["idvehiculo"])
		 {
			$respuesta=$con->update("catalogo_afiliado_persona_vehiculo",$row,"where ID='".$_POST['idvehiculo']."' ");
		 }
		else
		 {
			$row["IDUSUARIOCREACION"]=$_SESSION["user"];
			$row["FECHACREACION"]=date("Y-m-d H:i:s");
		
			$respuesta=$con->insert_reg("catalogo_afiliado_persona_vehiculo",$row);		
			$data=$con->reg_id();
			
			$row2["ID"]=$data;
			$row2["IDAFILIADO"]=$_POST["idafiliado"];
			$row2["IDUSUARIOMOD"]=$_SESSION["user"];		
			if($data)	$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_vehiculo_log",$row2);
			
		 }		
				
		echo "<script>";
		if(!$respuesta)
		 {
			echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
			echo "document.location.href='vehicular_dafiliado.php?idafiliado=".$_POST["idafiliado"]."' ";
		 }
		else
		 {
			echo "document.location.href='vehicular_dafiliado.php?idafiliado=".$_POST["idafiliado"]."' ";
		 }
		echo "</script>";
	
?>