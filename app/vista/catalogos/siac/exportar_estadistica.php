<?php

	session_start();

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');

		
	$con = new DB_mysqli();
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	list($fechahora,$fecha,$hora)=fechahora();

	$result_afil=$con->query($Sql_Afliadoriesgo);  
 	 
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
 <table width="539" border="0" align="center" cellpadding="1" cellspacing="1" style="border:1px solid #C5C5E2">
    <tr>
      <td bgcolor="#9393ca"><div align="center"><strong><?=_("IDCUENTA") ;?></strong></div></div></td>  
	  <td bgcolor="#9393ca"><div align="center"><strong><?=_("CUENTA") ;?></strong></div></div></td>
      <? if($_POST["ckbidplan"]){ ?><td bgcolor="#9393ca"><div align="center"><strong><?=_("IDPLAN") ;?></strong></div></div></td><?}?>
      <? if($_POST["ckbplan"]){ ?><td bgcolor="#9393ca"><div align="center"><strong><?=_("PLAN") ;?></strong></div></div></td><?}?>
      <? if($_POST["ckbcanal"]){ ?><td bgcolor="#9393ca"><div align="center"><strong><?=_("CANAL") ;?></strong></div></div></td><?}?>
      <? if($_POST["ckbsucursal"]){ ?><td bgcolor="#9393ca"><div align="center"><strong><?=_("SUCURSAL") ;?></strong></div></div></td><?}?>
      <td bgcolor="#9393ca"><div align="center"><strong><?=_("ACTIVOS") ;?></strong></div></div></td>
      <? if($_POST["ckbcancelados"]){ ?><td bgcolor="#9393ca"><div align="center"><strong><?=_("CANCELADOS") ;?></strong></div></div></td><?}?>
	  <? if($_POST["ckbmodalidadpg"]){ ?><td bgcolor="#9393ca"><div align="center"><strong><?=_("MODALIDAD-PG") ;?></strong></div></td><?}?>	  
      <? if($_POST["ckbtotal"]){ ?><td bgcolor="#9393ca"><div align="center"><strong><?=_("TOTALES") ;?></strong></div></div></td><?}?>
    </tr>
    <?
 
		
			while($reg = $result_afil->fetch_object())
			 {			 	 
				if($c%2==0) $fondo='#b9d1d9'; else $fondo='#F3F3F3';	
				if($c%2==0) $clase='trbuc3'; else $clase='trbuc1';

				$c=$c+1;
				$totalact=$reg->totalact;
				$totalcan=$reg->totalcan;
				
				$totalactivos=$totalactivos+$reg->activos;
				$totalcancelados=$totalcancelados+$reg->cancelados;
				
				$total=$reg->activos+$reg->cancelados;
				//$totalgen=$reg->totalact+$reg->totalcan;				
				$totalgen=$totalactivos+$totalcancelados;				
	?>
    <tr>
      <td align="center" bgcolor="<?=$fondo;?>"><?=$reg->IDCUENTA;?></td>
      <td bgcolor="<?=$fondo;?>"><?=$reg->cuenta;?></td>
      <? if($_POST["ckbidplan"]){ ?><td bgcolor="<?=$fondo;?>" align="center"><?=$reg->IDPROGRAMA;?></td><?}?>
      <? if($_POST["ckbplan"]){ ?><td bgcolor="<?=$fondo;?>"><?=$reg->programa;?></td><?}?>
      <? if($_POST["ckbcanal"]){ ?><td bgcolor="<?=$fondo;?>"><?=$reg->CANALS;?></td><?}?>
      <? if($_POST["ckbsucursal"]){ ?><td bgcolor="<?=$fondo;?>"><?=$reg->SUCURSALS;?></td><?}?>
     <td align="center" bgcolor="<?=$fondo;?>"><?=number_format($reg->activos);?></td>
      <? if($_POST["ckbcancelados"]){ ?><td align="center" bgcolor="<?=$fondo;?>"><?=number_format($reg->cancelados);?></td><?}?>
	  <? if($_POST["ckbmodalidadpg"]){ ?><td align="center" bgcolor="<?=$fondo;?>"><?=$modalidad_pg[$reg->ARRMODALIDADPG]." [".number_format($reg->TOTALMODALIDADPG)."]";?></td><?}?>	  
      <? if($_POST["ckbtotal"]){ ?><td align="center" bgcolor="<?=$fondo;?>"><?=number_format($total);?></td><?}?>
    </tr>
    <?
			}		 	 
	?>
    <tr>
      <td colspan="<?=$celdas?>" align="right"><strong><em><?=_("TOTAL GENERAL") ;?></em></strong></td>
      <td align="center" bgcolor="#004080"><div align="center"><span class="style3"><?=number_format($totalactivos);?></span></div></td>
      <? if($_POST["ckbcancelados"]){ ?><td align="center" bgcolor="#004080"><div align="center"><span class="style3"><?=number_format($totalcancelados);?></span></div></td><?}?>
      <? if($_POST["ckbmodalidadpg"]){ ?><td align="center" bgcolor="#004080"></td><?}?>
	  <? if($_POST["ckbtotal"]){ ?><td align="center" bgcolor="#004080"><div align="center"><span class="style3"><?=number_format($totalgen);?></span></div></td><?}?>
    </tr>	
  </table>
  </body>
</html>