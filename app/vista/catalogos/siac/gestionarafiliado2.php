<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_lang.inc.php');	
	include_once('../../../modelo/validar_permisos.php');	
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once('../../../modelo/functions.php');
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
	
		$prefijo = $con->lee_parametro('PREFIJO_LLAMADAS_SALIENTES');

		$Sql="SELECT
				  catalogo_afiliado.CVEAFILIADO,
				  catalogo_afiliado.AFILIADO_SISTEMA,
				  catalogo_canal_venta.DESCRIPCION          AS canal,
				  catalogo_sucursal.DESCRIPCION   AS sucursal,
				  catalogo_tipodocumento.DESCRIPCION as DOCUMENTO,
				  catalogo_afiliado_persona.IDTIPODOCUMENTO,
				  catalogo_afiliado_persona.IDDOCUMENTO,
				  catalogo_afiliado_persona.GENERO,
				  LOWER(catalogo_afiliado_persona.EMAIL1) AS EMAIL1,
				  LOWER(catalogo_afiliado_persona.EMAIL2) AS EMAIL2,
				  LOWER(catalogo_afiliado_persona.EMAIL3) AS EMAIL3,
				  catalogo_cuenta.IDCUENTA,
				  catalogo_afiliado.IDAFILIADO,
				  catalogo_afiliado.FECHAINICIOVIGENCIA,
				  catalogo_afiliado.FECHAFINVIGENCIA,
				  catalogo_cuenta.NOMBRE                    AS nombrecuenta,
				  catalogo_afiliado.STATUSASISTENCIA,
				  catalogo_status_comercial.DESCRIPCION AS STATUSCOMERCIAL,
				  catalogo_afiliado.ARRMODALIDADPG,
				  catalogo_programa.NOMBRE                  AS nombreprograma,
				  catalogo_programa.IDCODIGOSPONSOR,
				  catalogo_programa.IDPROGRAMA,
				  catalogo_afiliado_persona.APPATERNO,
				  catalogo_afiliado_persona.APMATERNO,
				  catalogo_afiliado_persona.NOMBRE,
				  catalogo_afiliado_persona_ubigeo.IDAFILIADO as ubigeo,
				  catalogo_afiliado_persona_ubigeo.CODPOSTAL,
				  catalogo_afiliado_persona_ubigeo.CVEPAIS,
				  catalogo_afiliado_persona_ubigeo.DIRECCION			  
				FROM catalogo_afiliado_persona
				  INNER JOIN catalogo_afiliado
					ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona.IDAFILIADO
				  LEFT JOIN catalogo_afiliado_persona_telefono
					ON catalogo_afiliado_persona_telefono.IDAFILIADO = catalogo_afiliado_persona.IDAFILIADO
				  INNER JOIN catalogo_programa
					ON catalogo_programa.IDPROGRAMA = catalogo_afiliado.IDPROGRAMA
				  INNER JOIN catalogo_cuenta
					ON catalogo_cuenta.IDCUENTA = catalogo_programa.IDCUENTA
				  LEFT JOIN catalogo_afiliado_canalventa
					ON catalogo_afiliado_canalventa.IDAFILIADO = catalogo_afiliado.IDAFILIADO	
				  LEFT JOIN catalogo_tipodocumento
					ON catalogo_tipodocumento.IDTIPODOCUMENTO = catalogo_afiliado_persona.IDTIPODOCUMENTO
				  LEFT JOIN catalogo_canal_venta
					ON catalogo_canal_venta.IDCANALVENTA = catalogo_afiliado.IDCANALVENTA
 				  LEFT JOIN catalogo_sucursal
					ON catalogo_sucursal.IDSUCURSAL = catalogo_afiliado.IDSUCURSAL
				  LEFT JOIN $con->catalogo.catalogo_status_comercial
					ON catalogo_status_comercial.STATUSCOMERCIAL = catalogo_afiliado.STATUSCOMERCIAL
						AND catalogo_status_comercial.IDCUENTA = catalogo_afiliado.IDCUENTA			
				  LEFT JOIN catalogo_afiliado_persona_ubigeo
					ON catalogo_afiliado_persona_ubigeo.IDAFILIADO = catalogo_afiliado_persona.IDAFILIADO										
				WHERE catalogo_afiliado.IDAFILIADO ='".$_REQUEST["idafiliado"]."'  GROUP BY catalogo_afiliado.IDAFILIADO";
			
		$result=$con->query($Sql);
		$reg = $result->fetch_object();	
		//echo $Sql;
		$Sql_telefono="SELECT
					  catalogo_afiliado_persona_telefono.NUMEROTELEFONO,
					  catalogo_afiliado_persona_telefono.CODIGOAREA
					FROM catalogo_afiliado_persona_telefono
					  INNER JOIN catalogo_afiliado
						ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_telefono.IDAFILIADO
					WHERE catalogo_afiliado_persona_telefono.IDAFILIADO ='".$reg->IDAFILIADO."' ORDER BY catalogo_afiliado_persona_telefono.PRIORIDAD
					LIMIT 4";
	 
		$resultel=$con->query($Sql_telefono);
		while($row = $resultel->fetch_object())
		 {
			$i=$i+1;
			$telefono[$i]=$row->NUMEROTELEFONO;
			$codigoa[$i]=$row->CODIGOAREA;
		 }

	
	if($reg->ubigeo!="" or $reg->ubigeo!=0)
	 {
		$ubigeo = new ubigeo();
		$ubigeo->leer("IDAFILIADO",$con->catalogo,"catalogo_afiliado_persona_ubigeo",$reg->ubigeo);
	 }		 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<title><?=_("SAC - Gestion");?></title>
	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	<link href="../../../../estilos/tablas/pagination.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>		
	
	<style type="text/css">
	<!--
	.style5 {color: #000000}
	.style6 {color: #333333}
	.style7 {color: #FFFFFF}
	.style8 {color: #666666}
	-->
	</style>

	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
	<style type="text/css">@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");</style>
	<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/soaa.ico">
	
	<script type="text/javascript">

		function llamada(codigoarea,numero){
			numero = codigoarea+numero;

			new Ajax.Request('../../../controlador/ajax/ajax_llamada.php',
			{	method : 'get',
				parameters: {
					prefijo: "",
					num: numero,
					ext: '<?=$_SESSION["extension"];?>'
					} 
			}
			);
		}

	</script>

	<script type="text/javascript">
		
		function validarIngreso(variable){		
	  	
           if(document.form1.txtacomentario.value==""){
                  alert("INGRESE LA OBSERVACION.");
                  document.form1.txtacomentario.focus();
                  return (false);
           }
		   
			return (true);    
		}   		   
	</script>
	<script type="text/javascript" src="../../../../librerias/treeview/jquery.js"></script>
	<script type="text/javascript" src="../../../../librerias/treeview/animatedcollapse.js"></script>
	<script type="text/javascript">

		animatedcollapse.addDiv('reclamo', 'fade=1,speed=400,group=pets1')
		animatedcollapse.addDiv('desafiliacion', 'fade=1,speed=400,group=pets2')
		animatedcollapse.addDiv('reintegro', 'fade=1,speed=400,group=pets3')
		animatedcollapse.addDiv('desafiliacionmov', 'fade=1,speed=400,group=pets4')
		animatedcollapse.addDiv('expedientehist', 'fade=1,speed=400,group=pets5')
		animatedcollapse.addDiv('expedienteprocc', 'fade=1,speed=400,group=pets6')
		animatedcollapse.addDiv('expedtelemercadeo', 'fade=1,speed=400,group=pets7')
		animatedcollapse.addDiv('beneficiarios', 'fade=1,speed=400,group=pets8')
		animatedcollapse.addDiv('vehiculos', 'fade=1,speed=400,group=pets9')

		animatedcollapse.ontoggle=function($, divobj, state){

		}

		animatedcollapse.init()

	</script>	
		
		<script type="text/javascript">
			
			window.onload = go;
			function go(){			
				Typecast.Init();			
			}
									
			function vercalendario(){
			
				Calendar.setup({
					inputField     :    "txtfechaejecuta",      // id of the input field
					ifFormat       :    "%Y-%m-%d",       // format of the input field
					showsTime      :    false,            // will display a time selector
					button         :    "cal-button-1",   // trigger for the calendar (button ID)
					singleClick    :    true,           // double-click mode
					step           :    1                // show all years in drop-down boxes (instead of every other year as default)
				});
			}
						
		</script>	
		
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
	
</head>
<body>
<?
//visualizar el logo de pruebas

    if($con->logoMensaje){
?>
	<div  id='en_espera'><? include("../../avisosystem.php");?> </div><br> 
<? 
	}  

  if($_REQUEST["btngestiona"]){ ?>
	<p align="right"><input type="button" name="button" id="button" value="VOLVER" style="width:80px; font-weight:bold" onclick="reDirigir('buscarafiliado.php?buscarafiliado=1&cmbbusqueda=<?=$_POST["cmbbusqueda"]?>')" /></p>
 <?
	}

	if(!$_REQUEST["btngestiona"]){ 
 ?>  
<div class="pagination"><a href="buscarafiliado.php"><?=_("Nueva Busqueda") ;?></a><a href="newafiliado.php"><?=_("Contacto No V&aacute;lido") ;?></a><a href="reportes.php"><?=_("Reporte") ;?></a><a href="estadisticas.php"><?=_("Estad&iacute;stica") ;?></a></div>
<? }?>

<form id="frm_afiliado" name="form1" method="post" action="gprocesos.php" onSubmit = "return validarIngreso(this)">
	 <input type="hidden" name="txtcodprograma" value="<?=$reg->IDPROGRAMA;?>" />
	 <input type="hidden" name="txtcodcuenta" value="<?=$reg->IDCUENTA;?>" />
	 <input type="hidden" name="idafiliado" value="<?=$reg->IDAFILIADO;?>" />
	 <input type="hidden" name="status" value="<?=$reg->STATUSASISTENCIA;?>" />

<h2 class="Box"><?=_("GESTION DEL AFILIADO") ;?></h2>  
	<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#F8F8F8" style="border:1px solid #EEEEEE">   
	<tr>
        <td width="30"><?=_("CODIGO ID") ;?></td>
		<td colspan="3">
          <input name="txtidentificador" type="text" class="classtexto" id="txtidentificador" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="28" value="<?=$reg->CVEAFILIADO;?>" readonly  />
        </td>
		<td><?=_("MODALID. PG") ;?></td>
		<td colspan="3">
		<?	$con->cmb_array("cmbmodalidad",$modalidad_pg,$reg->ARRMODALIDADPG," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'",""); ?>
	  </td>
    </tr>	
      <tr>
        <td width="18"><?=_("STATUS AFILIADO") ;?></td>
        <td colspan="3"><input  style="text-transform:uppercase;font-weight:bold;background-color:<? if($reg->STATUSASISTENCIA=="CAN")	echo "#FF3535"; else echo "#CCFFCC"; ?>" name="txtstatus" type="text" class="classtexto" id="txtstatus" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);"  value="<?=$desc_status_afi_asistencia[$reg->STATUSASISTENCIA];?>" size="14"  readonly="readonly"  /></td>
        <td width="30"><?=_("STATUS COMERCIAL") ;?></td>
		<td colspan="3">
          <input name="txtidentificador" type="text" class="classtexto" id="txtidentificador" style="text-transform:uppercase;font-weight:bold;background-color:#FFFF80" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="28" value="<?=($reg->STATUSCOMERCIAL)?$reg->STATUSCOMERCIAL:"S/D";?>" readonly  />
        </td>		
      </tr>
		<tr>
		  <td><?=_("INI. VIGENCIA") ;?></td>
		  <td colspan="3"><input name="txtfechaini" type="text" class="classtexto" id="txtfechaini" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="14" value="<?=$reg->FECHAINICIOVIGENCIA;?>" readonly  /></td>
		  <td><?=_("FIN VIGENCIA") ;?></td>
		  <td colspan="3"><input name="txtfechafin" type="text" class="classtexto" id="txtfechafin" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="14" value="<?=$reg->FECHAFINVIGENCIA;?>" readonly  /></td>
		</tr>
		<tr>
		  <td><?=_("CUENTA") ;?></td>
		  <td colspan="3"><input name="txtcuenta" type="text" class="classtexto" id="txtcuenta" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);"  value="<?=$reg->nombrecuenta;?>" size="30" readonly="readonly" /></td>
		  <td><span class="style5">
		    <?=_("PROGRAMA") ;?>
		  </span></td>
		  <td colspan="3"><input name="txtplan" type="text" class="classtexto" id="txtplan" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" value="<?=$reg->nombreprograma;?>" size="42" readonly="readonly" /><img src='../../../../imagenes/iconos/pdf.gif' alt='<?=_('VER DETALLE')?>' width='15' style='cursor: pointer;' title='<?=_('VER CONTRATO');?>' onclick="ver_detalle('<?=$reg->IDPROGRAMA;?>')"></img></td>
	  </tr>
		<tr>
            <td><?=_("CANAL VENTA") ;?></td>
        <td colspan="3"><input name="txtcanal" type="text" class="classtexto" id="txtcanal" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);"  value="<?=$reg->canal;?>" size="35" readonly="readonly" /></td>
        <td><?=_("SUCURSAL") ;?></td>
        <td colspan="3"><input name="txtcanalcuenta" type="text" class="classtexto" id="txtcanalcuenta" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);"  value="<?=$reg->sucursal;?>" size="35" readonly="readonly" /></td>
      </tr>	     
      <tr>
        <td><span class="style6">
        <?=_("NOMBRES") ;?>
        </span></td>
        <td width="246"><input name="txtnombres" type="text" class="classtexto" id="txtnombres" <?=($reg->AFILIADO_SISTEMA =="SINVALIDAR")?"":"readonly"?> style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);"   value="<?=$reg->NOMBRE;?>" size="35" maxlength="37"/></td>
        <td width="19"><?=_("APE.PATERNO") ;?></td>
        <td><input name="txtpaterno" type="text" class="classtexto" id="txtpaterno" <?=($reg->AFILIADO_SISTEMA =="SINVALIDAR")?"":"readonly"?> style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);"  value="<?=$reg->APPATERNO;?>" size="17"/></td>
        <td><?=_("APE.MATERNO") ;?></td>
        <td colspan="3"><input name="txtmaterno" type="text" class="classtexto" id="txtmaterno" <?=($reg->AFILIADO_SISTEMA =="SINVALIDAR")?"":"readonly"?> style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);"   value="<?=$reg->APMATERNO;?>" size="20" maxlength="17"/></td>				
      </tr>  
	  <tr>
        <td><?=_("#DOCUMENTO") ;?></td>
        <td><input name="txtndocumento" type="text" <?=($reg->AFILIADO_SISTEMA =="SINVALIDAR")?"":"readonly"?> class="classtexto" id="txtndocumento" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);"  value="<?=$reg->IDDOCUMENTO;?>" size="15" maxlength="17"/></td>
        <td><?=_("TIPO DOC") ;?></td>
        <td>
			<?	$con->cmbselectdata("SELECT IDTIPODOCUMENTO,DESCRIPCION FROM $con->catalogo.catalogo_tipodocumento ORDER BY DESCRIPCION","cmbtipodoc",$reg->IDTIPODOCUMENTO,"onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'",""); ?>		
		</td>				
        <td><?=_("GENERO") ;?></td>
        <td colspan="3">
			<?	$con->cmb_array("cmbgenero",$desc_genero,$reg->GENERO,"  class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'",""); ?>
		</td>
      </tr>
      <tr>
        <td width="23"><span class="style5"><?=_("TELEFONO1") ;?></span></td>
        <td><input name="txttelefono[]" type="text" class="classtexto" id="txttelefono1" <?=($reg->AFILIADO_SISTEMA =="SINVALIDAR")?"":"readonly"?> style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);"  value="<?=$telefono[1];?>" size="15" maxlength="17" /><? if($telefono[1]!=""){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$codigoa[1];?>','<?=$telefono[1];?>')" title="Llamar" /><? } ?></td>
        <td><span class="style5"><?=_("TELEFONO2") ;?></span></td>
        <td width="217"><input name="txttelefono[]" type="text" class="classtexto" id="txttelefono2" <?=($reg->AFILIADO_SISTEMA =="SINVALIDAR")?"":"readonly"?> style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);"   value="<?=$telefono[2];?>" size="15" maxlength="17"/><? if($telefono[2]!=""){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$codigoa[2];?>','<?=$telefono[2];?>')" title="Llamar" /><? } ?></td>
        <td><span class="style5"><?=_("TELEFONO3") ;?></span></td>
        <td width="180"><input name="txttelefono[]" type="text" class="classtexto" id="txttelefono3" <?=($reg->AFILIADO_SISTEMA =="SINVALIDAR")?"":"readonly"?> style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);"   value="<?=$telefono[3];?>" size="15" maxlength="17"/><? if($telefono[3]!=""){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$codigoa[3];?>','<?=$telefono[3];?>')" title="Llamar" /><? } ?></td>
        <td width="18"><span class="style5"><?=_("TELEFONO4") ;?></span></td>
        <td width="210"><input name="txttelefono[]" type="text" class="classtexto" id="txttelefono4" <?=($reg->AFILIADO_SISTEMA =="SINVALIDAR")?"":"readonly"?> style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);"   value="<?=$telefono[4];?>" size="15" maxlength="17"/><? if($telefono[4]!=""){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$codigoa[4];?>','<?=$telefono[4];?>')" title="Llamar" /><? } ?></td>
      </tr>   
      <tr>
        <td><?=_("EMAIL1") ;?></td>
        <td><input name="txtemail" type="text" class="classtexto" id="txtemail" <?=($reg->AFILIADO_SISTEMA =="SINVALIDAR")?"":"readonly"?> onfocus="coloronFocus(this);" onblur="colorOffFocus(this);"  value="<?=$reg->EMAIL1;?>" size="35" maxlength="70"/></td>
        <td><?=_("EMAIL2") ;?></td>
        <td colspan="3"><input name="txtemail2" type="text" class="classtexto" id="txtemail2" <?=($reg->AFILIADO_SISTEMA =="SINVALIDAR")?"":"readonly"?> onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);"  value="<?=$reg->EMAIL2;?>" size="35" maxlength="70"/></td>
        <td><?=_("EMAIL3") ;?></td>
        <td colspan="3"><input name="txtemail3" type="text" class="classtexto" id="txtemail3" <?=($reg->AFILIADO_SISTEMA =="SINVALIDAR")?"":"readonly"?> onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);"  value="<?=$reg->EMAIL3;?>" size="35" maxlength="70"/></td>
      </tr>

		<? include("../../../vista/includes/vista_entidades2.php");?>
	  <tr>
        <td><?=_("DIRECCION") ;?></td>
        <td colspan="3"><input name="txtdireccion" type="text" class="classtexto" id="txtdireccion" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);"  size="55" maxlength="45" value="<?=$reg->DIRECCION;?>" /></td>
        <td><?=_("AFILIADO") ;?></td>
        <td><input name="txtcodigop" type="text" class="classtexto" id="txtcodigop" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);"  value="<?=$reg->AFILIADO_SISTEMA;?>" size="15" maxlength="15"  readonly="readonly"  /></td>
        <td><input type="button" name="btngrabarAfiliado" id="btngrabarAfiliado" value="ACTUALIZAR AFILIADO" style="width:125px;font-size:10px;background-color:#EEB984" <?=($reg->AFILIADO_SISTEMA =="SINVALIDAR")?"":"disabled" ?> /></td>
      </tr>
  </table>
  
  <p></p>
