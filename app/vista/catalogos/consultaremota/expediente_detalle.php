<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once('../../../modelo/clase_unidadfederativa.inc.php');	
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
		
	$con= new DB_mysqli();
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
		
 	session_start(); 
	Auth::required($_SERVER['REQUEST_URI']);
	
	$idexpediente=$_GET["idexpediente"];
	 
	$var_color_ventas="#EBF6FF";
	$var_color_marcaje="#EBF6FF";

	$ubigeo = new ubigeo();
	$unidad = new unidadfederativa();
	
	$Sql="SELECT
		  CONCAT(catalogo_usuario.APELLIDOS,', ',catalogo_usuario.NOMBRES) AS usuario,
		  CONCAT(expediente_persona.APPATERNO,' ',expediente_persona.APMATERNO,', ',expediente_persona.NOMBRE) AS titular,
		  (SELECT
			 CONCAT(expediente_persona.APPATERNO,' ',expediente_persona.APMATERNO,', ',expediente_persona.NOMBRE)
		   FROM $con->temporal.expediente_persona
		   WHERE expediente_persona.ARRTIPOPERSONA = 'CONTACTO'
			   AND expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE) AS contacto,
		  expediente.IDEXPEDIENTE,
		  expediente.FECHAREGISTRO,
		  expediente.OBSERVACIONES,
		  expediente.OCURRIO,
		  expediente.NUMAUTORIZACION,
		  expediente.NOMAUTORIZACION,
		  expediente.DESCRIPCIONFISICA,
		  expediente_persona.IDPERSONA		  
		FROM $con->temporal.expediente
			INNER JOIN $con->temporal.expediente_persona
				ON expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE
			INNER JOIN $con->temporal.expediente_usuario
				ON expediente_usuario.IDEXPEDIENTE = expediente.IDEXPEDIENTE				
			INNER JOIN catalogo_usuario
			ON catalogo_usuario.IDUSUARIO = expediente_usuario.IDUSUARIO
		WHERE expediente.IDEXPEDIENTE='$idexpediente' AND expediente_usuario.ARRTIPOMOVEXP = 'REG' ";
 
	$result=$con->query($Sql);
	$reg = $result->fetch_object();
				
	$Sql_telefono="SELECT
				  expediente_persona_telefono.NUMEROTELEFONO
				FROM $con->temporal.expediente_persona_telefono
				  INNER JOIN $con->temporal.expediente_persona
					ON expediente_persona.IDPERSONA = expediente_persona_telefono.IDPERSONA
				WHERE expediente_persona_telefono.IDPERSONA = '".$reg->IDPERSONA."' 
				ORDER BY expediente_persona_telefono.PRIORIDAD
				LIMIT 4";				
				
	$resultel=$con->query($Sql_telefono);
	while($row = $resultel->fetch_object())
	 {
		$ii=$ii+1;
		$telefono[$ii]=$row->NUMEROTELEFONO;
		$codigoa[$ii]=$row->CODIGOAREA;		
		
	 }
	 
	$rsubigeo=$con->query("SELECT * FROM $con->temporal.expediente_ubigeo WHERE IDEXPEDIENTE='$idexpediente' ");
	if($reg2 = $rsubigeo->fetch_object())
	 {
    	$entidad[1]=$reg2->CVEENTIDAD1;
    	$entidad[2]=$reg2->CVEENTIDAD2;
    	$entidad[3]=$reg2->CVEENTIDAD3;
    	$entidad[4]=$reg2->CVEENTIDAD4;
    	$entidad[5]=$reg2->CVEENTIDAD5;
    	$entidad[6]=$reg2->CVEENTIDAD6;
    	$entidad[7]=$reg2->CVEENTIDAD7;
		
    	$arr = $unidad->nombre_entidades_array($entidad);
		
		$ubicacion=$arr[1]."/".$arr[2]."/".$arr[3]." : ".$reg2->DIRECCION."(".$reg2->REFERENCIA1." - ".$reg2->REFERENCIA2.")";
	 }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<LINK href='librerias/rpt_estilos.css' rel='stylesheet' type='text/css'>
	
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<style type="text/css">
	<!--
	.style1 {
		color: #003366;
		font-weight: bold;
	}
	-->
	</style>
	<script type="text/javascript">
		function llamada(codigoarea,numero){
			numero = codigoarea+numero;

			new Ajax.Request('../../../controlador/ajax/ajax_llamada.php',
			{	method : 'get',
				parameters: {
					prefijo: "",
					num: numero,
					ext: '<?=$_SESSION["extension"];?>'
					} 
			}
			);
		}


	</script>	
