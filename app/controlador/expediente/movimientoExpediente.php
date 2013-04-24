<?php

	include_once("../../modelo/clase_mysqli.inc.php");
	include_once("../../vista/login/Auth.class.php");
	
	$con= new DB_mysqli();
	
	$con->select_db($con->catalogo);
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	session_start(); 
 	//Auth::required();

	$Sql="SELECT
		  expediente_usuario.ARRTIPOMOVEXP,
		  expediente.IDEXPEDIENTE,
		  expediente_usuario.IDUSUARIO,
		  expediente_usuario.FECHAHORA
		FROM $con->temporal.expediente
		  INNER JOIN $con->temporal.expediente_usuario
			ON expediente_usuario.IDEXPEDIENTE = expediente.IDEXPEDIENTE
		WHERE expediente.IDEXPEDIENTE =".$idexpediente;
  
	$result = $con->query($Sql);
	 
		while($obj = $result->fetch_object())
		 {
			if($obj->ARRTIPOMOVEXP=="APE")		$horaini=$obj->FECHAHORA;
			if($obj->ARRTIPOMOVEXP=="TVA")		$horafin=$obj->FECHAHORA;

			
			$tiempovalidado=date("H:i:s", strtotime("00:00:00") + strtotime($horafin) - strtotime($horaini) );
								
			switch ($obj->ARRTIPOMOVEXP)
			{
				case "APE":
					$usuario_ape = $obj->IDUSUARIO;
					$fecha_ape = $obj->FECHAHORA;
					break;
				case "REG": 
					$usuario_reg = $obj->IDUSUARIO;
					$fecha_reg = $obj->FECHAHORA;
					break;	
				case "SEG": 
					$usuario_seg = $obj->IDUSUARIO;
					$fecha_seg = $obj->FECHAHORA;
					break;
				case "ASI": 
					$usuario_asi = $obj->IDUSUARIO;
					$fecha_asi = $obj->FECHAHORA;
					break;	
				case "TVA":						
					$usuario_valida = $obj->IDUSUARIO;
					$fecha_valida = $tiempovalidado;
					break;	

			}		
		}
				  
?>
	<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#444444" style="border:1px dashed #FFFFFF">
		<tr>
			<td width="33%" bgcolor="#BBE0FF"><?=_('COORD. APERTURA')?>: <strong><?=$usuario_ape;?></strong></td>
			<td width="33%" bgcolor="#8CCBFF"><?=_('COORD. REGISTRO')?>: <strong><?=$usuario_reg;?></strong></td>
			<td width="34%" bgcolor="#BBE0FF"><?=_('COORD. SEGUIMIENTO')?>: <strong><?=$usuario_seg;?></strong>&nbsp;</td>

		</tr>
		<tr>
			<td bgcolor="#BBE0FF"><?=_('FECHA APERTURA')?>&nbsp;&nbsp;&nbsp;: <strong><?=$fecha_ape;?></strong>&nbsp;</td>
			<td bgcolor="#8CCBFF"><?=_('FECHA REGISTRO')?>&nbsp;&nbsp;&nbsp;: <strong><?=$fecha_reg;?></strong></td>
			<td bgcolor="#BBE0FF"><?=_('FECHA SEGUIMIENTO')?>&nbsp;&nbsp;&nbsp;: <strong><?=$fecha_seg;?></strong></td>
		</tr>
	</table>

