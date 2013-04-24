<?php
	
	include_once('../../modelo/clase_lang.inc.php');
	include_once('../../modelo/clase_mysqli.inc.php');
	include_once('../../modelo/functions.php');
	include_once("browser.class.php");
		
	$con = new DB_mysqli();
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
		
	$con->select_db($con->catalogo);
	 
	if(isset($_POST["txturlacces"]))	$urlacces="&urlacces=".$_POST["txturlacces"];


/* $sistema.="Sistema Operativo: ".getOs();
$ua=getBrowser();
$sistema.="<br> Navegador: ".$ua['name'] . " " . $ua['version'];
$sistema.="<br > Agente Completo: " .$_SERVER['HTTP_USER_AGENT'];
echo $sistema; */
	
	$navegadorInfo=getBrowsers();
	
	list($fechahora,$fecha,$hora)=fechahora();

	//usuario bloqueado e inactivo
	$rsbloquado=$con->consultation("select BLOQUEADO,ACTIVO,COUNT(*) AS EXISTE from catalogo_usuario where IDUSUARIO='".strtoupper($_POST["txtusuario"])."'");
	if($rsbloquado[0][0]==1 and $rsbloquado[0][2] ==1)
	 {
		header("location:../../vista/login/index.php?msg=4$urlacces");
		exit;
	 }	
	else if($rsbloquado[0][1]==0  and $rsbloquado[0][2] ==1)
	 {
		header("location:../../vista/login/index.php?msg=5$urlacces");
		exit;
	 }
	 
	//tiempo maximo
	$rstiempo=$con->consultation("select if(DATO is null or DATO='',DATODEFAULT,DATO) as numerador from catalogo_parametro where IDPARAMETRO='TIEMPO_BLOQUEO_LOGIN' ");
	$tiempo=$rstiempo[0][0];
	//intentos maximo
	$rsintentos=$con->consultation("select if(DATO is null or DATO='',DATODEFAULT,DATO) as numerador from catalogo_parametro where IDPARAMETRO='NUMERO_INTENTOS_LOGIN' ");
	$maxintentos=$rsintentos[0][0];
	if($maxintentos==0)	$maxintentos=1;
	
	$rsusuario=$con->query("select IDUSUARIO,CONTRASENIA from catalogo_usuario where IDUSUARIO='".strtoupper($_POST["txtusuario"])."' ");

		if($rsusuario->num_rows ==1)	// Se comprueba si existe
		 {
            //guardamos los valores en la variable $row
			$row = $rsusuario->fetch_assoc(); 
			
			// Ahora comprobamos si tiene menos de $maxintentos intentos fallidos	
			$permitido=$con->consultation("select count(distinct IDUSUARIO) as bloqueado from $con->temporal.seguridad_acceso where IDUSUARIO='".strtoupper($_POST["txtusuario"])."' and (select COUNT(usu1.IDUSUARIO) as intentosmax from $con->temporal.seguridad_acceso as usu1 where usu1.IDUSUARIO='".strtoupper($_POST["txtusuario"])."' and usu1.FECHAMOD > (select MAX(FECHAMOD) from $con->temporal.seguridad_acceso as usu2 where usu2.IDUSUARIO='".strtoupper($_POST["txtusuario"])."' and IDUSUARIOMOVIMIENTO not in ('CI') ) ) >= $maxintentos AND NOW() > DATE_ADD((select max(FECHAMOD) from $con->temporal.seguridad_acceso where IDUSUARIO='".strtoupper($_POST["txtusuario"])."' and  IDUSUARIOMOVIMIENTO='CI'), INTERVAL $tiempo MINUTE) ");
			if($permitido[0]["bloqueado"] ==1)
			 {
					$rowper["IDUSUARIO"]=strtoupper($_POST["txtusuario"]);
					$rowper["APLICATIVO"]=_("SOAANG");
					$rowper["FECHAMOD"]=$fechahora;
					$rowper["IDUSUARIOMOVIMIENTO"]="FB";
					$rowper["IP"]=$_SERVER["REMOTE_ADDR"];
					$rowper["HOSTNAME"]="";
					$rowper["MACHINEID"]=" ";
					$rowper["VERSIONAPLICATIVO"]=$con->version;
					$rowper["NAVEGADOR"]=$navegadorInfo['name'];
					$rowper["VERSIONAVEGADOR"]=$navegadorInfo['version'];
					$rowper["EXTENSION"]=$_POST["txtextension"];
					$rowper["SOPERATIVO"]=getOs();
					
					$valor=$con->insert_reg("$con->temporal.seguridad_acceso",$rowper);
			 }
			 
			$intentos = $con->consultation("select COUNT(usu1.IDUSUARIO) as intentosmax from $con->temporal.seguridad_acceso as usu1 where usu1.IDUSUARIO='".strtoupper($_POST["txtusuario"])."' and usu1.FECHAMOD > (select MAX(FECHAMOD)  from $con->temporal.seguridad_acceso as usu2 where usu2.IDUSUARIO='".strtoupper($_POST["txtusuario"])."' and IDUSUARIOMOVIMIENTO !=('CI') ) and DATE_FORMAT(usu1.FECHAMOD,'%Y-%m-%d') = CURDATE() ");
 	
            if($intentos[0]["intentosmax"] < $maxintentos )
			 {
                /// En el caso de tener menos de $maxintentos intentos, se comprueba si la contraseña es correcta
                if(strtoupper(sha1(strtoupper($_POST["txtpassword"]))) == $row["CONTRASENIA"])
				 {	
					$rowacc["IDUSUARIO"]=strtoupper($_POST["txtusuario"]);
					$rowacc["APLICATIVO"]=_("SOAANG");
					$rowacc["FECHAMOD"]=$fechahora;
					$rowacc["IDUSUARIOMOVIMIENTO"]="LOGIN";
					$rowacc["IP"]=$_SERVER["REMOTE_ADDR"];
					$rowacc["HOSTNAME"]="";
					$rowacc["MACHINEID"]=" ";
					$rowacc["VERSIONAPLICATIVO"]=$con->version;
					$rowacc["NAVEGADOR"]=$navegadorInfo['name'];
					$rowacc["VERSIONAVEGADOR"]=$navegadorInfo['version'];
					$rowacc["EXTENSION"]=$_POST["txtextension"];
					$rowacc["SOPERATIVO"]=getOs();
					
					$con->insert_reg("$con->temporal.seguridad_acceso",$rowacc);
					
					$rowses["EXTENSION"]=$_POST["txtextension"];
					$rowses["SERVER_IP"]=$_SERVER["REMOTE_ADDR"];
					$rowses["PROGRAMA"]=_("SOAANG");
					$rowses["START_TIME"]=$fechahora;
					$rowses["SESION_NAME"]=$row["IDUSUARIO"];				
					
					// echo $_POST["txturlacces"]."--";
                    session_start(); // Se inicia la session
                    $_SESSION["user"] = $row["IDUSUARIO"]; // Variable de session que contiene al usuario
                    $_SESSION["userhost"] = $con->Prefix."soaang";					
					$_SESSION["extension"]= $_POST["txtextension"];
	
                   if(isset($_POST["txturlacces"]) and !empty($_POST["txturlacces"])){ 
					
						$urlFinal="https://".$_SERVER['HTTP_HOST'].urldecode($_POST["txturlacces"]);	
				
						header("Location:$urlFinal");
					} else{
						
						header("Location:../../../");
					}

                 }
				else
				 {			
					//contrasena inconrrecta
				
					$rowci["IDUSUARIO"]=strtoupper($_POST["txtusuario"]);
					$rowci["APLICATIVO"]=_("SOAANG");
					$rowci["IDUSUARIOMOVIMIENTO"]="CI";
					$rowci["IP"]=$_SERVER["REMOTE_ADDR"];
					$rowci["HOSTNAME"]="";
					$rowci["MACHINEID"]=" ";
					$rowci["VERSIONAPLICATIVO"]=$con->version;
					$rowci["NAVEGADOR"]=$navegadorInfo['name'];
					$rowci["VERSIONAVEGADOR"]=$navegadorInfo['version'];
					$rowci["EXTENSION"]=$_POST["txtextension"];
					$rowci["SOPERATIVO"]=getOs();
					
					$contrasena_inc=$con->insert_reg("$con->temporal.seguridad_acceso",$rowci);			 
	
					header("location:../../vista/login/index.php?msg=1$urlacces");
                 }
             }
			else
			 {
				//bloquear usuario x minutos
					
				 header("location:../../vista/login/index.php?msg=2$urlacces");
			 }
         } 
		else 
		 {
			//no existe usuario

				$rows["USUARIO_INVALIDO"]=strtoupper($_POST["txtusuario"]);
				$rows["APLICATIVO"]=_("SOAANG");
				$rowci["IDUSUARIOMOVIMIENTO"]="NU";
				$rows["FECHAMOD"]=$fechahora;
				$rows["TIPOACCESO"]="";
				$rows["IP"]=$_SERVER["REMOTE_ADDR"];
				$rows["HOSTNAME"]="";
				$rows["MACHINEID"]=" ";
				$rows["VERSIONAPLICATIVO"]=$con->version;
				$rows["NAVEGADOR"]=$navegadorInfo['name'];
				$rows["VERSIONAVEGADOR"]=$navegadorInfo['version'];
				$rows["EXTENSION"]=$_POST["txtextension"];
				$rows["SOPERATIVO"]=getOs();
			
			
						
			header("location:../../vista/login/index.php?msg=3$urlacces");
		 }
?>