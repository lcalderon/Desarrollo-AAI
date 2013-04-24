<?php

	include_once('../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();
	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	$con->select_db($con->catalogo);
	
	session_start(); 
	
?>
<html>
<head>
<title>American Assist</title>
	<script type="text/javascript" src="/estilos/functionjs/permisos.js"></script>
	<script type="text/javascript" src="/estilos/functionjs/validator.js"></script>
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/prototype.js"></script>	
	<link href="/estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	
	<style type="text/css">
	<!--
 	body {
		margin-left: 0px;
		margin-top: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
 
	}	
	-->
	</style>		
			
			<script language="JavaScript"> 

			function validarcampo(){
			
				document.myForm.txtpassword1.value=document.myForm.txtpassword1.value.replace(/^\s*|\s*$/g,"");					
				document.myForm.txtpassword2.value=document.myForm.txtpassword2.value.replace(/^\s*|\s*$/g,"");					
				
				var numeros="0123456789";
				var texto=document.myForm.txtpassword1.value.toUpperCase();
				var cantNumero=0;
				var cantLetra=0;

				var mayusculas="ABCDEFGHYJKLMNÑOPQRSTUVWXYZ";
		 
				for(i=0; i<texto.length; i++){
				  if(numeros.indexOf(texto.charAt(i),0)!=-1){
						cantNumero=cantNumero+1;
				  }					  
				}
			
				for(i=0; i<texto.length; i++){
				  if (mayusculas.indexOf(texto.charAt(i),0)!=-1){
					 cantLetra=cantLetra+1;
				  }				  
				}
				   
				if(document.myForm.txtpassword1.value =='' ){
					alert('<?=_("INGRESE LA CONTRASEÑA.") ;?>');
					document.myForm.txtpassword1.focus();
					
				} else if(document.myForm.txtpassword1.value.length < 8){
					alert('<?=_("LONGITUD MINIMA PARA LA CONTRASEÑA ES 8 DIGITOS.") ;?>');
					document.myForm.txtpassword1.focus();
					
				} else if(cantNumero ==0){
					alert('<?=_("LA CONTRASEÑA DEBE CONTENER AL MENOS UN NUMERO") ;?>');
					document.myForm.txtpassword1.focus();
					 
				} else if(cantLetra ==0){
					alert('<?=_("LA CONTRASEÑA DEBE CONTENER AL MENOS UN LETRA") ;?>'); 
					document.myForm.txtpassword1.focus();
					 
				} else if(document.myForm.txtpassword2.value ==''){
					alert('<?=_("REPITA LA CONTRASEÑA.") ;?>');
					document.myForm.txtpassword2.focus();
					
				} else if(document.myForm.txtpassword2.value.length < 8){
					alert('<?=_("LONGITUD MINIMA PARA LA CONTRASEÑA ES 8 DIGITOS.") ;?>');
					document.myForm.txtpassword2.focus();									 
									 
				} else if(document.myForm.txtpassword1.value != document.myForm.txtpassword2.value){
					alert('<?=_("CONTRASEÑAS NO COINCIDEN.") ;?>');
					document.myForm.txtpassword2.focus();
					 
				} else{

					new Ajax.Request('/app/vista/login/gpassword.php',{
						method : 'post',
						parameters: { idclave: $F('txtpassword1')},
						onSuccess: function(resp){
							alert(resp.responseText+'!!!');
						},
						onFailure: function() { alert('ERROR, NO SE HA REALIZADO LA OPERACION.'); }						
					});
					 
				}		
				
				return (false);				
			} 
		</script>
</head>
<body onload="document.myForm.txtpassword1.focus()">
	<form id="myForm" name="myForm" method="post" onSubmit = "return validarCampo(this)" >
		<table width="100%" border="0" cellpadding="4" cellspacing="1" style="border:1px solid #333366">
			<tr>
				<td colspan="2" style="color:#FFFFFF" bgcolor="#336699"><?=_("CAMBIAR CONTRASEÑA") ;?></td>
			</tr> 
			<tr>
				<td colspan="2" bgcolor="#dfdfdf"><strong><?=_("Ingrese la nueva contraseña.") ;?></strong></td>
			</tr>
			<tr>
				<td bgcolor="#F9F9F9"><?=_("NUEVA CONTRASEÑA") ;?></td>
				<td bgcolor="#F9F9F9"><input name="txtpassword1" type="password" id="txtpassword1" size="20" maxlength="20" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" /></td>
			</tr>
			<tr>
				<td bgcolor="#F9F9F9"><?=_("REPETIR CONTRASEÑA") ;?></td>
				<td bgcolor="#F9F9F9"><input name="txtpassword2" type="password" id="txtpassword2" size="20" maxlength="20" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" /></td>
			</tr> 
			<tr bgcolor="#F9F9F9">
				<td colspan="2"><input type="button" name="btnaceptar" id="btnaceptar" style="font-size:10px" value="<?=_("ACEPTAR") ;?>"  onclick="validarcampo();" /><div id="resultado" style="color:#FF0000; font-size:10px; width:160px; top:10px"></div> </td>
			</tr>
	  </table>
	  <input type="hidden" name="ususesion" id="ususesion" value="<?=$_SESSION['user'] ;?>" />
</form>
</body>
</html>