<?

	include_once('../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);
	
	$negociados=$con->consultation("select APLICAFORANEO,APLICANOCTURNO,APLICAFESTIVO from catalogo_costo where IDCOSTO=".$_POST["idcosto"]);
	$foraneo=$negociados[0][0];
	$nocturno=$negociados[0][1];
	$festivo=$negociados[0][2];

	if($_POST["negociado"]=="true" and !$_POST["idcosto"])
	 {
?>
<table width="100" border="0" align="left" cellpadding="1" class="catalogos" cellspacing="1" bgcolor="#FFD9D9">
	<tr>
	  <td width="90" style="text-align:left"><?=_("FORANEO") ;?></td>
	  <td width="92"><input name="ckbforaneo" type="checkbox" id="ckbforaneo" value="1" checked></td>
	</tr>
	<tr>
	  <td style="text-align:left">NOCTURNO</td>
	  <td><input name="ckbnocturno" type="checkbox" id="ckbnocturno" value="1" checked></td>
	</tr>
	<tr>
	  <td style="text-align:left">FESTIVO</td>
	  <td><input name="ckbfestivo" type="checkbox" id="ckbfestivo" value="1" checked></td>
	</tr>
</table>
<?
	 }
	else if($_POST["negociado"]=="true" and $_POST["idcosto"])
	 {
?>
<table width="100" border="0" align="left" cellpadding="1" class="catalogos" cellspacing="1" bgcolor="#FFD9D9">
	<tr>
	  <td width="90" style="text-align:left"><?=_("FORANEO") ;?></td>
	  <td width="92"><input name="ckbforaneo" type="checkbox" id="ckbforaneo" value="1" <?=($negociados[0][0]==1)?"checked":"";?> ></td>
	</tr>
	<tr>
	  <td style="text-align:left">NOCTURNO</td>
	  <td><input name="ckbnocturno" type="checkbox" id="ckbnocturno" value="1" <?=($negociados[0][1]==1)?"checked":"";?>></td>
	</tr>
	<tr>
	  <td style="text-align:left">FESTIVO</td>
	  <td><input name="ckbfestivo" type="checkbox" id="ckbfestivo" value="1" <?=($negociados[0][2]==1)?"checked":"";?>></td>
	</tr>
</table>


<?
	 }
?>