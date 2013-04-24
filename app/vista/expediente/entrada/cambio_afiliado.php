<?php

	session_start();

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/functions.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");

	$con= new DB_mysqli();
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

 	Auth::required();

//Validar id expediente     
    if(crypt($_GET["idexpediente"],'666')!=$_GET["varexis"] and $_GET["idexpediente"]){
        echo "<script>";
        echo "alert('*** ID DEL EXPEDIENTE NO ESTA VALIDADO!!! ***');";
        echo "window.close();";
        echo "</script>";

        die('*** ID DEL EXPEDIENTE NO ESTA VALIDADO!!! ***');
    }
	
	$idexpediente=$_GET["idexpediente"];
	
	$Sql="SELECT
			expediente.IDAFILIADO,
			expediente.ASIGNARTITULAR,
			expediente.CVEAFILIADO,
			expediente.IDCUENTA,
			expediente.IDPROGRAMA,
			catalogo_cuenta.NOMBRE
		FROM
			$con->temporal.expediente
		INNER JOIN $con->catalogo.catalogo_cuenta ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA
		WHERE
			expediente.IDEXPEDIENTE = '$idexpediente'";
	
	$result=$con->query($Sql);
	$row = $result->fetch_object();
	
	if($row->IDAFILIADO >0 || $row->ASIGNARTITULAR >0) die("<STRONG>!!! EXPEDIENTE NO VALIDO PARA LA ACTUALIZACION DEL TITULAR.</STRONG>");
	
	$Sql_programa="SELECT IDPROGRAMA,NOMBRE FROM $con->catalogo.catalogo_programa where IDCUENTA='".$row->IDCUENTA."' ORDER BY NOMBRE ";
	
//buscar si el plan esta validado para la activacion del afiliado
	$resultadoAct=$con->consultation("SELECT VALIDA_ACTIVACION FROM $con->catalogo.catalogo_programa WHERE IDPROGRAMA='".$_POST["opc2"]."'");
	$seActiva=$resultadoAct[0][0]; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=_('Cambio Post Asignacion')?></title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>	
	<style type="text/css">
		<!--
		.style3 {color: #FFFFFF; font-weight: bold; }
		-->
	</style>
	
	
		<script language="javascript">

			function grabar_actualizacion(){
				
				$('txtcveafiliado').value=$('txtcveafiliado').value.replace(/^\s*|\s*$/g,"");
				
				if($('txtcveafiliado').value ==''){
					
					alert('<?=_("INGRESE LA CLAVE DEL AFILIADO.") ;?>');
					$('txtcveafiliado').focus();					
					return (false);
					
				} else if($('cmbprograma').value ==''){
					
					alert('<?=_("SELECCIONE ALGUN PLAN.") ;?>');
					$('cmbprograma').focus();
					return (false);
					
				} else{					
				
					if(confirm('ESTA SEGURO DE PROSEGUIR CON LA ACTUALIZACION DEL TITULAR?. SE ACTULIZARA EXPEDIENTE Y ASISTENCIA(S).')){
						
						$('btnaceptar').value='PROCESANDO...';
						$('btnaceptar').disabled=true;

						new Ajax.Request('grabar_afiliado.php',{
							method : 'post',
							postBody: 'idexpediente=<?=$idexpediente?>'+'&cveafiliado='+$F('txtcveafiliado')+'&cmbplan='+$F('cmbprograma'),
							onSuccess: function(resp){

								if(resp.responseText ==1) 	alert('!!'+'<?=_("SE GRABO CORRECTAMENTE") ;?>'); else	alert('!!'+'<?=_("NO SE COMPLETO EL PROCESO.") ;?>');
								reDirigir('expediente_frmexpediente.php?idexpediente=<?=$idexpediente?>&varexis=<?=$_GET["varexis"]?>');
							}								
								
						});				 
						
					}				
				}
				
					return (false);
			}
		
		</script>
</head>

<body> 

	<form id="frmAsignacion" name="frmAsignacion" method="post" action="">		 
		 
		<table width="50%" align="center" border="0" cellpadding="1" cellspacing="1" style="border:solid 2px #000099">
			<tr bgcolor="#003366">
				<td colspan="4" style="color:#FFFFFF;font-size:14px"><strong>POST ASIGNACION TITULAR</strong></td>
			</tr>
			<tr>
				<td width="80"><strong>IDEXPEDIENTE:</strong></td>
				<td style="font-size:14px;color:blue"><strong><?=$idexpediente?></strong></td>
				<td align="right"><strong>CVEAFILIADO:</strong>&nbsp;</td>
				<td><input name="txtcveafiliado" type="text" id="txtcveafiliado" size="25" maxlength="30" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class='classtexto' value="<?=$row->CVEAFILIADO?>"  style="text-transform:uppercase;"/></td>
			</tr>
			<tr>
				<td align="right"><strong>CUENTA:</strong></td>
				<td style="color:#637583"><strong><?=$row->NOMBRE?></strong></td>
				<td align="right"><strong>PLAN</strong>&nbsp;</td>
				<td>
					<? $con->cmbselectdata($Sql_programa,"cmbprograma",$row->IDPROGRAMA,"onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'",0); ?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4" align="center"><input type="button" name="btnaceptar" id="btnaceptar" value="ACTUALIZAR ASIGNACION" style="text-align:center;font-weight:bold;font-size:10px;height:35px;" onclick="grabar_actualizacion()" />&nbsp;<input type="button" name="btncancelar" id="btncancelar" value="CANCELAR" style="text-align:center;font-size:10px;height:35px;" onclick="reDirigir('expediente_frmexpediente.php?idexpediente=<?=$idexpediente?>&origen=PROCESODEXP&varexis=<?=$_GET["varexis"]?>')"/></td>
			</tr>
		</table>
		
	</form>		 	
</body>
</html>