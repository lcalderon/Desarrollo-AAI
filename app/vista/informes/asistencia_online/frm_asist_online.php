<?php
	session_start();  
	
	include_once('../../../modelo/clase_lang.inc.php');	
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	include_once('../../../modelo/functions.php');
	
	$con= new DB_mysqli();
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	Auth::required($_SERVER['REQUEST_URI']);

	//validar_permisos("MENU_TRANSFER",1);

	//verificar permisos de accesos a las cuentas.	
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);
	
	$var_fecha_actual= date("Y-m-d");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=_("American Assist") ;?></title>
<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
<link href="../../../../estilos/styles/blue/rpt_style_web.css" rel="stylesheet" type="text/css">
<link href="../../../../estilos/styles/blue/rpt_style_button.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" href="../../../../librerias/thickbox/thickbox.css" type="text/css" media="screen" />

<link rel="stylesheet" type="text/css" href="../../../../librerias/jscal2-1.9/src/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="../../../../librerias/jscal2-1.9/src/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="../../../../librerias/jscal2-1.9/src/css/steel/steel.css" />
<script type="text/javascript" src="../../../../librerias/jscal2-1.9/src/js/jscal2.js"></script>
<script type="text/javascript" src="../../../../librerias/jscal2-1.9/src/js/lang/es.js"></script>	

<script type="text/javascript" src="../../../../librerias/js/util.js"></script>
<script language="javascript" src="../../../../librerias/jquery/js/jquery-1.6.2.min.js"></script>
<script language="javascript" src="../../../../librerias/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="../../../../librerias/thickbox/thickbox.js"></script>

<script language="javascript">
function Nuevo_Servicio()
{
	tb_show ("Nuevo","frm_nuevo_servicio.php?placeValuesBeforeTB_=savedValues&TB_iframe=true&height=400&width=900&inlineId=hiddenModalContent&modal=true");
}
</script>
</head>
<body>
<form name="frmexporta" id="frmexporta" method="POST">
	<table class="style_table" border="0" width="100%" bordercolor="#0c298f" cellspacing="0" cellpadding="2" align="center">
			<tr>
				<td width="6%"><div align="right"><strong><?=_("Cuenta:");?></strong></div></td>
				<td width="3%">
					<?php
						if($allcuentas==1)	$Sql_cuenta="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta ORDER BY NOMBRE"; else $Sql_cuenta=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";
						$con->cmbselectdata($Sql_cuenta,"cmbcuenta","","class='txt'","","TODOS >>>");	
					?>
				</td>
				<td width="12%"><div align="right"><strong><?=_("Fecha Inicio:") ;?></strong></div></td>
				<td width="14%">
                <table class="style_table" border="0" cellpadding="0" cellspacing="0">
                  <tr><td>
                  <input class="txt" name="sel_fec_ini" type="text" id="dateArrival" readonly="yes" style="width:100px" value="<?=$var_fecha_actual?>">
                  <script language="javascript">
                    var cal_1= Calendar.setup({
                    inputField : "dateArrival",
                    trigger    : "dateArrival",
                    align      : "",
                    animation  : false,
                    onSelect   : function() 
                        { 
                        this.hide();
                        }
                    });
                  </script>
                  </td>
                  <td style="padding-left:2px">
                  <img src="../../../../imagenes/iconos/calendar.png" id="fecha_ini" border="0" style="cursor:pointer">
                  <script language="javascript">
                   var cal_2= Calendar.setup({
                    inputField : "dateArrival",
                    trigger    : "fecha_ini",
                    align      : "",
                    animation  : false,
                    onSelect   : function() 
                        { 
                        this.hide();
                        }
                    });
                  </script>
                  </td>
                  </tr>
                </table>
                </td>
                <td width="8%"><div align="right"><strong><?=_("Fecha Fin:") ;?></strong></div></td>
				<td width="57%">
                <table class="style_table" border="0" cellpadding="0" cellspacing="0">
                  <tr><td>
                  <input class="txt" name="sel_fec_fin" type="text" id="dateArrival_2" readonly="yes" style="width:100px" value="<?=$var_fecha_actual?>">
                  <script language="javascript">
                    var cal_3= Calendar.setup({
                    inputField : "dateArrival_2",
                    trigger    : "dateArrival_2",
                    align      : "",
                    animation  : false,
                    onSelect   : function() 
                        { 
                        this.hide();
                        }
                    });
                  </script>
                  </td>
                  <td style="padding-left:2px">
                  <img src="../../../../imagenes/iconos/calendar.png" id="fecha_fin" border="0" style="cursor:pointer">
                  <script language="javascript">
                   var cal_4= Calendar.setup({
                    inputField : "dateArrival_2",
                    trigger    : "fecha_fin",
                    align      : "",
                    animation  : false,
                    onSelect   : function() 
                        { 
                        this.hide();
                        }
                    });
                  </script>
                  </td>
                  </tr>
                </table>
                </td>
			</tr>
            <tr>
            <td colspan="6" align="center" style="padding-top:12px">
            <table class="style_table" border="0" cellpadding="4" cellspacing="0" align="center">
            <tr>
            <td style="padding-top:10px"><input class="button small blue" type="button" id="btn_consultar" name="btn_consultar" value="Consultar"></td>
            <td style="padding-top:10px"><input class="button small blue" type="button" id="btn_nuevo" name="btn_nuevo" value="Nuevo Servicio" onClick="Nuevo_Servicio();"></td>
            </tr>
            </table>
            </td>
            </tr>
		</table>
	</form>
</body>
</html>