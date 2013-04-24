<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	
	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->temporal);
	
	$usuario=$_GET["idusuario"];

	session_start(); 
 	Auth::required();
	
?>

<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
	<title>American Assist</title>
	
	<link rel="stylesheet" href="treeview/jquery.treeview.css" />
	<link rel="stylesheet" href="treeview/demo/screen.css" />
	
	<script type="text/javascript" src="../../../../librerias/jquery-ui-1.7.1/development-bundle/jquery-1.3.2.js"></script>	 
	<script src="treeview/lib/jquery.cookie.js" type="text/javascript"></script>
	<script src="treeview/jquery.treeview.js" type="text/javascript"></script>	
	<script type="text/javascript" src="treeview/demo/demo.js"></script>
	
	<link type="text/css" href="../../../../librerias/jquery-ui-1.7.1/development-bundle/demos/demos.css" rel="stylesheet" />		
	<link type="text/css" href="../../../../librerias/jquery-ui-1.7.1/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
	<script type="text/javascript" src="../../../../librerias/jquery-ui-1.7.1/development-bundle/ui/ui.core.js"></script>
	<script type="text/javascript" src="../../../../librerias/jquery-ui-1.7.1/development-bundle/ui/ui.tabs.js"></script>
	
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	
		<script LANGUAGE="JavaScript">

			
			function validarIngreso(variable)
            {
				if(document.frmAddPro.cmbperfil.value=='')
				{
					alert('<?=_("SELECCIONE ALGUNA OPCION DE LA LISTA.") ;?>');
					document.frmAddPro.cmbperfil.focus();
					return (false);
				}
				
				if(!confirm('<?=_("TODOS LOS ACCESOS ANTERIORES SE PERDERAN, REALMENTE DESEA PROSEGUIR CON LOS NUEVOS CAMBIOS?.") ;?>'))
			    {
					return (false);
			    }
				  return (true);
            }
			
	</script>
	<script type="text/javascript">
		$(function() {
			$("#tabs").tabs();
		});
	</script>	

</head>
<body>
 	<form name="frmAddPro" method="post" action="gaccesos.php" onSubmit="return validarIngreso(this)"  >
		<div class="demo">
			<div id="tabs">
				<ul>
					<li><a href="#tabs-3"><?=_("PERFIL") ;?></a></li>
				</ul>
				<div id="tabs-3">
				
				<?=_("COPIAR ACCESOS TIPO") ;?>:<br>
				<? $con->cmbselectdata("select IDPLANTILLAPERFIL,NOMBRE from $con->catalogo.catalogo_plantillaperfil where NOMBRE!='' order by NOMBRE ","cmbperfil","","onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'",""); ?>
								 <input type="submit" name="btnaceptar2" style="font-weight:bold" id="btnaceptar2" value="<?=_("GRABAR ACCESOS") ;?>" title="<?=_("GRABAR ACCESOS") ;?>" />
				<input type="hidden" name="usuario" id="usuario" value="<?=$usuario;?>" />
				</div>				
			</div>

		</div>
	</form>
</body>
</html>