<?php
	
	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");	
	
	$con = new DB_mysqli();
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);
	 
	session_start();
 	Auth::required($_SERVER['REQUEST_URI']);
  	 
 	$sql="SELECT IDGRUPO FROM $con->temporal.grupo_usuario where IDUSUARIO='".$_GET["codigo"]."' order by IDGRUPO";
	$rsgrupo=$con->query($sql);
	while($rowgrupo = $rsgrupo->fetch_object())
	 {
		$accesogrupo[]=$rowgrupo->IDGRUPO; 
	 }
	 
	$result=$con->query("SELECT IDUSUARIO,NOMBRES,APELLIDOS,CONTRASENIA,ACTIVO,REINICIACONTRASENIA,EMAIL,BLOQUEADO,IDCARGO,IDUSUARIOMOD,FECHAMOD FROM $con->catalogo.catalogo_usuario where IDUSUARIO='".$_GET["codigo"]."'");
	$row = $result->fetch_object();
	
	$rsmoneda=$con->query("select IDMONEDA,DESCRIPCION from $con->catalogo.catalogo_moneda where DESCRIPCION!='' order by DESCRIPCION ");
	
	$IS_ADM=$con->consultation("SELECT COUNT(*) FROM $con->temporal.grupo_usuario where IDUSUARIO='".$_GET["codigo"]."' AND IDGRUPO='ADMI'");
	if($IS_ADM[0][0] ==0 and $_SESSION["user"]!="ADMINISTRADOR")	$subquery="WHERE IDGRUPO !='ADMI'";
	