<?
	$acciongen="''";
	$accionrei="''";
	$accionrea="''";
	$accionbaj="''";
	$accioncam="''";
	
	if($reg->STATUSASISTENCIA=="CAN"){
		$acciongen="showFormulario('GENERAL','','');document.form1.btnquejasre.disabled=false;;document.form1.btngenera.disabled=true;document.form1.btnreitegro.disabled=false;document.form1.btnreactivar.disabled=false;";
		$accionrei="showFormulario('REINTEGRO','','');document.form1.btnquejasre.disabled=false;document.form1.btnreitegro.disabled=true;document.form1.btngenera.disabled=false;document.form1.btnreactivar.disabled=false;";
		$accionrea="showFormulario('REACTIVACION','','');document.form1.btnquejasre.disabled=false;document.form1.btnreactivar.disabled=true;document.form1.btngenera.disabled=false;document.form1.btnreitegro.disabled=false;";
		$accionquejasr="showFormulario('QUEJASRECLAMO','$reg->IDCUENTA','$reg->IDPROGRAMA');document.form1.btnquejasre.disabled=true;document.form1.btnreitegro.disabled=false";		
		$accionbaj="''";
		$accioncam="''";
	 
	  }
	 else //if($reg->STATUSASISTENCIA=="CAN")
	  {
		$acciongen="showFormulario('GENERAL','','');document.form1.btnquejasre.disabled=false;document.form1.btngenera.disabled=true;document.form1.btndesafiliacion.disabled=false;document.form1.btnreitegro.disabled=false;document.form1.btnbaja.disabled=false;document.form1.btncambio.disabled=false;";
		$accionrei="showFormulario('REINTEGRO','','');document.form1.btnquejasre.disabled=false;document.form1.btnreitegro.disabled=true;document.form1.btndesafiliacion.disabled=false;document.form1.btngenera.disabled=false;document.form1.btnbaja.disabled=false;document.form1.btncambio.disabled=false;";
		$accionrea="''";
		$accionbaj="showFormulario('BAJAS','','');document.form1.btnquejasre.disabled=false;document.form1.btnbaja.disabled=true;document.form1.btndesafiliacion.disabled=false;document.form1.btngenera.disabled=false;document.form1.btnreitegro.disabled=false;document.form1.btncambio.disabled=false;";
		$accioncam="showFormulario('CAMBIOP','$reg->IDCUENTA','$reg->IDPROGRAMA');document.form1.btnquejasre.disabled=false;document.form1.btndesafiliacion.disabled=false;document.form1.btncambio.disabled=true;document.form1.btngenera.disabled=false;document.form1.btnreitegro.disabled=false;document.form1.btnbaja.disabled=false;";
		$accionquejasr="showFormulario('QUEJASRECLAMO','$reg->IDCUENTA','$reg->IDPROGRAMA');document.form1.btnquejasre.disabled=true;document.form1.btndesafiliacion.disabled=false;document.form1.btncambio.disabled=false;document.form1.btngenera.disabled=false;document.form1.btnreitegro.disabled=false;document.form1.btnbaja.disabled=false;";
		$accionDesafiliacion="showFormulario('DESAFILIACION','$reg->IDCUENTA','$reg->IDPROGRAMA');document.form1.btndesafiliacion.disabled=true;document.form1.btnquejasre.disabled=false;document.form1.btncambio.disabled=false;document.form1.btngenera.disabled=false;document.form1.btnreitegro.disabled=false;document.form1.btnbaja.disabled=false;";
	  
	  }
