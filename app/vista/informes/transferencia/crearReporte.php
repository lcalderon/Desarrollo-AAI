<?
	session_start();  
	
	include_once("../../../modelo/clase_lang.inc.php");
	include_once("../../../modelo/clase_mysqli.inc.php");

    $con= new DB_mysqli("replica");
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
 
//Inserta los datos
	$rows["IDUSUARIO"]=$_SESSION["user"];
	$respuesta=$con->insert_reg("$con->temporal.usuario_transferencia",$rows);
	
		// foreach($_REQUEST['chkopcion1'] as $indice => $chkopcion1){
		// echo $chkopcion1."**";
		// }
		
	 
		// die();
		
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
 <form id="frmExportar" name="frmExportar" method="post" action="exportar_transferencia.php">

	<?
		foreach($_REQUEST['chkopcion1'] as $indice => $chkopcion1){
	?>
		<input name="chkopcion1[]" type="hidden" id="chkopcion1" value="<?=$chkopcion1 ?>" />
	<?
		 }

		foreach($_REQUEST['chkopccosto'] as $indice => $chkopccosto){
	?>
		<input name="chkopccosto[]" type="hidden" id="chkopccosto" value="<?=$chkopccosto ?>"  />
	<?
		 }
		 
		foreach($_REQUEST['chkopmotivo'] as $indice => $chkopmotivo){
	?>
		<input name="chkopmotivo[]" type="hidden" id="chkopmotivo" value="<?=$chkopmotivo ?>"  />
	<?
		 }
		 
		foreach($_REQUEST['chkopcproveedor2'] as $indice => $chkopcproveedor2){
	?>
		<input name="chkopcproveedor2[]" type="hidden" id="chkopcproveedor2" value="<?=$chkopcproveedor2 ?>"  />
	<?
		 }
		 
		foreach($_REQUEST['chkopcproveedor'] as $indice => $chkopcproveedor){
	?>
		<input name="chkopcproveedor[]" type="hidden" id="chkopcproveedor" value="<?=$chkopcproveedor ?>"  />
	<?
		 }

		foreach($_REQUEST['chkopcion2'] as $indice => $chkopcion2){
	?>
		<input name="chkopcion2[]" type="hidden" id="chkopcion2" value="<?=$chkopcion2 ?>"  />
	<?
		 }

		foreach($_REQUEST['chkopcion3'] as $indice => $chkopcion3){
	?>
		<input name="chkopcion3[]" type="hidden" id="chkopcion3" value="<?=$chkopcion3 ?>"  />
	<?
		 }

		foreach($_REQUEST['chkopcion4'] as $indice => $chkopcion4){
	?>
		<input name="chkopcion4[]" type="hidden" id="chkopcion4" value="<?=$chkopcion4 ?>"  />
	<?
		 }

		foreach($_REQUEST['chkopcion5'] as $indice => $chkopcion5){
	?>
		<input name="chkopcion5[]" type="hidden" id="chkopcion5" value="<?=$chkopcion5 ?>"  />
	<?
		 }

		foreach($_REQUEST['chkopcion6'] as $indice => $chkopcion6){
	?>
		<input name="chkopcion6[]" type="hidden" id="chkopcion6" value="<?=$chkopcion6 ?>"  />
	<?
		 }

		foreach($_REQUEST['chkopcion7'] as $indice => $chkopcion7){
	?>
		<input name="chkopcion7[]" type="hidden" id="chkopcion7" value="<?=$chkopcion7 ?>"  />
	<?
		 }

		foreach($_REQUEST['chkopccalidad'] as $indice => $calidad){
	?>
		<input name="chkopccalidad[]" type="hidden" id="chkopcion8" value="<?=$calidad ?>"  />
	<?
		 }

		foreach($_REQUEST['cmbcuenta'] as $indice => $cuentas){
	?>
		<input name="cmbcuenta[]" type="hidden" id="chkopciones9" value="<?=$cuentas ?>"  />
	<?
		 }
 

		foreach($_REQUEST['chkopcvehicular'] as $indice => $vehiculos){
	?>
		<input name="chkopcvehicular[]" type="hidden" id="chkopciones9" value="<?=$vehiculos ?>"  />
	<?
		 }
	?>


	<input name="radio" type="hidden" id="chkopciones2" value="<?=$_REQUEST['radio'];?>"  />
	<input name="cmbanio" type="hidden" id="chkopciones3" value="<?=$_REQUEST['cmbanio'];?>"  />
	<input name="cmbmes" type="hidden" id="chkopciones4" value="<?=$_REQUEST['cmbmes'];?>"  />
	<input name="fechaini" type="hidden" id="chkopciones5" value="<?=$_REQUEST['fechaini'];?>"  />
	<input name="fechafin" type="hidden" id="chkopciones6" value="<?=$_REQUEST['fechafin'];?>"  />
	<input name="txtnombre" type="hidden" id="chkopciones7" value="<?=$_REQUEST['txtnombre'];?>"  />
	<input name="ckbtodocuenta" type="hidden" id="ckbtodocuenta" value="<?=$_REQUEST['ckbtodocuenta'];?>"  />
	<input name="cmbzonahoraria" type="hidden" id="cmbzonahoraria" value="<?=$_REQUEST['cmbzonahoraria'];?>"  />
	
</form>
</body>
</html>

