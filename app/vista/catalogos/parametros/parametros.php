<?php

include_once('../../../modelo/clase_mysqli.inc.php');
include_once("../../../vista/login/Auth.class.php");
include_once('../../../modelo/validar_permisos.php');

$con = new DB_mysqli();

$con->select_db($con->catalogo);

if ($con->Errno)
{
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
}

	session_start();
	Auth::required($_SERVER['REQUEST_URI']);

validar_permisos("MENU_CATPARAMETROS",1);

$rsgmt=$con->query("select IDGMT,NOMBRE,VALOR from catalogo_gmt order by NOMBRE ");
$rsmoneda=$con->query("select IDMONEDA,DESCRIPCION from catalogo_moneda where ACTIVO=1 order by IDMONEDA");
$rsmoneda2=$con->query("select IDMONEDA,DESCRIPCION from catalogo_moneda where ACTIVO=1 ");


$rsparam=$con->query("select IDPARAMETRO,DESCRIPCION,DATODEFAULT,DATO from catalogo_parametro order by IDPARAMETRO ");
while($regpar = $rsparam->fetch_object())
{
	$idpar=$regpar->IDPARAMETRO;
	$iddato=$regpar->IDPARAMETRO	;
	$titulo[$idpar]=$regpar->DESCRIPCION;
	$default[$iddato]=$regpar->DATODEFAULT;
	$dato[$iddato]=$regpar->DATO;
}

if($_REQUEST["cmbpais"]!="")	$valorp=$_REQUEST["cmbpais"]; else $valorp=$dato["IDPAIS"];

$rslocate=$con->query("select IDLOCALE,DESCRIPCION from catalogo_locale where ACTIVO=1 order by DESCRIPCION ");
$rsociedad=$con->query("select IDSOCIEDAD,NOMBRE from catalogo_sociedad where ACTIVO=1 order by NOMBRE ");

if($valorp!="" and $dato["IDPAIS"]==$valorp)
{
	$rsociedad2=$con->query("select IDSOCIEDAD from catalogo_parametro_sociedad order by PRIORIDAD ");
	while($regp = $rsociedad2->fetch_object())
	{
		$rowsoc[]=$regp->IDSOCIEDAD;
	}

	$rsmoned=$con->query("select IDMONEDA from catalogo_parametro_moneda order by PRIORIDAD  ");
	while($regm = $rsmoned->fetch_object())
	{
		$rowmon[]=$regm->IDMONEDA;
	}
}

if(count($rowmon)== 0)	$rowmon=$rsmoneda->num_rows;
?>
<html>
<head>
<title>American Assist</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	

		<link rel="stylesheet" href="../../../../librerias/tab-view/css/tab-view.css" type="text/css" media="screen">
		<script type="text/javascript" src="../../../../librerias/tab-view/js/ajax.js"></script>
		<script type="text/javascript" src="../../../../librerias/tab-view/js/tab-view.js"></script>
	
		<script type="text/javascript">
		function adicionarFila(){

			var tabla = document.getElementById("contenido").tBodies[0];
			var fila = document.createElement("TR");
			fila.setAttribute("align","center");

			var celda1 = document.createElement("TD");

			var celda2 = document.createElement("TD");
			var sel = document.createElement("SELECT");
			sel.setAttribute("size","1");
			sel.setAttribute("onFocus","coloronFocus(this)");
			sel.setAttribute("onBlur","colorOffFocus(this)");
			sel.setAttribute("class","classtexto");
			sel.setAttribute("name","cmbmoneda[]" );

			<?php

			$i=1;
			while($reg= $rsmoneda2->fetch_object())
			{
				echo "opcion$i = document.createElement('OPTION');\n";
				echo "opcion$i.innerHTML = '".$reg->DESCRIPCION."';\n";
				echo "opcion$i.value = '".$reg->IDMONEDA."';\n";
				if($reg->IDMONEDA == 1)	echo "opcion$i.selected = '".$reg->IDMONEDA."';\n";
				echo "sel.appendChild(opcion$i);\n";

				$i=$i+1;
			}
			?>

			celda2.appendChild(sel);

			//var celda3 = document.createElement('TD');
			var boton = document.createElement('IMG');

			boton.setAttribute('src','../../../../imagenes/iconos/deletep.gif');
			boton.setAttribute('title','ELIMINAR');
			boton.setAttribute('border','0');
			boton.setAttribute('height','14');
			boton.setAttribute('style','cursor:pointer');
			boton.onclick=function(){borrarFila(this);}
			celda2.appendChild(boton);

			fila.appendChild(celda1);
			fila.appendChild(celda2);
			//fila.appendChild(celda3);

			tabla.appendChild(fila);

		}

		function borrarFila(button){
			var fila = button.parentNode.parentNode;
			var tabla = document.getElementById('contenido').getElementsByTagName('tbody')[0];
			tabla.removeChild(fila);
		}

		function validarForm(){
			if(document.getElementById('cmbpais').value==''){
				alert('<?=_("SELECCIONE EL PAIS.") ;?>');
				document.getElementById('cmbpais').focus();

			}
			else if(document.getElementById('cmblocale').value==''){
				alert('<?=_("SELECCIONE ALGUN IDIOMA.") ;?>');
				document.getElementById('cmblocale').focus();
			}
			else{
				document.formp.action='gparametros.php';
				document.formp.submit();
			}
		}

		function validar(ip) {
			partes=ip.split('.');
			if (partes.length!=4) {
				alert('ip no valida');
				return;
			}
			for (i=0;i<4;i++) {
				num=partes[i];
				if (num>255 || num<0 || num.length==0 || isNaN(num)){
					alert('ip no valida');
					return;
				}
			}
			//alert('ip valida');
		}

		</script>		