?>
<html>
<head>
<title>American Assist</title> 

		<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />		
		
		<link href="../../../../estilos/fronter_css/jquery.windows-engine.css"	rel="stylesheet" type="text/css" />
		<script src="../../../../estilos/fronter_js/jquery.js" type="text/javascript"></script>
		<script src="../../../../estilos/fronter_js/jquery.validate.js" type="text/javascript"></script>
		<script src="../../../../estilos/fronter_js/jquery.windows-engine.js" type="text/javascript"></script>
		<script src="../../../../estilos/fronter_js/index.js" type="text/javascript"></script>
			
		<script language="JavaScript">
		
			function validarCampo(variable){
			
				document.frmeditregistro.txtnombre.value=document.frmeditregistro.txtnombre.value.replace(/^\s*|\s*$/g,"");			
				document.frmeditregistro.txtapellido.value=document.frmeditregistro.txtapellido.value.replace(/^\s*|\s*$/g,"");			
				document.frmeditregistro.txtcontrasena.value=document.frmeditregistro.txtcontrasena.value.replace(/^\s*|\s*$/g,"");		

				if(document.frmeditregistro.txtnombre.value =='' ){
					alert('<?=_("INGRESE EL NOMBRE") ;?>');
					document.frmeditregistro.txtnombre.focus();
					return (false);
				}
				else if(document.frmeditregistro.txtapellido.value =='' ){
					alert('<?=_("INGRESE EL APELLIDO") ;?>');
					document.frmeditregistro.txtapellido.focus();
					
					return (false);
				}		
					
				var nom=document.frmeditregistro.txtnombre.value.substring(0,1);
				var ape=document.frmeditregistro.txtapellido.value.substring(0,1);
				
				if((nom.length+ape.length+document.frmeditregistro.txtusuario.value.length) < 8)	{	alert('<?=_("LONGITUD MINIMA PARA EL USUARIO 8 DIGITOS") ;?>');		document.frmeditregistro.txtusuario.focus(); return (false); }
				if(document.frmeditregistro.txtcontrasena.value.length < 8)	{	alert('<?=_("LONGITUD MINIMA PARA LA CONTRASENA 8 DIGITOS") ;?>');		document.frmeditregistro.txtcontrasena.focus(); return (false); }
				
				if(document.frmeditregistro.txtrcontrasena.value != document.frmeditregistro.txtcontrasena.value){
					alert('<?=_("LA CONTRASENAS NO COINCIDEN") ;?>');
					document.frmeditregistro.txtrcontrasena.focus();
					return (false);
				}

				var nom=document.frmeditregistro.txtnombre.value.substring(0,1);
				var ape=document.frmeditregistro.txtapellido.value.substring(0,1);
				
				if((nom.length+ape.length+document.frmeditregistro.txtusuario.value.length) < 8)	{	alert('<?=_("LONGITUD MINIMA PARA EL USUARIO 8 DIGITOS.") ;?>');		document.frmeditregistro.txtusuario.focus(); return (false); }
				if(document.frmeditregistro.txtcontrasena.value.length < 8)	{	alert('<?=_("LONGITUD MINIMA PARA LA CONTRASENA 8 DIGITOS.") ;?>');		document.frmeditregistro.txtcontrasena.focus(); return (false); }				

				if(document.frmeditregistro.txtrcontrasena.value != document.frmeditregistro.txtcontrasena.value){
					alert('<?=_("LA CONTRASENAS NO COINCIDEN.") ;?>');
					document.frmeditregistro.txtrcontrasena.focus();
					return (false);
				}	
				
				var numeros="0123456789";
				var usuario=document.frmeditregistro.txtusuario.value.toUpperCase();
				var nombre=document.frmeditregistro.txtnombre.value.toUpperCase();
				var apellido=document.frmeditregistro.txtapellido.value.toUpperCase();
				var texto=document.frmeditregistro.txtcontrasena.value.toUpperCase();
				var cant=0;
				var cant2=0;

				var mayusculas="ABCDEFGHYJKLMNÑOPQRSTUVWXYZ";
		 
				   for(i=0; i<texto.length; i++){
					  if(numeros.indexOf(texto.charAt(i),0)!=-1){
							cant=cant+1;
					  }					  
				   }
				
				   for(i=0; i<texto.length; i++){
					  if (mayusculas.indexOf(texto.charAt(i),0)!=-1){
						 cant2=cant2+1;
					  }
					  
				   }	 		   

				if(cant == 0) { alert('<?=_("LA CONTRESENA DEBE CONTENER AL MENOS UN NUMERO") ;?>');	document.frmeditregistro.txtcontrasena.focus(); return (false); }
				if(cant2 == 0) { alert('<?=_("LA CONTRESENA DEBE CONTENER AL MENOS UN LETRA") ;?>'); 	document.frmeditregistro.txtcontrasena.focus();	return (false); }
 
   				// var pat = new RegExp(usuario);
				// var pat2 = new RegExp(nombre);
				// var pat3 = new RegExp(apellido);
				
				// if(pat.test(texto)) { alert('<?=_("LA CONTRASENA NO DEBE CONTENER EL NOMBRE,APELLIDO O NOMBRE DE USUARIO") ;?>');	document.frmeditregistro.txtcontrasena.focus();	return (false); }
				// if(pat2.test(texto)) { alert('<?=_("LA CONTRASENA NO DEBE CONTENER EL NONBRE,APELLIDO O NOMBRE DE USUARIO") ;?>');	document.frmeditregistro.txtcontrasena.focus();	return (false); }
				// if(pat3.test(texto)) { alert('<?=_("LA CONTRASENA NO DEBE CONTENER EL NONBRE,APELLIDO O NOMBRE DE USUARIO") ;?>');	document.frmeditregistro.txtcontrasena.focus();	return (false); }
			   
				return(isEmail(document.frmeditregistro.txtemail));
							
				return (true);
			}
		</script>