</head>
<body >
<?php
//Validar id expediente
 
	if(crypt($idexpediente,'666')!=$_GET["varexis"])
	{  
		echo "<script>";
		echo "alert('*** ID DEL EXPEDIENTE NO ESTA VALIDADO!!! ***');";
		echo "window.close();";
		echo "</script>";

		die('*** ID DEL EXPEDIENTE NO ESTA VALIDADO!!! ***');
	}
?>
	 <h2 class="Box"><?=_("DETALLE DEL EXPEDIENTE Y ASISTENCIAS") ;?></h2>

    <br>
    <TABLE border="1" width="99%" style="border-collapse:collapse" cellspacing="1" cellpadding="1" align="center">
    <tr>
		<td colspan="4" bgcolor="#F2F2F2" align="center"><b><?=_("DATOS DEL EXPEDIENTE") ;?></b></td>
	</tr>	
	<tr>
		<td bgcolor="#F2F2F2"><b><?=_("NUMERO DE EXPEDIENTE") ;?></b></td>
		<td><b><?=$reg->IDEXPEDIENTE;?></b></td>
		<td bgcolor="#F2F2F2"><b><?=_("FECHA DE REGISTRO") ;?></b></td>
		<td align="left"><?=$reg->FECHAREGISTRO;?></td>
	</tr>	
	<tr>
		<td width="18%" bgcolor="#F2F2F2"><b><?=_("NOMBRE DEL AFILIADO") ;?></b></td>
	  <td align="left" width="30%"><?=$reg->titular;?></td>
		<td width="18%" bgcolor="#F2F2F2"><b><?=_("NOMBRE DEL CONTACTO") ;?></b></td>
	  <td align="left" width="34%"><?=$reg->contacto;?></td>
	</tr>	
   <tr>
        <td bgcolor="#F2F2F2"><b><?=_("TELEFONO1") ;?></b></td>
        <td><input name="txttelefono[]" type="text" readonly class="classtexto" id="txttelefono[]" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="8<?=$telefono[1];?>" /><?
				$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
				//$con->cmbselectdata($sql,"cmbtelefono0",$tipotelefono[1]," onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","1");
			?></td>
        <td bgcolor="#F2F2F2"><span class="style5"><b><?=_("TELEFONO2") ;?></b></span></td>
        <td><input name="txttelefono[]" type="text" readonly class="classtexto" id="txttelefono[]" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[2];?>"/><?
				$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
				//$con->cmbselectdata($sql,"cmbtelefono1",$tipotelefono[2]," onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","1");
		?></td>
    </tr>
    <tr>
        <td bgcolor="#F2F2F2"><b><?=_("TELEFONO3") ;?></b></td>
        <td><input name="txttelefono[]" type="text" readonly class="classtexto" id="txttelefono[]" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[3];?>" /><?
			$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
			//$con->cmbselectdata($sql,"cmbtelefono2",$tipotelefono[3]," onchange=\"verificardiv('+','telefono3',this.value)\" onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","1");
		?></td>
        <td bgcolor="#F2F2F2"><b><?=_("TELEFONO4") ;?></b></td>
        <td><input name="txttelefono[]" type="text" readonly class="classtexto" id="txttelefono[]" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[4];?>"/><?
				$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
				//$con->cmbselectdata($sql,"cmbtelefono3",$tipotelefono[4]," onchange=\"verificardiv('+','telefono4',this.value)\" onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","1");
		?></td>
    </tr>	  
	<tr>
		<td bgcolor="#F2F2F2"><b><?=_("UBICACION") ;?></b></td>
		<td align="left" colspan="3"><?=$ubicacion;?></td>
	</tr>	
	<tr>
		<td bgcolor="#F2F2F2"><b><?=_("OBSERVACIONES") ;?></b></td>
		<td align="left" colspan="3"><?=$reg->OBSERVACIONES;?></td>
	</tr>
	<tr>
		<td bgcolor="#F2F2F2"><b><?=_("COORDINADOR") ;?></b></td>
		<td align="left" colspan="3"><?=$reg->usuario;?></td>
	</tr>
	<tr>
		<td width="18%" bgcolor="#F2F2F2"><b><?=_("NOMBRE AUTORIZANTE") ;?></b></td>
	  <td align="left" width="30%"><?=$reg->NOMAUTORIZACION;?></td>
		<td width="18%" bgcolor="#F2F2F2"><b><?=_("NUMERO AUTORIZACION") ;?></b></td>
	  <td align="left" width="34%"><?=$reg->NUMAUTORIZACION;?></td>
	</tr>		
