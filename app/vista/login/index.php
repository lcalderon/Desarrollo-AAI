<?php

	session_start();  

	include_once('../../modelo/clase_lang.inc.php');
	include_once('../../modelo/clase_mysqli.inc.php');
	include_once('../../modelo/functions.php');

	$con = new DB_mysqli();	
	
	if($_SESSION["userhost"] ==$con->Prefix."soaang")	header("Location:../../../");
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
 
	$con->select_db($con->catalogo);

	$rstiempo=$con->consultation("select if(DATO is null or DATO='',DATODEFAULT,DATO) as numerador from $con->catalogo.catalogo_parametro where IDPARAMETRO='TIEMPO_BLOQUEO_LOGIN' ");
	$tiempo=$rstiempo[0][0];
	$rsintentos=$con->consultation("select if(DATO is null or DATO='',DATODEFAULT,DATO) as numerador from $con->catalogo.catalogo_parametro where IDPARAMETRO='NUMERO_INTENTOS_LOGIN' ");
	$maxintentos=$rsintentos[0][0];
	
	if($_GET["msg"]==1)
	 {
		$msg=_("CONTRASENA INCORRECTA");
	 }		
	elseif($_GET["msg"]==2)
	 {
		$msg=_("SE HA REALIZADO MAS DE $maxintentos INTENTOS FALLIDOS CON ESTE USUARIO, POR SEGURIDAD SU CUENTA SE HA BLOQUEADO A PARTIR DE ESTOS MOMENTOS POR $tiempo MINUTO(S).");
	 }
	elseif($_GET["msg"]==3)
	 {
		$msg=_("NO EXISTE EL USUARIO");
	 }	
	elseif($_GET["msg"]==4)
	 {
		$msg=_("EL USUARIO ESTA BLOQUEADO PARA ACCEDER AL SISTEMA, POR FAVOR COORDINAR CON EL SUPERVISOR.");
	 }
 	elseif($_GET["msg"]==5)
	 {
		$msg=_("EL USUARIO ESTA INACTIVO PARA ACCEDER AL SISTEMA, POR FAVOR COORDINAR CON EL SUPERVISOR.");
	 }
	
	$array_result= verificar_licencia();
	$var_id= $array_result["id"];
	$var_dias_res= $array_result["num_dias_res"];
 
	$pais=substr($con->Prefix,0,2);
  	$rspais=$con->consultation("SELECT NOMBRE FROM $con->catalogo.catalogo_pais WHERE IDPAIS='".strtoupper($pais)."'");
	$nompais=$rspais[0][0]; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login al Sistema</title>
	<script type="text/javascript" src="../../../estilos/functionjs/permisos.js"></script>
	<script type="text/javascript" src="../../../estilos/functionjs/validator.js"></script>
	<link href="../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" type="image/x-icon" href="../../../imagenes/iconos/soaa.ico">	
