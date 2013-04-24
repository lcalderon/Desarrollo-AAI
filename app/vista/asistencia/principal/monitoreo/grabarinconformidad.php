<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../includes/arreglos.php");	
	
	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	session_start();

	$id_expediente=$_POST["id_expediente"];
	 
	$infoinconformidad=$con->consultation("SELECT IDCUENTA,IDPROGRAMA FROM  $con->temporal.expediente WHERE IDEXPEDIENTE='$id_expediente' ");

	$row["IDINCONFORMIDAD"]="";
	$row["FECHAINCONFORMIDAD"]=date("Y-m-d H:i:s");
	$row["IDCUENTA"]=$infoinconformidad[0][0];
	$row["IDPROGRAMA"]=$infoinconformidad[0][1];
	$row["IDASISTENCIA"]=$_POST["id_asistencia"];
	$row["MOTIVOLLAMADA"]=$_POST['txtmotllamada'];
	$row["IDDETMOTIVOLLAMADA"]=$_POST['cmbopciones'];
	$row["COMENTARIO"]=$_POST["txtacomentario"];
	$row["STATUS_SEGUIMIENTO"]="CER";
	$row["IDGRUPO"]=$_POST["cmbasignacionc"];
	$row["ARRPROCEDENCIA"]=$_POST["cmbprocedencia"]; 
	$row["IDUSUARIO"]=$_SESSION["user"];
	
	//Inserta los datos inconformidad

		$respinconformidad=$con->insert_reg("$con->temporal.inconformidad",$row);	
		if($respinconformidad)	$idinconformidad=$con->reg_id();
		
	//registrar bitacora
 
		$asis_bitacora[IDASISTENCIA]=$_POST["id_asistencia"];
		$asis_bitacora[COMENTARIO]="*** SE GENERO LA INCONFORMIDAD NRO $idinconformidad";
		$asis_bitacora[IDUSUARIOMOD]=$_SESSION["user"];

		if($idinconformidad)
		 {
			$con->insert_reg("$con->temporal.asistencia_bitacora_etapa8",$asis_bitacora);						 
		 }
	
	echo "<script>";
	if(!$idinconformidad)	echo "alert('"._("HUBO UN PROBLEMA, NO SE REGISTRO LA INCONFORMIDAD").".');";	else echo "alert('"._("SE GENERO LA INCONFORMIDAD ")."NRO $idinconformidad.');";
    echo "parent.win.close();";
    echo "</script>";
	
?>