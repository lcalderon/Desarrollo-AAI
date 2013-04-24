<?php

		$con = new DB_mysqli();
		if($con->Errno){
			printf("Fallo de conexion: %s\n", $con->Error);
			exit();
		}

		$Sql_resument="SELECT
						  catalogo_proveedor.NOMBREFISCAL,
						  asistencia_asig_proveedor.STATUSPROVEEDOR,
						  asistencia_asig_proveedor.FECHACONCLUIDO,
						  asistencia_asig_proveedor.FECHAASIGNACION,
						  asistencia_asig_proveedor.FECHACONTACTO,
						  TIMEDIFF(asistencia_asig_proveedor.FECHAASIGNACION,(SELECT MIN(asistencia_usuario.FECHAHORA) FROM $con->temporal.asistencia_usuario WHERE asistencia_usuario.IDASISTENCIA=asistencia.IDASISTENCIA AND IDETAPA=1) ) AS TIEMPO_ASIGNACION,
						  TIMEDIFF( asistencia_asig_proveedor.FECHACONTACTO, asistencia_asig_proveedor.FECHAASIGNACION) AS TIEMPO_CONTACTO,
                          (SELECT  MAX(asistencia_bitacora_etapa6.FECHAMOD) FROM $con->temporal.asistencia_bitacora_etapa6 WHERE asistencia_bitacora_etapa6.IDASIGPROV=asistencia_asig_proveedor.IDASIGPROV AND asistencia_bitacora_etapa6.IDASISTENCIA=asistencia.IDASISTENCIA AND asistencia_bitacora_etapa6.ARRCLASIFICACION='ARR_PROV') as fechaarribo
						FROM $con->temporal.asistencia
						  INNER JOIN $con->temporal.asistencia_asig_proveedor
							ON asistencia_asig_proveedor.IDASISTENCIA = asistencia.IDASISTENCIA
						  LEFT JOIN $con->catalogo.catalogo_proveedor 
							ON catalogo_proveedor.IDPROVEEDOR=asistencia_asig_proveedor.IDPROVEEDOR
						WHERE asistencia.IDASISTENCIA = '$idasistencia'
						ORDER BY asistencia_asig_proveedor.FECHAASIGNACION DESC
					";
		
		$rs_resumen=$con->query($Sql_resument);

		$Sql_data="SELECT
						(SELECT expediente_usuario.FECHAHORA FROM $con->temporal.expediente_usuario WHERE expediente_usuario.IDEXPEDIENTE=expediente.IDEXPEDIENTE AND expediente_usuario.ARRTIPOMOVEXP='APE') AS FECHA_EXP,  
						(SELECT MIN(asistencia_usuario.FECHAHORA) FROM $con->temporal.asistencia_usuario WHERE asistencia_usuario.IDASISTENCIA=asistencia.IDASISTENCIA AND IDETAPA=1) AS FECHA_ASIS,
						(SELECT asistencia_bitacora_etapa8.FECHAMOD FROM $con->temporal.asistencia_bitacora_etapa8 WHERE asistencia_bitacora_etapa8.IDASISTENCIA=asistencia.IDASISTENCIA AND asistencia_bitacora_etapa8.ARRCLASIFICACION = 'LLCNF') AS FECHA_SATISFACCION,
						(SELECT MIN(asistencia_bitacora_etapa8.FECHAMOD) FROM $con->temporal.asistencia_bitacora_etapa8 WHERE asistencia_bitacora_etapa8.IDASISTENCIA=asistencia.IDASISTENCIA AND asistencia_bitacora_etapa8.ARRCLASIFICACION = 'BIT' GROUP BY IDASISTENCIA) AS FECHA_CONCLUIDO,
                        (SELECT MIN(asistencia_bitacora_etapa3.FECHAMOD) FROM $con->temporal.asistencia_bitacora_etapa3 WHERE asistencia_bitacora_etapa3.IDASISTENCIA = asistencia.IDASISTENCIA AND asistencia_bitacora_etapa3.ARRCLASIFICACION = 'CONF_SERV' GROUP BY IDASISTENCIA) AS FECHA_CONFIRMACION,
                        TIMEDIFF((SELECT MIN(asistencia_bitacora_etapa3.FECHAMOD) FROM $con->temporal.asistencia_bitacora_etapa3 WHERE asistencia_bitacora_etapa3.IDASISTENCIA = asistencia.IDASISTENCIA  AND asistencia_bitacora_etapa3.ARRCLASIFICACION = 'CONF_SERV' GROUP BY IDASISTENCIA),
                        (SELECT expediente_usuario.FECHAHORA FROM $con->temporal.expediente_usuario WHERE expediente_usuario.IDEXPEDIENTE = expediente.IDEXPEDIENTE AND expediente_usuario.ARRTIPOMOVEXP = 'APE')) AS TIEMPO                        
                    FROM $con->temporal.expediente LEFT JOIN $con->temporal.asistencia		  
							ON asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE				
						LEFT JOIN $con->temporal.asistencia_usuario
							ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
						WHERE asistencia.IDASISTENCIA='$idasistencia'
						GROUP BY asistencia.IDASISTENCIA
				  ";

		$data=$con->consultation($Sql_data);
