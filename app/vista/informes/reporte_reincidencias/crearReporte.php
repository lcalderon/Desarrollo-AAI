<?
	session_start();  
	
	include_once("../../../modelo/clase_lang.inc.php");
	include_once("../../../modelo/clase_mysqli.inc.php");

    $con= new DB_mysqli("replica");
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
	//status asistencia
    foreach($_REQUEST['cmbstatusasistencia'] as $statusasistencia){
        $arrstatusasistencia[] ="'$statusasistencia'";
        $status_asistencias=implode(',',$arrstatusasistencia);
    }	
	
	//condicion servicios
    foreach($_REQUEST['cmbcondicionservicio'] as $condicionserv){
        $arrcondicionserv[] ="'$condicionserv'";
        $resp_condicionserv=implode(',',$arrcondicionserv);
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Excel</title>
	<script language="JavaScript" type="text/javascript">
		function generarExport(){
			document.frmExportar.submit();	
		}
	</script>
</head>
<body onLoad="javascript:generarExport()">

<div align="center">
	<div align="left" style="width:90%;font-weight:bold;background:#FFFF00;color:#0000FF;border:2px groove #0000FF">&nbsp;<?=_("SE ESTA GENERANDO EL REPORTE DE TRANFERENCIA, POR FAVOR NO ACTUALIZE LA PAGINA, ESPERE QUE EL PROCESO TERMINE") ;?>.</div>
</div>
	<form id="frmExportar" name="frmExportar" method="post" action="exportar_reincidencias.php">

		<input name="radio" type="hidden" value="<?=$_REQUEST['radio'];?>"  />
		<input name="cmbanio" type="hidden" value="<?=$_REQUEST['cmbanio'];?>"/>
		<input name="cmbmes" type="hidden" value="<?=$_REQUEST['cmbmes'];?>"/>
		<input name="fechaini" type="hidden" value="<?=$_REQUEST['fechaini'];?>"/>
		<input name="fechafin" type="hidden" value="<?=$_REQUEST['fechafin'];?>"/>
		<input name="cmbcuenta" type="hidden" value="<?=$_REQUEST['cmbcuenta'];?>"/>
		<input name="cmbstatusasistencia[]" type="hidden" value="<?=$status_asistencias;?>"/>
		<input name="cmbcondicionservicio[]" type="hidden" value="<?=$resp_condicionserv;?>"/>

	</form>
</body>
</html>

