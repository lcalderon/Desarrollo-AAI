<?php
	include_once('../../../modelo/clase_mysqli.inc.php');
	
	session_start();
	
	$con = new DB_mysqli();
	
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);
	
	$sqlmon="select CVEMEDIDA,DESCRIPCION from catalogo_medida where DESCRIPCION!='' order by DESCRIPCION ";
	 
	 if($_POST["opc"]=="MODELO1")
	  {
?>
<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {
	color: #FFFFFF;
	font-weight: bold;
}
-->
</style>
<table width="375" border="0" cellpadding="1" cellspacing="1" class="catalogos_modelo">
  <tr>
    <td width="176" style="background-color:#FFFFFF">&nbsp;</td>
    <td width="85" bgcolor="#333333"><div align="center" class="style1"><?=_("CANTIDAD") ;?></div></td>
    <td width="104" bgcolor="#333333"><div align="center" class="style1"><?=_("UNIDAD") ;?></div></td>
  </tr>
  <tr>
    <td bgcolor="#D3DEEB"><?=_("SERVICIO NORMAL"); ?></td>
    <td bgcolor="#D3DEEB"><div align="center">
      <input name="txtcantidad[]" type="text" id="txtcantidad[]" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-align:center" onkeypress="return validarnum(event)" >
    </div></td>
    <td bgcolor="#D3DEEB"><div align="center">
	<?
		$con->cmbselectopc($sqlmon,"cmbunidad[]",$medida[$indice]," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");								
	?>	
    </div></td>
  </tr>
  <tr>
    <td bgcolor="#D3DEEB"><?=_("RECARGO NOCTURNO"); ?></td>
    <td bgcolor="#D3DEEB"><div align="center">
      <input name="txtcantidad[]" type="text" id="txtcantidad[]" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-align:center" onkeypress="return validarnum(event)" >
    </div></td>
    <td bgcolor="#D3DEEB"><div align="center">
	<?
		$con->cmbselectopc($sqlmon,"cmbunidad[]",$medida[$indice]," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");								
	?>	
    </div></td>
  </tr>
  <tr>
    <td bgcolor="#D3DEEB"><?=_("RECARGO FERIADO"); ?></td>
    <td bgcolor="#D3DEEB"><div align="center">
      <input name="txtcantidad[]" type="text" id="txtcantidad[]" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-align:center" onkeypress="return validarnum(event)" >
    </div></td>
    <td bgcolor="#D3DEEB"><div align="center">	
	<?
		$con->cmbselectopc($sqlmon,"cmbunidad[]",$medida[$indice]," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");								
	?>	</div></td>
  </tr>
  <tr>
    <td bgcolor="#D3DEEB"><?=_("RECARGO DERIVACION") ;?></td>
    <td bgcolor="#D3DEEB"><div align="center">
      <input name="txtcantidad[]" type="text" id="txtcantidad[]" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-align:center" onkeypress="return validarnum(event)" >
    </div></td>
    <td bgcolor="#D3DEEB"><div align="center">
	<?
		$con->cmbselectopc($sqlmon,"cmbunidad[]",$medida[$indice]," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");								
	?>	
    </div></td>
  </tr>
</table>
<? 
}
else if($_POST["opc"]=="MODELO2")
{
?> 
<table width="315" border="0" cellpadding="1" cellspacing="1" class="catalogos_modelo">
  <tr>
    <td width="116" style="background-color:#FFFFFF">&nbsp;</td>
    <td width="93" bgcolor="#333333"><div align="center" class="style1"><?=_("CANTIDAD") ;?></div></td>
    <td width="96" bgcolor="#333333"><div align="center" class="style1"><?=_("UNIDAD") ;?></div></td>
  </tr>
  <tr>
    <td bgcolor="#D3DEEB"><div align="center">1-15</div></td>
    <td bgcolor="#D3DEEB"><div align="center">
      <input name="txtcantidad[]" type="text" id="txtcantidad[]" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-align:center" onkeypress="return validarnum(event)" >
    </div></td>
    <td bgcolor="#D3DEEB"><div align="center">
	<?
		$con->cmbselectopc($sqlmon,"cmbunidad[]",$medida[$indice]," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");								
	?>	
    </div></td>
  </tr>
  <tr>
    <td bgcolor="#D3DEEB"><div align="center">16-30</div></td>
    <td bgcolor="#D3DEEB"><div align="center">
      <input name="txtcantidad[]" type="text" id="txtcantidad[]" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-align:center" onkeypress="return validarnum(event)" >
    </div></td>
    <td bgcolor="#D3DEEB"><div align="center">
	<?
		$con->cmbselectopc($sqlmon,"cmbunidad[]",$medida[$indice]," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");								
	?>	
    </div></td>
  </tr>
  <tr>
    <td bgcolor="#D3DEEB"><div align="center">31-45</div></td>
    <td bgcolor="#D3DEEB"><div align="center">
      <input name="txtcantidad[]" type="text" id="txtcantidad[]" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-align:center" onkeypress="return validarnum(event)" >
    </div></td>
    <td bgcolor="#D3DEEB"><div align="center">		
	<?
		$con->cmbselectopc($sqlmon,"cmbunidad[]",$medida[$indice]," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");								
	?>	</div></td>
  </tr>
  <tr>
    <td bgcolor="#D3DEEB"><div align="center">46-60</div></td>
    <td bgcolor="#D3DEEB"><div align="center">
      <input name="txtcantidad[]" type="text" id="txtcantidad[]" size="3" maxlength="2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-align:center" onkeypress="return validarnum(event)" >
    </div></td>
    <td bgcolor="#D3DEEB"><div align="center">
	<?
		$con->cmbselectopc($sqlmon,"cmbunidad[]",$medida[$indice]," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");								
	?>	
    </div></td>
  </tr>
</table>
<? 
}
else if($_POST["opc"]==3)
{
?>
 	
 <table border="0" cellpadding="1" cellspacing="1" class="catalogos_costos">
              <tr>
                <td bgcolor="#FFFFFF" style="border:1px solid #CCCCCC" ></td>
				<td bgcolor="#000000"><span class="style1"> <?=_("ESTANDAR") ;?></span></td>
				<td bgcolor="#000000"><span class="style1" style="text-align:left"><?=_("NOCTURNO / FESTIVO") ;?></span></td>
				<td bgcolor="#000000"><span class="style1" style="text-align:left"><?=_("KM ADICIONAL") ;?></span></td>
				<td bgcolor="#000000"><span class="style1" style="text-align:left"><?=_("TROCHA") ;?></span></td>
				<td bgcolor="#000000"><span class="style1" style="text-align:left"><?=_("FALSO FLETE") ;?></span></td>
				<td bgcolor="#000000"><span class="style1" style="text-align:left"><?=_("UNIDAD") ;?></span></td>
              </tr>
              <tr>
                <td bgcolor="#EBEBEB" style="text-align:left"   ><span class="style3"><?=_("LOCAL") ;?></span></td>
                <td bgcolor="#EBEBEB" ><div align="right"><input name="txtstandar1" type="text" id="txtstandar1" size="5" value="<?=$rowpla[0]; ?>"  maxlength="5" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);">
                </div></td>
                <td bgcolor="#EBEBEB"><div align="right"><input name="txtnocturno1" type="text" id="txtnocturno1" size="5" maxlength="5" value="<?=$rowpla[1]; ?>"  onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);">
                </div></td>
                <td bgcolor="#EBEBEB"><div align="right"><input name="txtkmadd1" type="text" id="txtkmadd1" size="5" value="<?=$rowpla[2]; ?>"  maxlength="5" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);">
                </div></td>
                <td bgcolor="#EBEBEB"><div align="right"><input name="txttrocha1" type="text" id="txttrocha1" size="5" value="<?=$rowpla[3]; ?>"  maxlength="5" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);">
                </div></td>
                <td bgcolor="#EBEBEB"><div align="right"><input name="txtflete1" type="text" id="txtflete1" size="5" value="<?=$rowpla[4]; ?>"   maxlength="5" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);">
                </div></td>
                <td bgcolor="#EBEBEB">
				<?
					$con->cmbselectopc($sqlmon,"cmbmoneda1",($rowpla[5])?$rowpla[5]:1," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");
				?>				</td>
				</tr>
              <tr>
                <td bgcolor="#FFFFEA" style="text-align:left"><span class="style3"><?=_("FORANEO") ;?></span></td>
                <td bgcolor="#FFFFEA"><div align="right"><input name="txtstandar2" type="text" id="txtstandar2" size="5" value="<?=$rowpla[6]; ?>"  maxlength="5" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);">
                </div></td>
                <td bgcolor="#FFFFEA"><div align="right"><input name="txtnocturno2" type="text" id="txtnocturno2" size="5" value="<?=$rowpla[7]; ?>"  maxlength="5" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);">
                </div></td>
                <td bgcolor="#FFFFEA"><div align="right"><input name="txtkmadd2" type="text" id="txtkmadd2" size="5" value="<?=$rowpla[8]; ?>"   maxlength="5" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);">
                </div></td>
                <td bgcolor="#FFFFEA"><div align="right"><input name="txttrocha2" type="text" id="txttrocha2" size="5" value="<?=$rowpla[9]; ?>"   maxlength="5" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);">
                </div></td>
                <td bgcolor="#FFFFEA"><div align="right"><input name="txtflete2" type="text" id="txtflete2" size="5" value="<?=$rowpla[10]; ?>"   maxlength="5" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);">
                </div></td>
                <td bgcolor="#FFFFEA">
				<?
					$con->cmbselectopc($sqlmon,"cmbmoneda2",($rowpla[11])?$rowpla[11]:1," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");
				?>
				</td>
              </tr>
            </table>
 
 
 
  <?php 
	}
  ?>