?>
 
<table width="100%" border="0" cellpadding="1" cellspacing="1"  style="border:1px dashed #CCCCCC">
  <tr>
    <td width="146">
    <input type="button" name="btngenera" id="btngenera" value="<?=_('GENERALIDADES')?>" style="width:113px;font-size:10px;background-color:#A3BCE3" onclick=<?=$acciongen;?>  <? if($_REQUEST["buscarafiliado"])	echo "disabled"; ?>/></td>
    <td width="151"><input type="button" name="btnreitegro" id="btnreitegro" <? //if($reg->STATUSASISTENCIA=="CAN")	echo "disabled"; ?> value="<?=_('REINTEGRO')?>" style="width:113px;font-size:10px;background-color:#A3BCE3" onclick=<?=$accionrei;?> /></td>
    <td width="148"><input type="button" name="btnreactivar" id="btnreactivar" <? if($reg->STATUSASISTENCIA!="CAN")	echo "disabled"; ?> value="<?=_('REACTIVACION')?>" style="width:113px;font-size:10px;background-color:#A3BCE3" onclick=<?=$accionrea;?> /></td>
    <td width="144"><input type="button" name="btnbaja" id="btnbaja" <? if($reg->STATUSASISTENCIA=="CAN")	echo "disabled"; ?> value="<?=_('BAJA DEL AFILIADO')?>" style="width:113px;font-size:10px;background-color:#A3BCE3;" onclick=<?=$accionbaj;?>  /></td>
    <td width="145"><input type="button" name="btncambio" id="btncambio" <? if($reg->STATUSASISTENCIA =="CAN")	echo "disabled"; ?> value="<?=_('CAMBIO DE PLAN')?>" style="width:113px;font-size:10px;background-color:#A3BCE3" onclick=<?=$accioncam;?> /></td>
    <td width="113"><input type="button" name="btnquejasre" id="btnquejasre" <?// if($reg->STATUSASISTENCIA =="ACT")	echo "disabled"; ?> value="<?=_('RECLAMOS')?>" style="width:113px;font-size:10px;background-color:#A3BCE3" onclick=<?=$accionquejasr;?> /></td>
    <td width="113"><input type="button" name="btndesafiliacion" id="btndesafiliacion" <?// if($reg->STATUSASISTENCIA =="ACT")	echo "disabled"; ?> value="<?=_('DESAFILIACION')?>" style="width:113px;font-size:10px;background-color:#A3BCE3" onclick=<?=$accionDesafiliacion;?> /></td>
  </tr>
  <tr>
	<td><input type="button" name="btnfpago" value="<?=_('MEDIO DE PAGO')?>" style="width:113px;font-size:10px;background-color:#666666" <? if($reg->STATUSASISTENCIA =="CAN")	echo "disabled"; ?> onClick="window.open ('formapago.php?idafiliado=<?=$reg->IDAFILIADO;?>','mediop','height=260, width=750,left=100,top=100,resizable=no,scrollbars=yes,toolbar=no,status=yes');" /></td>
    <td><input type="button" name="btnbeneficiario" value="<?=_('BENEFICIARIO')?>" style="width:113px;font-size:10px;background-color:#666666" <? if($reg->STATUSASISTENCIA =="CAN")	echo "disabled"; ?>   onclick="window.open ('beneficiario.php?idafiliado=<?=$reg->IDAFILIADO;?>','beneficiario','height=360, width=1000,left=100,top=100,resizable=no,scrollbars=yes,toolbar=no,status=yes');" /></td>
    <td><input type="button" name="btnvehicular" value="<?=_('VEHICULOS')?>" style="width:113px;font-size:10px;background-color:#666666" <? if($reg->STATUSASISTENCIA =="CAN")	echo "disabled"; ?>   onclick="window.open ('vehicular_dafiliado.php?idafiliado=<?=$reg->IDAFILIADO;?>','vehicular','height=280, width=900,left=100,top=100,resizable=no,scrollbars=yes,toolbar=no,status=yes');" /></td>
    <td><input name="btndomicilio" type="button" id="btndomicilio" style="width:113px;font-size:10px;background-color:#666666"   onclick="window.open ('domicilio_dafiliado.php?idafiliado=<?=$reg->IDAFILIADO;?>','domicilio','height=430, width=900,left=100,top=100,resizable=no,scrollbars=yes,toolbar=no,status=yes');" value="<?=_('DOMICILIO')?>" <? if($reg->STATUSASISTENCIA=="CAN")	echo "disabled"; ?> /></td>

   <?php
   
   $idafiliado = $_REQUEST['idafiliado'];
   //  obtengo el id del proceso y la etiqueta del boton  
   $idproceso='';
   $etiqueta_boton='';
  
  $sql="select IDPROCESO,ETIQUETA_BOTON from $con->catalogo.catalogo_programa_proceso where IDPROGRAMA='$reg->IDPROGRAMA' AND IDCUENTA='$reg->IDCUENTA'";
   $resultpro = $con->query($sql);
   while($proceso = $resultpro->fetch_object()) {
   	$idproceso= $proceso->IDPROCESO;
   	$etiqueta_boton = $proceso->ETIQUETA_BOTON;
   }
   ?>
   
   <? if ($idproceso!=''):?>    
    <td><input type="button" name="btndesafiliaciones" value="<?=$etiqueta_boton?>" style="width:130px;font-size:10px;background-color:#666666" <? if($reg->STATUSASISTENCIA =="CAN")	echo "disabled"; ?>   onclick="window.open ('webService/form_desafiliaciones.php?IDPROGRAMA=<?=$reg->IDPROGRAMA?>&IDCUENTA=<?=$reg->IDCUENTA?>&IDTIPODOCUMENTO=<?=$reg->IDTIPODOCUMENTO?>&IDDOCUMENTO=<?=$reg->IDDOCUMENTO?>&IDAFILIADO=<?=$idafiliado?>&IDPROCESO=<?=$idproceso?>','desafiliaciones','height=320, width=600,left=100,top=100,resizable=no,scrollbars=yes,toolbar=no,status=yes,charset=iso-8859-1');" /></td>
   <?endif;?> 
    
  </tr>
