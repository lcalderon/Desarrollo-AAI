<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/functions.php');
	include_once('../../../modelo/validar_permisos.php');		
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	
	$con= new DB_mysqli();
	 
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	 $con->select_db($con->catalogo);	
		
 	session_start(); 
 	Auth::required();
	
	validar_permisos("MENU_SAC",1);
	
	$anio_vehiculo=mostrarAnio_Vehiculo();
	
	if($_GET["verinfo"])
	 {
		$_GET["idvehiculo"]=$_GET["verinfo"];	
		$readonly="readonly";
		$disabled="disabled";
	 }	

		$Sql_vehiculo="SELECT
					  catalogo_afiliado_persona_vehiculo.ID,
					  catalogo_afiliado_persona_vehiculo.IDAFILIADO,
					  catalogo_afiliado_persona_vehiculo.ANIO,
					  catalogo_afiliado_persona_vehiculo.ARRCOMBUSTIBLE,
					  catalogo_afiliado_persona_vehiculo.ARRTRANSMISION,
					  catalogo_afiliado_persona_vehiculo.COLOR,
					  catalogo_afiliado_persona_vehiculo.FECHAMOD,
					  catalogo_afiliado_persona_vehiculo.MARCA,
					  catalogo_afiliado_persona_vehiculo.NUMSERIECHASIS,
					  catalogo_afiliado_persona_vehiculo.NUMSERIEMOTOR,
					  catalogo_afiliado_persona_vehiculo.NUMVIN,
					  catalogo_afiliado_persona_vehiculo.PLACA,
					  catalogo_afiliado_persona_vehiculo.ACTIVO,
					  catalogo_afiliado_persona_vehiculo.SUBMARCA,
					  catalogo_afiliado_persona_vehiculo.USO
					FROM $con->catalogo.catalogo_afiliado_persona_vehiculo
					  INNER JOIN $con->catalogo.catalogo_afiliado
						ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_vehiculo.IDAFILIADO
					WHERE catalogo_afiliado_persona_vehiculo.IDAFILIADO= ".$_GET["idafiliado"]."
					ORDER BY catalogo_afiliado_persona_vehiculo.ID DESC";

		$rs_vehiculo=$con->query($Sql_vehiculo);

		if($_GET["idvehiculo"])
		 {
			$Sql_veh="SELECT
					  catalogo_afiliado_persona_vehiculo.ID,
					  catalogo_afiliado_persona_vehiculo.IDAFILIADO,
					  catalogo_afiliado_persona_vehiculo.ARRPESO,
					  catalogo_afiliado_persona_vehiculo.IDFAMILIAVEH,
					  catalogo_afiliado_persona_vehiculo.ANIO,
					  catalogo_afiliado_persona_vehiculo.ARRCOMBUSTIBLE,
					  catalogo_afiliado_persona_vehiculo.ARRTRANSMISION,
					  catalogo_afiliado_persona_vehiculo.COLOR,
					  catalogo_afiliado_persona_vehiculo.FECHAMOD,
					  catalogo_afiliado_persona_vehiculo.MARCA,
					  catalogo_afiliado_persona_vehiculo.NUMSERIECHASIS,
					  catalogo_afiliado_persona_vehiculo.NUMSERIEMOTOR,
					  catalogo_afiliado_persona_vehiculo.NUMVIN,
					  catalogo_afiliado_persona_vehiculo.PLACA,
					  catalogo_afiliado_persona_vehiculo.SUBMARCA,
					  catalogo_afiliado_persona_vehiculo.USO
					FROM $con->catalogo.catalogo_afiliado_persona_vehiculo
					  INNER JOIN $con->catalogo.catalogo_afiliado
						ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_vehiculo.IDAFILIADO
					WHERE catalogo_afiliado_persona_vehiculo.ID=".$_GET["idvehiculo"];

			$rsvehiculos=$con->query($Sql_veh);
			$rowveh = $rsvehiculos->fetch_object();	
		 }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>American Assist</title>

	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	

	<!-- se usa para el autocompletar -->
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../../estilos/suggest/ubigeo.css" rel="stylesheet" type="text/css" />	
	<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" >	 </link> 
	<link href="../../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" >	 </link>

	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_effects.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"> </script>

	<style type="text/css">
	<!--
	.style7 {color: #FFFFFF}
	body {
		margin: 1px; 
		padding: 0px;
	}
	-->
	</style>
 
		
	<script type="text/javascript">
			
			function verificardiv(nombrediv,valors,nombre){
				if(valors=='V'){
					comportamientoDiv('+',nombrediv);
					document.getElementById(nombre).value='O';
				}else{
					comportamientoDiv('-',nombrediv);
					document.getElementById(nombre).value='V';	
				} 
			 } 
			
	</script>
	<script type="text/javascript">

			function validarIngreso(valors) {
				
			if(document.form1.txtmarca.value==""){
					  alert('<?=_("INGRESE LA MARCA.") ;?>');
					  document.form1.txtmarca.focus();
					  return (false);
			   } 	

			 else if(document.form1.txtsubmarca.value==""){
					  alert('<?=_("INGRESE EL MODELO.") ;?>');
					  document.form1.txtsubmarca.focus();
					  return (false);
			   }  
			   else if(document.form1.txtplaca.value==""){
					  alert('<?=_("INGRESE LA PLACA.") ;?>');
					  document.form1.txtplaca.focus();
					  return (false);
			   }  		 
		
			if(confirm('<?=(!$_GET["idvehiculo"])?_("DESEA AGREGAR UN NUEVO REGISTRO?."):_("ESTA SEGURO QUE DESEA ACTUALIZAR LOS CAMBIOS?.") ;?>'))		  
			 {
					document.form1.action="gvehiculo.php" ;
					document.form1.submit();
			 }
			
				return (false);	 

			}
	</script> 
</head>
<body>
<form id="form1" name="form1" method="post" action="" onSubmit="return validarIngreso(this)">
<input type="hidden" name="idafiliado" value="<?=$_GET["idafiliado"];?>" />
<input type="hidden" name="idvehiculo" value="<?=$_GET["idvehiculo"];?>" />
 
<table width="250" border="0" cellpadding="1" cellspacing="1" bgcolor="#F3F2EB" style="border:1px solid #E3E1CD">
  <tr bgcolor="#72A9D3">
    <td colspan="10"><strong><?=_("REGISTRO VEHICULAR") ;?></strong></td>
    </tr>
  <tr>	
    <td><?=_("MARCA") ;?></td>
    <td><input type="text" name="txtmarca" id="txtmarca"  <?=($_GET["verinfo"])?$readonly:"" ;?> autocomplete="off"  onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' value="<?=$rowveh->MARCA;?>" style="text-transform:uppercase;"></td>
	<div id="mostrarmarca" class="autocomplete" style="display:none"></div>
    <td><?=_("MODELO") ;?></td>
    <td><input type="text" name="txtsubmarca" id="txtsubmarca" <?=($_GET["verinfo"])?$readonly:"" ;?> autocomplete="off" onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' value="<?=$rowveh->SUBMARCA;?>" style="text-transform:uppercase;"></td>
	<div id="mostrarsmarca" class="autocomplete" style="display:none"></div>
    <td><?=_("PLACA") ;?></td>
    <td><label>
      <input type="text" name="txtplaca" id="txtplaca" <?=($_GET["verinfo"])?$readonly:"" ;?> onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' value="<?=$rowveh->PLACA;?>" style="text-transform:uppercase;">
    </label></td>
    <td><?=_("VIN") ;?></td>
    <td><input type="text" name="txtvin" id="txtvin" <?=($_GET["verinfo"])?$readonly:"" ;?> onfocus='coloronFocus(this);' onblur='colorOffFocus(this);' class='classtexto' value="<?=$rowveh->NUMVIN;?>" style="text-transform:uppercase;" /></td>
    </tr>
  <tr>
    <td><?=_("TRANSMISION") ;?></td>
    <td><?
			$con->cmb_array("cmbtrasmision",$desc_transmision,$rowveh->ARRTRANSMISION," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'");
		?></td>
    <td><?=_("COMBUSTIBLE") ;?></td>
    <td><?
			$con->cmb_array("cmbcombustible",$desc_combustible,$rowveh->ARRCOMBUSTIBLE," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'");
		?></td>
    <td><?=_("USO") ;?></td>
    <td><?
			$con->cmb_array("cmbuso",$desc_uso,$rowveh->USO," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'")
		?></td>
    <td><?=_("FAMILIA") ;?></td>
    <td><?
			$con->cmbselectdata("select IDFAMILIAVEH,DESCRIPCION from catalogo_familiavehiculo order by DESCRIPCION","cmbfamilia",($rowveh->IDFAMILIAVEH)?$rowveh->IDFAMILIAVEH:"1"," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","2");
		?></td>
    </tr>
  <tr>
    <td><?=_("#MOTOR") ;?></td>
    <td><input type="text" name="txtmotor" id="txtmotor" <?=($_GET["verinfo"])?$readonly:"" ;?> onfocus='coloronFocus(this);' onblur='colorOffFocus(this);' class='classtexto' value="<?=$rowveh->NUMSERIEMOTOR;?>" style="text-transform:uppercase;" /></td>
    <td><?=_("#SERIE") ;?></td>
    <td><input type="text" name="txtserie" id="txtserie"  <?=($_GET["verinfo"])?$readonly:"" ;?> onfocus='coloronFocus(this);' onblur='colorOffFocus(this);' class='classtexto' value="<?=$rowveh->NUMSERIECHASIS;?>" style="text-transform:uppercase;" /></td>
    <td><?=_("PESO") ;?></td>
    <td><?
			$con->cmb_array("cmbpeso",$desc_peso,$rowveh->ARRPESO," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'")
		?></td>
    <td><?=_("ANIO") ;?></td>
    <td><?
				$con->cmb_array("cmbanio",$anio_vehiculo,$rowveh->ANIO," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'")
		?></td>
    </tr>
	<tr>
		<td><?=_("COLOR") ;?></td>
		<td colspan="7"><input name="txtcolor" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?> class='classtexto' id="txtcolor"  onfocus='coloronFocus(this);' onblur='colorOffFocus(this);' size="18" maxlength="15" value="<?=$rowveh->COLOR;?>" style="text-transform:uppercase;" /></td>
  </tr>
  <tr>
		<td colspan="8"></td>
  </tr>
 
</table>
<p>
	<input type="button" name="btnclose" id="btnclose" value="<?=_("CERRAR") ;?>" style="font-size:10px;" onclick="actualizaPadre()"/>
    <input type="submit" name="btngrabar" id="btngrabar" value="<?=_("REGISTRAR >>") ;?>" style="font-weight:bold;width:130px;font-size:10px;"  <?=($_GET["verinfo"])?"disabled":"";?> />
</p>
<table width="95%" border="0" cellpadding="1" cellspacing="1" bgcolor="#E0EFFC" style="border:1px solid #A6C9E2">
		<tr>
		  <td bgcolor="#E1EFFB"><div align="center"> <span class="style3">
	      <?=_("ID") ;?>
		  </span> </div></td>
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	     <strong> <?=_("FECHAREGISTRO") ;?></strong>
		  </span></div></td>	
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("MARCA") ;?>
		  </span></div></td>
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("SUBMARCA") ;?>
		  </span></div></td>
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("PLACA") ;?>
		  </span></div></td>
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("VIN") ;?>
		  </span></div></td>
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("TRANSMISION") ;?>
		  </span></div></td>
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("COMBUSTIBLE") ;?>
		  </span></div></td>		
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("STATUS") ;?>
		  </span></div></td> 
			<td   colspan="2"><div align="center"></div></td>
		</tr>	
		 <?
			while($reg = $rs_vehiculo->fetch_object())
			 {				
		?>		
		 <tr bgcolor="<?=($_GET["idvehiculo"]==$reg->ID and !$_GET["verinfo"])?"#FFBBBB":"#FFFFFF" ?>" >
			<td bgcolor="#A6C9E2"><div align="center"><strong><?=$reg->ID;?></strong></div></td>
			<td align="center"><?=$reg->FECHAMOD;?></td>									
			<td><?=$reg->MARCA;?></td>			
			<td><?=$reg->SUBMARCA;?></td>			
			<td><?=$reg->PLACA;?></td>
			<td><?=$reg->NUMVIN;?></td>
			<td><?=$desc_transmision[$reg->ARRTRANSMISION];?></td>
			<td><?=$desc_combustible[$reg->ARRCOMBUSTIBLE];?></td>
			<td align="center"><div id="div-status<?=$c;?>" style="text-align:center;width:65px;height:15px;background-color:<?=($reg->ACTIVO==1)?"#CEFFCE":"#FF2F2F";?>"><?=($reg->ACTIVO==1)?_("ACTIVO"):_("INACTIVO");?></div></td>
			<td style="text-align:center"><? if($_GET["idvehiculo"]==$reg->ID and !$_GET["verinfo"]){ ?><a href="vehicular_dafiliado.php?idafiliado=<?=$_GET["idafiliado"];?>"><img src="../../../../imagenes/iconos/cancelar_dato.jpg" title="<?=_("CANCELAR") ;?>" width="46" height="15" border="0" onClick="reDirigir('vehicular_dafiliado.php?idvehiculo=<?=$_GET["idvehiculo"];?>')"></a><? }else{ ?><a href="vehicular_dafiliado.php?idvehiculo=<?=$reg->ID;?>&idafiliado=<?=$_GET["idafiliado"];?>"><img src="../../../../imagenes/iconos/editar_dato.jpg" title="<?=_("EDITAR REGISTRO") ;?>" width="35" height="14" border="0"></a><? } ?></td>
			<td style="text-align:center"><input type="checkbox" name="chbactivar<?=$c;?>" id="chbactivar<?=$c;?>" <?=($reg->ACTIVO==1)?"checked":"";?> onclick="actulizar_status('<?=$reg->ID;?>','<?=$reg->IDAFILIADO;?>',this.checked,this.name,'<?="div-status".$c;?>')" title="<?=_("ACTIVAR/DESACTIVAR") ;?>" <? if($_GET["idvehiculo"]==$reg->ID and !$_GET["verinfo"]){ ?> disabled <? } ?>/></td>
			<td style="text-align:center"><img src="../../../../imagenes/iconos/historia_s.gif" title="<?=_("HISTORIAL") ;?>"  style="cursor:pointer" onClick="ventana_historialvehiculo('<?=$reg->ID;?>')" ></td>
 			
		 </tr>	
		<?
				$c=$c+1;
				$stylo="";
			}
		?>
  </table>

</form>
</body>
</html>
<script type="text/javascript">

	new Ajax.Autocompleter('txtmarca',	'mostrarmarca',"consulta_marca.php",
	{
		method: "post",
		paramName: "marca",

		minChars: 2,
		selectFirst: true
	}
	);
	
	
	new Ajax.Autocompleter('txtsubmarca','mostrarsmarca',"consulta_marca.php",
	{
		method: "post",
		paramName: "modelo",
		callback: function (element, entry){
			parametros = "&marca2="+ $F('txtmarca')
			return entry +parametros;
		 },

		minChars: 2,
		selectFirst: true
	}
	);
 
</script>

<script type="text/javascript">	
		
		var validar_func = '';
		var win = null;
		
		function ventana_historialvehiculo(id){
		
			if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: '<?=_("HISTORIAL -> STATUS VEHICULOS")?>',
					width: 350,
					height: 170,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: true,
					minimizable: false,
					maximizable: false,
					resizable: true,
					opacity: 0.95,
					url: 'historial_vehiculos.php?id='+id
				});

				win.showCenter();
				myObserver = {onDestroy: function(eventName, win1)
				{
					if (win1 == win) {
						win = null;
						Windows.removeObserver(this);
					}
				}
				}
				Windows.addObserver(myObserver);
			}
			return;
		}
			
			
		function actulizar_status(id,idcodigo,valor,nombre,nombrediv){
 
			if(valor)	valor=1; else valor=0;			
			if(valor==1)	msg='<?=_("DESEA ACTIVAR EL REGISTRO DEL VEHICULO?")?>.'; else msg='<?=_("DESEA INACTIVAR EL REGISTRO DEL VEHICULO?")?>.';				
			
			if(confirm(msg))
			 {
					new Ajax.Updater(nombrediv, 'g_statusveh.php', {
					  parameters: 'id='+id+'&idcodigo='+idcodigo+'&valor='+valor, 
					  method: 'post',
					  onSuccess: function(t){
							if(t.responseText=='INACTIVO')	$(nombrediv).style.background="#FF2F2F"; else $(nombrediv).style.background="#CEFFCE";

						}					  
					});

			 }
			else
			 {
				if(valor==1)	valor=0; else valor=1;	
				$(nombre).checked=valor;	
			 }
				
		}	
					
	</script>