</head>
	<body onLoad="document.frmeditregistro.txtusuario.focus();">
	<form name="frmeditregistro" action="actualizar_usuario.php" method="POST" onSubmit = "return validarCampo(this)">
		<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
		<table border="0" cellpadding="1" cellspacing="1" width="80%" class="catalogos">
			<tr bgcolor="#333333">
				<th width="32%" style="text-align:left"><?=_("EDITAR USUARIOS") ;?></th>
			</tr>
			<tr class='modo1'>
				<td><?=_("USUARIO") ;?></td>
				<td width="68%"><input name="txtusuario" id="txtusuario" readonly type="text" size="20" maxlength="15" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" value="<?=$row->IDUSUARIO;?>"><div id="usu"></div></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("NOMBRES") ;?></td>
				<td><input name="txtnombre" id="txtnombre" type="text" size="30" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;"  value="<?=$row->NOMBRES;?>"></td>
			</tr>	
			<tr class='modo1'>
				<td><?=_("APELLIDOS") ;?></td>
				<td><input name="txtapellido" id="txtapellido" type="text" size="30" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;"  value="<?=$row->APELLIDOS;?>"></td>
			</tr>		
			<tr class='modo1'>
				<td><?=_("CONTRASE&Ntilde;A") ;?></td>
				<td><input name="txtcontrasena" id="txtcontrasena" type="password" size="20"  maxlength="15" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;" value="<?=$row->CONTRASENIA;?>"></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("RE-CONTRASE&Ntilde;A") ;?></td>
				<td><input name="txtrcontrasena" id="txtrcontrasena" type="password" size="20"  maxlength="15" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;" value="<?=$row->CONTRASENIA;?>"></td>
			</tr>			
			<tr class='modo1'>
				<td><?=_("EMAIL") ;?></td>
				<td><input name="txtemail" id="txtemail" type="text" size="40" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" value="<?=$row->EMAIL;?>"></td>
			</tr>			
			<tr class='modo1'>
				<td><?=_("PERMISO CAMBIAR CONTRASE&Ntilde;A") ;?></td>
				<td><input type="checkbox" name="chkcambia" id="checkbox" <? if($row->REINICIACONTRASENIA==1) echo "checked " ; ?>  value="1" /></td>	
			</tr>		
			<tr class='modo1'>
				<td><?=_("ACTIVADO") ;?></td>
				<td><input type="checkbox" name="chkactivo" id="checkbox" <? if($row->ACTIVO==1) echo "checked " ; ?>  value="1" /></td>	
			</tr>
			<tr class='modo1'>
			  <td><?=_("GRUPOS") ;?></td>			  
				  <td>
				  <select name="cmbgrupos[]" id="cmbgrupos[]" size='7'  multiple onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'>
					<?		
						$i=0;	
						$result=$con->query("SELECT IDGRUPO,NOMBRE FROM $con->catalogo.catalogo_grupo $subquery order by NOMBRE  ");
						while($reg = $result->fetch_object())
						 {
							if(in_array($reg->IDGRUPO,$accesogrupo))	$selecion="selected";	else $selecion="";							
					?>
						<option value="<?=$reg->IDGRUPO;?>" <?=$selecion;?> ><?=$reg->NOMBRE;?></option>
					<? 
						 } 
					?>
					</select>
				</td>				
			</tr>
			<tr class='modo1'>
				<td><?=_("ULTIMO CAMBIO");?></td>
				<td><?=_("USUARIO");?>:&nbsp;<b><?=$row->IDUSUARIOMOD; ?></b>&nbsp;<?=_("FECHA");?>:&nbsp; <b><?=$row->FECHAMOD; ?></b>&nbsp;<img style="color:#CC0000; cursor:pointer" width="67" height="26" src="../../../../imagenes/iconos/historial.gif" title="<?=_('HISTORIAL DE CAMBIOS');?>" onClick="reDirigir_ventana('../../../../app/vista/catalogos/usuarios/historial.php?idusuario=<?=$row->IDUSUARIO; ?>','window','700','350','VENTANAUSUARIOS')"></img></td>
			</tr>
			<tr class='modo1'>
				<td colspan="2">
					<div align="center"><strong><a href="#" onClick="reDirigir_ventana('../../../../app/vista/catalogos/usuarios/copiarperfil.php?idusuario=<?=$row->IDUSUARIO; ?>','window', '450','200','VENTAUSUARIO')"><?=_('COPIAR PERFIL') ;?></a></strong>&nbsp;&nbsp;&nbsp;<strong><a href="#" onClick="reDirigir_ventana('../../../../app/vista/catalogos/usuarios/frmaccesos.php?idusuario=<?=$row->IDUSUARIO; ?>','window','860','480','VENTANAACCES')">
					<?=_('ANIADIR ACCESOS') ;?>
		      </a></strong></div></td>
			</tr>			
			<tr class='modo1'>				
				<td align="right"><input name="Submit"  class="botonstandar" type="submit" value="<?=_('GRABAR') ;?>" title="<?=_('GRABAR USUARIO') ;?>" >&nbsp;</td>
				<td><input  type="button" class="botonstandar" value="<?=_('REGRESAR') ;?>" onClick="reDirigir('general.php')" title="<?=_('REGRESAR') ;?>"></td>				
			</tr>
	    </table>            
			<input name="bloqueo" id="bloqueo" type="hidden" value="<?=$row->BLOQUEADO;?>" />					
			<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">					
			<input name="idusuario" type="hidden" value="<?=$_GET['codigo'];?>">
	</form>
		
	</body>
</html>