</TABLE>

    <? 
		$Sql_asistencias="SELECT
						  asistencia.ARRSTATUSASISTENCIA,
						  asistencia.ARRCONDICIONSERVICIO,
						  asistencia.ARRPRIORIDADATENCION,
						  asistencia.IDASISTENCIA,
						  catalogo_servicio.DESCRIPCION,
						  (SELECT
							 catalogo_proveedor.NOMBRECOMERCIAL
						   FROM $con->temporal.asistencia_asig_proveedor
							 INNER JOIN $con->catalogo.catalogo_proveedor
							   ON catalogo_proveedor.IDPROVEEDOR = asistencia_asig_proveedor.IDPROVEEDOR
						   WHERE asistencia_asig_proveedor.IDASISTENCIA = asistencia.IDASISTENCIA
						   ORDER BY asistencia_asig_proveedor.FECHAASIGNACION DESC LIMIT 1) AS proveedor						  
						FROM $con->temporal.asistencia
						  INNER JOIN catalogo_servicio
							ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
						WHERE asistencia.IDEXPEDIENTE='".$_GET["idexpediente"]."' ";
 
		$rs_asistencia=$con->query($Sql_asistencias);		
		 
		while($row = $rs_asistencia->fetch_object())
		 {
			$cont=$cont+1;	 
			
			$Sql_plantilla="SELECT
							   CONCAT(LOWER(catalogo_familia.DESCRIPCION),'_',REPLACE(catalogo_plantilla.VISTA,'.php','')), 
                               asistencia.IDFAMILIA,
                               catalogo_plantilla.ETIQUETAAPRESENTAR,
                               catalogo_plantilla.CAMPOAPRESENTAR,
                               ETIQUETARESPUESTA,
                               CAMPORESPUESTA                      
							FROM $con->temporal.asistencia
							  INNER JOIN $con->catalogo.catalogo_servicio
								ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
							  INNER JOIN $con->catalogo.catalogo_familia
								ON catalogo_familia.IDFAMILIA = catalogo_servicio.IDFAMILIA  								
							  INNER JOIN $con->catalogo.catalogo_plantilla
								ON catalogo_plantilla.IDPLANTILLA = catalogo_servicio.IDPLANTILLA
							WHERE asistencia.IDASISTENCIA = '".$row->IDASISTENCIA."'";
			 	        
			$retornplan=$con->consultation($Sql_plantilla);
            $nombreplan=$retornplan[0][0];
            $nometiqueta=$retornplan[0][2];
            $nombrecapo=$retornplan[0][3];
            $nometiqueta_resp=$retornplan[0][4];
			$nombrecapo_resp=$retornplan[0][5];
                                   
			//if($retornplan[0][1]==3) $campo="REPARACION"; else if($retornplan[0][1]==5) $campo="SOLUCIONFALLA"; else $campo="''";
			 if($nombrecapo_resp!="")    $nombrecapo_resp=",".$nombrecapo_resp;   else $nombrecapo_resp="";
			$ocurrido=$con->consultation("SELECT $nombrecapo $nombrecapo_resp FROM $con->temporal.asistencia_$nombreplan WHERE IDASISTENCIA='".$row->IDASISTENCIA."' ");
           
            $Sql_asignacion="SELECT
                          COALESCE(MAX(arr.FECHAASIGNACION), '0000-00-00 00:00:00')
                        FROM $con->temporal.asistencia_asig_proveedor arr
                        WHERE arr.idasistencia = '".$row->IDASISTENCIA."'  ";
                        
            $Sql_arribo="SELECT
						  COALESCE(MAX(arr.FECHAARRIBO), '0000-00-00 00:00:00')
						FROM $con->temporal.asistencia_asig_proveedor arr
						WHERE arr.idasistencia = '".$row->IDASISTENCIA."' ";
		
			$Sql_contacto="SELECT
							  COALESCE(MAX(arr2.FECHACONTACTO), '0000-00-00 00:00:00')
							FROM $con->temporal.asistencia_asig_proveedor arr2
							WHERE arr2.idasistencia = '".$row->IDASISTENCIA."' ";
			
			$Sql_termino="SELECT
							  MAX(FECHACONCLUIDO)
							FROM $con->temporal.asistencia_asig_proveedor
							WHERE statusproveedor = 'PC'
								AND idasistencia = '".$row->IDASISTENCIA."' ";	

			$sql_conformidad="SELECT
							  COMENTARIO
							FROM $con->temporal.asistencia_bitacora_etapa8
							WHERE IDASISTENCIA = '".$row->IDASISTENCIA."'
								AND COMENTARIO LIKE '%CONFO%'
							LIMIT 1 ";
										
			
			$fechaasigna=$con->consultation($Sql_asignacion);
			$fechaarribo=$con->consultation($Sql_arribo);
			$fechacontacto=$con->consultation($Sql_contacto);
			$fechatermino=$con->consultation($Sql_termino);
			$conformidad=$con->consultation($sql_conformidad);
			 
	?>
		<br><br>
		
<TABLE border="1" width="99%" bordercolor="#0c298f" style="border-style:solid;border-collapse:collapse" cellspacing="0" cellpadding="2" align="center">
			<tr bgcolor="#EAEAEA">
				<td colspan="4" align="center"><span class="style1"><?=_("ASISTENCIA BRINDADA #$cont") ;?></span></td>
  </tr>		
			<tr>
				<td align="center" bgcolor="#F2F2F2" colspan="2"><b class="style1"><?=_("SERVICIO") ;?></b></td>
				<td align="center" bgcolor="#F2F2F2" colspan="2"><b class="style1"><?=_("PRIORIDAD Y CONDICION DEL SERVICIO") ;?></b></td>
			</tr>		
			<tr>
				<td align="center" colspan="2"><?=$row->DESCRIPCION;?></td>
				<td align="center" colspan="2"><?=$desc_prioridadAtencion[$row->ARRPRIORIDADATENCION]." - ".$desc_cobertura_servicio[$row->ARRCONDICIONSERVICIO];?></td>
			</tr>
			<tr>
				<td width="25%" align="center" bgcolor="#F2F2F2" class="style1"><b><?=_("Nro Asistencia") ;?></b></td>
				<td width="23%" align="center" bgcolor="#F2F2F2" class="style1"><b><?=_("Proveedor") ;?></b></td>
			  <td width="32%" align="center" bgcolor="#F2F2F2" class="style1"><b><?=_("Fecha Asignacion") ;?></b></td>
			  <td width="20%" align="center" bgcolor="#F2F2F2" class="style1"><b><?=_("Fecha Arribo") ;?></b></td>
			</tr>		
			<tr>
				<td align="center"><b><?=$row->IDASISTENCIA;?></b></td>
				<td><?=$row->proveedor;?></td>
				<td align="center"><?=$fechaasigna[0][0];?>&nbsp;</td>
				<td align="center"><?=$fechaarribo[0][0];?></td>
			</tr>		
			<tr>
				<td align="center" bgcolor="#F2F2F2" class="style1"><b><?=_("Fecha Contacto") ;?></b></td>
				<td align="center" bgcolor="#F2F2F2" class="style1"><b><?=_("Fecha Termino") ;?></b></td>
				<td align="center" bgcolor="#F2F2F2" class="style1"><b><?=_("Estado Asistencia") ;?></b></td>
				<td align="center" bgcolor="#F2F2F2" class="style1"><b><?=_("Conformidad") ;?></b></td>
			</tr>		
			<tr>
				<td align="center"><?=$fechacontacto[0][0];?>&nbsp;</td>
				<td align="center"><?=$fechatermino[0][0];?></td>
				<td align="center"><?=$desc_status_asistencia[$row->ARRSTATUSASISTENCIA];?></td>
				<td><?=($conformidad[0][0])?$conformidad[0][0]:_("NO ENCONTRADO");?></td>
			</tr>
			<tr>
			  <td bgcolor="#F2F2F2" class="style1"><div align="center"><b>
		      <?=$nometiqueta;?>
		      </b></div></td>
			  <td colspan="3" bgcolor="#F2F2F2" class="style1">&nbsp;</td>
		    </tr>
			<tr>
			  <td colspan="4"><?=$ocurrido[0][0];?>&nbsp;</td>
			</tr>
            <? if($nombrecapo_resp!=""){?>	
			<tr>
			  <td bgcolor="#F2F2F2" class="style1"><div align="center"><b>
		      <?=$nometiqueta_resp;?>
		      </b></div></td>
			  <td colspan="3" bgcolor="#F2F2F2" class="style1">&nbsp;</td>
		    </tr>
			<tr>
			  <td colspan="4"><?=utf8_encode($ocurrido[0][1]);?>&nbsp;</td>
			</tr>	
            <? }?>	
	</TABLE>


	<? 
		$nombrefiscal="";
		unset($fecha); 
	 }

	?>
</body>
</html>
