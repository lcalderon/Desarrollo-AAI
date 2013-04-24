<?php

	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../vista/login/Auth.class.php");	
	$con = new DB_mysqli();

		
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
	session_start();
	Auth::required($_POST["txturl"]);

	function registar_parametro($data,$id,$con)
	 {	
		$con->query("update $con->catalogo.catalogo_parametro set DATO='".$data."' where IDPARAMETRO='".$id."' ");		
	 }

	$rowcamp[]=($_POST["cmblocale"])?$_POST["cmblocale"].".UTF-8":$_POST["cmblocale"];
	$rowcamp[]=$_POST["cmbpais"];
	$rowcamp[]=$_POST["txtsociedad"];	
	$rowcamp[]=$_POST["cmbgmt"];
	$rowcamp[]=$_POST["txtintervaloext"];
	$rowcamp[]=$_POST["txtintervaloint"];
	$rowcamp[]=$_POST["txtcalldesbordedes"];
	$rowcamp[]=$_POST["txtcalldesbordehas"];
	$rowcamp[]=$_POST["txtcallnormaldes"];
	$rowcamp[]=$_POST["txtcallnormalhas"];
	$rowcamp[]=$_POST["txtcallviciodes"];
	$rowcamp[]=$_POST["txtcallviciohas"];
	$rowcamp[]=$_POST["txtlongitudec"];
	$rowcamp[]=$_POST["txtncatalogo"];
	$rowcamp[]=$_POST["txtpcaducido"];
	$rowcamp[]=$_POST["txtdecimales"];
	$rowcamp[]=$_POST["txtmillares"];
	$rowcamp[]=$_POST["txttasigna"];
	$rowcamp[]=$_POST["txttregistro"];	
	$rowcamp[]=$_POST["txtinactividad"];	
	$rowcamp[]=$_POST["txtnubigeo"];	
	$rowcamp[]=$_POST["txtprefijo"];	
	$rowcamp[]=$_POST["txtextensioncab"];	
	$rowcamp[]=$_POST["cmbcabina"];	
	$rowcamp[]=$_POST["txtcontextocab"];	
	$rowcamp[]=$_POST["txtextensionsup"];	
	$rowcamp[]=$_POST["cmbsupervisor"];	
	$rowcamp[]=$_POST["txtcontextosup"];		
	$rowcamp[]=$_POST["txtextensionalar"];	
	$rowcamp[]=$_POST["cmbalarma"];	
	$rowcamp[]=$_POST["txtcontextoalar"];	
	$rowcamp[]=$_POST["txtipservidor"];	
	$rowcamp[]=$_POST["txtusuariomag"];	
	$rowcamp[]=$_POST["txtpassmanag"];	
	$rowcamp[]=$_POST["txtintentousu"];	
	$rowcamp[]=$_POST["txttiempousu"];	
	$rowcamp[]=$_POST["txttareas"];	
	$rowcamp[]=$_POST["txtcde"];	
	$rowcamp[]=$_POST["txtcosto"];	
	$rowcamp[]=$_POST["txtsatisfaccion"];	
	$rowcamp[]=$_POST["txtinfra"];	
	$rowcamp[]=$_POST["txtfidelidad"];	
	$rowcamp[]=$_POST["txtcdeleve"];	
	$rowcamp[]=$_POST["txtcdegrave"];	
	$rowcamp[]=$_POST["txtcdileve"];	
	$rowcamp[]=$_POST["txtcdigrave"];	
	$rowcamp[]=$_POST["tiempo_verde_monitor"];
	$rowcamp[]=$_POST["tiempo_ambar_monitor"];
	

	registar_parametro($rowcamp[0],"IDLOCALE",$con);
	registar_parametro($rowcamp[1],"IDPAIS",$con);
	registar_parametro($rowcamp[2],"IDSOCIEDAD",$con);
	registar_parametro($rowcamp[3],"GMT",$con);
	registar_parametro($rowcamp[4],"INTERVALO_MONITOR_EX",$con);
	registar_parametro($rowcamp[5],"INTERVALO_MONITOR_IN",$con);
	registar_parametro($rowcamp[6],"LLAMADA_DESBORDE_DES",$con);
	registar_parametro($rowcamp[7],"LLAMADA_DESBORDE_HAS",$con);
	registar_parametro($rowcamp[8],"LLAMADA_NORMAL_DESDE",$con);
	registar_parametro($rowcamp[9],"LLAMADA_NORMAL_HASTA",$con);
	registar_parametro($rowcamp[10],"LLAMADA_VICIO_DESDE",$con);
	registar_parametro($rowcamp[11],"LLAMADA_VICIO_HASTA",$con);
	registar_parametro($rowcamp[12],"LONGITUD_DECIMALES",$con);
	registar_parametro($rowcamp[13],"PAG_CATALOGOS",$con);
	registar_parametro($rowcamp[14],"PRCTJ_TIEMPO_CADUCID",$con);
	registar_parametro($rowcamp[15],"SEPARADOR_DECIMALES",$con);
	registar_parametro($rowcamp[16],"SEPARADOR_MILLARES",$con);
	registar_parametro($rowcamp[17],"TIEMPO_ASIGNACION",$con);
	registar_parametro($rowcamp[18],"TIEMPO_REGISTRO",$con);
	registar_parametro($rowcamp[19],"TIEMPO_DESLOGEO",$con);
	registar_parametro($rowcamp[20],"UBIGEO_NIVELES_ENTIDADES",$con);	
	registar_parametro($rowcamp[21],"PREFIJO_LLAMADAS_SALIENTES",$con);
	registar_parametro($rowcamp[22],"NUM_EXTENSION_CABINA",$con);
	registar_parametro($rowcamp[23],"PROTOCOLO_CABINA",$con);
	registar_parametro($rowcamp[24],"CONTEXTO_CABINA",$con);
	registar_parametro($rowcamp[25],"NUM_EXTENSION_SUPERVISOR",$con);
	registar_parametro($rowcamp[26],"PROTOCOLO_SUPERVISOR",$con);
	registar_parametro($rowcamp[27],"CONTEXTO_SUPERVISOR",$con);
	registar_parametro($rowcamp[28],"NUM_EXTENSION_ALARMA",$con);
	registar_parametro($rowcamp[29],"PROTOCOLO_ALARMA",$con);
	registar_parametro($rowcamp[30],"CONTEXTO_ALARMA",$con);
	registar_parametro($rowcamp[31],"IPSERVIDOR_ASTERISK",$con);
	registar_parametro($rowcamp[32],"USUARIO_MANAGER",$con);
	registar_parametro($rowcamp[33],"USUARIO_PASSWORD_MANAGER",$con);
	registar_parametro($rowcamp[34],"NUMERO_INTENTOS_LOGIN",$con);
	registar_parametro($rowcamp[35],"TIEMPO_BLOQUEO_LOGIN",$con);
	registar_parametro($rowcamp[36],"NUMERO_TAREAS",$con);
	registar_parametro($rowcamp[37],"RANKING_CDE",$con);
	registar_parametro($rowcamp[38],"RANKING_C0STO",$con);
	registar_parametro($rowcamp[39],"RANKING_SATISFACCION",$con);
	registar_parametro($rowcamp[40],"RANKING_INFRAESTRUCTURA",$con);
	registar_parametro($rowcamp[41],"RANKING_FIDELIDAD",$con);
	registar_parametro($rowcamp[42],"LEVE_CDE",$con);
	registar_parametro($rowcamp[43],"GRAVE_CDE",$con);
	registar_parametro($rowcamp[44],"LEVE_CDI",$con);
	registar_parametro($rowcamp[45],"GRAVE_CDI",$con);
	registar_parametro($rowcamp[46],"TIEMPO_VERDE_MONITOR",$con);
	registar_parametro($rowcamp[47],"TIEMPO_AMBAR_MONITOR",$con);
 
	//$data=$con->consultation("SELECT LAST_INSERT_ID()");

			$con->query("delete from $con->catalogo.catalogo_parametro_sociedad");
			$con->query("delete from $con->catalogo.catalogo_parametro_moneda");
			
			foreach($_POST['chkdesc'] as $indice => $sociedad) { 
			
				
			
				$row1["IDSOCIEDAD"]=$sociedad;
				$row1["PRIORIDAD"]=$indice;
				$con->insert_reg("$con->catalogo.catalogo_parametro_sociedad",$row1);
			}
			
			foreach($_POST['cmbmoneda'] as $indice2 => $moneda) { 
			
					$row2["IDMONEDA"]=$moneda;
					$row2["PRIORIDAD"]=$indice2;
					$con->insert_reg("$con->catalogo.catalogo_parametro_moneda",$row2);				
			}
			
			$con->query("update $con->catalogo.catalogo_pais set ACTIVO=0 where ACTIVO=1 ");
			$con->query("update $con->catalogo.catalogo_pais set ACTIVO=1 where ACTIVO=0 and  IDPAIS='".$_POST["idpais"]."'");
 
	echo "<script>";
	echo "document.location.href='parametros.php?cmbpais=".$_POST["cmbpais"]."'";
    echo "</script>";

?>