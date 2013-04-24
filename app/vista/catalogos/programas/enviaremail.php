<?php
	
	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start(); 
	include_once('phpmailer/class.phpmailer.php');

	$result=$con->query("SELECT catalogo_programa.IDPROGRAMA,catalogo_programa.PILOTO,catalogo_programa.FECHAFINVIGENCIA,catalogo_programa.FECHAINIVIGENCIA,catalogo_programa.NOMBRE,catalogo_cuenta.NOMBRE AS cuenta FROM catalogo_programa INNER JOIN catalogo_cuenta ON catalogo_cuenta.IDCUENTA=catalogo_programa.IDCUENTA where catalogo_programa.IDPROGRAMA='".$_POST["programa"]."'");
	$row = $result->fetch_object();
	
	$cuerpo_mail = "<html><head><title>American Assist</title></head><body>";
	$cuerpo_mail = "<p>Id: $row->IDPROGRAMA <br/>Programa: $row->NOMBRE<br/>Cuenta: $row->cuenta<br/>Piloto: $row->PILOTO<br/>Inicio de Vigencia: $row->FECHAINIVIGENCIA<br/>Fin de Vigencia: $row->FECHAFINVIGENCIA<br/></p>";
	$data=$con->consultation("SELECT  count(*) as cantidad from catalogo_programa_servicio where IDPROGRAMA='".$_POST["programa"]."'");
	
	if($data[0][0] > 0)
	 {
	$cuerpo_mail.="
			<table border='0'cellpadding='1' cellspacing='1' width='90%' bgcolor='#FFFFFF' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif' >
			<tr bgcolor='#666666' >				
				<th style='color:#FFFFFF'>SERVICIO</th>
				<th style='color:#FFFFFF'>MONTO</th>
				<th style='color:#FFFFFF'>#EVENTOS</th>
				<th style='color:#FFFFFF'>FRECUENCIA</th>
			</tr>";
	
			//$rservicios=$con->query("select catalogo_programa.NOMBRE AS programa,catalogo_moneda.DESCRIPCION as nombremoneda,if(catalogo_programa_servicio.TIPOCOBERTURA='CX','Conexion',if(catalogo_programa_servicio.TIPOCOBERTURA='CP','CoPago',if(catalogo_programa_servicio.TIPOCOBERTURA='PE','Por Evento','Sin L&iacute;mite'))) as tipcobertura,catalogo_programa_servicio.MONTOXSERV,catalogo_programa_servicio.IDPROGRAMA,catalogo_programa_servicio.IDSERVICIO,catalogo_programa.IDPROGRAMA,catalogo_programa.NOMBRE,catalogo_servicio.DESCRIPCION, catalogo_programa_servicio.IDMONEDA,catalogo_tipofrecuencia.NOMBRE,catalogo_programa_servicio.EVENTOS from catalogo_programa_servicio inner join catalogo_servicio on catalogo_servicio.IDSERVICIO=catalogo_programa_servicio.IDSERVICIO inner join catalogo_programa  on catalogo_programa.IDPROGRAMA=catalogo_programa_servicio.IDPROGRAMA inner join catalogo_tipofrecuencia  on catalogo_tipofrecuencia.IDTIPOFRECUENCIA=catalogo_programa_servicio.IDTIPOFRECUENCIA inner join catalogo_moneda  on catalogo_moneda.IDMONEDA=catalogo_programa_servicio.IDMONEDA where catalogo_programa.IDPROGRAMA='".$_POST["programa"]."' order by catalogo_servicio.DESCRIPCION ");
			$rservicios=$con->query("SELECT catalogo_moneda.DESCRIPCION AS nombremoneda,IF(catalogo_programa_servicio.TIPOCOBERTURA='CX','Conexion',IF(catalogo_programa_servicio.TIPOCOBERTURA='CP','CoPago',IF(catalogo_programa_servicio.TIPOCOBERTURA='PE','Por Evento','Sin L&iacute;mite'))) AS tipcobertura,catalogo_programa_servicio.MONTOXSERV,catalogo_programa_servicio.IDPROGRAMA,catalogo_programa_servicio.IDSERVICIO,catalogo_servicio.DESCRIPCION, catalogo_programa_servicio.IDMONEDA,catalogo_tipofrecuencia.NOMBRE,catalogo_programa_servicio.EVENTOS FROM catalogo_programa_servicio INNER JOIN catalogo_servicio ON catalogo_servicio.IDSERVICIO=catalogo_programa_servicio.IDSERVICIO INNER JOIN catalogo_tipofrecuencia  ON catalogo_tipofrecuencia.IDTIPOFRECUENCIA=catalogo_programa_servicio.IDTIPOFRECUENCIA INNER JOIN catalogo_moneda  ON catalogo_moneda.IDMONEDA=catalogo_programa_servicio.IDMONEDA WHERE catalogo_programa_servicio.IDPROGRAMA='".$_POST["programa"]."' ORDER BY catalogo_servicio.DESCRIPCION  ");
	
			while($rowserv = $rservicios->fetch_object())
			 {
				if($rowserv->MONTOXSERV == "0.00")	$rowserv->MONTOXSERV="";
				if($rowserv->EVENTOS == "0.00")	$rowserv->EVENTOS="Ilimitado";
				
				$monto=$rowserv->MONTOXSERV." ".$rowserv->nombremoneda." ".$rowserv->tipcobertura;	
		
				$cuerpo_mail.="
					<tr bgcolor='#BDCCDF' >
						<td align='left'>$rowserv->DESCRIPCION</td>
						<td align='right'>$monto</td>
						<td>$rowserv->EVENTOS</td>			
						<td>$rowserv->NOMBRE</td>
					</tr>"; 		 
			 }
				 
			$cuerpo_mail.=" </table> ";
		}
			
						
 	 $rsemail=$con->query("SELECT SUBSTRING($con->temporal.seguridad_modulosxusuario.IDMODULO,19,10) AS tipo,CONCAT(APELLIDOS,',',NOMBRES) AS nombreusu,EMAIL FROM $con->temporal.seguridad_modulosxusuario INNER JOIN catalogo_usuario ON catalogo_usuario.IDUSUARIO=$con->temporal.seguridad_modulosxusuario.IDUSUARIO  WHERE IDMODULO IN('PROGRAMAS_AUTORIZASISTEMAS','PROGRAMAS_AUTORIZACOMERCIAL','PROGRAMAS_AUTORIZAFINANZAS','PROGRAMAS_AUTORIZACALIDAD')");	
	 while($row = $rsemail->fetch_object())
	  {
		$direccion="";
 
 	 	 if($row->tipo=="SISTEMAS")
		  {
				$mail = new phpmailer();

				$mail->IsSMTP();
				$mail->IsHtml(true); //  Establecer formato HTML
				$mail->Subject = 'CONFORMIDAD DEL NUEVO PROGRAMA.';
	
			$clave=$con->consultation("select CLAVE from catalogo_programa_conformidad where IDPROGRAMA='".$_POST["programa"]."' and NOMBRE='".$row->tipo."'");
			$clave=$clave[0][0];
			$cuerpo_mail2.="<BR><a href='https://200.62.224.237:800/app/vista/catalogos/programas/conformidad.php?programa=".$_POST["programa"]."&codigo=$clave'>Click aqu&iacute; para aceptar el Programa</a>";
			$cuerpo_mail2.="<BR><BR><a href='https://200.62.224.237:800/app/vista/catalogos/programas/motivorechazo.php?programa=".$_POST["programa"]."&codigo=$clave'>Click aqu&iacute; para rechazar el Programa</a>";
			$cuerpo_mail2.="</body></html>";
			$direccion=$row->EMAIL;
			$mail->AddAddress($direccion);
			$mail->Body = $cuerpo_mail.$cuerpo_mail2;
			$mail->AltBody = "";
			
		 } 
		else if($row->tipo=="FINANZAS")	
		 {
			$mail = new phpmailer();

			$mail->IsSMTP();
			$mail->IsHtml(true); //  Establecer formato HTML
			$mail->Subject = 'CONFORMIDAD DEL NUEVO PROGRAMA.';	
			
			$clave=$con->consultation("select CLAVE from catalogo_programa_conformidad where IDPROGRAMA='".$_POST["programa"]."' and NOMBRE='".$row->tipo."'");
			$clave=$clave[0][0];		 
			$cuerpo_mail2.="<BR><a href='https://200.62.224.237:800/app/vista/catalogos/programas/conformidad.php?programa=".$_POST["programa"]."&codigo=$clave'>Click aqu&iacute; para aceptar el Programa</a>";
			$cuerpo_mail2.="<BR><BR><a href='https://200.62.224.237:800/app/vista/catalogos/programas/motivorechazo.php?programa=".$_POST["programa"]."&codigo=$clave'>Click aqu&iacute; para rechazar el Programa</a>";			
			$cuerpo_mail2.= "</body></html>";
			$mail->AddAddress($row->EMAIL);
			$mail->Body = $cuerpo_mail.$cuerpo_mail2;						
		 }	
		else if($row->tipo=="CALIDAD")
		 {
		 
			$mail = new phpmailer();

			$mail->IsSMTP();
			$mail->IsHtml(true); //  Establecer formato HTML
			$mail->Subject = 'CONFORMIDAD DEL NUEVO PROGRAMA.';	
			
			$clave=$con->consultation("select CLAVE from catalogo_programa_conformidad where IDPROGRAMA='".$_POST["programa"]."' and NOMBRE='".$row->tipo."'");
			$clave=$clave[0][0];		 
			$cuerpo_mail2.="<BR><a href='https://200.62.224.237:800/app/vista/catalogos/programas/conformidad.php?programa=".$_POST["programa"]."&codigo=$clave'>Click aqu&iacute; para aceptar el Programa</a>";
			$cuerpo_mail2.="<BR><BR><a href='https://200.62.224.237:800/app/vista/catalogos/programas/motivorechazo.php?programa=".$_POST["programa"]."&codigo=$clave'>Click aqu&iacute; para rechazar el Programa</a>";
			$cuerpo_mail2.= "</body></html>";
			$direccion=$row->EMAIL;
			$mail->AddAddress($direccion);
			$mail->Body = $cuerpo_mail.$cuerpo_mail2;

		 }	
		else if($row->tipo=="OPERACIONES")		
		 {
			$mail = new phpmailer();

			$mail->IsSMTP();
			$mail->IsHtml(true); //  Establecer formato HTML
			$mail->Subject = 'CONFORMIDAD DEL NUEVO PROGRAMA.';	
			
			$clave=$con->consultation("select CLAVE from catalogo_programa_conformidad where IDPROGRAMA='".$_POST["programa"]."' and NOMBRE='".$row->tipo."'");
			$clave=$clave[0][0];		 
			$cuerpo_mail2.="<BR>	<a href='https://200.62.224.237:800/app/vista/catalogos/programas/conformidad.php?programa=".$_POST["programa"]."&codigo=$clave'>Click aqu&iacute; para aceptar el Programa</a>";
			$cuerpo_mail2.="<BR><BR><a href='https://200.62.224.237:800/app/vista/catalogos/programas/motivorechazo.php?programa=".$_POST["programa"]."&codigo=$clave'>Click aqu&iacute; para rechazar el Programa</a>";			
			$cuerpo_mail2.= "</body></html>";
			$mail->AddAddress($row->EMAIL);
			$mail->Body = $cuerpo_mail.$cuerpo_mail2;

		 }	
			
			if($_POST["programa"]!="")	$oka=$mail->Send();			
			
			$clave=""; 
			$cuerpo_mail2="";
	  } 
	 
  
	if(!$oka) 
	 {
		echo 'HUBO UN PROBLEMA, NO SE ENVIARON LOS EMAILS.';
	 }
	else
	 {
		echo 'LOS EMAILS FUERON ENVIADOS SATISFACTORIAMENTE.';
	 }

	
	   // 1.
      //IP compartido
   // 2.
      // echo "IP Share: " . $_SERVER['HTTP_CLIENT_IP'] . "<br />";
   // 3.
      //IP Proxy
   // 4.
      // echo "IP Proxy: " . $_SERVER['HTTP_X_FORWARDED_FOR'] . "<br />";
   // 5.
      //IP Acceso
   // 6.
      // echo "IP Access: " . $_SERVER['REMOTE_ADDR'] . "<br />";



?>