</table>
 

	<div id="tipogestion" style="display:none"></div> 
	
<br/>
<? 


	if($_REQUEST["idafiliado"])
	 {
	
//filtrando los reclamos

		$Sql_reclamo="SELECT
			  retencion.STATUS_SEGUIMIENTO,
			  retencion.IDAFILIADO,
			  retencion.IDRETENCION,
			  LEFT(retencion.FECHARETENCION,10) AS fecharet,
			  RIGHT(retencion.FECHARETENCION,8) AS horaret,
			  retencion.IDCUENTA,
			  retencion.IDPROGRAMA,
			  retencion.MOTIVOLLAMADA,
			  catalogo_detallemotivollamada.DESCRIPCION,
			  retencion.DEFENSACONSUMIDOR,
			  retencion.ARRPROCEDENCIA,
			  retencion.MESREINTEGRO,
			  DAYOFWEEK(retencion.FECHARETENCION)  as diasemana,
			 (SELECT
				 COUNT(DISTINCT retencion_seguimiento.IDGRUPO)
			   FROM $con->temporal.retencion_seguimiento
			   WHERE retencion_seguimiento.IDRETENCION = retencion.IDRETENCION) AS cantidad_area,			  
			  retencion.IDUSUARIO
			FROM $con->temporal.retencion
			  LEFT JOIN $con->catalogo.catalogo_detallemotivollamada
				ON catalogo_detallemotivollamada.IDDETMOTIVOLLAMADA = retencion.IDDETMOTIVOLLAMADA
			WHERE retencion.IDAFILIADO='".$reg->IDAFILIADO."' AND retencion.MOTIVOLLAMADA = 'QUEJASRECLAMO'
			ORDER BY retencion.IDRETENCION DESC ";					
 
		$rsquejas=$con->query($Sql_reclamo);
		$numreclamo=$rsquejas->num_rows*1;
?>

<h2><a href="#" rel="toggle[reclamo]" data-openimage="../../../../imagenes/iconos/esconder.gif" data-closedimage="../../../../imagenes/iconos/mostrar.gif"><img src="../../../../imagenes/iconos/mostrar.gif"   border="0"></a><font size='2px'><?=_("HISTORICO DE RECLAMOS") ;?> [<?=$numreclamo;?>]</font></h2>
<div id="reclamo" id="reclamo" speed="400" groupname="pets11" >
<?			
	if($numreclamo >0)
	 {
?>

<table width="95%" border="0" cellpadding="1" cellspacing="1">
  <tr bgcolor="#000000">
    <td width="8%"><div align="center" class="style5 style7"><strong><?=_('Caso')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Fecha')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Hora')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Cuenta')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Plan')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Gestion')?></strong></div></td>
    <td width="26%"><div align="center" class="style5 style7"><strong><?=_('Motivo')?></strong></div></td>
    <td width="7%"><div align="center" class="style5 style7"><strong><?=_('Procedencia')?></strong></div></td>
    <td width="7%"><div align="center" class="style5 style7"><strong><?=_('Def.Consumidor')?></strong></div></td>
    <td width="7%"><div align="center" class="style5 style7"><strong><?=_('Seguimiento')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Usuario')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Detalle')?></strong></div></td>
    </tr>
	<?
	while($fila = $rsquejas->fetch_object())
	 {
		if($fila->STATUS_SEGUIMIENTO!="CON") $color= calculo_tiemporespuesta($fila->cantidad_area,$fila->fecharet,$fila->diasemana);		 
	 
	?>
  <tr bgcolor="#B1C3D9" >
    <td><div align="center">
      <?=$fila->IDRETENCION;?>
    </div></td>
    <td><div align="center"><?=$fila->fecharet;?></div></td>
    <td><div align="center"><?=$fila->horaret;?></div></td>
    <td><div align="center">
      <?=$fila->IDCUENTA;?>
    </div></td>
    <td><div align="center"><?=$fila->IDPROGRAMA;?></div></td>
    <td><div align="center">
      <?=$fila->MOTIVOLLAMADA;?>
    </div></td>
    <td><?=utf8_encode($fila->DESCRIPCION);?></td>
    <td><div align="center"><?=$procedencia_mediogestion[$fila->ARRPROCEDENCIA];?></div></td>
    <td><div align="center"><?=($fila->DEFENSACONSUMIDOR)?"SI":"NO";?></div></td>
    <td bgcolor="<?=$color;?>" <?// if($nombre=="0"){	echo "bgcolor='#FFAC59'"; } else if($nombre==1){ echo "bgcolor='#FF4F4F'"; } else{  echo "bgcolor=''"; } ?> ><div align="center"><strong><?=$statusproceso_sac[$fila->STATUS_SEGUIMIENTO];?></strong></div></td>
    <td><?=$fila->IDUSUARIO;?></td>
    <td><div align="center">
	<input type="button" name="button" id="button" value="<?=_('DETALLE')?>" style="font-size:9px;" onclick="window.open('detalle.php?idcaso=<?=$fila->IDRETENCION;?>','reclamo','resizable=no,location=1,status=1,scrollbars=1,width=810,height=440');" />
    </div></td>
    </tr>
	<?
			$ii=$ii+1;
			$nombre	="";
			$color	="";
	}
	?>
