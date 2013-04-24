<?php
	
	session_start(); 
	
	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	
	$con = new DB_mysqli();
	if ($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

 	Auth::required($_GET["urlActivo"]); 
		
	$Sql="SELECT
			  IF(catalogo_programa_servicio.ETIQUETA IS NULL,catalogo_servicio.DESCRIPCION,catalogo_programa_servicio.ETIQUETA) AS ETIQUETA,
			  catalogo_etapa.DESCRIPCION as etapa,
			  asistencia.IDASISTENCIA,
			  catalogo_servicio.DESCRIPCION,
			  MIN(asistencia_usuario.FECHAHORA) AS fecha_asisreg,
			  asistencia.ARRPRIORIDADATENCION AS clasifiservicio,
			  asistencia.IDUSUARIORESPONSABLE AS responsable,
			  asistencia.ARRSTATUSASISTENCIA  AS statusasis,
			  asistencia.IDETAPA,
			  asistencia.ARRCONDICIONSERVICIO AS clasificobertura
		FROM $con->temporal.asistencia
		  LEFT JOIN $con->catalogo.catalogo_programa_servicio
			ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO			
		  INNER JOIN $con->catalogo.catalogo_servicio
			ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
		  INNER JOIN $con->temporal.asistencia_usuario
			ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
		  INNER JOIN $con->catalogo.catalogo_etapa
			ON catalogo_etapa.IDETAPA = asistencia.IDETAPA
		WHERE asistencia.IDEXPEDIENTE =".$_GET["idexpediente"]." 
		GROUP BY asistencia.IDEXPEDIENTE,asistencia.IDASISTENCIA,asistencia.IDSERVICIO
		ORDER BY asistencia.IDASISTENCIA DESC ";
 
	$rs_asitencia=$con->query($Sql);
?>
<div id="resultado" Style="overflow:auto;padding-top:1px; padding-Left:1px; padding-bottom:15px;height:175px; width:100%;">		
	<table width="100%" border="0" cellpadding="1" cellspacing="1" style="border:1px solid #E0E0E0">
		<tr>
			<td bgcolor="#000000" style="color:#5e9900"><div align="center"><strong><?=_("#ASISTENCIA") ;?></strong></div></td>
			<td bgcolor="#000000" style="color:#5e9900"><div align="center"><strong><?=_("NOMBRE SERVICIO");?></strong></div></td>
			<td bgcolor="#000000" style="color:#5e9900"><div align="center"><strong><?=_("CLASIF. SERVICIO") ;?></strong></div></td>
			<td bgcolor="#000000" style="color:#5e9900"><div align="center"><strong><?=_("STATUS") ;?></strong></div></td>
			<td bgcolor="#000000" style="color:#5e9900"><div align="center"><strong><?=_("ETAPA") ;?></strong></div></td>
			<td bgcolor="#000000" style="color:#5e9900"><div align="center"><strong><?=_("RESPONSABLE") ;?></strong></div></td>
			<td bgcolor="#000000" style="color:#5e9900"><div align="center"><strong><?=_("FECHA CREACION") ;?></strong></div></td>
		</tr>
		 <?
			
			if($_REQUEST["idexpediente"]){
			 	while($reg = $rs_asitencia->fetch_object()){
					
					$varexis=crypt($reg->IDASISTENCIA,"666");
					
					$datos=$con->consultation("SELECT
                                                    SUBSTR(MIN(CONCAT(FECHAHORA,',',IDUSUARIO)),21,15) AS usuario,
                                                    SUBSTR(MIN(CONCAT(FECHAHORA,',',IDUSUARIO)),1,19) AS fecha  
												FROM $con->temporal.asistencia_usuario
												WHERE IDASISTENCIA ='".$reg->IDASISTENCIA."'  AND IDETAPA=1 ");  
												 
												
					$dirname=$con->consultation("SELECT
										catalogo_etapa.URL
									FROM $con->temporal.asistencia
									  INNER JOIN $con->catalogo.catalogo_etapa
										ON catalogo_etapa.IDETAPA = asistencia.IDETAPA
									WHERE asistencia.IDASISTENCIA =".$reg->IDASISTENCIA);				
					if($c%2==0) $fondo='#8ccbff'; else $fondo='#cce8ff';
		?>		
		 <? if($_GET["gestion"] and $_GET["gestion"]=="CALIDAD"){ ?><tr bgcolor="<?=$fondo;?>" style="cursor:pointer" title="<?=$reg->IDASISTENCIA." - ".$reg->ETIQUETA." / ".$reg->etapa;?>" onclick="window.open('../../calidad/etapa0.php?idasistencia=<?=$reg->IDASISTENCIA?>&varexis=<?=$varexis?>&gestion=CALIDAD')" ><?}else{?><tr bgcolor="<?=$fondo;?>" style="cursor:pointer" title="<?=$reg->IDASISTENCIA." - ".$reg->ETIQUETA." / ".$reg->etapa;?>" onclick="window.open('<?=$dirname[0][0];?>?idasistencia=<?=$reg->IDASISTENCIA;?>','ASISTENCIA_<?=$reg->IDASISTENCIA?>')" ><? }?>
			<td bgcolor="#5bb6ff" width="10%"><div align="center"><strong><?=$reg->IDASISTENCIA;?></strong></div></td>
			<td><?=utf8_encode($reg->ETIQUETA);?></td>
			<td align="center"><?=$desc_cobertura_servicio[$reg->clasificobertura];?></td>
			<td align="center"><?=$desc_status_asistencia[$reg->statusasis];?></td>
			<td align="center"><?=$reg->etapa;?></td>
			<td align="center"><?=$datos[0][0];?></td>			
			<td align="center"><?=$datos[0][1];?></td>
		</tr>
	<?
					$c=$c+1;
					$stylo="";
				}
			}
	?>
	</table>
</div>