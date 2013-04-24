<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	
	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start();
	Auth::required();

 	if($_POST["opc"] == "GENERAL"){
?>
<h2 class="Box"><?=_("GESTION DE LLAMADA") ;?>(<?=_("Generalidad") ;?>)</h2>
	<input name="txtmotllamada" type="hidden" id="txtmotllamada" value="GENERALIDAD">
	<table width="510px" border="0" cellpadding="1" cellspacing="1" bgcolor="#c6ffc6" style="border:1px solid #51ff51">
		<tr>
			<td><?=_("MOTIVO:") ;?></td>
			<td><?
					$sql="select IDDETMOTIVOLLAMADA,DESCRIPCION from catalogo_detallemotivollamada where MOTIVOLLAMADA='GENERALIDAD' AND ACTIVO=1 order by DESCRIPCION";
					$con->cmbselectdata($sql,"cmbopciones","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
			?></td>
		</tr>
		<tr>
			<td><?=_("OBSERVACION:") ;?></td>
			<td><textarea name="txtacomentario" id="txtacomentario" cols="75" rows="3" style="text-transform:uppercase;" class="classtexto"  onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"></textarea></td>
		</tr>
		<? if(!$_POST["opc2"]){ ?>
		<tr>
			<td>
			  <div align="right">
				<input type="button" name="btncerrar" id="btncerrar" value="<?=_("CANCELAR") ;?>"  style="font-size:10px;" onclick="document.form1.btngenera.disabled=false;document.getElementById('tipogestion').style.display='none';"/>
			  </div></td>
			<td  ><input type="submit" name="btnaceptar" id="btnaceptar" value="<?=_("GUARDAR CAMBIOS") ;?>"  style="font-weight:bold;font-size:10px;"/></td>
		</tr>
		<? } ?>
	</table>

<?
  } else if($_POST["opc"] == "BAJAS"){
?> 
<h2 class="Box"><?=_("GESTION DE LLAMADA") ;?>(<?=_("Baja del Afiliado") ;?>)</h2>
 <input name="txtmotllamada" type="hidden" id="txtmotllamada" value="BAJASERVICIO">
 <table width="61%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FF6C6C" style="border:1px solid #FF2D2D">		
    <tr>
		<td width="257"><?=_("MOTIVOS DE BAJA:") ;?></td>
        <td width="322"><?=_("OBSERVACION:") ;?></td>
    </tr>
    <tr>
		<td>
		<?
				$sql="select IDDETMOTIVOLLAMADA,DESCRIPCION from catalogo_detallemotivollamada where MOTIVOLLAMADA='BAJASERVICIO' AND ACTIVO=1 order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbopciones","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
		?></td>
        <td rowspan="4"><textarea name="txtacomentario" id="txtacomentario" cols="50" rows="3" style="text-transform:uppercase;" class="classtexto"  onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"></textarea></td>
    </tr>
    <tr>
		<td height="2"></td>
    </tr>
    <tr>
		<td height="2">&nbsp;</td>
    </tr>
    <tr>
		<td height="2">&nbsp;</td>
    </tr>    

    <tr>
		<td height="2">
			<div align="right">
				<input type="button" name="btncerrar" id="btncerrar" value="<?=_("CANCELAR") ;?>"  style="font-size:10px;" onClick="document.form1.btnbaja.disabled=false;document.getElementById('tipogestion').style.display='none';"/>
			</div>
		</td>
        <td height="2"><input type="submit" name="btnaceptar" id="btnaceptar" value="<?=_("GUARDAR CAMBIOS") ;?>"  style="font-weight:bold;font-size:10px;"/></td>
    </tr>
</table>
<?	
  }
else if($_POST["opc"] == "REINTEGRO"){ 

?>
<h2 class="Box"><?=_("GESTION DE LLAMADA") ;?>(<?=_("Reintegro") ;?>)</h2>
 <input name="txtmotllamada" type="hidden" id="txtmotllamada" value="REINTEGRO">
			
   <table width="61%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFCD9B" style="border:1px solid #FF7B3C">		
    <tr>
		<td colspan="2"><?=_("MOTIVOS DE REINTEGRO:") ;?></td>
		<td width="319"><?=_("OBSERVACION:") ;?></td>
    </tr>
    <tr>
		<td colspan="2"><?
				$sql="select IDDETMOTIVOLLAMADA,DESCRIPCION from catalogo_detallemotivollamada  where MOTIVOLLAMADA='REINTEGRO' AND ACTIVO=1 order by ORDENAMIENTO DESC";
				$con->cmbselectdata($sql,"cmbopciones","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
		?></td>
		<td rowspan="4"><textarea name="txtacomentario" id="txtacomentario" cols="50" rows="4" style="text-transform:uppercase;" class="classtexto"  onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"></textarea></td>
    </tr>
    <tr>
		<td width="121"><?=_("MESES SOLICITADOS:") ;?></td>
		<td width="143" height="2"><input name="txtmessol" type="text" id="txtmessol" size="3" maxlength="1" class="classtexto"  onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);document.form1.txtmontoefec.value=Math.round(document.form1.txtmessol.value*document.form1.txtmontosol.value*100)/100" style="text-align:center" onKeyPress="return validarnum(event)" value="0"></td>
  </tr>
  <tr>
		<td><?=_("MONTO SOLICITADO:") ;?></td>
		<td height="2"><input name="txtmontosol" type="text" id="txtmontosol" size="7" maxlength="6" class="classtexto"  onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);document.form1.txtmontoefec.value=Math.round(document.form1.txtmessol.value*document.form1.txtmontosol.value*100)/100" onKeyPress="return numeroDecimal(event)"  value="0.00"  style="text-align:right;"></td>
  </tr>
  <tr>
		<td><?=_("FECHA EJECUCION:") ;?></td>	  
		<td height="2"><input name="txtfechaejecusion" type="text" class="classtexto"  readonly id="txtfechaejecusion" size="14" maxlength="10" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" ><button type="button" id="f_trigger_b3">...</button></td>
    </tr>
    <tr>
		<td><?=_("MONTO EFECTUADO:") ;?></td>
		<td height="2"><input name="txtmontoefec" type="text" class="classtexto" id="txtmontoefec" style="text-align:right;border:0px"  onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" onKeyPress="return numeroDecimal(event)" value="0.00"  maxlength="7"  size="12" maxlength="10" readonly="readonly"></td>
		<td>&nbsp;</td>
    </tr>
	<? if(!$_POST["opc2"]){ ?>	
    <tr>
		<td>&nbsp;</td>
		<td height="2">
		  <div align="right">
		    <input type="button" name="btncerrar" id="btncerrar" value="<?=_("CANCELAR") ;?>"  style="font-size:10px;" onClick="document.form1.btnreitegro.disabled=false;document.getElementById('tipogestion').style.display='none';"/>
          </div></td>
        <td height="2"><input type="submit" name="btnaceptar" id="btnaceptar" value="<?=_("GUARDAR CAMBIOS") ;?>"  style="font-weight:bold;font-size:10px;"/></td>
    </tr>
	<? } ?>
</table>
<?
  } else if($_POST["opc"] == "CAMBIOP"){

?>
<h2 class="Box"><?=_("GESTION DE LLAMADA") ;?>(<?=_("Cambio de Plan") ;?>)</h2>
 <input name="txtmotllamada" type="hidden" id="txtmotllamada" value="CAMBIOPROGRAMA">
 <table width="61%" border="0" cellpadding="1" cellspacing="1" bgcolor="#F2F7FD" style="border:1px solid #CBDFF8">		
    <tr>
		<td width="257"><?=_("PLAN:") ;?></td>
        <td width="322"><?=_("OBSERVACION:") ;?></td>
    </tr>
    <tr>
		<td>
		<?
            $sql="select IDPROGRAMA,NOMBRE from catalogo_programa WHERE IDPROGRAMA!='".$_POST["programa"]."' and IDCUENTA='".$_POST["cuenta"]."' order by NOMBRE";
			
            $sql_plan="select COUNT(*) from $con->catalogo.catalogo_programa WHERE IDPROGRAMA!='".$_POST["programa"]."' and IDCUENTA='".$_POST["cuenta"]."' GROUP BY IDCUENTA ";
            
            $cantidadplan=$con->consultation($sql_plan);
            
            if($cantidadplan[0][0] <1){  $datodisa="disabled"; $msj=_("NO SE PUEDE REALIZAR LA OPERACION, NO EXISTE PLAN "); }
            
			$con->cmbselectdata($sql,"cmboplans","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
		?>
		</td>
        <td rowspan="4"><textarea name="txtacomentario" id="txtacomentario" cols="50" rows="3" style="text-transform:uppercase;" class="classtexto"  onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"><?=$msj?></textarea></td>
    </tr>
    <tr>
		<td height="2"><?=_("MOTIVO:") ;?></td>
    </tr>
    <tr>
      		<td>
		<?
				$sql="select IDDETMOTIVOLLAMADA,DESCRIPCION from catalogo_detallemotivollamada  where MOTIVOLLAMADA='CAMBIOPLAN' AND ACTIVO=1 order by ORDENAMIENTO DESC";
				$con->cmbselectdata($sql,"cmbopciones","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
		?></td>
    </tr>
    <tr>
		<td height="2">&nbsp;</td>
    </tr>    
    <tr>
		<td height="2">
		  <div align="right">
		    <input type="button" name="btncerrar" id="btncerrar" value="<?=_("CANCELAR") ;?>"  style="font-size:10px;" onClick="document.form1.btncambio.disabled=false;document.getElementById('tipogestion').style.display='none';"/>
          </div></td>
        <td height="2"><input type="submit" name="btnaceptar" id="btnaceptar" value="<?=_("GUARDAR CAMBIOS") ;?>" <?=$datodisa?> style="font-weight:bold;font-size:10px;"/></td>
    </tr>
</table>
<?	
  }
else if($_POST["opc"] == "REACTIVACION"){ 
?>
<h2 class="Box"><?=_("GESTION DE LLAMADA") ;?>(<?=_("Reactivar Cuenta") ;?>)</h2>
 <input name="txtmotllamada" type="hidden" id="txtmotllamada" value="REACTIVACION">
 <table width="61%" border="0" cellpadding="1" cellspacing="1" bgcolor="#9CE36C" style="border:1px solid #488D1B">		
    <tr>
		<td width="257"><?=_("MOTIVOS DE REACTIVACION:") ;?></td>
        <td width="322"><?=_("OBSERVACION:") ;?></td>
    </tr>
    <tr>
		<td>
		<?
				$sql="select IDDETMOTIVOLLAMADA,DESCRIPCION from catalogo_detallemotivollamada where MOTIVOLLAMADA='REACTIVACION' AND ACTIVO=1 order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbopciones","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
		?></td>
        <td rowspan="4"><textarea name="txtacomentario" id="txtacomentario" cols="50" rows="3" style="text-transform:uppercase;" class="classtexto"  onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"></textarea></td>
    </tr>
    <tr>
      <td height="2"></td>
    </tr>
    <tr>
      <td height="2">&nbsp;</td>
    </tr>
    <tr>
      <td height="2">&nbsp;</td>
    </tr>    
    <tr>
		<td height="2">
		  <div align="right">
		    <input type="button" name="btncerrar" id="btncerrar" value="<?=_("CANCELAR") ;?>"  style="font-size:10px;" onClick="document.form1.btnreactivar.disabled=false;document.getElementById('tipogestion').style.display='none';"/>
          </div>
		 </td>
        <td height="2"><input type="submit" name="btnaceptar" id="btnaceptar" value="<?=_("GUARDAR CAMBIOS") ;?>"  style="font-weight:bold;font-size:10px;"/></td>
    </tr>
</table>

<?	
  }
else if($_POST["opc"] == "QUEJASRECLAMO"){
?>
<h2 class="Box"><?=_("GESTION DE LLAMADA") ;?>(<?=_("Reclamos y Quejas") ;?>)</h2>
 <input name="txtmotllamada" type="hidden" id="txtmotllamada" value="QUEJASRECLAMO">
 <table width="60%" border="0" cellpadding="1" cellspacing="1" bgcolor="#B1C3D9" style="border:1px solid #999999">		
    <tr>
		<td width="257"><?=_("MOTIVO DE RECLAMO:") ;?></td>
        <td width="322"><?=_("ASIGNACION CASO:") ;?></td>
    </tr>
    <tr>
		<td>
		<?
			$sql="select IDDETMOTIVOLLAMADA,DESCRIPCION from catalogo_detallemotivollamada  where MOTIVOLLAMADA='QUEJASRECLAMO' AND ACTIVO=1 order by ORDENAMIENTO DESC";
			$con->cmbselectdata($sql,"cmbopciones","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
		?>
		</td>
        <td><?

			$con->cmbselectdata("select IDGRUPO,NOMBRE from catalogo_grupo order by ORDENAMIENTO DESC","cmbasignacionc","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","","SELECCIONE");
				
		?></td>
    </tr>
    <tr>
		<td height="2">&nbsp;</td>
		<td>&nbsp;</td>
    </tr>
    <tr>
		<td height="2"><?=_("PROCEDENCIA Y MEDIO DE GESTION:") ;?></td>
		<td><?=_("OBSERVACION:") ;?></td>
    </tr>
    <tr>
		<td height="21">
			<?
				 
				$con->cmb_array("cmbprocedencia",$procedencia_mediogestion,"","class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1","","1");
			?>
		</td>
		<td rowspan="2"><textarea name="txtacomentario" id="txtacomentario" cols="55" rows="3" style="text-transform:uppercase;" class="classtexto"  onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"></textarea></td>
    </tr>    
    <tr>
		<td height="2">&nbsp;</td>
    </tr>
    <tr>
		<td height="2" colspan="2"></td>
    </tr>
	<? if(!$_POST["opc2"]){ ?>	
    <tr>
		<td height="2">
		  <div align="right">
		    <input type="button" name="btncerrar" id="btncerrar" value="<?=_("CANCELAR") ;?>"  style="font-size:10px;" onClick="document.form1.btnquejasre.disabled=false;document.getElementById('tipogestion').style.display='none';"/>
          </div>
		</td>
        <td height="2"><input type="submit" name="btnaceptar" id="btnaceptar" value="<?=_("GUARDAR CAMBIOS") ;?>"  style="font-weight:bold;font-size:10px;"/></td>
    </tr>
	<? } ?>
</table>
<?	
  }
else if($_POST["opc"] == "DESAFILIACION"){
?>
<h2 class="Box"><?=_("GESTION DE LLAMADA") ;?>(<?=_("Desafiliaci&oacute;n") ;?>)</h2>
 <input name="txtmotllamada" type="hidden" id="txtmotllamada" value="DESAFILIACION">
 <table width="61%" border="0" cellpadding="1" cellspacing="1" bgcolor="#ebcd50" style="border:2px solid #af7b03">		
    <tr>
		<td width="257"><?=_("MOTIVOS DE BAJA:") ;?></td>
        <td width="322"><?=_("OBSERVACION:") ;?></td>
    </tr>
    <tr>
		<td>
		<?
				$sql="select IDDETMOTIVOLLAMADA,DESCRIPCION from catalogo_detallemotivollamada where MOTIVOLLAMADA='DESAFILIACION' AND ACTIVO=1 order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbopciones","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
		?></td>
        <td rowspan="4"><textarea name="txtacomentario" id="txtacomentario" cols="50" rows="3" style="text-transform:uppercase;" class="classtexto"  onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"></textarea></td>
    </tr>
    <tr>
		<td height="2">&nbsp;</td>
    </tr>
    <tr>
		<td height="2"><?=_("RESULTADO") ;?>:</td>
    </tr>
    <tr>
		<td height="2"><input name="txtresultado" type="text" id="txtresultado" size="25" readonly class="classtexto"  onFocus="coloronFocus(this);" onBlur="colorOffFocus(this)" style="text-align:center"></td>
    </tr>
    <tr>
		<td height="2">
			<div align="right">
				<input type="button" name="btncerrar" id="btncerrar" value="<?=_("CANCELAR") ;?>"  style="font-size:10px;" onClick="document.form1.btndesafiliacion.disabled=false;document.getElementById('tipogestion').style.display='none';"/>
			</div>
		</td>
        <td height="2"><input type="submit" name="btnaceptar" id="btnaceptar" value="<?=_("GUARDAR CAMBIOS") ;?>"  style="font-weight:bold;font-size:10px;"/></td>
    </tr>
</table>
<?php	
  }  
?> 