</table>
<?
	}else{
	echo _('SIN REGISTROS.');
 
	}
	
// filtrando desafiliaciones
	
	$Sql_desafiliacion="SELECT
		  retencion.STATUS_SEGUIMIENTO,
		  retencion.IDRETENCION,
		  LEFT(retencion.FECHARETENCION,10) AS fecharet,
		  RIGHT(retencion.FECHARETENCION,8) AS horaret,
		  retencion.IDCUENTA,
		  retencion.IDPROGRAMA,
		  retencion.MOTIVOLLAMADA,
		  catalogo_motivoscancelacionAON.DESCRIPCION,
		  retencion.MESREINTEGRO,
		  retencion.IDUSUARIO
		FROM $con->temporal.retencion
		  LEFT JOIN $con->catalogo.catalogo_motivoscancelacionAON
			ON catalogo_motivoscancelacionAON.IDMOTIVOCANCELACION = retencion.IDDETMOTIVOLLAMADA
		WHERE retencion.IDAFILIADO='".$reg->IDAFILIADO."' AND retencion.MOTIVOLLAMADA = 'DESAFILIACION'
		ORDER BY retencion.IDRETENCION DESC ";
 
	$rs_desafiliacion=$con->query($Sql_desafiliacion);
	$numdesafiliacion=$rs_desafiliacion->num_rows*1;
		
?>
</div>

<br>
<h2><a href="#" rel="toggle[desafiliacion]" data-openimage="../../../../imagenes/iconos/esconder.gif" data-closedimage="../../../../imagenes/iconos/mostrar.gif"><img src="../../../../imagenes/iconos/mostrar.gif"   border="0"></a><font size='2px'><?=_("HISTORICO DE DESAFILIACIONES") ;?> [<?=$numdesafiliacion;?>]</font></h2>
<div id="desafiliacion" id="desafiliacion" speed="400" groupname="pets22" style="display:none">
<?			
	if($numdesafiliacion >0)
	 {
?>

<table width="95%" border="0" cellpadding="1" cellspacing="1">
  <tr bgcolor="#000000">
    <td width="8%"><div align="center" class="style5 style7"><strong><?=_('Caso')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Fecha')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Hora')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Cuenta')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Plan')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Gestion')?></strong></div></td>
    <td width="26%"><div align="center" class="style5 style7"><strong><?=_('Motivo')?></strong></div></td>
    <td width="7%"><div align="center" class="style5 style7"><strong><?=_('Seguimiento')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Usuario')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Detalle')?></strong></div></td>
    </tr>
	<?
	while($fila = $rs_desafiliacion->fetch_object())
	 {		
	?>
  <tr bgcolor="#ebcd50" >
    <td><div align="center">
      <?=$fila->IDRETENCION;?>
    </div></td>
    <td><div align="center"><?=$fila->fecharet;?></div></td>
    <td><div align="center"><?=$fila->horaret;?></div></td>
    <td><div align="center"><?=$fila->IDCUENTA;?></div></td>
    <td><div align="center"><?=$fila->IDPROGRAMA;?></div></td>
    <td><div align="center">
      <?=$fila->MOTIVOLLAMADA;?>
    </div></td>
    <td><?=utf8_encode($fila->DESCRIPCION);?></td>
	<td <? if($nombre=="0"){	echo "bgcolor='#FFAC59'"; } else if($nombre==1){ echo "bgcolor='#FF4F4F'"; } else{  echo "bgcolor=''"; } ?>  ><div align="center"><strong><?=$statusproceso_sac[$fila->STATUS_SEGUIMIENTO];?></strong></div></td>	
    <td><?=$fila->IDUSUARIO;?></td>
    <td><div align="center">
	<input type="button" name="button" id="button" value="<?=_('DETALLE')?>" style="font-size:9px;" onclick="window.open('detalle.php?idcaso=<?=$fila->IDRETENCION;?>','desafiliacion','resizable=no,location=1,status=1,scrollbars=1,width=810,height=440');" />
    </div></td>
    </tr>
	<?
			$ii=$ii+1;
			$nombre	="";
	}
	?>
</table>
<?
	} else{
	
	echo _('SIN REGISTROS.');
 
	}
	
// filtrando los reintegros
	
	$Sql="SELECT
		  retencion.STATUS_SEGUIMIENTO,
		  retencion.IDRETENCION,
		  LEFT(retencion.FECHARETENCION,10) AS fecharet,
		  RIGHT(retencion.FECHARETENCION,8) AS horaret,
		  retencion.IDCUENTA,
		  retencion.IDPROGRAMA,
		  retencion.MOTIVOLLAMADA,
		  catalogo_detallemotivollamada.DESCRIPCION,
		  retencion.MESREINTEGRO,
		  retencion.IDUSUARIO
		FROM $con->temporal.retencion
		  LEFT JOIN $con->catalogo.catalogo_detallemotivollamada
			ON catalogo_detallemotivollamada.IDDETMOTIVOLLAMADA = retencion.IDDETMOTIVOLLAMADA
		WHERE retencion.IDAFILIADO='".$reg->IDAFILIADO."' AND retencion.MOTIVOLLAMADA = 'REINTEGRO'
		ORDER BY retencion.IDRETENCION DESC ";

	$rs_reintegro=$con->query($Sql);
	$numreintegros=$rs_reintegro->num_rows*1;
		
?>
</div>

<br>
<h2><a href="#" rel="toggle[reintegro]" data-openimage="../../../../imagenes/iconos/esconder.gif" data-closedimage="../../../../imagenes/iconos/mostrar.gif"><img src="../../../../imagenes/iconos/mostrar.gif"   border="0"></a><font size='2px'><?=_("HISTORICO DE REINTEGROS") ;?> [<?=$numreintegros;?>]</font></h2>
<div id="reintegro" id="reintegro" speed="400" groupname="pets22" style="display:none">
<?			
	if($numreintegros >0)
	 {
?>

<table width="95%" border="0" cellpadding="1" cellspacing="1">
  <tr bgcolor="#000000">
    <td width="8%"><div align="center" class="style5 style7"><strong><?=_('Caso')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Fecha')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Hora')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Cuenta')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Plan')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Gestion')?></strong></div></td>
    <td width="26%"><div align="center" class="style5 style7"><strong><?=_('Motivo')?></strong></div></td>
    <td width="7%"><div align="center" class="style5 style7"><strong><?=_('Reitegro')?></strong></div></td>
    <td width="7%"><div align="center" class="style5 style7"><strong><?=_('Seguimiento')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Usuario')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Detalle')?></strong></div></td>
    </tr>
	<?
	while($fila = $rs_reintegro->fetch_object())
	 {		
	?>
  <tr bgcolor="#FFBE9F" >
    <td><div align="center">
      <?=$fila->IDRETENCION;?>
    </div></td>
    <td><div align="center"><?=$fila->fecharet;?></div></td>
    <td><div align="center"><?=$fila->horaret;?></div></td>
    <td><div align="center">
      <?=$fila->IDCUENTA;?>
    </div></td>
    <td><div align="center"><?=$fila->IDPROGRAMA;?></div></td>
    <td><div align="center">
      <?=$fila->MOTIVOLLAMADA;?>
    </div></td>
    <td><?=utf8_encode($fila->DESCRIPCION);?></td>
    <td><div align="center"><?=$fila->MESREINTEGRO;?></div></td>
	<td <? if($nombre=="0"){	echo "bgcolor='#FFAC59'"; } else if($nombre==1){ echo "bgcolor='#FF4F4F'"; } else{  echo "bgcolor=''"; } ?>  ><div align="center"><strong><?=$statusproceso_sac[$fila->STATUS_SEGUIMIENTO];?></strong></div></td>	
    <td><?=$fila->IDUSUARIO;?></td>
    <td><div align="center">
	<input type="button" name="button" id="button" value="<?=_('DETALLE')?>" style="font-size:9px;" onclick="window.open('detalle.php?idcaso=<?=$fila->IDRETENCION;?>','reitegros','resizable=no,location=1,status=1,scrollbars=1,width=810,height=440');" />
    </div></td>
    </tr>
	<?
			$ii=$ii+1;
			$nombre	="";
	}
	?>
