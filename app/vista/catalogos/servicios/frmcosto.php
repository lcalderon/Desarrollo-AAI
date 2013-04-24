<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start();
	
	$ii=1;
	$rs_servicosto=$con->query("select IDCOSTO,IDSERVICIO,MONTO,IDMONEDA,UNIDAD,IDMEDIDA from catalogo_servicio_costo where IDSERVICIO=".$_POST["servicio"]." order by IDCOSTO ");
	while($regp = $rs_servicosto->fetch_object())
	 {
		$rowcosto[]=$regp->IDCOSTO;
		$rowmonto[$ii]=$regp->MONTO;
		$rowmoneda[$ii]=$regp->IDMONEDA;
		$rowunidad[$ii]=$regp->UNIDAD;
		$rowmedida[$ii]=$regp->IDMEDIDA;
		$ii=$ii+1;
	 }	

	$rscosto=$con->query("select IDCOSTO,DESCRIPCION from catalogo_costo where ACTIVO=1 and IDFAMILIA=".$_POST["familia"]." order by DESCRIPCION ");

	$lista_moneda = $con->uparray("select IDMONEDA,DESCRIPCION from catalogo_moneda where DESCRIPCION!='' order by DESCRIPCION ");
	$lista_medida = $con->uparray("select IDMEDIDA,DESCRIPCION from catalogo_medida where DESCRIPCION!='' order by DESCRIPCION ");
	
	if($_POST["servicio"]==0)
	 {
?>
		 <table style="width:100%" border="0" align="center" cellpadding="1" cellspacing="1" class="costos">
						  <tr>
							  <td>&nbsp;</td>
							  <td style="text-align:center"><i><?=_("MONTO");?></i></td>
							  <td align="center"><i><?=_("MONEDA");?></i></td>
							  <td align="center"><i><?=_("UNIDAD");?></i></td>
							  <td align="center"><i><?=_("MEDIDA");?></i></td>
						  </tr>
					<?	
						
						while($reg = $rscosto->fetch_object())
						 {		
							$indice=$reg->IDCOSTO;
					?>							
						  <tr class='modo'>
							  <td style="text-align:left"><input type="checkbox" name="chkdesc[]" value="<?=$reg->IDCOSTO; ?>" ><?=$reg->DESCRIPCION; ?></td>
							  <td><input name="txtmonto<?=$indice; ?>" type="text" id="txtmonto" size="5" maxlength="5" class="classtexto" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);"></td>
							  <td>								 
								<?
									$con->cmbselect_ar("cmbmoneda$indice",$lista_moneda, 'S/.'," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ");
								?>							  </td>
							  <td><input name="txtunidad<?=$indice; ?>" type="text" id="txtunidad" size="4" maxlength="2" class="classtexto" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" style="text-align:center;" onKeyPress="return validarnumero(event)"></td>
							  <td>
								<?
									$con->cmbselect_ar("cmbmedida$indice",$lista_medida, 'EVENTO'," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ");
								?>							  </td>
						  </tr>
					<?	 							
						 }
					?>
				    </table>		
<?
	 }
	else 
	 {
?>	
					<table style="width:100%" border="0" align="center" cellpadding="1" cellspacing="1" class="costos">
					  <tr>
						  <td>&nbsp;</td>
						  <td style="text-align:center"><i><?=_("MONTO");?></i></td>
						  <td align="center"><i><?=_("MONEDA");?></i></td>
						  <td align="center"><i><?=_("UNIDAD");?></i></td>
						  <td align="center"><i><?=_("MEDIDA");?></i></td>
					  </tr>
					<?		
						
						while($reg = $rscosto->fetch_object())
						 {		
							$indice=$reg->IDCOSTO;
							if(in_array($reg->IDCOSTO,$rowcosto))	
							{
								$marcar="checked";	
								$i=$i+1;
								$monto=$rowmonto[$i];
								$moneda=$rowmoneda[$i];
								$unidad=$rowunidad[$i];
								$medida=$rowmedida[$i];
								
							}
					?>							
						  <tr class='modo'>
							  <td style="text-align:left"><input type="checkbox" name="chkdesc[]" value="<?=$reg->IDCOSTO; ?>"  <?=$marcar;?> ><?=$reg->DESCRIPCION; ?></td>
							  <td><input name="txtmonto<?=$indice; ?>" type="text" id="txtmonto" size="5" maxlength="5" class="classtexto" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=$monto; ?>"></td>
								<td>								 
								<?
									$con->cmbselect_ar("cmbmoneda$indice",$lista_moneda,$moneda," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ");
								?>
								</td>
							  <td><input name="txtunidad<?=$indice; ?>" type="text" id="txtunidad" size="4" maxlength="2" class="classtexto" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" style="text-align:center;" onKeyPress="return validarnumero(event)" value="<?=$unidad; ?>"></td>
							  <td>
								<?
									$con->cmbselect_ar("cmbmedida$indice",$lista_medida,$medida," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ");
								?>							  </td>
						  </tr>
					<?	 
							$monto="";
							$moneda="";
							$unidad="";
							$medida="";
							$marcar="";					
							
						 }
					?>
				    </table>
<?					
	 }
?>