<style type="text/css">
<!--
.style1 {color: #FFFFFF}
-->
</style>
<link href="../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
		
	<? if($_GET["msg"]){ ?>  <script> alert('<?=$msg?>'); </script> <? } ?>
	

		<script language="JavaScript">
		
			function func_ingresar(v_result, v_dias_res){
		 
				document.myForm.txtusuario.value=document.myForm.txtusuario.value.replace(/^\s*|\s*$/g,"");			
				document.myForm.txtpassword.value=document.myForm.txtpassword.value.replace(/^\s*|\s*$/g,"");			

				if(document.myForm.txtusuario.value =='' ){
					alert('<?=_("INGRESE EL USUARIO.") ;?>');
					document.myForm.txtusuario.focus();
					return (false);
				}		
				else if(document.myForm.txtpassword.value =='' ){
					alert('<?=_("INGRESE LA CONTRASENA.") ;?>');
					document.myForm.txtpassword.focus();
					return (false);
				}
				
				//Verificar resultado para el ingreso con licencia
				if(v_result == 1){
					alert('EL SISTEMA HA EXPIRADO, SOLICITAR LICENCIA.');
					return (false);
				} else if(v_result == -1){
					alert('*** QUEDAN: '+v_dias_res+' DIA(S) PARA LA EXPIRACION DEL SISTEMA, SOLICITAR LICENCIA. ***');					
				}
				
				//document.myForm.submit();
			}				
		 
		/* 	function validarcampo(){
			
				document.form1.txtusuario2.value=document.form1.txtusuario2.value.replace(/^\s*|\s*$/g,"");					
				document.form1.txtpassword2.value=document.form1.txtpassword2.value.replace(/^\s*|\s*$/g,"");					
				
				if(document.form1.txtusuario2.value =='' ){
					alert('<?=_("INGRESE EL USUARIO.") ;?>');
					document.form1.txtusuario2.focus();
					return false;
				}				
				else if(document.form1.txtpassword2.value ==''){
					alert('<?=_("INGRESE LA CONTRASENA.") ;?>');
					document.form1.txtpassword2.focus();
					return false;
				}				
				
				document.form1.submit();
			} */
		</script>
</head>
<body Onload="document.myForm.txtusuario.focus()">
	<br><br><br><br><br><br>
	<form id="myForm" name="myForm" action="acceso.php" method="POST" onSubmit = "return func_ingresar('<?=$var_id?>','<?=$var_dias_res?>')">
		<input  type="hidden" name="txturlacces" id="txturlacces" value="<?=urlencode($_GET["urlacces"])?>"/>
		<table width="295" border="0" cellpadding="1" cellspacing="1" align="center">
			<tr>
			    <td colspan="2" height="70"  background="../../../../imagenes/logos/pais/<?=$pais?>.jpg" title="SOAANG-<?=$nompais?>"></td>
			</tr>
			<tr>
			    <td  style="border:1px solid #CCCCCC" width="92" height="44"  ><img src="../../../imagenes/logos/logo_1.png"/></td>
			    <td style="border:1px solid #CCCCCC" bgcolor="#FFFFF2"><em><strong><?=_("INGRESE EL USUARIO Y LA CONTRASE&Ntilde;A") ;?></strong></em></td>
			</tr>
			<tr> 
				<td bgcolor="#336699"><span class="style1"><?=_("USUARIO") ;?></span></td>
				<td style="border:1px solid #CCCCCC"><input name="txtusuario" type="text" id="txtusuario" size="20" maxlength="20"  title="<?=_("USUARIO") ;?>" class="eventos"></td>
			</tr>
			<tr>
				<td bgcolor="#336699"><span class="style1"><?=_("CONTRASE&Ntilde;A") ;?></span></td>
				<td style="border:1px solid #CCCCCC"><input name="txtpassword" type="password" id="txtpassword" size="15" maxlength="20" title="<?=_("CONTRSENA") ;?>" class="eventos"/></td>
			</tr>
			<tr>
				<td bgcolor="#336699"><span class="style1"><?=_("EXTENSION") ;?></span></td>
				<td style="border:1px solid #CCCCCC"><input name="txtextension" type="text" id="txtextension" size="15" maxlength="12" title="<?=_("CONTRSENA") ;?>" class="eventos" value="<?=$_GET["pl"] ;?>"/></td>
			</tr>
			<tr>
				<td align="center"></td>
				<td bgcolor="#EFEFEF" style="border:1px solid #CCCCCC"><input name="btnacceder" type="submit" id="btnacceder" value="<?=_("Acceder") ;?>" title="<?=_("INICIAR SESION") ;?>" style=" width:160px"/></td>
			</tr>
			<tr>
				<td align="center"><strong><?=_("Versi&oacute;n") ;?> <?=$con->version;?></strong></td>
				<td><?=_("Fecha Liberaci&oacute;n") ;?> <?=$con->fechaversion;?></td>
			</tr>          
		</table>
		<input type="hidden" name="idexped" id="idexped" value="<?=$_GET["idexped"] ;?>"/>
	  	<input type="hidden" name="origen" id="origen" value="<?=$_GET["origen"] ;?>"/>
	</form>	 
</body>
</html>