</table>

<?
	}else{
?>
<?=_('SIN REGISTROS.')?>
<?
	}
	
//filtrar historial movimientos
	
	$Sql="SELECT
		  retencion.STATUS_SEGUIMIENTO,
		  retencion.IDRETENCION,
		  LEFT(retencion.FECHARETENCION,10) AS fecharet,
		  RIGHT(retencion.FECHARETENCION,8) AS horaret,
		  retencion.IDCUENTA,
		  retencion.IDPROGRAMA,
		  retencion.MOTIVOLLAMADA,
		  catalogo_detallemotivollamada.DESCRIPCION,
		  retencion.MESREINTEGRO,
		  retencion.IDUSUARIO
		FROM $con->temporal.retencion
		  LEFT JOIN $con->catalogo.catalogo_detallemotivollamada
			ON catalogo_detallemotivollamada.IDDETMOTIVOLLAMADA = retencion.IDDETMOTIVOLLAMADA
		WHERE retencion.IDAFILIADO='".$reg->IDAFILIADO."' AND retencion.MOTIVOLLAMADA NOT IN('QUEJASRECLAMO','REINTEGRO','DESAFILIACION')
		ORDER BY retencion.IDRETENCION DESC ";
	
	$rsretencion=$con->query($Sql);
	$numdesafiliar=$rsretencion->num_rows*1;
		
?>
</div>
<br>

<h2><a href="#" rel="toggle[desafiliacionmov]" data-openimage="../../../../imagenes/iconos/esconder.gif" data-closedimage="../../../../imagenes/iconos/mostrar.gif"><img src="../../../../imagenes/iconos/mostrar.gif"  border="0"></a><font size='2px'><?=_("HISTORICO DE MOVIMIENTOS") ;?> [<?=$numdesafiliar;?>]</font></h2>
<div id="desafiliacionmov" id="desafiliacionmov" speed="400" groupname="pets33"  style="display:none">
<?			
	if($numdesafiliar >0)
	 {
?>

<table width="95%" border="0" cellpadding="1" cellspacing="1">
  <tr bgcolor="#000000">
    <td width="8%"><div align="center" class="style5 style7"><strong><?=_('Caso')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Fecha')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Hora')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Cuenta')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Plan')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Gestion')?></strong></div></td>
    <td width="26%"><div align="center" class="style5 style7"><strong><?=_('Motivo')?></strong></div></td>
    <td width="7%"><div align="center" class="style5 style7"><strong><?=_('Reitegro')?></strong></div></td>
    <td width="7%"><div align="center" class="style5 style7"><strong><?=_('Seguimiento')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Usuario')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Detalle')?></strong></div></td>
    </tr>
	<?
	while($fila = $rsretencion->fetch_object())
	 {		
		$nombre= calculo_tiemporespuesta($fila->IDRETENCION);
	?>
  <tr bgcolor="#E9E6D1" >
    <td><div align="center">
      <?=$fila->IDRETENCION;?>
    </div></td>
    <td><div align="center"><?=$fila->fecharet;?></div></td>
    <td><div align="center"><?=$fila->horaret;?></div></td>
    <td><div align="center">
      <?=$fila->IDCUENTA;?>
    </div></td>
    <td><div align="center"><?=$fila->IDPROGRAMA;?></div></td>
    <td><div align="center">
      <?=$fila->MOTIVOLLAMADA;?>
    </div></td>
    <td><?=utf8_encode($fila->DESCRIPCION);?></td>
    <td><div align="center"><?=$fila->MESREINTEGRO;?></div></td>
	<td <? if($nombre=="0"){	echo "bgcolor='#FFAC59'"; } else if($nombre==1){ echo "bgcolor='#FF4F4F'"; } else{  echo "bgcolor=''"; } ?>  ><div align="center"><strong><?=$statusproceso_sac[$fila->STATUS_SEGUIMIENTO];?></strong></div></td>	
    <td><?=$fila->IDUSUARIO;?></td>
    <td><div align="center">
	<input type="button" name="button" id="button" value="<?=_('DETALLE')?>" style="font-size:9px;" onclick="window.open('detalle.php?idcaso=<?=$fila->IDRETENCION;?>','mywindow2','location=yes,status=yes,scrollbars=yes,resizable=no,width=810,height=440');" />
    </div></td>
    </tr>
	<?
			$ii=$ii+1;
			$nombre	="";			
	}
	?>
</table>
<?
	}else{
?>
<?=_('SIN REGISTROS.')?>
<?
	}

//filtrar  beneficiarios

	$Sql_beneficiario="SELECT
					  catalogo_afiliado_beneficiario.IDBENEFICIARIO,
					  catalogo_afiliado_beneficiario.GENERO,
					  catalogo_afiliado_beneficiario.ARRPARENTESCO,
					  catalogo_afiliado_beneficiario.IDUSUARIOMOD,
					  catalogo_afiliado.IDAFILIADO,
					  catalogo_afiliado_beneficiario.IDBENEFICIARIO,
					  catalogo_afiliado_beneficiario.IDDOCUMENTO,
					  CONCAT(catalogo_afiliado_beneficiario.APPATERNO,' ',catalogo_afiliado_beneficiario.APMATERNO,', ',catalogo_afiliado_beneficiario.NOMBRE) AS nombres,
					  catalogo_afiliado_beneficiario.FECHAMOD
					FROM catalogo_afiliado_beneficiario
					  INNER JOIN catalogo_afiliado
						ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_beneficiario.IDAFILIADO
					  LEFT JOIN catalogo_afiliado_beneficiario_telefono
						ON catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO = catalogo_afiliado_beneficiario.IDBENEFICIARIO
					  LEFT JOIN catalogo_afiliado_beneficiario_ubigeo
						ON catalogo_afiliado_beneficiario_ubigeo.IDBENEFICIARIO = catalogo_afiliado_beneficiario.IDBENEFICIARIO    
					WHERE catalogo_afiliado_beneficiario.IDAFILIADO = ".$reg->IDAFILIADO."
					GROUP BY catalogo_afiliado_beneficiario.IDBENEFICIARIO
					ORDER BY catalogo_afiliado_beneficiario.FECHAMOD DESC ";

		$rs_beneficiario=$con->query($Sql_beneficiario);
		$numbeneficiario=$rs_beneficiario->num_rows*1;		
