<?php

	session_start();
	
	include_once("../../../modelo/clase_lang.inc.php");
	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../../modelo/validar_permisos.php");	
	include_once("../../includes/arreglos.php");
	include_once("../../../modelo/functions.php");
	
	$con = new DB_mysqli();
		
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

	Auth::required();	

	validar_permisos("MENU_SAC",1);
	
//verificar permisos de accesos a las cuentas
	// Pais origen
	$paisDefault=$con->lee_parametro("IDPAIS");
	
	$paisOrigen=($_POST["cmbpais"])?$_POST["cmbpais"]:$paisDefault;
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"],$paisOrigen);
 
//paises disponibles
		$pais=$con->consultation("SELECT TODOPAISES FROM $con->catalogo.catalogo_usuario WHERE IDUSUARIO='".$_SESSION["user"]."'");
		if($pais[0][0] ==1){
		
			$SqlPais="SELECT
				  catalogo_pais.IDPAIS,
				  catalogo_pais.NOMBRE
				FROM $con->catalogo.catalogo_pais
				  INNER JOIN $con->catalogo.catalogo_parametro_pais
					ON catalogo_parametro_pais.IDPAIS = catalogo_pais.IDPAIS
				ORDER BY catalogo_pais.NOMBRE";
		} else{
		
			$SqlPais="SELECT
					  catalogo_pais.IDPAIS,
					  catalogo_pais.NOMBRE
					FROM  $con->catalogo.catalogo_pais
					  INNER JOIN  $con->temporal.acceso_usuarioxpais
						ON acceso_usuarioxpais.IDPAIS = catalogo_pais.IDPAIS
					WHERE acceso_usuarioxpais.IDUSUARIO = '".$_SESSION["user"]."'
					ORDER BY catalogo_pais.NOMBRE";
		}

	$Sql="SELECT
		  catalogo_cuenta.NOMBRE,
		  retencion.MOTIVOLLAMADA,
		  COUNT(*)  AS cantidad
		FROM $con->temporal.retencion
		  INNER JOIN catalogo_cuenta
			ON catalogo_cuenta.IDCUENTA = retencion.IDCUENTA $cmbCuentas";
	
	$Sql=$Sql." GROUP BY retencion.IDCUENTA,retencion.MOTIVOLLAMADA ORDER BY catalogo_cuenta.NOMBRE,cantidad,retencion.MOTIVOLLAMADA ";
	
