<?php

	session_start();
 
	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../vista/login/Auth.class.php");
	
	Auth::required();
 	
	$con = new DB_mysqli();	
	if ($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

		//obtenemos las variables
		$idasistencia=$_POST["asistencia"];
		$p1 = $_POST["p1"];
		$p2 = $_POST["p2"];
		$p3 = $_POST["p3"];
		$p4 = $_POST["p4"];
	 	$p5 = $_POST["p5"];
		$a1 = $_POST["a1"];
		$a2 = $_POST["a2"];  
		$a3 = $_POST["a3"];
	
		if($_POST["txtPath"]) $grabacion= $_POST["txtPath"];		
 
		$enc["IDASISTENCIA"]=$idasistencia;
		$enc["P1"]=$p1;
		$enc["P2"]=$p2;
		$enc["P3"]=$p3;
		$enc["P4"]=$p4;
		$enc["P5"]=$p5;
		$enc["A1"]=$a1;
		$enc["A2"]=$a2;
		$enc["A3"]=$a3;	
		if($_POST["txtPath"]) $enc["GRABACION"]=utf8_encode($grabacion);
		$enc["IDCOORDINADOR"]=$_POST["cmbcoordinador"];
		$enc["OBSERVACION"]=utf8_encode($_POST["txtacomentario"]);

	
		//calculamos el promedio de la auditoria
		if($a1==0 || $a2 ==0 || $a3==1) $enc["EVALUACIONAUDITOR"] = (($p1+$p2+$p3+$p4+$p5)/5)*5; else $enc["EVALUACIONAUDITOR"] = (($p1+$p2+$p3+$p4+$p5)/5)*10;
	  
		//insertamos los datos de la auitoria
		
		$hist["IDASISTENCIA"]=$idasistencia;;
		$hist["IDAUDITOR"]=$_SESSION["user"];
		$hist["STATUSAUDITA"]=$_POST["cmbevaluacionAud"];	
		
		if(!$_POST["idAuditoria"]){
		
			$enc["IDAUDITOR"]=$_SESSION["user"];		
			$con->insert_reg("$con->temporal.asistencia_auditoria_calidad",$enc);
		 
			//Grabar historico		
			$con->insert_reg("$con->temporal.asistencia_auditoria_historico",$hist); 
			
		} else{

			$con->update("$con->temporal.asistencia_auditoria_calidad",$enc,"WHERE IDASISTENCIA='$idasistencia'");			
			//Grabar historico		
			$con->insert_reg("$con->temporal.asistencia_auditoria_historico",$hist); 
		}
	
		//ACTUALIZAMOS EL ESTATUS EN LA TABLA ASISTENCIA
		$eval["EVALAUDITORIA"]=$_POST["cmbevaluacionAud"];
		$con->update("$con->temporal.asistencia",$eval,"WHERE IDASISTENCIA='$idasistencia'");
		
		//REGISTROS EL USUARIO QUE HIZO LA AUDITORIA
		$sup["IDASISTENCIA"]=$idasistencia;
		$sup["IDUSUARIO"]=$_SESSION["user"];
		$sup["PROCESO"]="AUDITO";
		$sup["OBSERVACION"]=$_POST["cmbevaluacionAud"];
		$con->insert_reg("$con->temporal.asistencia_usuario_calidad",$sup);	
?>
<script>	
	document.location.href='gestionarAuditoria.php?asistencia=<?=$idasistencia?>';	
</script>