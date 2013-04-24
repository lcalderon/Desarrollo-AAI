<?php

	session_start();

	include_once("../../modelo/clase_mysqli.inc.php");

	$idasistencia=strtolower($_REQUEST["idasistencia"]);

	if($idasistencia){

		$con = new DB_mysqli();
		if($con->Errno){
			printf("Fallo de conexion: %s\n", $con->Error);
			exit();
		}

		$result = $con->query("SELECT * FROM $con->catalogo.catalogo_etapa WHERE IDETAPA LIKE '".$_GET["idetapa"]."%'");
		while($reg=$result->fetch_object()){

			$Sql_deficiencia="SELECT
                          catalogo_deficiencia.CVEDEFICIENCIA,
                          catalogo_deficiencia.NOMBRE,
                          expediente_deficiencia.IDETAPA,
                          expediente_deficiencia.ORIGEN,
                          expediente_deficiencia.MOVIMIENTO,
                          expediente_deficiencia.FECHAREGISTRO,
                          expediente_deficiencia.IDPROVEEDOR,
                          expediente_deficiencia.IDDEFICIENCIA_CORRELATIVO,
                          IF(catalogo_deficiencia.ORIGEN='EXTERNO',catalogo_proveedor.NOMBRECOMERCIAL,expediente_deficiencia.IDCOORDINADOR) AS IDCOORDINADOR,
                          expediente_deficiencia.ORIGEN,
                          expediente_deficiencia.MOVIMIENTO
                        FROM $con->catalogo.catalogo_deficiencia
                          INNER JOIN $con->temporal.expediente_deficiencia
                            ON expediente_deficiencia.CVEDEFICIENCIA = catalogo_deficiencia.CVEDEFICIENCIA
                          LEFT JOIN $con->catalogo.catalogo_proveedor
                            ON catalogo_proveedor.IDPROVEEDOR = expediente_deficiencia.IDPROVEEDOR
                        WHERE expediente_deficiencia.IDETAPA ='$reg->IDETAPA'
                            AND expediente_deficiencia.IDASISTENCIA = '$idasistencia'
                        ORDER BY expediente_deficiencia.ORIGEN,expediente_deficiencia.CVEDEFICIENCIA DESC ";


			if(!$_GET["idetapa"]){ ?>
			<fieldset style="background-color:#E2EBEF;width:60%">
				<legend style='font-weight:bold;font-size:14px'>&nbsp;&nbsp;<?=$reg->IDETAPA." - ".$reg->DESCRIPCION?></legend>
			<? } ?>
				<table width='<?=($_GET["idetapa"])?"58%":"100%"?>'  border='1' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'  style="border-collapse:collapse;border:1px solid #336699;font-size:09px">
					<tbody>
				<?
					$exec_deficiencia=$con->query($Sql_deficiencia);
					while($rset_deficiencia=$exec_deficiencia->fetch_object()){

						switch($rset_deficiencia->MOVIMIENTO){
							case "NUEVO":  $color = "#F04E00"; break;
							case "VALIDA": $color ="#F40000"; break;
							case "RETIRA": $color="#007500"; break;
							default: $color="#F04E00";
						}

						if($rset_deficiencia->MOVIMIENTO =="NUEVO")	$cantidadDefActivas++;
				?>
					<tr>
						<td align='center' width="30px"><input type='image' id='bitacora' src='/imagenes/iconos/info.png' title="<?=_("Ver Historial")?>" onClick="presentar_formulario('','/app/vista/calidad/vista_deficiencia_historial.php','<?=_("CIERRE LA VENTANA ANTERIOR")?>','<?=_("HISTORIAL DEFICIENCIA")?>','580','90','','<?=$rset_deficiencia->IDDEFICIENCIA_CORRELATIVO?>')"></td>
				<?
					if($rset_deficiencia->MOVIMIENTO =="RETIRA"){
				?>
						<td bgcolor="<?=$color?>" colspan="2" style="color:#FFFFFF;font-weight:bold" align="center"><?=_("RETIRADO")?></td>
				<?
					} else if($rset_deficiencia->MOVIMIENTO =="VALIDA"){
				?>
						<td bgcolor="<?=$color?>" colspan="2" align="center" style="color:#FFFFFF;font-weight:bold" ><?=_("VALIDADO")?></td>
				<?
					} else{
				?>
						<td align='center' width="72px"><input type="button" name='txtvalida' id='txtvalida' value="<?=_("VALIDAR")?>" class="cancelar" <?=$desactivado2?> onClick="presentar_formulario('1','/app/vista/calidad/validardeficiencia.php','<?=_("CIERRE LA VENTANA ANTERIOR")?>','<?=$rset_deficiencia->CVEDEFICIENCIA."-".$rset_deficiencia->NOMBRE?>','410','210','','<?=$rset_deficiencia->IDDEFICIENCIA_CORRELATIVO?>','valida')" ></td>
						<td align='center' width="73px"><input type="button" name='txtretirar' id='txtretirar' value="<?=_("RETIRAR")?>" class="guardar" <?=$desactivado2?> onClick="presentar_formulario('1','/app/vista/calidad/validardeficiencia.php','<?=_("CIERRE LA VENTANA ANTERIOR")?>','<?=$rset_deficiencia->CVEDEFICIENCIA."-".$rset_deficiencia->NOMBRE?>','410','210','','<?=$rset_deficiencia->IDDEFICIENCIA_CORRELATIVO?>','retira')" ></td>
				<?
					}
				?>
						<td width='30px' bgcolor="<?=$color?>" align='center' style='color:#FFFFFF;font-weight:bold'><?=$rset_deficiencia->CVEDEFICIENCIA?></td>
						<td width='78px' align="center"><?=$rset_deficiencia->ORIGEN?></td>
						<td width='100px'><?=$rset_deficiencia->IDCOORDINADOR?></td>
						<td><?=$rset_deficiencia->NOMBRE?></td>
					</tr>
				<?
					}
				?>
					</tbody>
				</table>
		<? if(!$_GET["idetapa"]){ ?></fieldset><? } ?>
		<br>
	<?
		}
	}
?>