?>
</div>
<br>
<h2><a href="#" rel="toggle[beneficiarios]" data-openimage="../../../../imagenes/iconos/esconder.gif" data-closedimage="../../../../imagenes/iconos/mostrar.gif"><img src="../../../../imagenes/iconos/mostrar.gif"  border="0"></a><font size='2px'><?=_("INFORMACION DE BENEFICIARIOS") ;?> [<?=$numbeneficiario;?>]</font></h2>
<div id="beneficiarios" id="beneficiarios" speed="400" groupname="pets33"  style="display:none">
<?	
	if($numbeneficiario >0)
	 {
?>

<table width="95%" border="0" cellpadding="1" cellspacing="1">
  <tr bgcolor="#000000">
    <td width="8%"><div align="center" class="style5 style7"><strong><?=_('ID')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Beneficiario')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Telefono1')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Telefono2')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Telefono3')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Telefono4')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('N&ordm;Documento')?></strong></div></td>
    <td width="7%"><div align="center" class="style5 style7"><strong><?=_('Parentesco')?></strong></div></td>
    <td width="7%"><div align="center" class="style5 style7"><strong><?=_('Genero')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Usuario')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Detalle')?></strong></div></td>
    </tr>
	<?
		while($fila = $rs_beneficiario->fetch_object())
		 {
	?>
  <tr bgcolor="#F2F2F2" >
    <td><div align="center">
      <?=$fila->IDBENEFICIARIO;?>
    </div></td>
    <td width="20%"><?=$fila->nombres;?></div></td>
	<?
 		$cont=0;
		$Sql_tel="SELECT
				  catalogo_afiliado_beneficiario_telefono.NUMEROTELEFONO
				FROM catalogo_afiliado_beneficiario_telefono
				  INNER JOIN catalogo_afiliado_beneficiario
					ON catalogo_afiliado_beneficiario.IDBENEFICIARIO = catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO
				WHERE catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO = '".$fila->IDBENEFICIARIO."'
				ORDER BY catalogo_afiliado_beneficiario_telefono.PRIORIDAD
				LIMIT 4";
		
		$resultel=$con->query($Sql_tel);											
		while($row = $resultel->fetch_object())
		 {
			$cont=$cont+1;
			$telefonoben[$cont]=$row->NUMEROTELEFONO;
		 }
		 
		for ($c=1;$c<=4;$c++)				
		 {
	?>
    <td><div align="center"><?=$telefonoben[$c];?></div></td>
	<?		
			$telefonoben[$c]="";
		 }
	?>	
    <td><?=$fila->IDDOCUMENTO;?></td>
    <td><div align="center"><?=$desc_parentesco[$fila->ARRPARENTESCO];?></div></td>
	<td><div align="center"><?=$desc_genero[$fila->GENERO];?></div></td>	
    <td><?=$fila->USUARIOMOD;?></td>
    <td><div align="center">
	<input type="button" name="button" id="button" value="<?=_('DETALLE')?>" style="font-size:9px;" onclick="window.open('beneficiario.php?idafiliado=<?=$reg->IDAFILIADO;?>&verinfo=<?=$fila->IDBENEFICIARIO;?>','perbeneficiarios','location=yes,status=yes,scrollbars=yes,resizable=no,height=360,width=1000');" />
    </div></td>
    </tr>
	<?
			$ii=$ii+1;
		}
	?>
</table>
<?
	}else{
?>
<?=_('SIN REGISTROS.')?>
<?
	}

//filtrar vehiculos
	
	$sql_veh="SELECT
			  catalogo_afiliado.IDAFILIADO,
			  catalogo_afiliado_persona_vehiculo.IDUSUARIOMOD,
			  catalogo_afiliado_persona_vehiculo.ANIO,
			  catalogo_afiliado_persona_vehiculo.ARRCOMBUSTIBLE,
			  catalogo_afiliado_persona_vehiculo.ARRTRANSMISION,
			  catalogo_afiliado_persona_vehiculo.COLOR,
			  catalogo_afiliado_persona_vehiculo.FECHAMOD,
			  catalogo_afiliado_persona_vehiculo.ID,
			  catalogo_afiliado_persona_vehiculo.MARCA,
			  catalogo_afiliado_persona_vehiculo.NUMSERIECHASIS,
			  catalogo_afiliado_persona_vehiculo.NUMSERIEMOTOR,
			  catalogo_afiliado_persona_vehiculo.NUMVIN,
			  catalogo_afiliado_persona_vehiculo.PLACA,
			  catalogo_afiliado_persona_vehiculo.SUBMARCA,
			  catalogo_afiliado_persona_vehiculo.USO
			FROM catalogo_afiliado_persona_vehiculo
			  INNER JOIN catalogo_afiliado
				ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_vehiculo.IDAFILIADO
			WHERE catalogo_afiliado.IDAFILIADO = ".$reg->IDAFILIADO." 
			ORDER BY catalogo_afiliado_persona_vehiculo.ID DESC";

	$rs_vehiculo=$con->query($sql_veh);
	$numvehiculos=$rs_vehiculo->num_rows*1;
	
?>
</div>

<br>

<h2><a href="#" rel="toggle[vehiculos]" data-openimage="../../../../imagenes/iconos/esconder.gif" data-closedimage="../../../../imagenes/iconos/mostrar.gif"><img src="../../../../imagenes/iconos/mostrar.gif"  border="0"></a><font size='2px'><?=_("INFORMACION VEHICULOS") ;?> [<?=$numvehiculos;?>]</font></h2>
<div id="vehiculos" id="vehiculos" speed="400" groupname="pets33"  style="display:none">
<?	
	if($numvehiculos >0)
	 {
?>

<table width="95%" border="0" cellpadding="1" cellspacing="1">
  <tr bgcolor="#000000">
    <td width="8%"><div align="center" class="style5 style7"><strong><?=_('ID')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Marca')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('SubMarca')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Placa')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Vin')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Transmision')?></strong></div></td>
    <td width="10%"><div align="center" class="style5 style7"><strong><?=_('Combustible')?></strong></div></td>
    <td width="7%"><div align="center" class="style5 style7"><strong><?=_('Uso')?></strong></div></td>
    <td width="7%"><div align="center" class="style5 style7"><strong><?=_('# Motor')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Usuario')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Detalle')?></strong></div></td>
    </tr>
	<?
		while($fila = $rs_vehiculo->fetch_object())
		 {
	?>
  <tr bgcolor="#E1EFFB" >
    <td><div align="center"><?=$fila->ID;?></div></td>
    <td width="20%"><?=$fila->MARCA;?></div></td>
    <td><?=$fila->SUBMARCA;?></td>
    <td><?=$fila->PLACA;?></td>
    <td><?=$fila->NUMVIN;?></td>
    <td><div align="center"><?=$desc_transmision[$fila->ARRTRANSMISION];?></div></td>
	<td><div align="center"><?=$desc_combustible[$fila->ARRCOMBUSTIBLE];?></div></td>	
	<td><div align="center"><?=$desc_uso[$fila->USO];?></div></td>	
    <td><?=$fila->NUMSERIEMOTOR;?></td>
    <td><?=$fila->IDUSUARIOMOD;?></td>
    <td><div align="center">
	<input type="button" name="button" id="button" value="<?=_('DETALLE')?>" style="font-size:9px;" onclick="window.open('vehicular_dafiliado.php?idafiliado=<?=$reg->IDAFILIADO;?>&verinfo=<?=$fila->ID;?>','vervehicular','location=yes,status=yes,scrollbars=yes,resizable=no,height=280, width=900');" />
    </div></td>
    </tr>
	<?
			$ii=$ii+1;		 
		}
	?>
</table>
<?
	}else{
?>
<?=_('SIN REGISTROS.')?>
<?
	}
	
//filtrar expediente historico	
	$Sql_historico="SELECT
			  expediente.IDEXPEDIENTE,
			  expediente.IDUSUARIOMOD,
			  catalogo_cuenta.NOMBRE,
			  LEFT(expediente.FECHAREGISTRO,10) AS fecha,
			  RIGHT(expediente.FECHAREGISTRO,8) AS hora,
			  expediente.IDCUENTA
			FROM $con->temporal.expediente
			  INNER JOIN catalogo_cuenta
				ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA
			WHERE expediente.IDAFILIADO = ".$reg->IDAFILIADO."
			ORDER BY expediente.FECHAREGISTRO";
 
	$rshistorico=$con->query($Sql_historico);
	$numexphisto=$rshistorico->num_rows*1;
	
