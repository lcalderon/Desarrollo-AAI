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

		$search = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ã¡,Ã©,Ã­,Ã³,Ãº,Ã±,ÃÃ¡,ÃÃ©,ÃÃ­,ÃÃ³,ÃÃº,ÃÃ±");
		$replace = explode(",","á,é,í,ó,ú,Ñ,Á,É,Í,Ó,Ú,Ñ,á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ");
		$observacion= str_replace($search, $replace, strtoupper($_POST["txtaobservacion"])); 
 
		if($_POST["cmb_proveedor"] >0) $idproveedor= $_POST["cmb_proveedor"]; else $idproveedor=$_POST["cmbproveedor"];

		$cvedeficiencia=$_POST["hid_cvedefeficiencia"];
		$idasistencia = $_POST["hid_idasistencia"];
		$idexpediente=$_POST["hid_idexpediente"];
		$observacion=utf8_encode($observacion);
		$idproveedor =$idproveedor;
		$coordinador=$_POST["cmbcoordinador"];
		$supervisor = $_SESSION["user"];
		$idetapa = $_POST["hid_idetapa"];
		$origen=$_POST["hid_accion"];
		$idprincipal=$_POST["idprincipal"];

		if($origen =="nuevo"){
	
			if($idproveedor=="") $idproveedor=0;
			$def[IDDEFICIENCIA_CORRELATIVO]="";
			$def[CVEDEFICIENCIA]=$cvedeficiencia;
			$def[IDETAPA]=$idetapa;
			$def[IDASISTENCIA]=$idasistencia;
			$def[IDEXPEDIENTE]=$idexpediente;
			$def[ORIGEN]="MANUAL";
			$def[MOVIMIENTO]=$origen;
			$def[IDPROVEEDOR]=$idproveedor;
			$def[IDCOORDINADOR]=$coordinador;
			$def[IDSUPERVISOR]=$supervisor;
			
			$resp=$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
			$idprincipal=$con->reg_id();
			
		} elseif($origen =="valida"){
	
			if($idproveedor =="") $idproveedor=0;
		
			$defu[MOVIMIENTO]=$origen;
			$defu[IDCOORDINADOR]=$coordinador;
			$defu[IDSUPERVISOR]=$supervisor;
			$resp=$con->update("$con->temporal.expediente_deficiencia",$defu,"WHERE IDDEFICIENCIA_CORRELATIVO='$idprincipal'");
		
			$asist[STATUSCALIDAD]="EVALUADO";
			$con->update("asistencia",$asist,"WHERE IDASISTENCIA ='".$idasistencia."' AND STATUSCALIDAD!='CERRADO' ");
	
		} elseif($origen =="retira"){
	
			if($idproveedor =="") $idproveedor=0;
			$defur[MOVIMIENTO]=$origen;
			$defur[IDPROVEEDOR]=$idproveedor;
			$defur[IDCOORDINADOR]=$coordinador;
			$defur[IDSUPERVISOR]=$supervisor;
			$defur[ACTIVO]=0;
			 
			$resp=$con->update("$con->temporal.expediente_deficiencia",$defur,"WHERE IDDEFICIENCIA_CORRELATIVO='$idprincipal'");
			
			$asist[STATUSCALIDAD]="EVALUADO";
			$con->update("asistencia",$asist,"WHERE IDASISTENCIA ='".$idasistencia."' AND STATUSCALIDAD!='CERRADO' ");		
		}
	
	//historico deficiencia
		$defb[IDDEFICIENCIA_CORRELATIVO]=$idprincipal;
		$defb[CVEDEFICIENCIA]=$cvedeficiencia;
		$defb[IDETAPA]=$idetapa;
		$defb[IDASISTENCIA]=$idasistencia;
		$defb[IDEXPEDIENTE]=$idexpediente;
		$defb[COMENTARIO]=$observacion;
		$defb[IDPROVEEDOR]=$idproveedor;
		$defb[IDCOORDINADOR]=$coordinador;
		$defb[IDUSUARIOMOD]=$supervisor;
		$defb[ORIGEN]=$origen;
		
		if($resp) $con->insert_reg("$con->temporal.expediente_deficiencia_bitacora",$defb);
 
 	echo "<script language='javascript'>";
	echo "parent.win.close();";	
	echo "</script>";

?>