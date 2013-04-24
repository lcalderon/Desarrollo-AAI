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
<div align="left" style="width:90%;font-weight:bold;background:#FFFF00;color:#0000FF;border:2px groove #0000FF">&nbsp;SE ESTA GENERANDO EL EXCEL DE ESTADISTICA DE ENCUESTA, POR FAVOR NO ACTUALIZE LA PAGINA, ESPERE QUE EL PROCESO TERMINE.</div>
</div>
 <form id="frmExportar" name="frmExportar" method="post" action="exportar_encuesta.php">

	<?
		foreach($_REQUEST['cmbstatusEncuenta'] as $indice => $chkopcion1){
	?>
		<input name="cmbstatusEncuenta[]" type="hidden" id="cmbstatusEncuenta" value="<?=$chkopcion1 ?>"  />
	<?
		 }
	?>
	 
	<input name="cmbmes" type="hidden" id="cmbmes" value="<?=$_REQUEST['cmbmes'];?>"  />
	<input name="cmbanio" type="hidden" id="cmbanio" value="<?=$_REQUEST['cmbanio'];?>"  />
	<input name="txtccl" type="hidden" id="txtccl" value="<?=$_REQUEST['txtccl'];?>"  />
	<input name="txtobjetivo" type="hidden" id="txtobjetivo" value="<?=$_REQUEST['txtobjetivo'];?>"  />
	<input name="cmbcuenta" type="hidden" id="cmbcuenta" value="<?=$_REQUEST['cmbcuenta'];?>"  />
	<input name="cmbplan" type="hidden" id="cmbplan" value="<?=$_REQUEST['cmbplan'];?>"  />

</form>
</body>
</html>

