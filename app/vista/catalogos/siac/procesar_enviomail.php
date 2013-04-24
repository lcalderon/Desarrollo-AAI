<?php

		$cuerpo_mail="<html><head><title>American Assist</title></head><body>";
		$cuerpo_mail.="INFORMACION DEL RECLAMO GENERADO <br/><br/>";
		
		$cuerpo_mail.="
			<table border='0'cellpadding='1' cellspacing='1' width='100%' bgcolor='#999999' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif' >
				<tr bgcolor='#333333' >				
					<th style='color:#FFFFFF;text-align:center;font-weight:bolder'>#CASO</th>
					<th style='color:#FFFFFF'>CUENTA</th>
					<th style='color:#FFFFFF'>PROGRAMA</th>										
					<th style='color:#FFFFFF'>GESTION</th>
					<th style='color:#FFFFFF'>PROCEDENCIA</th>
					<th style='color:#FFFFFF'>MOTIVO</th>
					<th style='color:#FFFFFF'>FECHA</th>
					<th style='color:#FFFFFF'>AREARESP.</th>
					<th style='color:#FFFFFF'>USUARIO</th>
					<th style='color:#FFFFFF'>STATUS_SEG.</th>
					<th style='color:#FFFFFF'>COMENTARIO</th>
				</tr>"; 

	$Sql="SELECT
		  catalogo_grupo.NOMBRE AS ARERESP,
		  catalogo_detallemotivollamada.DESCRIPCION,
		  retencion.IDUSUARIO,
		  retencion.IDRETENCION,
		  retencion.FECHARETENCION,
		  retencion.COMENTARIO,
		  retencion.ARRPROCEDENCIA,
		  retencion.MOTIVOLLAMADA,
		  retencion.IDGRUPO,
		  retencion.STATUS_SEGUIMIENTO,
		  catalogo_cuenta.NOMBRE                    AS CUENTA,
		  catalogo_programa.NOMBRE                  AS PLAN,
		  retencion_seguimiento.ARRVALIDEZ,
		  retencion.FECHADISPOSICION,
		  retencion.DEFENSACONSUMIDOR,
		  retencion.RESPUESTAFORMAL,
		  retencion_seguimiento.IDSEGUIMIENTO
		FROM $con->temporal.retencion
		  LEFT JOIN $con->temporal.retencion_seguimiento
			ON retencion_seguimiento.IDRETENCION = retencion.IDRETENCION
		  INNER JOIN $con->catalogo.catalogo_grupo
			ON catalogo_grupo.IDGRUPO = retencion.IDGRUPO			
		  INNER JOIN $con->catalogo.catalogo_detallemotivollamada
			ON catalogo_detallemotivollamada.IDDETMOTIVOLLAMADA = retencion.IDDETMOTIVOLLAMADA
		  INNER JOIN $con->catalogo.catalogo_cuenta
			ON catalogo_cuenta.IDCUENTA = retencion.IDCUENTA
		  INNER JOIN $con->catalogo.catalogo_programa
			ON catalogo_programa.IDPROGRAMA = retencion.IDPROGRAMA
		WHERE retencion.IDRETENCION = '$idreclamo' ";	

			if($idreclamo)
			 {  					
					$rsreclamos=$con->query($Sql);	
					$rowrec = $rsreclamos->fetch_object();			 	 
					$procedencia=$procedencia_mediogestion[$rowrec->ARRPROCEDENCIA];
					$statusproceso=$statusproceso_sac[$rowrec->STATUS_SEGUIMIENTO];
					$validez=$validez_sac[$rowrec->ARRVALIDEZ];
					$defensaconsumidor=($rowrec->DEFENSACONSUMIDOR)?"SI":"NO";
					$respformal=($rowrec->RESPUESTAFORMAL)?"SI":"NO";
					
					$asunto="RECLAMO SAC CASO # ".$rowrec->IDRETENCION;
					
						$cuerpo_mail.="
							<tr bgcolor='#DBE4EE' >
								<td style='text-align:center;font-weight:bolder'>$rowrec->IDRETENCION</td>
								<td>$rowrec->CUENTA</td>
								<td>$rowrec->PLAN</td>								
								<td align='left'>$rowrec->MOTIVOLLAMADA</td>
								<td>$procedencia</td>
								<td align='left'>$rowrec->DESCRIPCION</td>						
								<td>$rowrec->FECHARETENCION</td>
								<td>$rowrec->ARERESP</td>
								<td>$rowrec->IDUSUARIO</td>
								<td>$statusproceso</td>
								<td height='40%'>$rowrec->COMENTARIO</td>
							</tr>";
						
							$cuerpo_mail.=" </table> ";
						
						 if($rowrec->IDSEGUIMIENTO)
						 {						  
							//$asunto="SEGUIMIENTO DEL RECLAMO - CAMBIO DE RESPONSABLE";
							
							$cuerpo_mail.="<br>
								<table border='0' bgcolor='#999999' cellpadding='1' cellspacing='1' width='65%' style='font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif' >
									<tr bgcolor='#000000' >				
										<th style='color:#FFFFFF'>VALIDEZ</th>
										<th style='color:#FFFFFF'>FECHADISPOSICION</th>
										<th style='color:#FFFFFF'>DEFENSACONSUMIDOR</th>
										<th style='color:#FFFFFF'>RESPUESTAFORMAL</th>
									</tr>"; 
						
							$cuerpo_mail.="
								<tr bgcolor='#FFFFBB' >
									<td>$validez</td>
									<td align='center'>$rowrec->FECHADISPOSICION</td>					
									<td align='center'>$defensaconsumidor</td>
									<td align='center'>$respformal</td>
								</tr>"; 
							$cuerpo_mail.="</table>";								
						 }					 
					
						$cuerpo_mail.="<br><br><br><br><p>Por favor no responda este mensaje, es generado automáticamente desde una cuenta no monitoreada.</p>";	
					
						$cuerpo_mail.= "</body></html>";					

//consultado mail del area 

					/**
 *                          $Sql_mail="SELECT
 * 								  IF(GROUP_CONCAT(LOWER(catalogo_usuario.EMAIL)) IS NOT NULL AND GROUP_CONCAT(LOWER(responsable_area_email.EMAIL)) IS NOT NULL,CONCAT(GROUP_CONCAT(LOWER(catalogo_usuario.EMAIL)),',',GROUP_CONCAT(LOWER(responsable_area_email.EMAIL))), 								  
 * 								  IF(GROUP_CONCAT(LOWER(catalogo_usuario.EMAIL)) IS NOT NULL AND GROUP_CONCAT(LOWER(responsable_area_email.EMAIL)) IS NULL,GROUP_CONCAT(LOWER(catalogo_usuario.EMAIL)),								  
 * 								  IF(GROUP_CONCAT(LOWER(catalogo_usuario.EMAIL)) IS NULL AND GROUP_CONCAT(LOWER(responsable_area_email.EMAIL)) IS NOT NULL,GROUP_CONCAT(LOWER(responsable_area_email.EMAIL)), ''))) AS EMAILTOTAL
 * 								FROM $con->temporal.responsable_area
 * 								  INNER JOIN $con->catalogo.catalogo_usuario
 * 									ON catalogo_usuario.IDUSUARIO = responsable_area.IDRESPONSABLE
 * 								  LEFT JOIN $con->temporal.responsable_area_email
 * 									ON responsable_area_email.IDGRUPO = responsable_area.IDGRUPO								
 * 								WHERE responsable_area.IDGRUPO = '$rowrec->IDGRUPO'
 * 								ORDER BY catalogo_usuario.EMAIL";
 */
                            $Sql_mail="SELECT IF(GROUP_CONCAT(LOWER(grupo_emailexterno.EMAIL)) IS NOT NULL, 
                                          CONCAT(GROUP_CONCAT(DISTINCT LOWER(grupo_usuario.EMAIL)),',',GROUP_CONCAT(DISTINCT LOWER(grupo_emailexterno.EMAIL))),  GROUP_CONCAT(DISTINCT LOWER(grupo_usuario.EMAIL)) )
                                          
                                          AS EMAILTOTAL
                                        FROM $con->temporal.grupo_usuario
                                          LEFT JOIN $con->temporal.grupo_emailexterno
                                            ON grupo_emailexterno.IDGRUPO = grupo_usuario.IDGRUPO
                                        WHERE grupo_usuario.IDGRUPO = '$rowrec->IDGRUPO'";
	                // echo $Sql_mail;
					// die();
					$direcciones=$con->consultation($Sql_mail);
			 
					$mails = explode(',',$direcciones[0][0]);
					 
					if(count($mails) >0)	$resp=enviar_mails($mails,$asunto,$cuerpo_mail);
			 } 
?> 
