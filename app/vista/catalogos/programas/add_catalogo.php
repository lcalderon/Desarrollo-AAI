<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	
	$con = new DB_mysqli();
	
	$con->select_db($con->catalogo);	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
 	session_start(); 
 	Auth::required($_SERVER['REQUEST_URI']); 
 
	if($_GET["codigo"])
	 {
		$result=$con->query("select catalogo_programa.PILOTO,catalogo_programa.ACTIVO, catalogo_cuenta.NOMBRE, catalogo_programa.IDCUENTA,catalogo_programa.IDPROGRAMA,catalogo_programa.NOMBRE,catalogo_programa.FECHAINIVIGENCIA,catalogo_programa.FECHAFINVIGENCIA from catalogo_programa inner join catalogo_cuenta on catalogo_cuenta.IDCUENTA=catalogo_programa.IDCUENTA where catalogo_programa.IDPROGRAMA='".$_GET["codigo"]."'");
		$row = $result->fetch_object();
				
		$rscuentas=$con->query("select IDCUENTA,NOMBRE from catalogo_cuenta  ");
		$sololectura=" readonly";		
	 }

?>
<html>
	<head>
		<title>American Assist</title> 

		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/ajax_catalogo.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="../../../../estilos/functionjs/func_global.js"></script>			
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>	
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
		<style type="text/css">@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");</style>

		<script language="JavaScript">
		
			function validarCampo(variable){
			
				document.frmaddregistro.nombre.value=document.frmaddregistro.nombre.value.replace(/^\s*|\s*$/g,"");			
				document.frmaddregistro.txtcodigo.value=document.frmaddregistro.txtcodigo.value.replace(/^\s*|\s*$/g,"");			
				
				if(document.frmaddregistro.txtcodigo.value =='' ){
					alert('<?=_("INGRESE EL CODIGO DEL PROGRAMA.") ;?>');
					document.frmaddregistro.txtcodigo.focus();
					return (false);
				}					
				else if(document.frmaddregistro.nombre.value =='' ){
					alert('<?=_("INGRESE LA DESCRIPCION DEL PROGRAMA.") ;?>');
					document.frmaddregistro.nombre.focus();
					return (false);
				}
				else if(document.frmaddregistro.cmbcuenta.value =='' ){
					alert('<?=_("SELECCIONE LA CUENTA.") ;?>');
					document.frmaddregistro.cmbcuenta.focus();
					return (false);
				}				
				// else if(document.frmaddregistro.piloto.value =='' ){
					// alert('<?=_("INGRESE EL PILOTO.") ;?>');
					// document.frmaddregistro.piloto.focus();
					// return (false);
				// }			
						
				return (true);
			}
			
		</script>
		
		<script language="JavaScript">
		
			function validarCampoAgrega(variable){
			
				if(document.frmagrega.cmbservicio.value =='' ){
					alert('<?=_("SELECCIONE EL SERVICIO.") ;?>');
					document.frmagrega.cmbservicio.focus();
					return (false);					
				}
				else if(document.frmagrega.txtmonto.value =='' ){
					alert('<?=_("INGRESE EL MONTO.") ;?>');
					document.frmagrega.txtmonto.focus();
					return (false);
				}	
				else if(document.frmagrega.txtevento.value =='' ){
					alert('<?=_("INGRESE EL NUMERO DE EVENTO.") ;?>');
					document.frmagrega.txtevento.focus();
					return (false);
				}				
				else if(document.frmagrega.cbmtfrecuencia.value =='' ){
					alert('<?=_("SELECCIONE EL TIPO DE FRECUENCIA.") ;?>');
					document.frmagrega.cbmtfrecuencia.focus();
					return (false);
				}				
								
				return (true);
			}
		</script>
		
		<script language="JavaScript">
		
			function formatotxt(){
				 
				if(document.getElementById('cbmtcobertura').value =='SL' || document.getElementById('cbmtcobertura').value =='CX' ){
				
					document.getElementById('txtmonto').value=0;
					document.getElementById('cmbmoneda').selectedIndex=0;
					document.getElementById('cmbmoneda').disabled=true;
					document.getElementById("txtmonto").readOnly = true;								
				}
				else
				{
					document.getElementById('cmbmoneda').selectedIndex=2;					
					document.getElementById("txtmonto").readOnly = false;
					document.getElementById('cmbmoneda').disabled=false;

				}	
			}	
			
 
			function verfica_validacion(){

				new Ajax.Request('verficar_ckeckcuenta.php', {
					parameters: { idcuenta: $F('cmbcuenta') },
					method: 'post',
					onSuccess: function(resp) {
					
						if(resp.responseText==1){
							$('ckbvalidacion').checked=true;
							//$('ckbvalidacion').disabled=true;
						}
						else{
							$('ckbvalidacion').checked=false;
							//$('ckbvalidacion').disabled=true;
						}
					}

				});				
			}
		</script>		
	</head>
	<body onLoad="document.frmaddregistro.txtcodigo.focus();">
	<form name="frmaddregistro" action="grabar_programa.php" enctype="multipart/form-data" method="POST" onSubmit = "return validarCampo(this)" >
			<input name="opc" type="hidden" value="<?=$_GET["opc"]; ?>">			
			<input type="hidden" name="MAX_FILE_SIZE" value="<?PHP echo(1024*1024*100); ?>" />
			<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
		<table border="0" cellpadding="1" cellspacing="1" width="70%" class="catalogos">
			<tr bgcolor="#333333">
				<th style="text-align:left"><?=_("AGREGAR PLAN") ;?></th>
			</tr>
			<tr class='modo1'>
				<td><?=_("CODIGO") ;?></td>			
				<td><input name="txtcodigo" type="text" id="txtcodigo" size="14" maxlength="10" onFocus="coloronFocus(this);clear_all(this.id)" onBlur="colorOffFocus(this);clear_all(this.id)" class="classtexto" style="text-transform:uppercase;" ></td> 				
			</tr>
			<tr class='modo1'>
				<td><?=_("DESCRIPCION") ;?></td>
				<td><input name="nombre" id="nombre" type="text" value="<?=$row->NOMBRE; ?>" size="40" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;"></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("CUENTA") ;?></td>						 
				<td><?				
						$con->cmbselectdata("select IDCUENTA,NOMBRE from catalogo_cuenta order by NOMBRE","cmbcuenta",$_GET["codigo"],"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto'",($_GET["codigo"])?"1":"",_("SELECCIONE")); 							
						?>
				</td>
			</tr>
			<tr class='modo1'>
				<td><?=_("PILOTO") ;?></td>
				<td><input name="piloto" id="piloto" type="text" size="20" maxlength="20" onFocus="coloronFocus(this);" onKeyPress="return validarnum(event)" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;"></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("PLAN VIP") ;?></td>
				<td><input type="checkbox" name="chkvip" id="chkvip" value="1" ></td>
			</tr>
				<tr class='modo1'>
				  <td><?=_("VALIDACION EXTERNA") ;?></td>
				  <td><div id="div-validacionext"><input type="checkbox" name="ckbvalidacion" id="ckbvalidacion" value="1" ></div></td>
		  </tr>					
			<tr class='modo1'>
				<td><?=_("FECHA INI. VIGENCIA") ;?></td>
				<td><input type="text" readonly class="classtexto"  id="fechainiserv" name="fechainiserv" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" />
				  <button   id="cal-button-1">...</button>
			    <script type="text/javascript">
					Calendar.setup({
					  inputField    : "fechainiserv",
					  button        : "cal-button-1",
					  align         : "Tr"
					});
				  </script>&nbsp;<img src="/imagenes/iconos/limpiar.jpg" title="LIMPIAR FECHA" onClick="document.frmaddregistro.fechainiserv.value='0000-00-00' " style="cursor:pointer" /></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("FECHA FIN VIGENCIA") ;?></td>
				<td><input type="text" readonly id="fechafinserv" name="fechafinserv" class="classtexto" maxlength="10" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" />
				  <button   id="cal-button-2">...</button>
	             <script type="text/javascript">
						Calendar.setup({
						  inputField    : "fechafinserv",
						  button        : "cal-button-2",
						  align         : "Tr"
						});
					 </script>&nbsp;<img src="/imagenes/iconos/limpiar.jpg" title="LIMPIAR FECHA" onClick="document.frmaddregistro.fechafinserv.value='0000-00-00' " style="cursor:pointer" /></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("ACTIVADO") ;?></td>
				<td><input type="checkbox" name="chkactivo" id="checkbox"  value="1"   /></td>	
			</tr>
			<tr class='modo1'>
				<td><?=_("CONTRATO") ;?></td>
				<td><input name="userfile"  type="file" size="30" maxlength="" /></td>
			</tr>
				
			<tr class='modo1'>
				<?php
						if($_GET["opc"])
						 {
					?>
				<td align="right"><input  type="button" class="botonstandar" value="<?=_('CERRAR') ;?>" onclick="parent.MochaUI.closeWindow(parent.$('youtube'));" title="<?=_('CERRAR') ;?>"></td>
				<?php
						 }
						else
						 {
					?>
				<td align="right"><input  type="button" class="botonstandar" value="<?=_('REGRESAR') ;?>" onClick="reDirigir('general.php')" title="<?=_('REGRESAR') ;?>"></td>
				<?php
						 }
					?>
				<td align="left"><input name="Submit"  class="botonstandar" type="submit" value="<?=_('GRABAR') ;?>" title="<?=_('GRABAR PRODUCTO') ;?>" >&nbsp;</td>
			</tr>
	    </table>            
			<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">						
	</form>
		
	</body>
</html>