?>
</div>
<br>
<h2><a href="#" rel="toggle[expedientehist]" data-openimage="../../../../imagenes/iconos/esconder.gif" data-closedimage="../../../../imagenes/iconos/mostrar.gif"><img src="../../../../imagenes/iconos/mostrar.gif"   border="0"></a><font size='2px'><?=_("EXPEDIENTE HISTORICOS") ;?> [<?=$numexphisto;?>]</font></h2>
<div id="expedientehist" id="expedientehist" speed="400" groupname="pets44" style="display:none">
<?			
	if($numexphisto >0)
	 {
?>
<table width="95%" border="0" cellpadding="1" cellspacing="1">
  <tr bgcolor="#000000">
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Expediente')?></strong></div></td>
    <td width="13%"><div align="center" class="style5 style7"><strong><?=_('Fecha Apertura')?></strong></div></td>
    <td width="12%"><div align="center" class="style5 style7"><strong><?=_('Hora Apertura')?></strong></div></td>
    <td width="24%"><div align="center" class="style5 style7"><strong><?=_('Contacto')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Cuenta')?></strong></div></td>
    <td width="27%"><div align="center" class="style5 style7"><strong><?=_('Asistencia')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Usuario')?></strong></div></td>
	<td width="6%"><div align="center" class="style5 style7"><strong><?=_('Detalle')?></strong></div></td>
    </tr>
	<?			
		while($fila=$rshistorico->fetch_object())
		 {			
	?>
  <tr bgcolor="#F4F4F4" >
    <td><div align="center">
      <?=$fila->IDEXPEDIENTE;?>
    </div></td>
    <td><div align="center">
      <?=$fila->fecha;?>
    </div></td>
    <td><div align="center">
      <?=$fila->hora;?>
    </div></td>
    <td>
      <?=$fila->NOMCONTACTO;?>    </td>
    <td><div align="center">
      <?=$fila->IDCUENTA;?>
    </div></td>
    <td><table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000" >
		<?			
			$Sql_asistencia="SELECT
							  asistencia.IDASISTENCIA,
							  catalogo_servicio.DESCRIPCION
							FROM $con->temporal.asistencia
							  INNER JOIN catalogo_servicio
								ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
							WHERE asistencia.IDEXPEDIENTE = ".$fila->IDEXPEDIENTE."
							ORDER BY catalogo_servicio.DESCRIPCION";
			
			$rsasistencia=$con->query($Sql_asistencia);
			while($rowa=$rsasistencia->fetch_object())
			 {		 
		?>
		<tr>
			<td width="10%" bgcolor="#B9DCFF" style="font-size:9px"><div align="center" class="style8">
			  <?=$rowa->IDASISTENCIA;?>
		    </div></td>
			<td width="69%" bgcolor="#B9DCFF" style="font-size:9px"><span class="style8">
			  <?=$rowa->DESCRIPCION;?>
			</span></td>
        </tr>
		<?		 
			 }
		?>
    </table></td>
    <td><div align="center">
      <?=$fila->IDUSUARIOMOD;?>
    </div></td>
	<td><div align="center">
	<input type="button" name="button" id="button" value="<?=_('DETALLE')?>" style="font-size:9px;" onclick="window.open('expedientes_detalle.php?idexpediente=<?=$fila->IDEXPEDIENTE;?>','dethistorico','location=no,status=no,scrollbars=1,resizable=no,height=550,width=950');" />
    </div></td>
    </tr>
	<?		 
		}
	?>
</table>
<?
	}else{
?>
<?=_('SIN REGISTROS.')?>
<?
	}
	
	$rsproceso=$con->query("SELECT catalogo_cuentas.NOMBRE,expediente.CVEEXPED,left(expediente.FECHAREGISTRO,10) as fecha,right(expediente.FECHAREGISTRO,8) as hora ,expediente.IDCUENTA,expediente.CVEUSUARIO from $con->temporal.expediente inner join catalogo_cuentas on catalogo_cuentas.IDCUENTA= expediente.IDCUENTA where expediente.CVEAFILIADO=".$reg->IDAFILIADO." and expediente.CVESTATEXP=0 order by expediente.FECHAREGISTRO");
	$numexpedproce=$rsproceso->num_rows*1;
		
?>
</div>
<br>
<h2><a href="#" rel="toggle[expedienteprocc]" data-openimage="../../../../imagenes/iconos/esconder.gif" data-closedimage="../../../../imagenes/iconos/mostrar.gif"><img src="../../../../imagenes/iconos/mostrar.gif"   border="0"></a><font size='2px'><?=_("EXPEDIENTE EN PROCESO") ;?> [<?=$numexpedproce;?>]</font></h2>
<div id="expedienteprocc" id="expedienteprocc" speed="400" groupname="pets55"  style="display:none">
<?			
	if($numexpedproce >0)
	 {
?>
<table width="100%" border="0" cellpadding="1" cellspacing="1">
  <tr bgcolor="#000000">
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Expediente')?></strong></div></td>
    <td width="13%"><div align="center" class="style5 style7"><strong><?=_('Fecha Apertura')?></strong></div></td>
    <td width="12%"><div align="center" class="style5 style7"><strong><?=_('Hora Apertura')?></strong></div></td>
    <td width="24%"><div align="center" class="style5 style7"><strong><?=_('Contacto')?></strong></div></td>
    <td width="6%"><div align="center" class="style5 style7"><strong><?=_('Cuenta')?></strong></div></td>
    <td width="27%"><div align="center" class="style5 style7"><strong><?=_('Asistencia')?></strong></div></td>
    <td width="9%"><div align="center" class="style5 style7"><strong><?=_('Usuario AA')?></strong></div></td>
    </tr>
	<?			
		while($fila = $rsproceso->fetch_object())
		 {			
	?>
  <tr bgcolor="#F4F4F4" >
    <td bgcolor="#C6FFC6"><div align="center">
      <?=$fila->CVEEXPED;?>
    </div></td>
    <td bgcolor="#C6FFC6"><div align="center">
      <?=$fila->fecha;?>
    </div></td>
    <td bgcolor="#C6FFC6"><div align="center">
      <?=$fila->hora;?>
    </div></td>
    <td bgcolor="#C6FFC6">
      <?=$fila->NOMCONTACTO;?>    </td>
    <td bgcolor="#C6FFC6"><div align="center">
      <?=$fila->IDCUENTA;?>
    </div></td>
    <td bgcolor="#C6FFC6"><table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000" >
<?			
	$con->select_db("arhistoria");
	$rsasistencia=$con->query("SELECT asistencias.NUMASISTENCIA,catalogo_servicios.DESCRIPCION from $con->historia.asistencias inner join catalogo_servicios on catalogo_servicios.CVESERVICIO=asistencias.CVESERVICIO where asistencias.CVEEXPED=".$fila->CVEEXPED." order by catalogo_servicios.DESCRIPCION");

	while($rowa=$rsasistencia->fetch_object())
	 {	
?>
		<tr>
			<td width="10%" bgcolor="#B9DCFF" style="font-size:9px"><div align="center" class="style8">
			  <?=$rowa->NUMASISTENCIA;?>
		    </div></td>
			<td width="69%" bgcolor="#B9DCFF" style="font-size:9px"><span class="style8">
			  <?=$rowa->DESCRIPCION;?>
			</span></td>
        </tr>
<?		 
	 }
?>
    </table></td>
    <td bgcolor="#C6FFC6"><div align="center">
      <?=$fila->CVEUSUARIO;?>
    </div></td>
    </tr>
		<?		 
			}
		?>
</table>
<?
	}else{
?>
<?=_('SIN REGISTROS.')?>
<?
	}
	
	// $con->select_db("asterisk");
	// $rsasterisk=$con->query("SELECT list_id,campaign_id,lead_id,user,phone_number,call_date,status,comments from  asterisk.vicidial_log  where lead_id=".$reg->IDPERSONA);

	// $numtelemercadeo=$rsasterisk->num_rows*1;	
?>
</div>
 
<?
		
	} 
?>


</form>

</body>
</html>
<script type="text/javascript">
function ver_detalle(idprograma){

	var validar_func = '';
	var win = null;
	
		if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
		else
		{
			win = new Window({
				className: "alphacube",
				title: '<?=_("DETALLE DEL CONTRATO")?>',
				width: 800,
				height: 500,
				showEffect: Element.show,
				hideEffect: Element.hide,
				destroyOnClose: true,
				minimizable: false,
				maximizable: false,
				resizable: true,
				opacity: 0.95,
				url: "../../plantillas/contrato.php?idprograma="+idprograma
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


	new Event.observe('btngrabarAfiliado','click',function()
	{

		if(confirm('<?=_("DESEA PROSEGUIR CON LA ACTUALIZACION DEL AFILIADO?")?>.')){

			new Ajax.Request('grabarAfiliado.php',{
				method: 'post',
				parameters:  $('frm_afiliado').serialize(true),
					onSuccess: function(t) {
						//alert(t.responseText);
					
					}
			});	
		}	
	});

</script>
