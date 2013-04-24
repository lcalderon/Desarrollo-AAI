<?php

	session_start();

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../includes/arreglos.php");
		
	$con = new DB_mysqli();
	
	$con->select_db($con->catalogo);
			
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	list($fechahora,$fecha,$hora)=fechahora();

	$result_exp=$con->query($Sql);  
 	 
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=REPORTE_AFILIADOS-$fecha.xls");
	header('Pragma: no-cache');
	header('Expires: 0"');

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>American Assist</title>
    <style type="text/css">
	<!--
	.style3 {color: #FFFFFF; font-weight: bold; }
	-->
    </style>
</head>
<body>
<table width="200" border="1" cellpadding="1" cellspacing="1" style="border:1px solid #333333">
  <tr>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("#CASO") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("CUENTA") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("PLAN") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("CVEAFILIADO") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("NOMBRE") ;?></span></div></td>
	<td bgcolor="#000000"><div align="center"><span class="style3"><?=_("TELEFONO1") ;?></span></div></td>
	<td bgcolor="#000000"><div align="center"><span class="style3"><?=_("TELEFONO2") ;?></span></div></td>	
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("PROVINCIA") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("DIRECCION") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("IDAFILIADO") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("FECHAGESTION") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("HORAMINUTOSGEST") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("HORA") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("MES") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("GESTION") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("DETALLEMOTIVOLLAMADA") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("MESREITEGRO") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("MONTOSOLICITADO") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("FECHAEJECUSION") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("MONTOEFECTUADO") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("VALIDEZ") ;?></span></div></td>	
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("AREARESPONSABLE") ;?></span></div></td>	
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("PROCEDENCIA") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("DEFENSACONSUMIDOR") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("STATUSCASO") ;?></span></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("USUARIO") ;?></span></div></td>	
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("FECHAAFILIACION") ;?></span></div></td>    
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("FECHACANCELACION") ;?></span></div></td>
	<td bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("EXP./ASIS.") ;?></strong></div></td>
    <td bgcolor="#000000"><div align="center"><span class="style3"><?=_("COMENTARIO") ;?></span></div></td>
  </tr>
	<?
	while($fila=$result_exp->fetch_object())
	 {	
		if($fila->status_seguimiento=="")	$fila->status_seguimiento="S/D";
		
		if($fila->MOTIVOLLAMADA =="DESAFILIACION"){					
			$resp_motivoLlamada=$con->consultation("SELECT DESCRIPCION from $con->catalogo.catalogo_motivoscancelacionAON WHERE IDMOTIVOCANCELACION=$fila->IDDETMOTIVOLLAMADA");
			$fila->DETMOTIVOLLADA=$resp_motivoLlamada[0][0];
		}		
	?>  
  <tr bgcolor="#FFFFFF">
    <td><?=$fila->IDRETENCION;?></td>
    <td><?=$fila->CUENTA;?></td>
    <td><?=$fila->PLAN;?></td>
    <td><?=$fila->cveafil;?></td>
    <td><?=$fila->nombres;?></td>
	 <?
		$Sql_tel="SELECT
				  catalogo_afiliado_persona_telefono.NUMEROTELEFONO
				FROM catalogo_afiliado_persona_telefono
				  INNER JOIN catalogo_afiliado_persona
					ON catalogo_afiliado_persona.IDAFILIADO = catalogo_afiliado_persona_telefono.IDAFILIADO
				WHERE catalogo_afiliado_persona_telefono.IDAFILIADO = '".$fila->IDAFILIADO."'
				ORDER BY catalogo_afiliado_persona_telefono.PRIORIDAD LIMIT 2";				
		
		$resultel=$con->query($Sql_tel);
		while($row = $resultel->fetch_object())
		 {
			$ii=$ii+1;
			$telefono[$ii]=$row->NUMEROTELEFONO;
		 }
			$ii=0;
		
		for ($i=1;$i<=2;$i++)				
		 {
	?>


		<td><?=$telefono[$i];?></td>
	<?		
			$telefono[$i]="";
		 }
		 
		if($fila->STATUS_SEGUIMIENTO=="")	$fila->STATUS_SEGUIMIENTO="S/D";
	?>	
    <td><?=$fila->PROVINCIA;?></td>
    <td><?=$fila->DIRECCION;?></td>
    <td><?=$fila->IDAFILIADO;?></td>
    <td><?=$fila->FECHA;?></td>
    <td><?=$fila->HORAMIN;?></td>
    <td><?=$fila->HORA;?></td>
    <td><?=$mes_del_anio[$fila->MES];?></td>
    <td><?=$fila->MOTIVOLLAMADA;?></td>
    <td><?=utf8_encode($fila->DETMOTIVOLLADA);?></td>
    <td><?=$fila->MESREINTEGRO;?></td>
    <td><?=$fila->MONTOSOLICITADO;?></td>
    <td><?=$fila->FECHAEJECUSION;?></td>
	<td><?=($fila->MESREINTEGRO*$fila->MONTOSOLICITADO);?></td>
    <td><?=$validez_sac[$fila->ARRVALIDEZ];?></td>
    <td><?=($fila->NOMSAC)?$fila->NOMSAC:"S/D";?></td>
    <td><?=$procedencia_mediogestion[$fila->ARRPROCEDENCIA];?></td>
    <td><?=($fila->DEFENSACONSUMIDOR==1)?"SI":"NO";?></td>
    <td><?=$statusproceso_sac[$fila->STATUS_SEGUIMIENTO];?></td>
    <td><?=$fila->IDUSUARIO;?></td>	
    <td><?=$fila->FECHAINICIOVIGENCIA;?></td>    
    <td><?=$fila->FECHACANCELACION;?></td>
	<td><?=$fila->RECLA_ASIS;?></td>
    <td><?=utf8_encode($fila->COMENTARIO);?></td>
  </tr>
<?
	}
?>  
</table>
</body>
</html>
<?php
//ob_end_flush();
?>