?>
	<table width="90%" border="1" cellpadding="1" cellspacing="1" style='border-collapse:collapse;font-size:10px'>
		<tr style="color:#FFFFFF;font-weight:bold">
			<td width="38%" bgcolor="#6385AD"><div align="center"><?=_("MOVIMIENTO")?></div></td>
			<td width="22%" bgcolor="#6385AD"><div align="center"><?=_("FECHA")?></div></td>
			<td width="20%" bgcolor="#6385AD"><div align="center"><?=_("HORA")?></div></td>
			<td width="20%" bgcolor="#6385AD"><div align="center"><?=_("TIEMPO")?></div></td>
		</tr>
		<tr>
			<td style="font-weight:bold"><?=_("APERTURA EXPEDIENTE")?></td>
			<td align="center"><?=substr($data[0][0],0,10); ?></td>
			<td align="center"><?=substr($data[0][0],10,9); ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="font-weight:bold"><?=_("REG. DE ASISTENCIA")?></td>
			<td align="center"><?=substr($data[0][1],0,10); ?></td>
			<td align="center"><?=substr($data[0][1],10,9); ?></td>
			<td>&nbsp;</td>
		</tr>	 
		<tr>
			<td style="font-weight:bold"><?=_("CONFIRMA. DEL SERVICIO")?></td>
			<td align="center"><?=substr($data[0][4],0,10); ?></td>
			<td align="center"><?=substr($data[0][4],10,9); ?></td>
			<td align="center"><?=$data[0][5]; ?></td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>  
		 <?
		  
			while($row = $rs_resumen->fetch_object()){			 
				$conta++;
		?>
		<tr>
			<td colspan="4" style="font-weight:bold;font-size:11px" ><strong><?=$conta."-".$row->NOMBREFISCAL."(".$row->STATUSPROVEEDOR.")"?></strong></td>
		</tr>
		<tr>
			<td><?=_("ASIGNACION")?></td>
			<td align="center"><?=substr($row->FECHAASIGNACION,0,10); ?></td>
			<td align="center"><?=substr($row->FECHAASIGNACION,10,19); ?></td>
			<td align="center"><?=$row->TIEMPO_ASIGNACION; ?></td>
		</tr>
        <tr>
            <td><?=_("FECHA ARRIBO");?></td>
            <td align="center"><?=(substr($row->fechaarribo,0,10)!="0000-00-00")?substr($row->fechaarribo,0,10):""; ?></td>
            <td align="center"><?=(substr($row->fechaarribo,0,10)!="0000-00-00")?substr($row->fechaarribo,10,9):""; ?></td>
            <td>&nbsp;</td>
        </tr>
		<tr>
			<td><?=_("CONTACTO");?></td>
			<td align="center"><?=(substr($row->FECHACONTACTO,0,10)!="0000-00-00")?substr($row->FECHACONTACTO,0,10):""; ?></td>
			<td align="center"><?=(substr($row->FECHACONTACTO,0,10)!="0000-00-00")?substr($row->FECHACONTACTO,10,19):""; ?></td>
			<td align="center"><?=$row->TIEMPO_CONTACTO; ?></td>
		</tr>		
        <tr>
			<td><?=_("PROVEEDOR CONCLUIDO");?></td>
			<td align="center"><?=(substr($row->FECHACONCLUIDO,0,10)!="0000-00-00")?substr($row->FECHACONCLUIDO,0,10):""; ?></td>
			<td align="center"><?=(substr($row->FECHACONCLUIDO,0,10)!="0000-00-00")?substr($row->FECHACONCLUIDO,10,19):""; ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr> 		
		<? 	} ?> 
		<tr>
			<td style="font-weight:bold"><?=_("LLAMADA DE SATISFAC.")?></td>
			<td align="center"><?=substr($data[0][2],0,10); ?></td>
			<td align="center"><?=substr($data[0][2],10,9); ?></td>
			<td>&nbsp;</td>
		</tr> 
		<tr>
			<td style="font-weight:bold"><?=_("ASISTENCIA CONCLUIDA")?></td>
			<td align="center"><?=substr($data[0][3],0,10); ?></td>
			<td align="center"><?=substr($data[0][3],10,9); ?></td>
			<td>&nbsp;</td>
		</tr>		
	</table>
	<br>