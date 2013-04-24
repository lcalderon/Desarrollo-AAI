<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../includes/arreglos.php");

	$con = new DB_mysqli();
	
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start(); 
 
	$result=$con->query("select catalogo_programa_servicio.IDPROGRAMASERVICIO,catalogo_programa_servicio.ETIQUETA,catalogo_programa_servicio.COMENTARIO_EXCLUSION,catalogo_programa_servicio.TIPOCOBERTURA,if(catalogo_programa_servicio.TIPOCOBERTURA='CX','Conexion',if(catalogo_programa_servicio.TIPOCOBERTURA='CP','CoPago',if(catalogo_programa_servicio.TIPOCOBERTURA='PE','Por Evento','Sin Limite'))) as tipcobertura,catalogo_programa_servicio.IDPROGRAMA,catalogo_programa_servicio.IDSERVICIO,catalogo_programa.IDPROGRAMA,catalogo_programa.NOMBRE,catalogo_servicio.DESCRIPCION, catalogo_programa_servicio.MONTOXSERV,catalogo_tipofrecuencia.IDTIPOFRECUENCIA,catalogo_tipofrecuencia.NOMBRE,catalogo_programa_servicio.EVENTOS,catalogo_programa_servicio.IDMONEDA,catalogo_programa_servicio.IDSERVICIO from catalogo_programa_servicio inner join catalogo_servicio on catalogo_servicio.IDSERVICIO=catalogo_programa_servicio.IDSERVICIO inner join catalogo_programa  on catalogo_programa.IDPROGRAMA=catalogo_programa_servicio.IDPROGRAMA inner join catalogo_tipofrecuencia  on catalogo_tipofrecuencia.IDTIPOFRECUENCIA=catalogo_programa_servicio.IDTIPOFRECUENCIA where catalogo_programa_servicio.IDPROGRAMASERVICIO='".$_POST["idprograma"]."' order by catalogo_servicio.DESCRIPCION ");
	$row = $result->fetch_object();
	
	$rsfrecuencia=$con->query("select IDTIPOFRECUENCIA,NOMBRE from catalogo_tipofrecuencia order by NOMBRE  ");
	
	$rservicio=$con->query("select IDSERVICIO,DESCRIPCION from catalogo_servicio order by DESCRIPCION ");
	
	$rsmoneda=$con->query("SELECT catalogo_moneda.IDMONEDA,catalogo_moneda.DESCRIPCION FROM catalogo_moneda INNER JOIN catalogo_parametro_moneda ON catalogo_parametro_moneda.IDMONEDA=catalogo_moneda.IDMONEDA ORDER BY catalogo_moneda.DESCRIPCION  ");
 
 
	if($_POST["idserv"])
	 {
         //editar servicio plan         
?>
		<FORM name="frmeditar" METHOD="post" ACTION="actualizar_programa.php" onSubmit = "return validarCampoEdita(this)">
			 
			<table border="0" cellpadding="2" cellspacing="0" width="90%" class="catalogos">
				<tr class='modo1'>
					<td width="24%"><?=_("SERVICIOS") ;?></td>
					<td colspan="3">
           
                    
                    <? 
                        $con->cmbselectdata("select IDSERVICIO,DESCRIPCION from catalogo_servicio order by DESCRIPCION ","cmbservicio",$row->IDSERVICIO,"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ",($_POST["opc2"] <1)?"":"1"); 
                    ?>                    
				 
                    </td>	
				</tr>
				<tr class='modo1'>
					<td><?=_("NOMBRE COMERCIAL") ;?></td>
					<td colspan="3"><input name="txtetiqueta" type="text" id="txtetiqueta" size="80"  style="text-transform:uppercase;" maxlength="150" VALUE="<?=utf8_encode($row->ETIQUETA); ?>" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
				</tr>
				<tr class='modo1'>
					<td><?=_("MONTO MAX.") ;?></td>
					<td><input name="txtmonto" id="txtmonto" type="text" size="6" maxlength="6"  value="<?=$row->MONTOXSERV; ?>" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
					<select name="cmbmoneda" id="cmbmoneda"  onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" >					
					<?
						while($regm = $rsmoneda->fetch_object())
						 {
							if($regm->IDMONEDA == $row->IDMONEDA)
							 {
					?>
								<option value="<?=$regm->IDMONEDA; ?>" selected><?=$regm->DESCRIPCION; ?></option>
					<?
							 }
							else
							 {
					?>
								<option value="<?=$regm->IDMONEDA; ?>" ><?=$regm->DESCRIPCION; ?></option>
					<?
							 }
						 }
					?>
					</select>
					<td width="13%"><?=_("TIPO COBERTURA") ;?></td>
					<td width="43%"><?
						$con->cmb_array("cbmtcobertura",$desc_tipo_cobertura,$row->TIPOCOBERTURA," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1")
						?></td>					  
				</tr>
				<tr class='modo1'>
					<td><?=_("EVENTO MAX.") ;?></td>
					<td><input name="txtevento" id="txtevento" type="text" size="05" maxlength="2" value="<?=$row->EVENTOS; ?>" onKeyPress="return validarnum(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" ></td>
					<td><?=_("FRECUENCIA") ;?></td>
					<td><select name="cbmtfrecuencia" id="cbmtfrecuencia"  onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">					
					<?
						while($regfre = $rsfrecuencia->fetch_object())
						 {
							if($regfre->IDTIPOFRECUENCIA == $row->IDTIPOFRECUENCIA)
							 {
					?>
								<option value="<?=$regfre->IDTIPOFRECUENCIA; ?>" selected><?=$regfre->	NOMBRE; ?></option>
					<?
							 }
							else
							 {
					?>
								<option value="<?=$regfre->IDTIPOFRECUENCIA; ?>" ><?=$regfre->NOMBRE; ?></option>
					<?
							 }
						 }
					?>
					</select>					</td>
				</tr>							
				<tr class='modo1'>
				  <td colspan="4"  ><strong><?=_("EXCEPCIONES") ;?></strong>:</td>
			  </tr>
				<tr class='modo1'>
				  <td colspan="4"><label>				    
				      <textarea name="txtarexclusion" id="txtarexclusion" style="text-transform:uppercase;" cols="80" rows="3" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"><?=utf8_encode($row->COMENTARIO_EXCLUSION); ?></textarea>
				  </label></td>
			  </tr>
				<tr class='modo1'>
					<td colspan="4" align="right"><input name="cerrar" type="button" value="-" title="<?=_('CERRAR') ;?>"  onclick="mostrar_ocualtarDiv(this,'bloque')">   <input name="Submit" type="submit" value="Grabar Servicio" title="<?=_('GRABAR SERVICIO') ;?>" ></td>
				</tr>
		</table> 


			<input name="opc" type="hidden" value="<?=$_POST["opc"]; ?>">
			<input name="idserv" type="hidden" value="<?=$_POST["idserv"];?>">
			<input name="idprograma" type="hidden" value="<?=$row->IDPROGRAMA; ?>">
			<input name="idprogservicio" type="hidden" value="<?=$row->IDPROGRAMASERVICIO; ?>">
			<input name="editarservicio" type="hidden" value="ok">
		</FORM>
		
<?
	 }
	else 
	 {
         //agregar servicio plan
?>	
		<FORM name="frmagrega" METHOD="post" ACTION="actualizar_programa.php" onSubmit = "return validarCampoAgrega(this)">		
		
		<table border="0" cellpadding="2" cellspacing="0" width="90%" class="catalogos">
				<tr class='modo1'>
					<td width="24%"><?=_("SERVICIOS") ;?></td>
					<td colspan="3"><? $con->cmbselect_db('cmbservicio',"select IDSERVICIO,DESCRIPCION from catalogo_servicio order by DESCRIPCION ", 'Blank','  onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" ',"","SELECCIONE"); ?></td>
				</tr>
				<tr class='modo1'>
				  <td><?=_("NOMBRE COMERCIAL") ;?></td>
				  <td colspan="3"><label>
				    <input name="txtetiqueta" type="text" id="txtetiqueta" size="80"  style="text-transform:uppercase;" maxlength="150" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
				  </label></td>
				</tr>
				<tr class='modo1'>
					<td><?=_("MONTO MAX.") ;?></td>
					<td width="20%"><input name="txtmonto" id="txtmonto" type="text" size="06" maxlength="06" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
						<? $con->cmbselectdata('SELECT catalogo_moneda.IDMONEDA,catalogo_moneda.DESCRIPCION FROM catalogo_moneda INNER JOIN catalogo_parametro_moneda ON catalogo_parametro_moneda.IDMONEDA=catalogo_moneda.IDMONEDA ORDER BY catalogo_moneda.DESCRIPCION ', 'cmbmoneda','PEN',' id="cmbmoneda" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" ',"2"); ?></td>
					<td width="13%"><?=_("TIPO COBERTURA") ;?></td>
					<td width="43%"><?
						$con->cmb_array("cbmtcobertura",$desc_tipo_cobertura,"PEVE"," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1")
						?></td>				  
				</tr>  
				<tr class='modo1'>
					<td><?=_("EVENTO MAX.") ;?></td>
					<td><input name="txtevento" id="txtevento" type="text" size="05" maxlength="2" onKeyPress="return validarnum(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"></td>
					<td><?=_("FRECUENCIA") ;?></td>
					<td><? $con->cmbselectdata('select IDTIPOFRECUENCIA,NOMBRE from catalogo_tipofrecuencia order by NOMBRE ', 'cbmtfrecuencia','Anual', ' id="cbmtfrecuencia" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" ',""); ?></td>
				</tr>										
				<tr class='modo1'>
				  <td colspan="4"  ><strong><?=_("EXCEPCIONES") ;?></strong>:</td>
			  </tr>
				<tr class='modo1'>
				  <td colspan="4"  ><label>
				    
				      <textarea name="txtarexclusion" id="txtarexclusion" style="text-transform:uppercase;" cols="80" rows="3" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"></textarea>
			       
				  </label></td>
			  </tr>
				<tr class='modo1'>
					<td colspan="4" align="right"><input name="cerrar" type="button" value="-" title="<?=_('CERRAR') ;?>"  onclick="mostrar_ocualtarDiv(this,'bloque')"><input name="Submit" type="submit" value="Grabar Servicio" title="<?=_('GRABAR SERVICIO') ;?>" ></td>
				</tr>				
			</table>

			<input name="idprograma" type="hidden" value="<?=$_POST["idprograma"];?>">
			<input name="frmagregaserv" type="hidden" value="ok">	
			<input name="opc" type="hidden" value="<?=$_POST["opc"]; ?>">			
		</FORM>	
<?					
	 }
?>