<?php

	include_once('../../../../app/modelo/clase_mysqli.inc.php');
	include_once('../../../../app/modelo/functions.php');

	$con= new DB_mysqli();
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
 	session_start();  
 	
	//	$dato=$con->consultation("select count(*) as valor from $con->catalogo.catalogo_afiliado_medio_pago where IDAFILIADO='".$_POST['idafiliado']."' and ID='".$_POST["idmediopg"]."'");

		if(!$_POST["id"])
		 {
			$row["ID"]="";
			$row["IDMEDIOPAGO"]=$_POST['cmbtipotarjeta'];
			$row["IDAFILIADO"]=$_POST['idafiliado'];
			$row["NOMBRETITULAR"]=strtoupper($_POST['txttitular']);
			$row["IDDOCUMENTO"]=$_POST['txtiddocumento'];
			$row["NUMEROTARJETA"]=$_POST['txtnumtarjeta'];
			$row["DIGITOVERIFICADOR"]=$_POST['txtdigitover'];
			$row["FECHAVENCIMIENTO"]=$_POST['cmbmes']."/".$_POST['cmbanio'];
			$row["CODIGOSEGURIDAD"]=$_POST['txtcodigoseg'];
			$row["IDUSUARIOMOD"]=$_SESSION["user"];
			$row["IDUSUARIOCREACION"]=$_SESSION["user"];
			$row["FECHACREACION"]=date("Y-m-d H:i:s");

	//Inserta los datos

			$respuesta=$con->insert_reg("$con->catalogo.catalogo_afiliado_medio_pago",$row);		
		 }
		else
		 {
			$row["IDMEDIOPAGO"]=strtoupper($_POST['cmbtipotarjeta']);
			$row["NOMBRETITULAR"]=strtoupper($_POST['txttitular']);
			$row["IDDOCUMENTO"]=$_POST['txtiddocumento'];
			$row["NUMEROTARJETA"]=$_POST['txtnumtarjeta'];
			$row["DIGITOVERIFICADOR"]=$_POST['txtdigitover'];
			$row["FECHAVENCIMIENTO"]=$_POST['cmbmes']."/".$_POST['cmbanio'];
			$row["CODIGOSEGURIDAD"]=$_POST['txtcodigoseg'];
			$row["IDUSUARIOMOD"]=$_SESSION["user"];
			
			$respuesta=$con->update("$con->catalogo.catalogo_afiliado_medio_pago",$row,"where ID='".$_POST['id']."' ");
		 }
		
	echo "<script>";
	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	echo "document.location.href='formapago.php?idafiliado=".$_POST['idafiliado']."' ";	
    echo "</script>";
	
?>