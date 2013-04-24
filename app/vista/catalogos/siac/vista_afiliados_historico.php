<?php

	session_start();

	include_once("../../../modelo/clase_mysqli.inc.php");
 	
	$con = new DB_mysqli();
		
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}	
?>
		<table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" style="border:1px solid #C5C5E2;width:100%">
			<tr bgcolor="#ACACD7">
				<td bgcolor="#ACACD7"><div align="center"><strong><?=_("IDCUENTA") ;?></strong></div></td>  
				<td bgcolor="#ACACD7"><div align="center"><strong><?=_("CUENTA") ;?></strong></div></td>
				<td bgcolor="#ACACD7"><div align="center"><strong><?=_("PLAN") ;?></strong></div></td>
				<td bgcolor="#ACACD7"><div align="center"><strong><?=_("ACTIVOS") ;?></strong></div></td>
				<td bgcolor="#ACACD7"><div align="center"><strong><?=_("CANCELADOS") ;?></strong></div></td>
				<td bgcolor="#ACACD7"><div align="center"><strong><?=_("TOTALES") ;?></strong></div></td>
			</tr>
			<?	
				$Sql_historicoAfi="SELECT
									catalogo_afiliado_historial.IDCUENTA,
									catalogo_afiliado_historial.IDPLAN,
									catalogo_cuenta.NOMBRE AS CUENTA,
									catalogo_programa.NOMBRE AS PLAN,
									catalogo_afiliado_historial.ACTIVOS,
									catalogo_afiliado_historial.CANCELADOS,
									(
										catalogo_afiliado_historial.ACTIVOS + catalogo_afiliado_historial.CANCELADOS
									)AS TOTALES
								FROM
									 $con->catalogo.catalogo_afiliado_historial
								INNER JOIN  $con->catalogo.catalogo_cuenta ON catalogo_cuenta.IDCUENTA = catalogo_afiliado_historial.IDCUENTA
								INNER JOIN  $con->catalogo.catalogo_programa ON catalogo_programa.IDPROGRAMA = catalogo_afiliado_historial.IDPLAN
								WHERE
									catalogo_afiliado_historial.FECHACREACION = '".$_POST["fechadia"]."'
								GROUP BY 1,2 ORDER BY 3,4";

				if($_POST["btnconsultarhist"]){
					$result_hist=$con->query($Sql_historicoAfi);
					while($row = $result_hist->fetch_object()){	 
						if($c%2==0) $fondo='#EAEAFF'; else $fondo='#F3F3F3';
						$c=$c+1;
					
					$totalactivosh=$totalactivosh+$row->ACTIVOS;
					$totalcanceladosh=$totalcanceladosh+$row->CANCELADOS;			
					$totalgenh=$totalactivosh+$totalcanceladosh;	
				
			?>
			<tr bgcolor="<?=$fondo;?>">
				<td align="center"><?=$row->IDCUENTA;?></td>
				<td align="center" style="text-align:left"><?=$row->CUENTA;?></td>
				<td align="center" style="text-align:left"><?=$row->PLAN;?></td>
				<td align="center"><?=$row->ACTIVOS;?></td>
				<td align="center"><?=$row->CANCELADOS;?></td>
				<td align="center"><?=$row->TOTALES;?></td>
			</tr>
			<? 		} } ?>			
			<tr>
				<td align="right" colspan="3"><strong><em><?=_("TOTAL GENERAL") ;?></em></strong></td>
				<td align="center" bgcolor="#004080"><div align="center"><span class="style3"><?=number_format($totalactivosh);?></span></div></td>
				<td align="center" bgcolor="#004080"><div align="center"><span class="style3"><?=number_format($totalcanceladosh);?></span></div></td>
				<td align="center" bgcolor="#004080"><div align="center"><span class="style3"><?=number_format($totalgenh);?></span></div></td>
			</tr>				
		</table>