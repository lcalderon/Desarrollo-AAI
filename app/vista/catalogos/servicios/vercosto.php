<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");

	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start();
	Auth::required();
 
	// session_start(); 
 	// Auth::required();

	$rscosto=$con->query("select CVECOSTO,DESCRIPCION from catalogo_costos where ACTIVO=1 and CVEFAMILIA=".$_POST["idfamilia"]." order by DESCRIPCION ");	
	$rsservicio=$con->query("select CVECOSTO from catalogo_servicios_costo where CVESERVICIO=".$_POST["idservicio"]." order by CVECOSTO ");	
 
	$i=1;
	while($rows= $rsservicio->fetch_object())
	 {
		 $dato[$i]=$rows->CVECOSTO;		 
		 $i=$i+1;		 
	 }
	 
?>
			<table border="0" align="center" cellpadding="1" cellspacing="1" class="costos">
						<tr>
							<td><?=_("CONCEPTOS") ;?></td>
						</tr>
				<? 
					while($reg = $rscosto->fetch_object())
					 {			
				?>							 
						<tr class='modo'>
							<td style="text-align:left"><input type="checkbox" name="chkcosto[]" value="<?=$reg->CVECOSTO; ?>" <? if(in_array($reg->CVECOSTO, $dato))	echo "checked"; ?>  ><?=utf8_decode($reg->DESCRIPCION); ?></td>							
							<!--td>								 
							<?
								//$sqlmon="select CVEMONEDA,DESCRIPCION from catalogo_monedas where DESCRIPCION!='' order by DESCRIPCION ";
								//$con->cmbselectopc($sqlmon,"cmbmoneda$indice",$moneda[$indice]," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");
							?>							</td>
							<td><input name="txtunidad<?//=$indice; ?>" type="text" id="txtunidad" value="<?//=$unidad[$indice]; ?>" size="4"  maxlength="2" class="classtexto" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" style="text-align:center;" onKeyPress="return validarnum(event)"></td>
							<td>
							<?
								//$sqlmed="select CVEMEDIDA,DESCRIPCION from catalogo_medida where DESCRIPCION!='' order by DESCRIPCION ";
								//$con->cmbselectopc($sqlmed,"cmbmedida$indice",$medida[$indice]," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","");								
							?>							</td -->
						</tr>
				<?		
					 }
				?>
					</table>