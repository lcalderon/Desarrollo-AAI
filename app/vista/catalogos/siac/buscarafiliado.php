<?php
 
	session_start(); 

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once('../../../modelo/functions.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	
	$con= new DB_mysqli();	
	if($con->Errno){
	
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
 
	if($_REQUEST["buscarafiliado"])	$nombreopcional="BUSQUEDA_AFILIADO_EXP";
	validar_permisos("MENU_SAC",1,$nombreopcional); 
//verificar sesion activa.
	Auth::required($_SERVER['REQUEST_URI']);

//sesion del contenido de busqueda	
	if($_POST['busqueda']!="" and $_SESSION["user"]!="")	$_SESSION["busqueda"] =$_REQUEST['busqueda'];
	if($_POST['busqueda'] and $_SESSION["user"]!="")	$_SESSION["cuenta"] =$_REQUEST['cuenta'];
	if($_POST['busqueda'] and $_SESSION["user"]!="")	$_SESSION["cmbbusqueda"] =$_REQUEST['cmbbusqueda'];

	if($_SESSION["busqueda"] and !$_GET['busqueda'])	$_REQUEST['busqueda']=$_SESSION["busqueda"];
	if($_SESSION['busqueda'] and !$_GET['cuenta'])	$_REQUEST['cuenta']=$_SESSION["cuenta"];
	if($_SESSION['busqueda'] and !$_GET['busqueda'])	$_REQUEST['cmbbusqueda']=$_SESSION["cmbbusqueda"];
	 
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);
 
	$quitar= array(',','  ','%','\'','/','\\');
	$txtnombre=trim(str_replace($quitar, "",$_REQUEST['busqueda']));	
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<title><?=_("SAC - Busqueda");?></title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link rel="stylesheet" href="../../../../estilos/tablas/pagination.css" media="all">	
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	<link rel="stylesheet" href="../../../../librerias/tinytablev3.0/style_sac.css" />
		<script type="text/javascript" src="../../../../estilos/functionjs/func_global.js"></script>	
	<!-- se usa para del prototype -->
	<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../../estilos/suggest/ubigeo.css" rel="stylesheet" type="text/css" />	
	
	<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" ></link> 
	<link href="../../../../librerias/windows_js_1.3/themes/mac_os_x.css" rel="stylesheet" type="text/css" ></link>

	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_effects.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"></script>
	
	<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/soaa.ico">	  
	<style type="text/css">
	<!--
	.style3 {color: #FFFFFF; font-weight: bold; }
	html body {
		margin:2px;
		padding:1px;
	}
	-->
	</style>
</head>
<body onLoad="document.frmBuscarAfiliado.busqueda.focus();">
<?
//visualizar el logo de pruebas
    if($con->logoMensaje){
?>
	<div  id='en_espera'><? include("../../avisosystem.php");?> </div><br> 
<? } ?>

<? if(!$_REQUEST["buscarafiliado"]){ ?><div class="pagination"><span class="current"><?=_("Nueva Busqueda") ;?></span><a href="newafiliado.php"><?=_("Contacto No V&aacute;lido") ;?></a><a href="reportes.php"><?=_("Reporte") ;?></a><a href="estadisticas.php"><?=_("Estad&iacute;stica") ;?></a></div><? } ?>

<h2 class="Box"><?=_("BUSQUEDA DE AFILIADOS") ;?></h2>
<form id="frmBuscarAfiliado" name="frmBuscarAfiliado" method="post" action="buscarafiliado.php">
	<input type="hidden" id="buscarafiliado" name="buscarafiliado" value="<?=$_REQUEST["buscarafiliado"];?>"/>	
	<input type="hidden" id="txturl" name="txturl" value="<?=$_SERVER['REQUEST_URI']?>"/>	

	<table width="800" border="0" cellpadding="1" cellspacing="1" bgcolor="" style="border:1px solid #999999">
		<tr bgcolor="#597d98">
			<td colspan="3" bgcolor="#597d98" style="color:#FFFFFF" height="18"><strong><?=_("BUSQUEDA DE AFILIADO") ;?></strong></td>
		</tr>
		<tr>
			<td width="102" height="27"><strong><?=_("CUENTA") ;?>:</strong></td>
			<td>
			<?
				if($allcuentas==1)	$sql="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta ORDER BY NOMBRE"; else $sql=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";
				$con->cmbselectdata($sql,"cuenta",$_REQUEST["cuenta"],"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this)' ","",_("SELECCIONE"),"",_(">>> TODOS"));
			?>
			</td>
			<td width="133" rowspan="2" align="right">
				<input type="button" name="btnbuscar" id="btnbuscar" value=">>> <?=_("BUSCAR") ;?> <<<" onClick="paginar_total(1,1,2)" style="font-weight:bold;width:150px;height:50px;font-size:10px"/>
			</td>
		</tr>
		<tr>
			<td><strong><?=_("BUSQUEDA POR") ;?>:</strong></td>
			<td width="475"> 
				<select name="cmbbusqueda" id="cmbbusqueda" class="classtexto" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);">
					<option value="1" <? if($_REQUEST["cmbbusqueda"]==1)	echo "selected";?> ><?=_("IDENTIFICADOR") ;?></option>
					<option value="2" <? if($_REQUEST["cmbbusqueda"]==2)	echo "selected";?> ><?=_("NOMBRE/APELLIDO") ;?></option>
					<option value="3" <? if($_REQUEST["cmbbusqueda"]==3)	echo "selected";?> ><?=_("NUM. DOCUMENTO") ;?></option>
					<option value="4" <? if($_REQUEST["cmbbusqueda"]==4)	echo "selected";?> ><?=_("PLACA") ;?></option>
					<option value="5" <? if($_REQUEST["cmbbusqueda"]==5)	echo "selected";?> ><?=_("TELEFONO") ;?></option>        
					<option value="6" <? if($_REQUEST["cmbbusqueda"]==6)	echo "selected";?> ><?=_("# CASO") ;?></option>	
					<option value="7" <? if($_REQUEST["cmbbusqueda"]==7)	echo "selected";?> ><?=_("NOM. BENEFICIARIO") ;?></option>
				</select>
				<input name="busqueda" type="text" id="busqueda" value="<?=$txtnombre;?>" style="text-transform:uppercase;" class="classtexto" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="65" onKeyPress="return enabledEnter(event)"/>
			</td>
		</tr>
	</table>

</form> 
		<div id="contenido_vip"><?php //include('paginador_vip.php')?></div>
		<div id="contenido" ><?php include('paginador.php')?></div>
</body>
</html>

	<script type="text/javascript">
	
		function paginar_total(nropagina,opc1,opc2){
			
			if($F('cuenta') ==''){
				alert('<?=_("SELECIONE LA CUENTA") ;?>');
				$('cuenta').focus();
				return false;
			} else if($F('busqueda').trim() ==''){
				alert('<?=_("INGRESE ALGUN VALOR") ;?>');
				$('busqueda').focus();
				return false;
			} 
			
			if(opc1 ==1){
			
				new Ajax.Updater('contenido', 'paginador.php', {
					method : 'get',
					parameters: {
									pag: nropagina, 
									cuenta: $F('cuenta'), 
									cmbbusqueda: $F('cmbbusqueda') ,
									btnbuscar: $F('btnbuscar') ,
									buscarafiliado: $F('buscarafiliado') ,
									txturl: $F('txturl') ,
									busqueda: $F('busqueda')									
					},
					onCreate: function(objeto){
						document.getElementById('contenido').innerHTML= '<img src="../../../../imagenes/iconos/loader.gif">';
						document.getElementById('btnbuscar').value= '<?=_("PROCESANDO...") ;?>';
						document.getElementById('btnbuscar').disabled= true;
					},
					onSuccess: function(t){
							document.getElementById('btnbuscar').disabled= false;
							document.getElementById('btnbuscar').value= '>>> <?=_("BUSCAR") ;?> <<<';	
					}
				});			
			}	 
			//vip
			if(opc2 ==2){
			
				new Ajax.Updater('contenido_vip', 'paginador_vip.php', {
					method : 'get',
					parameters: {
									pag: nropagina, 
									cuenta: $F('cuenta'), 
									cmbbusqueda: $F('cmbbusqueda') ,
									btnbuscar: $F('btnbuscar') ,
									buscarafiliado: $F('buscarafiliado') ,
									txturl: $F('txturl') ,
									busqueda: $F('busqueda')									
					},
					onCreate: function(objeto){
						document.getElementById('contenido_vip').innerHTML= '<img src="../../../../imagenes/iconos/loader.gif">';
						document.getElementById('btnbuscar').value= '<?=_("PROCESANDO...") ;?>';
						document.getElementById('btnbuscar').disabled= true;
					},
					onSuccess: function(t){
							document.getElementById('btnbuscar').disabled= false;
							document.getElementById('btnbuscar').value= '>>> <?=_("BUSCAR") ;?> <<<';	
					}
				});			
			}
		}
	</script>