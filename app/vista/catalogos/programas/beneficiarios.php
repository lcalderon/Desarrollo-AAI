<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();
	
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start(); 
	 
	if(!$_POST["idserv"])
	 {
		$rsbeneficiario=$con->query("select catalogo_tipo_beneficiario.DESCRIPCION,catalogo_programa_beneficiario.IDPROGRAMA,catalogo_programa_beneficiario.IDTIPOBENEFICIARIO,catalogo_programa_beneficiario.ACTIVO from catalogo_programa_beneficiario inner join catalogo_tipo_beneficiario on catalogo_tipo_beneficiario.IDTIPOBENEFICIARIO=catalogo_programa_beneficiario.IDTIPOBENEFICIARIO where catalogo_programa_beneficiario.IDPROGRAMA='".$_POST["idpro"]."' order by catalogo_tipo_beneficiario.DESCRIPCION ");
	 }
	else	 
	 {
		$rsbeneficiario=$con->query("select catalogo_tipo_beneficiario.DESCRIPCION,catalogo_programa_servicio_beneficiario.IDPROGRAMA,catalogo_programa_servicio_beneficiario.IDTIPOBENEFICIARIO,catalogo_programa_servicio_beneficiario.IDSERVICIO,catalogo_programa_servicio_beneficiario.ACTIVO from catalogo_programa_servicio_beneficiario inner join catalogo_tipo_beneficiario on catalogo_tipo_beneficiario.IDTIPOBENEFICIARIO=catalogo_programa_servicio_beneficiario.IDTIPOBENEFICIARIO where catalogo_programa_servicio_beneficiario.IDPROGRAMA='".$_POST["idpro"]."' and catalogo_programa_servicio_beneficiario.IDSERVICIO ='".$_POST["idserv"]."' order by catalogo_tipo_beneficiario.DESCRIPCION ");
	 }

?>
<form id="form1" name="form1" method="post" action="">
	<table width="279" border="0" cellpadding="1" cellspacing="0"  class="beneficiario">    
		<tr>
			<td colspan="2" style="background-color:#000066" ><font color="#FFFFFF"><strong><?=_("BENEFICIARIOS") ;?></strong></font></td>
			<td title="<?=_('CERRAR') ;?>" style="background-color:#000066;cursor:pointer" onclick="comportamientoDiv('-','beneficiario')" align="center" ><font size="2px" color="#FFFFFF"><b>x</b></font></td>
		</tr>
		<?
		$i=0;
		while($regben = $rsbeneficiario->fetch_object())
		 {
			
			//$activo=$regben->ACTIVO;
			
			if($regben->ACTIVO== 1)
			 {			
				 
		?>			 
		<tr>
			<td width="20"><b><input type="checkbox" name="chkbeneficiario" id="chkbeneficiario" value="<?=$regben->IDTIPOBENEFICIARIO; ?>"  checked /></b></td>
			<td width="230"><b><?=utf8_encode($regben->DESCRIPCION); ?></b></td>
			<td width="19">&nbsp;</td>
		</tr>
		<?
			 }
			else
			 {
		?>
		<tr>
			<td width="20"><b>
			<input type="checkbox" name="chkbeneficiario>" id="chkbeneficiario"   value="<?=$regben->IDTIPOBENEFICIARIO; ?>"  /></b></td>
			<td width="230"><b><?=utf8_encode($regben->DESCRIPCION); ?></b></td>
			<td width="19">&nbsp;</td>
		</tr>
		<?
			 }
				$i=$i+1;
		 }
		?>
	</table>
<?	
	if(!$_POST["idserv"])
	 {
?>
		<input type="button" name="btnaplicar" id="btnaplicar" value="Aplicar"  title="Aplicar Cambios" class="boton" onClick="activarBeneficiarios('<?=$_POST["idpro"]; ?>','','one');">
		<input type="button" name="btnaplicart" id="btnaplicart" value="Aplicar a Todos" title="Aplicar Cambios" class="boton" onClick="activarBeneficiarios('<?=$_POST["idpro"]; ?>','','all');">
<?
	 }
	else
	 {
?>
		<input type="button" name="btnaplicar" id="btnaplicar" value="Aplicar" title="Aplicar Cambios"  class="boton" onClick="activarBeneficiarios('<?=$_POST["idpro"]; ?>','<?=$_POST["idserv"]; ?>','');">
<? 
	 }
?>
</form>