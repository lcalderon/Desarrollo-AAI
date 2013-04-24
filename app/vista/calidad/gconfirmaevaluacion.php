<?php

	session_start();

	include_once("../../modelo/clase_mysqli.inc.php");
	include_once("../../vista/login/Auth.class.php");

	Auth::required();

	$con = new DB_mysqli();
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

//registramos usuario que valido deficiencia
		$sup[IDASISTENCIA]=$_POST["hid_asistencia"];
		$sup[IDUSUARIO]=$_SESSION["user"];
		$sup[PROCESO]="CONFIRMACION";
		$sup[OBSERVACION]=$_GET["cmbevaluacion"];
		$con->insert_reg("$con->temporal.asistencia_usuario_calidad",$sup);

//actualizamos la tabla asistencia para indicar que las deficiencias fueron confirmadas y evaluadas
		$asist[STATUSCALIDAD]=$_GET["cmbevaluacion"];
		$con->update("$con->temporal.asistencia",$asist,"  WHERE IDASISTENCIA ='".$_POST["hid_asistencia"]."'");

	echo "<script language='javascript'>";
	echo "parent.close();";		
	echo "</script>";
?>