</head>
<body onLoad="cargarmsg()">

<script type="text/javascript"  src="../../../../librerias/wz_tooltip/wz_tooltip.js"></script>
<script type="text/javascript" src="../../../../librerias/wz_tooltip/tip_balloon.js"></script>
<script type="text/javascript" src="../../../../librerias/wz_tooltip/tip_centerwindow.js"></script>

<form id="formp" name="formp" method="post" action="" onSubmit = "return validarForm(this)">
	<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
	<table width="604" border="0" cellpadding="2" cellspacing="1" class="parametros">		
		<tr>
			<td colspan="2"><?=_("PAIS") ;?>
	     <?	 
	     $sqlpais="select IDPAIS,NOMBRE from catalogo_pais order by NOMBRE ";
	     $con->cmbselectopc($sqlpais,"cmbpais",$valorp," onChange='formp.submit();' class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");
		?>&nbsp;<img src="../../../../imagenes/iconos/info.png" alt="<?=_("AYUDA") ;?>" onMouseOver="TagToTip('T2TExtensions', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions"><?=$titulo["CVEPAIS"] ;?></span></td>
		</tr>		
		<tr> 
		  <td width="97"><?=_("SOCIEDAD") ;?></td>
		  <td width="496" style="text-align:center"><img src="../../../../imagenes/iconos/info.png" alt="<?=_("AYUDA") ;?>" onMouseOver="TagToTip('T2TExtensions2', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()" ><span style="display: none;" id="T2TExtensions2"><?=$titulo["IDSOCIEDAD"] ;?></span></td>
		</tr>
		<tr>
			<td colspan="2">
				<div Style="overflow:auto;padding-top:1px; padding-Left:1px; padding-bottom:15px;height:130px; width:300px; border:1px solid #336699">
					<table width="100%" border="0" align="center" cellpadding="1" cellspacing="1"  >
						<?								
						while($reg = $rsociedad->fetch_object())
						{

							if ($i%2==0) $fondo='#FFFFFB'; else $fondo="#bbe0ff";
							if(in_array($reg->IDSOCIEDAD,$rowsoc))	$marcar="checked";
						?>							
							<tr bgcolor=<?=$fondo; ?> >
								<td><input type="checkbox" name="chkdesc[]" value="<?=$reg->IDSOCIEDAD; ?>" <?=$marcar; ?> ><font size="1px"><b>[<?=$reg->IDSOCIEDAD; ?>]</b></font>-<?=$reg->NOMBRE; ?></td>
							</tr>
						<?	 
						$i++;
						$marcar="";
						}
						?>
					</table>
				</div>
			</td>
		</tr>
		<tr> 
			<td><?=_("IDIOMA") ;?></td>
			<td><select name="cmblocale" id="cmblocale" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
              <option value="" selected>Seleccione</option>
              <?
              while($reg = $rslocate->fetch_object())
              {
              	if($reg->IDLOCALE == substr($dato["IDLOCALE"],0,5))
              	{
			?>
              <option value="<?=$reg->IDLOCALE; ?>" selected><?=utf8_encode($reg->DESCRIPCION); ?></option>
            <?
              	}
              	else
              	{
			?>
              <option value="<?=$reg->IDLOCALE; ?>"><?=utf8_encode($reg->DESCRIPCION); ?></option>
            <?	 
              	}
              }
			?>
            </select>&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions3', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions3"><?=$titulo["IDLOCALE"] ;?></span></td>
		</tr>
		<tr>
			<td><?=_("GMT") ;?></td>
			<td><select name="cmbgmt" id="cmbgmt" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
				<option value="" selected>Seleccione</option>
			<?
			while($regmt = $rsgmt->fetch_object())
			{
				if($regmt->IDGMT == $dato["GMT"])
				{
			?>
					<option value="<?=$regmt->IDGMT; ?>" selected><?=utf8_encode($regmt->NOMBRE); ?></option>
			<?
				}
				else
				{
			?>
					<option value="<?=$regmt->IDGMT; ?>"><?=utf8_encode($regmt->NOMBRE); ?></option>
			<?	 
				}
			}
			?>
			</select>&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions3x', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions3x"><?=$titulo["GMT"] ;?></span></td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="594"  id="contenido"  border="0" cellpadding="0" cellspacing="0" >	 
					<?							 
					for ($in = 1 ; $in <= count($rowmon) ; $in ++) {
					?>
		<tr>
			<td width="103"><? if($in==1) echo _("MONEDA") ;?></td>								
		    <td width="491">
					<select name="cmbmoneda[]" id="cmbmoneda" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">							
						<? 
						$rsmoneda=$con->query("select IDMONEDA,DESCRIPCION from catalogo_moneda where ACTIVO=1 order by IDMONEDA ");
						while($rowm = $rsmoneda->fetch_object())
						{
							if($rowm->IDMONEDA == $rowmon[$in-1])
							{
						?>	 
						
						<option value="<?=$rowm->IDMONEDA; ?>" selected><?=utf8_decode($rowm->DESCRIPCION); ?></option>							
						<?
							}
							else
							{
						?> 
						<option value="<?=$rowm->IDMONEDA; ?>" ><?=utf8_decode($rowm->DESCRIPCION); ?></option>							
						<?
							}
						}
						?>								
					  </select>
					  <? 

					  if($in == 1 ) {
					  	if($rsmoneda2->num_rows > 0 ) {

						  ?>
						  <input name="nueva_fila" type="button" id="nueva_fila" class="boton" value="NUEVO" onClick="adicionarFila()"> <? } ?>&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" alt="<?=_("AYUDA") ;?>" onMouseOver="TagToTip('T2TExtensions4', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions4">MONEDA A UTILIZAR</span>
						  <? } ?>
						  <? if($in > 1) { ?>
						 <img src="../../../../imagenes/iconos/deletep.gif" onClick="borrarFila(this)" border='0' width="16" height="14" title="<?=_("ELIMINAR") ;?>" >
						  <?  } ?>						</td>
				  </tr>
					<?						
					}
					?>
			 </table></td>
		</tr>
	</table>	
  <div id="dhtmlgoodies_tabView2">
	<div class="dhtmlgoodies_aTab">
		<table width="200" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFD" class="parametros">
			<tr>
				<td><?=_("INTERVALOS DE SEGUIMIENTO EXTERNO") ;?></td>
				<td><input name="txtintervaloext" type="text" id="txtintervaloext" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["INTERVALO_MONITOR_EX"]=="")?$default["INTERVALO_MONITOR_EX"]:$dato["INTERVALO_MONITOR_EX"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions5', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions5"><?=$titulo["INTERVALO_MONITOR_EX"];?></span></td>
			</tr>
			<tr>
				<td><?=_("INTERVALOS DE SEGUIMIENTO INTERNO") ;?></td>
				<td><input name="txtintervaloint" type="text" id="txtintervaloint" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["INTERVALO_MONITOR_IN"]=="")?$default["INTERVALO_MONITOR_IN"]:$dato["INTERVALO_MONITOR_IN"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions6', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions6"><?=$titulo["INTERVALO_MONITOR_IN"] ;?></span></td>
			</tr>
			<tr>
				<td><?=_("LLAMADA DE DESBORDE DESDE") ;?></td>
				<td><input name="txtcalldesbordedes" type="text" id="txtcalldesbordedes" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["LLAMADA_DESBORDE_DES"]=="")?$default["LLAMADA_DESBORDE_DES"]:$dato["LLAMADA_DESBORDE_DES"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions7', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions7"><?=$titulo["LLAMADA_DESBORDE_DES"] ;?></span></td>
			</tr>
			<tr>
				<td><?=_("LLAMADA DE DESBORDE HASTA") ;?></td>
				<td><input name="txtcalldesbordehas" type="text" id="txtcalldesbordehas" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["LLAMADA_DESBORDE_HAS"]=="")?$default["LLAMADA_DESBORDE_HAS"]:$dato["LLAMADA_DESBORDE_HAS"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions8', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions8"><?=$titulo["LLAMADA_DESBORDE_HAS"] ;?></span></td>
			</tr>
			<tr>
				<td><?=_("LLAMADA NORMAL DESDE") ;?></td>
				<td><input name="txtcallnormaldes" type="text" id="txtcallnormaldes" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["LLAMADA_NORMAL_DESDE"]=="")?$default["LLAMADA_NORMAL_DESDE"]:$dato["LLAMADA_NORMAL_DESDE"]; ?>" />&nbsp;&nbsp;<span style="display: none;" id="T2TExtensions9"><?=$titulo["LLAMADA_NORMAL_DESDE"] ;?></span><img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions9', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"></td>
			</tr>
			<tr>
				<td><?=_("LLAMADA NORMAL HASTA") ;?></td>
				<td><input name="txtcallnormalhas" type="text" id="txtcallnormalhas" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["LLAMADA_NORMAL_HASTA"]=="")?$default["LLAMADA_NORMAL_HASTA"]:$dato["LLAMADA_NORMAL_HASTA"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions10', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions10"><?=$titulo["LLAMADA_NORMAL_HASTA"] ;?></span></td>
			</tr>
			<tr>
				<td><?=_("LLAMADA DE VICIO DESDE") ;?></td>
				<td><input name="txtcallviciodes" type="text" id="txtcallviciodes" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["LLAMADA_VICIO_DESDE"]=="")?$default["LLAMADA_VICIO_DESDE"]:$dato["LLAMADA_VICIO_DESDE"]; ?>"/>&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions11', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions11"><?=$titulo["LLAMADA_VICIO_DESDE"] ;?></span></td>
			</tr>
			<tr>
				<td><?=_("LLAMADA DE VICIO HASTA") ;?></td>
				<td><input name="txtcallviciohas" type="text" id="txtcallviciohas" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["LLAMADA_VICIO_HASTA"]=="")?$default["LLAMADA_VICIO_HASTA"]:$dato["LLAMADA_VICIO_HASTA"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions12', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions12"><?=$titulo["LLAMADA_VICIO_HASTA"] ;?></span></td>
			</tr>
			<tr>
				<td><?=_("NIVELES DE UBIGEO") ;?></td>
				<td><input name="txtnubigeo" type="text" id="txtnubigeo" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["UBIGEO_NIVELES_ENTIDADES"]=="")?$default["UBIGEO_NIVELES_ENTIDADES"]:$dato["UBIGEO_NIVELES_ENTIDADES"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions13', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions13"><?=$titulo["UBIGEO_NIVELES_ENTIDADES"] ;?></span></td>
			</tr>
		</table>
	</div>	
	<div class="dhtmlgoodies_aTab">
		<table width="200" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFD" class="parametros">
			<tr>
				<td><?=_("TIEMPO DE REGISTRO") ;?></td>
				<td><input name="txttregistro" type="text" id="txttregistro" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["TIEMPO_REGISTRO"]=="")?$default["TIEMPO_REGISTRO"]:$dato["TIEMPO_REGISTRO"]; ?>" />MIN.&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions14', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions14"><?=$titulo["TIEMPO_REGISTRO"] ;?></span></td>
			</tr>
			<tr>
				<td><?=_("TIEMPO DE ASIGNACION") ;?></td>
				<td><input name="txttasigna" type="text" id="txttasigna" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["TIEMPO_ASIGNACION"]=="")?$default["TIEMPO_ASIGNACION"]:$dato["TIEMPO_ASIGNACION"]; ?>" />MIN.&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions15', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions15"><?=$titulo["TIEMPO_ASIGNACION"] ;?></span></td>
			</tr>
			<tr>
				<td><?=_("TIEMPO DE DESLOGEO") ;?></td>
				<td><input name="txtinactividad" type="text" id="txtinactividad" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["TIEMPO_DESLOGEO"]=="")?$default["TIEMPO_DESLOGEO"]:$dato["TIEMPO_DESLOGEO"]; ?>" />MIN.&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions16', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions16"><?=$titulo["TIEMPO_DESLOGEO"] ;?></span></td>
			</tr>
			<tr>
				<td><?=_("% TIEMPO DE CADUCIDAD") ;?></td>
				<td><input name="txtpcaducido" type="text" id="txtpcaducido" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["PRCTJ_TIEMPO_CADUCID"]=="")?$default["PRCTJ_TIEMPO_CADUCID"]:$dato["PRCTJ_TIEMPO_CADUCID"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions17', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions17"><?=$titulo["PRCTJ_TIEMPO_CADUCID"] ;?></span></td>
			</tr>
			
			<tr>
				<td><?=_("TIEMPO ANTES DE VISUALIZACION DE LAS TAREAS") ;?></td>
				<td><input name="tiempo_verde_monitor" type="text" id="tiempo_verde_monitor" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["TIEMPO_VERDE_MONITOR"]=="")?$default["TIEMPO_VERDE_MONITOR"]:$dato["TIEMPO_VERDE_MONITOR"]; ?>" />MIN.&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions1T1', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions1T1"><?=$titulo["TIEMPO_VERDE_MONITOR"] ;?></span></td>
			</tr>
			
			<tr>
				<td><?=_("TIEMPO EN AMBAR EN EL MONITOR") ;?></td>
				<td><input name="tiempo_ambar_monitor" type="text" id="tiempo_ambar_monitor" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["TIEMPO_AMBAR_MONITOR"]=="")?$default["TIEMPO_AMBAR_MONITOR"]:$dato["TIEMPO_AMBAR_MONITOR"]; ?>" />MIN.&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions1T2', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions1T2"><?=$titulo["TIEMPO_AMBAR_MONITOR"] ;?></span></td>
			</tr>
			
			
			
			<tr>
				<td><?=_("CATALOGO DE # LINEAS DE PAGINACION") ;?></td>
				<td><input name="txtncatalogo" type="text" id="txtncatalogo" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["PAG_CATALOGOS"]=="")?$default["PAG_CATALOGOS"]:$dato["PAG_CATALOGOS"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions18', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions18"><?=$titulo["PAG_CATALOGOS"] ;?></span></td>
			</tr>
			<tr>
				<td colspan="2" bgcolor="#333333"></td>
			</tr>
			<tr>
				<td><?=_("N# INTENTOS DE LOGEO") ;?></td>
				<td><input name="txtintentousu" type="text" id="txtintentousu" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["NUMERO_INTENTOS_LOGIN"]=="")?$default["NUMERO_INTENTOS_LOGIN"]:$dato["NUMERO_INTENTOS_LOGIN"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions189', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions189"><?=$titulo["NUMERO_INTENTOS_LOGIN"] ;?></span></td>
			</tr>
			<tr>
				<td><?=_("TIEMPO DE BLOQUEO LOGIN") ;?></td>
				<td><input name="txttiempousu" type="text" id="txttiempousu" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["TIEMPO_BLOQUEO_LOGIN"]=="")?$default["TIEMPO_BLOQUEO_LOGIN"]:$dato["TIEMPO_BLOQUEO_LOGIN"]; ?>" />MIN.&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions188', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions188"><?=$titulo["TIEMPO_BLOQUEO_LOGIN"] ;?></span></td>
			</tr>  
			<tr>
				<td><?=_("NUMERO DE TAREAS") ;?></td>
				<td><input name="txttareas" type="text" id="txttareas" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["NUMERO_TAREAS"]=="")?$default["NUMERO_TAREAS"]:$dato["NUMERO_TAREAS"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensionsTareas', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensionsTareas"><?=$titulo["NUMERO_TAREAS"] ;?></span></td>
			</tr>
		</table>
	</div>
	<div class="dhtmlgoodies_aTab">
		<table width="200" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFD" class="parametros">
		  <tr>
			<td><?=_("SEPARADOR DE DECIMALES") ;?></td>
			<td><input name="txtdecimales" type="text" class="classtexto" id="txtdecimales" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=($dato["SEPARADOR_DECIMALES"]=="")?$default["SEPARADOR_DECIMALES"]:$dato["SEPARADOR_DECIMALES"]; ?>" size="3" maxlength="2" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions19', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions19"><?=$titulo["SEPARADOR_DECIMALES"] ;?></span></td>
		  </tr>
		  <tr>
			<td><?=_("SEPARADOR DE MILLARES") ;?></td>
			<td><input name="txtmillares" type="text" id="txtmillares" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" value="<?=($dato["SEPARADOR_MILLARES"]=="")?$default["SEPARADOR_MILLARES"]:$dato["SEPARADOR_MILLARES"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions20', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions20"><?=$titulo["SEPARADOR_MILLARES"] ;?></span></td>
		  </tr>
		  <tr>
			<td><?=_("LONGITUD  DE DECIMALES") ;?></td>
			<td><input name="txtlongitudec" type="text" id="txtlongitudec" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" value="<?=($dato["LONGITUD_DECIMALES"]=="")?$default["LONGITUD_DECIMALES"]:$dato["LONGITUD_DECIMALES"]; ?>" />&nbsp;&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions21', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions21"><?=$titulo["LONGITUD_DECIMALES"] ;?></span></td>
		  </tr>
		</table>
	</div>	
	<div class="dhtmlgoodies_aTab">
<table width="37%" border="0" align="center" cellpadding="1" cellspacing="1"  class="parametros" >
  <tr>
    <td width="266"><?=_("CDE/SKILL") ;?></td>
    <td width="77">
      <input name="txtcde" type="text" id="txtcde" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" onKeyPress="return validarnum(event)" style="text-align:center" value="<?=($dato["RANKING_CDE"]=="")?$default["RANKING_CDE"]:$dato["RANKING_CDE"]; ?>" />
      <strong>%</strong>&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TRankingCDE', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TRankingCDE">
				  <?=$titulo["RANKING_CDE"] ;?>
    </span></td>
  </tr>
  <tr>
    <td><?=_("COSTO") ;?></td>
    <td>
      <input name="txtcosto" type="text" id="txtcosto" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  onKeyPress="return validarnum(event)" style="text-align:center" value="<?=($dato["RANKING_C0STO"]=="")?$default["RANKING_C0STO"]:$dato["RANKING_C0STO"]; ?>" />
    <strong>%</strong>&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TRankingCosto', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TRankingCosto">
				  <?=$titulo["RANKING_C0STO"] ;?>
				  </span></td>
  </tr>
  <tr>
    <td><?=_("SATISFACCION CLIENTE") ;?></td>
    <td><input name="txtsatisfaccion" type="text" id="txtsatisfaccion" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  onKeyPress="return validarnum(event)" style="text-align:center" value="<?=($dato["RANKING_SATISFACCION"]=="")?$default["RANKING_SATISFACCION"]:$dato["RANKING_SATISFACCION"]; ?>"/>
    <strong>%</strong>&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TRankingSatis', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TRankingSatis">
				  <?=$titulo["RANKING_SATISFACCION"] ;?>
				  </span></td>
  </tr>
  <tr>
    <td><?=_("INFRAESTRUCTURA") ;?></td>
    <td><span class="style1">
      <input name="txtinfra" type="text" id="txtinfra" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  onKeyPress="return validarnum(event)" style="text-align:center" value="<?=($dato["RANKING_INFRAESTRUCTURA"]=="")?$default["RANKING_INFRAESTRUCTURA"]:$dato["RANKING_INFRAESTRUCTURA"]; ?>"/>
    <strong>%</strong></span>&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TRankingInfra', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TRankingInfra">
				  <?=$titulo["RANKING_INFRAESTRUCTURA"] ;?>
				  </span></td>
  </tr>
  <tr>
    <td><?=_("FIDELIDAD") ;?></td>
    <td><span class="style1">
      <input name="txtfidelidad" type="text" id="txtfidelidad" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  onKeyPress="return validarnum(event)" style="text-align:center" value="<?=($dato["RANKING_FIDELIDAD"]=="")?$default["RANKING_FIDELIDAD"]:$dato["RANKING_FIDELIDAD"]; ?>"/>
    <strong>%</strong></span>&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TRankingFid', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TRankingFid">
				  <?=$titulo["RANKING_FIDELIDAD"] ;?>
				  </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCEEFF">&nbsp;</td>
    <td bgcolor="#CCEEFF">&nbsp;</td>
  </tr>
  <tr>
    <td><?=_("CALCULO DE DEFICIENCIA EXTERNA LEVE") ;?></td>
    <td><span class="style1">
      <input name="txtcdeleve" type="text" id="txtcdeleve" size="3" maxlength="3" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  onKeyPress="return numeroDecimal(event)" style="text-align:center" value="<?=($dato["LEVE_CDE"]=="")?$default["LEVE_CDE"]:$dato["LEVE_CDE"]; ?>"/>
    </span><span>&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TCDELEVE', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TCDELEVE">
				  <?=$titulo["LEVE_CDE"] ;?>
				  </span></td>
  </tr>
  <tr>
    <td><?=_("CALCULO DE DEFICIENCIA EXTERNA GRAVE") ;?></td>
    <td><span class="style1">
      <input name="txtcdegrave" type="text" id="txtcdegrave" size="3" maxlength="3" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  onKeyPress="return numeroDecimal(event)" style="text-align:center" value="<?=($dato["GRAVE_CDE"]=="")?$default["GRAVE_CDE"]:$dato["GRAVE_CDE"]; ?>"/>
    </span><span>&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TCDEGRAVE', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TCDEGRAVE">
				  <?=$titulo["GRAVE_CDE"] ;?>
				  </span></td>
  </tr>
  <tr>
    <td><?=_("CALCULO DE DEFICIENCIA INTERNA LEVE") ;?></td>
    <td><span class="style1">
      <input name="txtcdileve" type="text" id="txtcdileve" size="3" maxlength="3" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  onKeyPress="return numeroDecimal(event)" style="text-align:center" value="<?=($dato["LEVE_CDI"]=="")?$default["LEVE_CDI"]:$dato["LEVE_CDI"]; ?>"/>
    </span><span>&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TCDILEVE', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TCDILEVE">
				  <?=$titulo["LEVE_CDI"] ;?>
				  </span></td>
  </tr>
  <tr>
    <td><?=_("CALCULO DE DEFICIENCIA INTERNA GRAVE") ;?></td>
    <td><span class="style1">
      <input name="txtcdigrave" type="text" id="txtcdigrave" size="3" maxlength="3" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  onKeyPress="return numeroDecimal(event)" style="text-align:center" value="<?=($dato["GRAVE_CDI"]=="")?$default["GRAVE_CDI"]:$dato["GRAVE_CDI"]; ?>"/>
    </span><span>&nbsp;<img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TCDIGRAVE', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TCDIGRAVE">
				  <?=$titulo["GRAVE_CDI"] ;?>
				  </span></td>
  </tr>
</table>


	</div>
	
	
	<div class="dhtmlgoodies_aTab">		
		<table width="200" border="0" cellpadding="1" cellspacing="1" class="parametros">
			<tr>
				<td><?=_("PREFIJO LLAMADAS SALIENTES") ;?></td>
				<td><input name="txtprefijo" type="text" class="classtexto" id="txtprefijo" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=($dato["PREFIJO_LLAMADAS_SALIENTES"]=="")?$default["PREFIJO_LLAMADAS_SALIENTES"]:$dato["PREFIJO_LLAMADAS_SALIENTES"]; ?>" size="4" maxlength="3" />
				</td>
				<td>&nbsp;</td>
				<td><img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions22', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions22"><?=$titulo["PREFIJO_LLAMADAS_SALIENTES"] ;?></span></td>
			</tr>
			<tr>
				<td><?=_("LISTADO EXTENSION DE CABINA:") ;?></td>
				<td>&nbsp;&nbsp;</td>
				<td>&nbsp;</td>
				<td><img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions23', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions23">
				  <?=$titulo["LISTADO_EXTENSION_CABINAS"] ;?></span></td>
			</tr>
			<tr>
				<td colspan="2" class="modo1"><div align="right"><?=_("NUMERO DE EXTENSION") ;?></div></td>
				<td class="modo1"><input name="txtextensioncab" type="text" id="txtextensioncab" size="15" maxlength="12" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" value="<?=($dato["NUM_EXTENSION_CABINA"]=="")?$default["NUM_EXTENSION_CABINA"]:$dato["NUM_EXTENSION_CABINA"]; ?>" onKeyPress="return validarnum(event)" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"  class="modo1"><div align="right"><?=_("PROTOCOLO") ;?></div></td>
				<td  class="modo1"><select name="cmbcabina" id="cmbcabina" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
				  <option value="">SELECCIONE</option>
				  <option value="IAX" <? if($dato["PROTOCOLO_CABINA"]=="IAX")	echo "Selected"; ?> >IAX</option>
				  <option value="SIP" <? if($dato["PROTOCOLO_CABINA"]=="SIP")	echo "Selected"; ?>>SIP</option>
				  </select>		    </td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"  class="modo1"><div align="right">
					<?=_("CONTEXTO") ;?>
				</div></td>
				<td  class="modo1"><input name="txtcontextocab" type="text" id="txtcontextocab" size="14" maxlength="15" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" value="<?=($dato["CONTEXTO_CABINA"]=="")?$default["CONTEXTO_CABINA"]:$dato["CONTEXTO_CABINA"]; ?><?=$dato["CONTEXTO_CABINA"]; ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"><?=_("LISTADO EXTENSION DE SUPERVISOR") ;?></td>
				<td>&nbsp;</td>
				<td><img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions24', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions24">
				  <?=$titulo["LISTADO_EXTENSION_SUPERVISOR"] ;?>
				  </span></td>
			</tr>
			<tr>
				<td colspan="2"  class="modo1"><div align="right">
					<?=_("NUMERO DE EXTENSION") ;?>
				</div></td>
				<td class="modo1"><input name="txtextensionsup" type="text" id="txtextensionsup" size="15" maxlength="12" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" value="<?=($dato["INTERVALO_MONITOR_IN"]=="")?$default["NUM_EXTENSION_SUPERVISOR"]:$dato["NUM_EXTENSION_SUPERVISOR"]; ?>" onKeyPress="return validarnum(event)" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"  class="modo1"><div align="right">
					<?=_("PROTOCOLO") ;?>
				</div></td>
				<td class="modo1"><select name="cmbsupervisor" id="cmbsupervisor" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
				  <option value="">SELECCIONE</option>
				 <option value="IAX" <? if($dato["PROTOCOLO_SUPERVISOR"]=="IAX")	echo "Selected"; ?> >IAX</option>
				 <option value="SIP" <? if($dato["PROTOCOLO_SUPERVISOR"]=="SIP")	echo "Selected"; ?> >SIP</option>
				</select></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"  class="modo1"><div align="right">
				  <?=_("CONTEXTO") ;?>
				</div></td>
				<td class="modo1"><input name="txtcontextosup" type="text" id="txtcontextosup" size="14" maxlength="15" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" value="<?=($dato["CONTEXTO_SUPERVISOR"]=="")?$default["CONTEXTO_SUPERVISOR"]:$dato["CONTEXTO_SUPERVISOR"]; ?>" /></td>
				<td>&nbsp;</td>
			</tr>		
			<tr>
				<td colspan="2"><?=_("LISTADO EXTENSION ALARMA SONORA") ;?>&nbsp;</td>
				<td>&nbsp;</td>
				<td><img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions25', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions25">
				  <?=$titulo["LISTADO_EXTENSION_ALARMA"] ;?>
				  </span></td>
			</tr>
			<tr>
				<td colspan="2"  class="modo1"><div align="right">
					<?=_("NUMERO DE EXTENSION") ;?>
				</div></td>
				<td  class="modo1"><input name="txtextensionalar" type="text" id="txtextensionalar" size="15" maxlength="12" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" value="<?=($dato["NUM_EXTENSION_ALARMA"]=="")?$default["NUM_EXTENSION_ALARMA"]:$dato["NUM_EXTENSION_ALARMA"]; ?>" onKeyPress="return validarnum(event)" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"  class="modo1"><div align="right"><?=_("PROTOCOLO") ;?></div></td>
				<td class="modo1"><select name="cmbalarma" id="cmbalarma" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
					<option value="">SELECCIONE</option>
					<option value="IAX" <? if($dato["PROTOCOLO_ALARMA"]=="IAX")	echo "Selected"; ?> >IAX</option>
					<option value="SIP" <? if($dato["PROTOCOLO_ALARMA"]=="SIP")	echo "Selected"; ?> >SIP</option>
				  </select>            </td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"  class="modo1"><div align="right"><?=_("CONTEXTO") ;?></div></td>
				<td class="modo1"><input name="txtcontextoalar" type="text" id="txtcontextoalar" size="14" maxlength="15" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" value="<?=($dato["CONTEXTO_ALARMA"]=="")?$default["CONTEXTO_ALARMA"]:$dato["CONTEXTO_ALARMA"]; ?>" /></td>
				<td>&nbsp;</td>
			</tr>		  
			<tr>
				<td><?=_("IP SERVIDOR ASTERISK LOCAL") ;?></td>
				<td><input name="txtipservidor" type="text" id="txtipservidor" size="15" maxlength="18" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" value="<?=($dato["IPSERVIDOR_ASTERISK"]=="")?$default["IPSERVIDOR_ASTERISK"]:$dato["IPSERVIDOR_ASTERISK"]; ?>" />
				 </td>
				<td>&nbsp;</td>
				<td><img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions26', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions26">
				  <?=$titulo["IPSERVIDOR_ASTERISK"] ;?>
				  </span></td>
			</tr>		
			<tr>
				<td><?=_("USUARIO MANAGER") ;?></td>
				<td colspan="2"><input name="txtusuariomag" type="text" id="txtusuariomag" size="22" maxlength="18" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" value="<?=($dato["USUARIO_MANAGER"]=="")?$default["USUARIO_MANAGER"]:$dato["USUARIO_MANAGER"]; ?>" />&nbsp;&nbsp;<span style="display: none;" id="T2TExtensions21"><?=$titulo["USUARIO_MANAGER"] ;?></span></td>
				<td><img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions27', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions27">
				  <?=$titulo["USUARIO_MANAGER"] ;?>
				  </span></td>
			</tr>	
			<tr>
				<td><?=_("PASSWORD USUARIO MANAGER") ;?></td>
				<td>
				<input name="txtpassmanag" type="password" id="txtpassmanag" size="22" maxlength="18" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" value="<?=($dato["USUARIO_PASSWORD_MANAGER"]=="")?$default["USUARIO_PASSWORD_MANAGER"]:$dato["USUARIO_PASSWORD_MANAGER"]; ?>" /></td>
				<td>&nbsp;</td>
				<td><img src="../../../../imagenes/iconos/info.png" onMouseOver="TagToTip('T2TExtensions28', ABOVE, true, OFFSETX, -17, BALLOON, true)" onMouseOut="UnTip()"><span style="display: none;" id="T2TExtensions28"><?=$titulo["USUARIO_PASSWORD_MANAGER"];?></span></td>
			</tr>
		 </table>		
	</div>	
</div>
<script type="text/javascript">
initTabs('dhtmlgoodies_tabView2',Array('<?=_("FUNCIONAMIENTO") ;?>','<?=_("TIEMPOS") ;?>','<?=_("FINANCIERO") ;?>','<?=_("RANKING PROVEEDOR") ;?>','<?=_("PBX") ;?>'),0,433,300,Array(false,false,false,false));
</script>
<br><br><br><br>
	<input type="button" name="btnaceptar" id="btnaceptar" value="<?=_("GRABAR CAMBIOS") ;?>" title="<?=_("GRABAR CAMBIOS") ;?>" onCLick="validarForm()"  >
	<input type="hidden" name="idpais" id="idpais" value="<?=$_REQUEST["idpais"];?>">
	<input type="hidden" name="idpar" id="idpar" value="<?=$row->IDPARAMETRO;?>">	
</form>					
</body>
</html>