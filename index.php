<?php
	session_start();
	include_once("app/modelo/clase_lang.inc.php");
	include_once("app/modelo/clase_mysqli.inc.php");
	include_once("app/vista/login/Auth.class.php");
	//include_once("app/vista/login/ActionUser.class.php");
	
	$con = new DB_mysqli();		
	if ($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	 
	$con->select_db($con->catalogo);
	
	if(!$_SESSION["user"]){

		header("Location:/app/vista/login/");
		break;
	}

 	Auth::required(); 
	
	// list($codigosc,$nombresc,$ubic)=Auth::verifyAccess("MENU_CAT");
	// list($codigosm,$nombresm,$ubim,$target)=Auth::verifyAccess("MENU_MOD");
	// list($codigoseg,$nombreseg,$ubiseg)=Auth::verifyAccess("MENU_SEG");
	// list($codigoinf,$nombreinf,$ubiinf)=Auth::verifyAccess("MENU_INF");
	// list($codigopro,$nombrepro,$ubipro)=Auth::verifyAccess("MENU_PRO");

	$nombrepais=$con->consultation("SELECT NOMBRE FROM catalogo_pais WHERE IDPAIS='".substr($con->Prefix,0,2)."'");
	$data=$con->consultation("select concat(NOMBRES,' ',APELLIDOS) as datoname,REINICIACONTRASENIA from catalogo_usuario where IDUSUARIO='".$_SESSION["user"]."'");
	
// obtengo las opciones del menu
	//$sql= "select MODULO,PROGRAMA,RUTA,MODOVISTA from pe_pruebas_soaang_temporal.modulos_programas where ACTIVO=1";
	
	$sql= "SELECT seguridadmodulo.MODULO,seguridadmodulo.DESCRIPCION AS PROGRAMA,seguridadmodulo.UBICACION AS RUTA,seguridadmodulo.MODOVISTA ,seguridadmodulo.ANCHO ,seguridadmodulo.ALTO,seguridadmodulo.TARGET AS TITULO  FROM $con->temporal.seguridadmodulo
			INNER JOIN $con->temporal.seguridad_modulosxusuario ON seguridad_modulosxusuario.IDMODULO=seguridadmodulo.IDMODULO
			WHERE seguridad_modulosxusuario.IDUSUARIO='".$_SESSION['user']."' AND seguridadmodulo.MODULO!='' ORDER BY seguridadmodulo.MODULO,seguridadmodulo.DESCRIPCION";

	$result = $con->query($sql);
	while ($reg=$result->fetch_object()){
			$menu[$reg->MODULO][]=array($reg->PROGRAMA,$reg->RUTA,$reg->MODOVISTA,$reg->ANCHO,$reg->ALTO,$reg->TITULO);
	}
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
<title>...::: <?=_("SOAANG American Assist")?> :::...</title>
<link href="estilos/fronter_css/menu.css"	rel="stylesheet" type="text/css" />
<link href="estilos/fronter_css/jquery.windows-engine.css"	rel="stylesheet" type="text/css" />
<script src="estilos/fronter_js/jquery.js" type="text/javascript"></script>
<script src="estilos/fronter_js/jquery.validate.js" type="text/javascript"></script>
<script src="estilos/fronter_js/jquery.windows-engine.js" type="text/javascript"></script>
<script src="estilos/fronter_js/index.js" type="text/javascript"></script>
<link rel="icon" href="imagenes/iconos/logo_icon.ico" >
</head>
<body>
<div class="container">
	<div id="header">
		<span ><img src='imagenes/logos/Logo_AAI_title.png'></img></span>
		
		
	 	<span class="disclaimer_tit">
	 	<br/></br>
	 	<br/>
	 	<font color ='#FFFFFF' size="2"><?=_("American Assist Internacional")?></font> <font color ='#4b9bcc' size="2">SOAANG - <?=$nombrepais[0][0]?></font></font>
	 	</span>		
		
	 	<span class="disclaimer">
	 	<br/>
			<a href="#"  onclick="reDirigir_ventana('/app/vista/login/cambiarpass.php','window','400','160','CAMBIO_DE_CONTRASEÑA')" title="Cambio de clave"><font color ='#c4c5c6'><?=_("Cambio de Contraseña")?><strong></strong></font></a>
		</br>		
	 	<br/>

	 	<font color ='#edd267'><?=_("Bienvenido")?>: <strong><?=$data[0][0]." [".$_SESSION['user']?>]</strong></font>
	 	</span>		  
		  
		<ul class="topnav">
		<?php foreach ($menu as $index=>$modulos):?>
			<li><a href="#"><?php echo $index ?></a>
				<ul class="subnav">
				<?php foreach ($modulos as $programas):?>
					<? if ($programas[2]=='link'): ?>
					<li><a href='<?=$programas[1]?>' target='_blank'><?php echo $programas[0]?></a></li>
					<? else :?>
					<li><a id='' onclick="reDirigir_ventana('<?=$programas[1]?>','<?=$programas[2]?>','<?=$programas[3]?>','<?=$programas[4]?>','<?=$programas[5] ?>')" ><?php echo $programas[0]?></a></li>
					<? endif;?>
				<?php endforeach;?>	
				</ul>
			</li>	
		<?php endforeach;?>
		<li><a href="app/vista/login/logout.php" title="Salir del sistema"><font color='red'><?=_('Cerrar Sesi&oacute;n')?></font></a></li>
        </ul>


		
	</div>
</div>
<br/></br>
<iframe id='desktop' src="" name="iframe_a" width="100%" height="500px" scrolling="auto" frameborder="0" style="background: url(imagenes/logos/<?= $con->logo?>)no-repeat center;"></iframe>
</body>
</html>