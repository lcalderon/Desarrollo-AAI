<?php
	
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	
	$con = new DB_mysqli();
	
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	 $con->select_db($con->catalogo);	
		
 	session_start(); 
 	//Auth::required(); 
		
	$Sql_asist="SELECT
				  asistencia.IDASISTENCIA,
				  catalogo_servicio.DESCRIPCION,
				  asistencia_usuario.FECHAHORA,
				  asistencia.ARRPRIORIDADATENCION AS clasifiservicio,
				  asistencia.IDUSUARIORESPONSABLE AS responsable,
				  asistencia.ARRSTATUSASISTENCIA  AS statusasis,
				  asistencia.IDETAPA,
				  asistencia.ARRCONDICIONSERVICIO AS clasificobertura
				FROM $con->temporal.asistencia
				  INNER JOIN catalogo_servicio
					ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
				  INNER JOIN $con->temporal.asistencia_usuario
					ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
				WHERE asistencia.IDEXPEDIENTE = '".$_POST["idcodigo"]."'
					AND asistencia.IDFAMILIA = '".$_POST["opc1"]."'
					AND asistencia_usuario.IDETAPA = asistencia.IDETAPA";
 
		$rs_asitencia=$con->query($Sql_asist);	
 
?>
<center>
 <table width="80%" border="0" align="center"  cellpadding="1" cellspacing="1"   style="border:1px solid #E0E0E0" >
		<tr>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("# ASISTENCIA") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("NOMBRE SERVICIO") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("CLASIF. SERVICIO") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("CLASIF. COBERTURA") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("STATUS") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("USU.RESPOSABLE") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("FECHAREGISTRO") ;?></span></div></td>
		</tr>	
		 <?
			
			if($_REQUEST["opc1"])
			 {
				while($reg = $rs_asitencia->fetch_object())
				 {			 	 
				//if($c%2==0) $fondo='#DBDBDB'; else $fondo='#F9F9F9';	
		?>		
		<tr title="<?=$reg->nombres;?>" >
 			<td bgcolor="#BED2DC"  ><div align="center"><strong><?=$reg->IDASISTENCIA;?></strong></div></td>
			<td><?=$reg->DESCRIPCION;?></td>			
			<td align="center"><?=$desc_prioridadAtencion[$reg->clasifiservicio];?></td>		 
			<td align="center"><?=$desc_cobertura_servicio[$reg->clasificobertura];?></td>		 
			<td align="center"><?=$desc_status_asistencia[$reg->statusasis];?></td> 
			<td align="center"><?=$reg->responsable;?></td>		 
			<td align="center"><?=$reg->FECHAHORA;?></td>
		 </tr>	
		<?
					 $c=$c+1;
					 $stylo="";
				 }
			 }
		?>
  </table>
  </center>