// Afliados en riesgos
	$sqlCanal="";
	$sqlSucursal="";
	//condicionar cuentas	
	foreach($_POST["cmbcuenta"] as $idcuenta){			
		$cuentas[] ="'$idcuenta'";
		$idcuentas =implode(',',$cuentas);
	 }
	 
	 if($idcuentas and !$_POST["todascuentas"])	$ver_cuentas="catalogo_cuenta.IDCUENTA IN($idcuentas) AND ";
 
	//condicionar opciones check
	$celdas=2;
	if($_POST["ckbplan"]){	
		$celdas=$celdas+1;
		$agrupaplan="3,"; 
	}

	if($_POST["ckbcancelados"]){
		$agrupacancelados=",6";
	}
	
	if($_POST["ckbcanal"]){
		$celdas=$celdas+1;
	}	
	
	if($_POST["ckbsucursal"]){
		$celdas=$celdas+1;
	}
	
	if($_POST["ckbmodalidadpg"]){
		//$celdas=$celdas+1;
	}
	
	if($_POST["ckbcanal"])	$agrupacanal="7,";
	if($_POST["ckbsucursal"])	$agrupasucursal="8,";
	if($_POST["ckbmodalidadpg"])	$agrupacondicionpg=",12";
 
	$marcado="checked";

	if($_POST["btnconsultar"] or $_POST["btnexportar"]){
	
		if($_POST["todascuentas"] ==1) $marcado="checked"; else $marcado="";
		if($_POST["ckbplan"]) $marcadoplan="checked"; else $marcadoplan="";
		if($_POST["ckbcancelados"]) $marcadcancelados="checked"; else $marcadcancelados="";
		if($_POST["ckbmodalidadpg"]) $marcamodalidadpg="checked"; else $marcamodalidadpg="";
		if($_POST["ckbtotal"]) $marcadototales="checked"; else $marcadototales="";
		if($_POST["ckbcanal"]){
			$marcadocanal="checked"; else $marcadocanal="";
			$sqlCanal="LEFT JOIN $con->catalogo.catalogo_canal_venta ON catalogo_canal_venta.IDCANALVENTA = catalogo_afiliado.IDCANALVENTA";
		}
		if($_POST["ckbsucursal"]){
			$marcadosucursal="checked"; else $marcadosucursal="";
			$sqlSucursal="LEFT JOIN catalogo_sucursal ON catalogo_sucursal.IDSUCURSAL = catalogo_afiliado.IDSUCURSAL";
			}
		if($_POST["ckbfecha"]) $marcadofecha="checked"; else $marcadofecha="";
	
	}
	 
	if(!$_POST["todascuentas"]) $idcuentas="catalogo_cuenta.IDCUENTA IN($idcuentas) AND"; else  $idcuentas="";
 
	$Sql_Afliadoriesgo="SELECT
	/* CONSULTA SAC ESTADISTICA */
		  catalogo_cuenta.IDCUENTA,
		  catalogo_cuenta.NOMBRE       AS cuenta,
		  catalogo_programa.IDPROGRAMA,
		  catalogo_programa.NOMBRE     AS programa,		  
		  (SELECT
			 COUNT(*)
		   FROM $con->catalogo.catalogo_afiliado
		   WHERE catalogo_afiliado.STATUSASISTENCIA = 'ACT'
		   GROUP BY catalogo_afiliado.STATUSASISTENCIA) AS totalact,
		  (SELECT
			 COUNT(*)
		   FROM $con->catalogo.catalogo_afiliado
		   WHERE catalogo_afiliado.STATUSASISTENCIA = 'CAN'
		   GROUP BY catalogo_afiliado.STATUSASISTENCIA) AS totalcan,
		    catalogo_canal_venta.DESCRIPCION AS CANALS,
			catalogo_sucursal.DESCRIPCION AS SUCURSALS,  
		  SUM(IF(catalogo_afiliado.STATUSASISTENCIA='ACT',1,0)) AS activos,
		  SUM(IF(catalogo_afiliado.STATUSASISTENCIA='CAN',1,0)) AS cancelados,
		  if(catalogo_afiliado.STATUSASISTENCIA!='',COUNT(*),0) AS totales,
		  ARRMODALIDADPG,
		  COUNT(*) AS TOTALMODALIDADPG
		FROM $con->catalogo.catalogo_afiliado
			INNER JOIN $con->catalogo.catalogo_cuenta
				ON catalogo_cuenta.IDCUENTA = catalogo_afiliado.IDCUENTA
			INNER JOIN $con->catalogo.catalogo_programa
			ON catalogo_programa.IDPROGRAMA = catalogo_afiliado.IDPROGRAMA
			$sqlCanal
			$sqlSucursal		
		  /*$con->catalogo.catalogo_programa,
		  $con->catalogo.catalogo_cuenta*/
		  
		WHERE $ver_cuentas catalogo_afiliado.AFILIADO_SISTEMA='VALIDADO'"; 
	
 	if(trim($_POST["fechaini"]) and trim($_POST["fechafin"]) and !$_POST["ckbfecha"])	$Sql_Afliadoriesgo=$Sql_Afliadoriesgo." and LEFT(catalogo_afiliado.FECHACREACION,10) BETWEEN '".$_POST["fechaini"]."' AND '".$_POST["fechafin"]."' ";
 	if(trim($_POST["fechaini"]) and trim($_POST["fechafin"]) and $_POST["ckbfecha"])	$Sql_Afliadoriesgo=$Sql_Afliadoriesgo." and catalogo_afiliado.FECHAINICIOVIGENCIA >='".$_POST["fechaini"]."' AND catalogo_afiliado.FECHAINICIOVIGENCIA <='".$_POST["fechafin"]."' ";
				
	$Sql_Afliadoriesgo=$Sql_Afliadoriesgo." GROUP BY 1,$agrupaplan $agrupacanal $agrupasucursal 5 $agrupacancelados $agrupacondicionpg ORDER BY catalogo_cuenta.NOMBRE,catalogo_programa.NOMBRE";
 // echo $Sql_Afliadoriesgo;
	if($_POST["btnconsultar"] or $_POST["btnexportar"]) $result_afil=$con->query($Sql_Afliadoriesgo); 

	if($_POST["btnexportar"])  include("exportar_estadistica.php");

	if($_POST["todascuentas"] ==0)	foreach ($_POST["cmbcuenta"] as $nombre)  $seleccionado[]=$nombre;
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=_("SAC-Estadistica") ;?></title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="../../../../estilos/tablas/pagination.css" media="all">
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
		<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/soaa.ico">
		
	<script type="text/javascript">
		
		function validarIngreso(variable){	 
		
           if(document.form1.fechaini.value!="" && document.form1.fechafin.value==""){
                  alert('<?=_("SELECCIONE ALGUNA FECHA") ;?>');
                  document.form1.fechafin.focus();
                  return (false);
           }		
           if(document.form1.fechafin.value!="" && document.form1.fechaini.value==""){
                  alert('<?=_("SELECCIONE ALGUNA FECHA") ;?>');
                  document.form1.fechaini.focus();
                  return (false);
           }
		
			return (true);    
		}
		
		function marcar_todascuentas(){
		 
			var Lista=document.getElementById('cmbcuenta');
			var Arreglo = $A(Lista);
			
			if ($('todascuentas').checked)
			Arreglo.each(function(el, indice){
				el.selected=true;
			});
			else
			Arreglo.each(function(el, indice){
				el.selected=false;
			});
			return;
		}
				
		function desactiva_check(){
			$('todascuentas').checked=false;
			return;
		}
								
		function hablitarCheck(check){
			if(check ==true) $('ckbmodalidadpg').disabled=false; else { $('ckbmodalidadpg').disabled=true; $('ckbmodalidadpg').checked=false;}
		}
						
		function mostrarEtiqueta(){
				if($('ckbfecha').checked){
					document.getElementById('div-fregis').style.display='none';
					document.getElementById('div-fafilia').style.display='block';				
				} else {
				
					document.getElementById('div-fregis').style.display='block';
					document.getElementById('div-fafilia').style.display='none';						
				}
			
		}
		
	</script>
	<link rel="stylesheet" href="../../../../librerias/TinyAccordion/style.css" type="text/css" />
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
	<style type="text/css">@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");</style> 
    <style type="text/css">
		<!--
		.style3 {color: #FFFFFF; font-weight: bold; }
		-->
    </style>
</head>
<body>
<?
//visualizar el logo de pruebas
    if($con->logoMensaje){
?>
    <div  id='en_espera'><? include("../../avisosystem.php");?> </div><br> 
<? } ?>
<div class="pagination"><a href="buscarafiliado.php"><?=_("Nueva Busqueda") ;?></a><a href="newafiliado.php"><?=_("Contacto No V&aacute;lido") ;?></a><a href="reportes.php"><?=_("Reporte") ;?></a><span class="current"><?=_("Estad&iacute;stica") ;?></span></div>
<h2 class="Box"><?=_("GESTION DE ESTADISTICAS") ;?></h2>

 
 <ul class="acc" id="acc">
	<li>
		<h3>AFILIADOS EN RIESGO</h3>
		<div class="acc-section">
			<div class="acc-content"> 
			<form id="form1" name="form1" method="post" action="" onSubmit = "return validarIngreso(this)" >
		<!-- table width="90%"  border="0" cellpadding="1" cellspacing="1" bgcolor="#F5F5F5" style="border:1px solid #DBDBDB">
			<tr>
				<td colspan="4" align="right"><?=_("PAIS")?></td>
				<td>
					<?
						// $con->cmbselectdata($SqlPais,"cmbpais",$paisOrigen,"onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","");
						// include("combopais.php"); 
						
					?>
				</td>
			</tr>		
			<tr>
				<td width="87" >&nbsp;</td>
				<td width="174" ><?=_("PERIODO DE FECHA") ;?>:</td>
				<td width="270"><?=_("DE") ;?> 
					<input name="fechaini" type="text" class="classtexto"  id="fechaini" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$_POST["fechaini"];?>" />
					<button   id="cal-button-1">...</button>
					<script type="text/javascript">
						Calendar.setup({
						  inputField    : "fechaini",
						  button        : "cal-button-1",
						  align         : "Tr"
						});
				  </script></td>				  
				<td width="153"><?=_("AL") ;?>
					<input name="fechafin" type="text" class="classtexto" id="fechafin" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$_POST["fechafin"];?>" />
					<button   id="cal-button-2">...</button>
					<script type="text/javascript">
						Calendar.setup({
						  inputField    : "fechafin",
						  button        : "cal-button-2",
						  align         : "Tr"
						});
					</script></td>
				<td width="153"><input type="submit" name="Submit" id="button" value="<?=_("GENERAR") ;?>"  style="font-weight:bold;width:140px;font-size:10px;"  /></td>
			</tr>
		</table -->		
 
			<table border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F5F5F5" style="border:1px solid #DBDBDB;width:100%">
				<tr>
					<td width="74" align="right"><div id="div-fregis" style="display:<?=(!$_POST["ckbfecha"])?"block":"none"?>"><?=_("FECHA REGIS.") ;?></div><div id="div-fafilia" style="display:<?=($_POST["ckbfecha"])?"block":"none"?>"><?=_("FECHA AFILIA.") ;?></div></td>
					<td width="283"> 
						<input name="fechaini" type="text" class="classtexto"  id="fechaini" readonly onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$_POST["fechaini"];?>" />
							<button   id="cal-button-1">...</button>
							<script type="text/javascript">
								Calendar.setup({
								  inputField    : "fechaini",
								  button        : "cal-button-1",
								  align         : "Tr"
								});
							</script><?=_("AL") ;?>
						  <input name="fechafin" type="text" class="classtexto" id="fechafin" readonly onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$_POST["fechafin"];?>" />
						  <button   id="cal-button-2">...</button>
							<script type="text/javascript">
							Calendar.setup({
							  inputField    : "fechafin",
							  button        : "cal-button-2",
							  align         : "Tr"
							});
							</script>&nbsp;<img src="../../../../imagenes/iconos/limpiar.jpg" title="<?=_('LIMPIAR FECHA') ;?>" onClick="document.getElementById('fechaini').value='';document.getElementById('fechafin').value=''" style="cursor:pointer" />
							<input name="ckbfecha" type="checkbox" id="ckbfecha" <?=$marcadofecha?>  onclick="mostrarEtiqueta()" title="<?=_("CAMBIAR FECHA");?>"/>
					</td>
					<td width="104"><input type="submit" name="btnexportar" id="btnexportar" value="<?=_("EXPORTAR") ;?>&gt;&gt;&gt;" style="text-align:center;font-weight:bold;font-size:10px"></td>
				</tr>
				<tr>
				  <td align="right"><?=_("CUENTAS");?></td>
				  <td colspan="2">-&gt;<?=_("TODOS") ;?><input name="todascuentas" type="checkbox" id="todascuentas" <?=$marcado?>  onclick="marcar_todascuentas()" value="1"/></td></tr>
				<tr>
				  <td align="right">&nbsp;</td>
						  <td colspan="2">
				<div id="div-cuenta">			 
						 
						<select name="cmbcuenta[]" id="cmbcuenta" size='5' onclick="desactiva_check()" multiple onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'>
						<?		
							//if($_POST["todascuentas"])	$seleccion="SELECTED";
							//$sql_cuenta="select IDCUENTA,NOMBRE from $con->catalogo.catalogo_cuenta $cmbCuentas ORDER BY NOMBRE";
							if($allcuentas==1)	$sql_cuenta="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta ORDER BY NOMBRE"; else $sql_cuenta=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";
							$resultcue=$con->query($sql_cuenta);
							while($reg = $resultcue->fetch_object())
							 {
								if (in_array($reg->IDCUENTA,$seleccionado)) $selected='selected';	else $selected='';
								if(count($seleccionado) <1) $selected="selected";
						?>
							<option value="<?=$reg->IDCUENTA;?>" <?=$selected?> ><?=$reg->NOMBRE;?></option>
						<? 
							 } 
						?>
						</select>
								
				</div>		</td>
				</tr>
				<tr>
					<td colspan="3" align="center"> <div align="center">
						<table align="center" border="1" cellpadding="1" cellspacing="1" bgcolor="#E1FFE1" style="border-collapse:collapse">
							<tr>
								<td width="18%"><div align="right"><?=_("IDCUENTA") ;?></div></td>
								<td width="5%"><input name="ckbidcuenta" type="checkbox" id="ckbidcuenta" value="idcuenta" disabled checked="checked" /></td>
								<td width="17%"><div align="right"><?=_("CUENTA") ;?></div></td>
								<td width="7%"><input name="ckbcuenta" type="checkbox" id="ckbcuenta" value="nombrecuenta" disabled checked="checked" /></td>
								<td width="14%"><div align="right"><?=_("PLAN") ;?></div></td>
								<td width="6%"><input name="ckbplan" type="checkbox" id="ckbplan" value="nombreplan" <?=$marcadoplan?> onClick="hablitarCheck(this.checked)" /></td>
								<td width="20%"><div align="right"><?=_("ACTIVOS") ;?></div></td>
								<td width="7%"><input name="ckbactivos" type="checkbox" id="ckbactivos" value="activos" disabled checked="checked" /></td>
							</tr>
							<tr>
								<td><div align="right"><?=_("CANCELADOS") ;?></div></td>
								<td><input name="ckbcancelados" type="checkbox" id="ckbcancelados" value="cancelados" <?=$marcadcancelados?> /></td>
								<td><div align="right"><?=_("TOTALES") ;?></div></td>
								<td><input name="ckbtotal" type="checkbox" id="ckbtotal" value="total" <?=$marcadototales?> /></td>
								<td><div align="right"><?=_("CANAL") ;?></div></td>
								<td><input name="ckbcanal" type="checkbox" id="ckbcanal" value="canal" <?=$marcadocanal?>/></td>
								<td><div align="right"><?=_("SUCURSAL") ;?></div></td>
								<td><input name="ckbsucursal" type="checkbox" id="ckbsucursal" value="sucursal" <?=$marcadosucursal?> /></td>
							</tr>	
							<tr>
								<td colspan="7"><div align="right"><?=_("MODALIDADPG") ;?></div></td>
								<td ><input name="ckbmodalidadpg" type="checkbox" id="ckbmodalidadpg" value="modalidadpg" <?=$marcamodalidadpg?> /></td>							 
							</tr>
						</table>
					</div></td>
			  </tr>
			  <tr>
				  <td colspan="3" align="right">
					<div align="center">
					  <input type="submit" name="btnconsultar" id="btnconsultar" value="CONSULTAR >>>" style="text-align:center;font-weight:bold;font-size:10px"/>
					</div>
				  </td>
			  </tr>
			</table>		
		</form>
  
<!-- table width="539" border="0" align="center" cellpadding="1" cellspacing="1" style="border:1px solid #999999">
    <tr bgcolor="#A8D3FF">
      <td><div align="center"><strong><?=_("CUENTA") ;?></strong></div></td>
      <td><div align="center"><strong><?=_("GESTION") ;?></strong></div></td>
      <td><strong><?=_("CANTIDAD") ;?></strong></div></td>
    </tr>
	 <?
		// while($reg = $result->fetch_object())
		 // {			 	 
			// if($c%2==0) $fondo='#EAEAFF'; else $fondo='#F3F3F3';	
			// if($c%2==0) $clase='trbuc3'; else $clase='trbuc1';			
	?>
    <tr bgcolor=<?=$fondo;?>>
      <td><?=$reg->NOMBRE;?></td>
      <td><?=$reg->MOTIVOLLAMADA;?></td>
      <td align="center"><?=$reg->cantidad;?></td>
    </tr>
		<?	
			// $c=$c+1;		
		 // }
		?>	
  </table -->
			  <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" style="border:1px solid #C5C5E2;width:100%">
				<tr bgcolor="#ACACD7">
				  <td bgcolor="#ACACD7"><div align="center"><strong><?=_("IDCUENTA") ;?></strong></div></td>  
				  <td bgcolor="#ACACD7"><div align="center"><strong><?=_("CUENTA") ;?></strong></div></td>
				  <? if($_POST["ckbplan"]){ ?><td bgcolor="#ACACD7"><div align="center"><strong><?=_("PLAN") ;?></strong></div></td><?}?>
				  <? if($_POST["ckbcanal"]){ ?><td bgcolor="#ACACD7"><div align="center"><strong><?=_("CANAL") ;?></strong></div></td><?}?>
				  <? if($_POST["ckbsucursal"]){ ?><td bgcolor="#ACACD7"><div align="center"><strong><?=_("SUCURSAL") ;?></strong></div></td><?}?>
				  <td bgcolor="#ACACD7"><div align="center"><strong><?=_("ACTIVOS") ;?></strong></div></div></td>
				  <? if($_POST["ckbcancelados"]){ ?><td bgcolor="#ACACD7"><div align="center"><strong><?=_("CANCELADOS") ;?></strong></div></td><?}?>
				  <? if($_POST["ckbmodalidadpg"]){ ?><td bgcolor="#ACACD7"><div align="center"><strong><?=_("MODALIDAD-PG") ;?></strong></div></td><?}?>
				  <? if($_POST["ckbtotal"]){ ?><td bgcolor="#ACACD7"><div align="center"><strong><?=_("TOTALES") ;?></strong></div></td><?}?>
				</tr>
				<?
					if($_POST["btnconsultar"]){
					
						while($reg = $result_afil->fetch_object()){			 	 
							if($c%2==0) $fondo='#EAEAFF'; else $fondo='#F3F3F3';	
							if($c%2==0) $clase='trbuc3'; else $clase='trbuc1';

							$c=$c+1;
							//$totalact=$reg->totalact;
							//$totalcan=$reg->totalcan;
							
							$totalactivos=$totalactivos+$reg->activos;
							$totalcancelados=$totalcancelados+$reg->cancelados;
							
							$total=$reg->activos+$reg->cancelados;
							//$totalgen=$reg->totalact+$reg->totalcan;				
							$totalgen=$totalactivos+$totalcancelados;				
				?>
				<tr bgcolor="<?=$fondo;?>">
				  <td align="center"><?=$reg->IDCUENTA;?></td>
				  <td><?=$reg->cuenta;?></td>
				  <? if($_POST["ckbplan"]){ ?><td><?=$reg->programa;?></td><?}?>
				  <? if($_POST["ckbcanal"]){ ?><td><?=$reg->CANALS;?></td><?}?>
				  <? if($_POST["ckbsucursal"]){ ?><td><?=$reg->SUCURSALS;?></td><?}?>
				 <td align="center"><?=number_format($reg->activos);?></td>
				  <? if($_POST["ckbcancelados"]){ ?><td align="center"><?=number_format($reg->cancelados);?></td><?}?>
				  <? if($_POST["ckbmodalidadpg"]){ ?><td align="center"><?=($reg->ARRMODALIDADPG or $reg->ARRMODALIDADPG =="SD")?$modalidad_pg[$reg->ARRMODALIDADPG]." [".number_format($reg->TOTALMODALIDADPG)."]":"";?></td><?}?>
				  <? if($_POST["ckbtotal"]){ ?><td align="center"><?=number_format($total);?></td><?}?>
				</tr>
				<?
						}
					}		 
				?>
				<tr>
				  <td colspan="<?=$celdas?>" align="right"><strong><em><?=_("TOTAL GENERAL") ;?></em></strong></td>
				  <td align="center" bgcolor="#004080"><div align="center"><span class="style3"><?=number_format($totalactivos);?></span></div></td>
				  <? if($_POST["ckbcancelados"]){ ?><td align="center" bgcolor="#004080"><div align="center"><span class="style3"><?=number_format($totalcancelados);?></span></div></td><?}?>
				  <? if($_POST["ckbmodalidadpg"]){ ?><td align="center" bgcolor="#004080"></td><?}?>
				  <? if($_POST["ckbtotal"]){ ?><td align="center" bgcolor="#004080"><div align="center"><span class="style3"><?=number_format($totalgen);?></span></div></td><?}?>
				</tr>	
			  </table>  
			</div>
		</div>
	</li><li>
		<h3>HISTORICO  AFILIADOS</h3>
		<div class="acc-section">
			<div class="acc-content">
				<form id="frmhistoricoafi" name="frmhistoricoafi" method="post" action="">
					<table border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F5F5F5" style="border:1px solid #DBDBDB;width:100%">
						<tr>
							<td width="74" align="right"><?=_("DIA DEL MES") ;?></td>
						  <td width="283"> 
							<input name="fechadia" type="text" class="classtexto"  id="fechadia" readonly onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=date("Y-m-d");?>" />
								<button   id="cal-button-3">...</button>
								<script type="text/javascript">
									Calendar.setup({
									  inputField    : "fechadia",
									  button        : "cal-button-3",
									  align         : "Tr"
									});
								</script>						  
							</td>
							<td width="104"><input type="button" name="btnconsultarhist" id="btnconsultarhist" value="<?=_("CONSULTAR") ;?>&gt;&gt;&gt;" onclick="consultarHistorico()" style="text-align:center;font-weight:bold;font-size:10px"></td>
						</tr>				
					</table>
					<div id="div-historico"><? include("vista_afiliados_historico.php");?></div>									
				</form>
			</div>
		</div>
	</li>
	
</ul>

<script type="text/javascript" src="../../../../librerias/TinyAccordion/script.js"></script>
<script type="text/javascript">
	var parentAccordion=new TINY.accordion.slider("parentAccordion");
	parentAccordion.init("acc","h3",0,0);

	var nestedAccordion=new TINY.accordion.slider("nestedAccordion");
	nestedAccordion.init("nested","h3",1,-1,"acc-selected");
</script>

<script type="text/javascript">
	//consultar afiliado historico
		function consultarHistorico(){
		
			new Ajax.Updater('div-historico', 'vista_afiliados_historico.php', {
				parameters : $('frmhistoricoafi').serialize(true),
				method: 'post',
				onCreate: function(objeto){
					document.getElementById('btnconsultarhist').value= 'Procesando...';
					document.getElementById('btnconsultarhist').disabled= true;
				},
				onSuccess: function(resp) {
					document.getElementById('btnconsultarhist').value= '>>> CONSULTAR';
					document.getElementById('btnconsultarhist').disabled= false;
					ddocument.getElementById('txtnombre').focus();					
				}
			});
		}
</script>			
			
</body>
</html>