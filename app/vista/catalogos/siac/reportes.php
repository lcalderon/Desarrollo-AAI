<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/functions.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	
	$con = new DB_mysqli();
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start();
	Auth::required($_SERVER['REQUEST_URI']);
 	
	validar_permisos("MENU_SAC",1);
	
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);
	
	if($_POST["cmbcuenta"])	$ver_cuentas="catalogo_cuenta.IDCUENTA='".$_REQUEST["cmbcuenta"]."' AND ";
	if($_POST["chkreclamoAsistencia"])	$reclamos_asistencias="AND retencion.IDASISTENCIA!='0'";
 
		$Sql="SELECT
              /* BUSCAR AFILIADO - REPORTE SAC */ 
              catalogo_afiliado.CVEAFILIADO,
              CONCAT(' ',catalogo_afiliado.CVEAFILIADO) as cveafil,
			  catalogo_afiliado.FECHAINICIOVIGENCIA,
			  /*catalogo_afiliado.FECHACANCELACION,*/
              if(catalogo_afiliado.STATUSASISTENCIA='CAN',(SELECT SUBSTR(MAX(ret.FECHARETENCION),1,10) FROM $con->temporal.retencion ret WHERE ret.IDAFILIADO=retencion.IDAFILIADO AND MOTIVOLLAMADA='BAJASERVICIO'),'') as FECHACANCELACION,
			  catalogo_afiliado_persona_ubigeo.DIRECCION,
			  catalogo_detallemotivollamada.DESCRIPCION AS DETMOTIVOLLADA,
			  CONCAT(catalogo_afiliado_persona.APPATERNO,' ',catalogo_afiliado_persona.APMATERNO,', ',catalogo_afiliado_persona.NOMBRE) AS nombres,			  
			  catalogo_cuenta.NOMBRE                    AS CUENTA,
			  catalogo_programa.NOMBRE                  AS PLAN,
			  retencion.IDAFILIADO,
			  retencion.IDRETENCION,
			  LEFT(retencion.FECHARETENCION,10)         AS FECHA,
			  RIGHT(retencion.FECHARETENCION,8)         AS HORAMIN,
			  LEFT(RIGHT(FECHARETENCION,8),2)         AS HORA,
			  RIGHT(LEFT(FECHARETENCION,7),2)         AS MES,
			  retencion.MOTIVOLLAMADA,
			  retencion.IDUSUARIO,
			  retencion.ARRVALIDEZ,
			  retencion.STATUS_SEGUIMIENTO,
			  retencion.ARRPROCEDENCIA,
			  retencion.DEFENSACONSUMIDOR,
			  retencion.FECHADISPOSICION,
			  retencion.MESREINTEGRO,
			  retencion.FECHAEJECUSION,
			  retencion.IDGRUPO,
			  retencion.COMENTARIO,
			  retencion.MONTOSOLICITADO,
			  retencion.IDDETMOTIVOLLAMADA,
			  IF(retencion.IDEXPEDIENTE,CONCAT(retencion.IDEXPEDIENTE,'-',retencion.IDASISTENCIA),'') AS RECLA_ASIS,
			  DAYOFWEEK(retencion.FECHARETENCION)  as diasemana,
			  (SELECT
				 COUNT(DISTINCT retencion_seguimiento.IDGRUPO) AS cantidad
			   FROM $con->temporal.retencion_seguimiento
			   WHERE retencion_seguimiento.IDRETENCION = retencion.IDRETENCION) AS cantidad_area,
			  catalogo_grupo.NOMBRE AS NOMSAC,
			  IF( catalogo_afiliado_persona_ubigeo.cveentidad2 != 0, ent2.descripcion, '' ) AS PROVINCIA
			FROM $con->temporal.retencion
			  INNER JOIN $con->catalogo.catalogo_afiliado
				ON catalogo_afiliado.IDAFILIADO = retencion.IDAFILIADO
			  INNER JOIN $con->catalogo.catalogo_afiliado_persona
				ON catalogo_afiliado_persona.IDAFILIADO = catalogo_afiliado.IDAFILIADO
			  LEFT JOIN $con->catalogo.catalogo_cuenta
				ON catalogo_cuenta.IDCUENTA = retencion.IDCUENTA
			  LEFT JOIN $con->catalogo.catalogo_programa
				ON catalogo_programa.IDPROGRAMA = retencion.IDPROGRAMA
			  LEFT JOIN $con->catalogo.catalogo_detallemotivollamada
				ON catalogo_detallemotivollamada.IDDETMOTIVOLLAMADA = retencion.IDDETMOTIVOLLAMADA
			  LEFT JOIN $con->catalogo.catalogo_afiliado_persona_telefono
				ON catalogo_afiliado_persona_telefono.IDAFILIADO = catalogo_afiliado_persona.IDAFILIADO
			  LEFT JOIN $con->catalogo.catalogo_afiliado_persona_ubigeo
				ON catalogo_afiliado_persona_ubigeo.IDAFILIADO = catalogo_afiliado.IDAFILIADO
			  LEFT JOIN $con->catalogo.catalogo_grupo
				ON catalogo_grupo.IDGRUPO = retencion.IDGRUPO	
				  LEFT JOIN $con->catalogo.catalogo_entidad ent1
					ON ent1.cveentidad1 = catalogo_afiliado_persona_ubigeo.cveentidad1
					  AND ent1.cveentidad2 = 0
					  AND ent1.cveentidad3 = 0
					  AND ent1.cveentidad4 = 0
					  AND ent1.cveentidad5 = 0
					  AND ent1.cveentidad6 = 0
					  AND ent1.cveentidad7 = 0
				  LEFT JOIN $con->catalogo.catalogo_entidad ent2
					ON ent2.cveentidad1 = catalogo_afiliado_persona_ubigeo.cveentidad1
					  AND ent2.cveentidad2 = catalogo_afiliado_persona_ubigeo.cveentidad2
					  AND ent2.cveentidad3 = 0
					  AND ent2.cveentidad4 = 0
					  AND ent2.cveentidad5 = 0
					  AND ent2.cveentidad6 = 0
					  AND ent2.cveentidad7 = 0
				  LEFT JOIN $con->catalogo.catalogo_entidad ent3
					ON ent3.cveentidad1 = catalogo_afiliado_persona_ubigeo.cveentidad1
					  AND ent3.cveentidad2 = catalogo_afiliado_persona_ubigeo.cveentidad2
					  AND ent3.cveentidad3 = catalogo_afiliado_persona_ubigeo.cveentidad3
					  AND ent3.cveentidad4 = 0
					  AND ent3.cveentidad5 = 0
					  AND ent3.cveentidad6 = 0
					  AND ent3.cveentidad7 = 0  				  
					  
			WHERE
				/*$ver_cuentas LEFT(RIGHT(FECHARETENCION,8),2)   BETWEEN '".$_POST["cmbhoraini"]."' AND '".$_POST["cmbhorafin"]."' AND*/
				$ver_cuentas LEFT(RIGHT(FECHARETENCION,8),2) AND retencion.IDUSUARIO LIKE '".$_POST["cmbusuario"]."%' and retencion.IDGRUPO like '".$_POST["cmbarearesp"]."%'  AND
				retencion.STATUS_SEGUIMIENTO like '".$_POST["radio"]."%' $reclamos_asistencias ";

		if($_POST["cmbbusqueda"]!="null")
		 {
		 	$busqueda=($_REQUEST["cmbbusqueda"]!=3)?filtradoStr($_REQUEST['txtnombres'],true):filtradoStr($_REQUEST['txtnombres'],false);
			
			if($_POST["cmbbusqueda"] ==1)
			 {
				if(!ctype_digit($_REQUEST["txtnombres"]))		$busqueda="null";		else 	$busqueda=$_REQUEST["txtnombres"];
				$Sql=$Sql." and  retencion.IDRETENCION =".$busqueda." ";
			 } 
			else if($_POST["cmbbusqueda"] ==2) 	
			 {
				$Sql=$Sql." and catalogo_afiliado.CVEAFILIADO ='".$_REQUEST["txtnombres"]."' ";
				$busqueda="null";					
			 }	
			 else if($_POST["cmbbusqueda"] ==3)	
			 {
				$Sql=$Sql." and  MATCH( catalogo_afiliado_persona.NOMBRE, catalogo_afiliado_persona.APPATERNO, catalogo_afiliado_persona.APMATERNO ) AGAINST('".$busqueda."' IN BOOLEAN MODE) ";	
			 }	
			else if($_POST["cmbbusqueda"] ==4)	
			 {
				$Sql=$Sql." and catalogo_afiliado_persona_telefono.NUMEROTELEFONO like '".$busqueda."%' ";	
			 }		
		 }
		 else
		 {
			$Sql_cantidad="SELECT
						   COUNT(*)
						FROM $con->temporal.retencion
						  INNER JOIN $con->catalogo.catalogo_afiliado
							ON catalogo_afiliado.IDAFILIADO = retencion.IDAFILIADO
						  INNER JOIN $con->catalogo.catalogo_afiliado_persona
							ON catalogo_afiliado_persona.IDAFILIADO = catalogo_afiliado.IDAFILIADO
						  LEFT JOIN $con->catalogo.catalogo_cuenta
							ON catalogo_cuenta.IDCUENTA = retencion.IDCUENTA
						  LEFT JOIN $con->catalogo.catalogo_programa
							ON catalogo_programa.IDPROGRAMA = retencion.IDPROGRAMA
						  LEFT JOIN $con->catalogo.catalogo_detallemotivollamada
							ON catalogo_detallemotivollamada.IDDETMOTIVOLLAMADA = retencion.IDDETMOTIVOLLAMADA
						  LEFT JOIN $con->catalogo.catalogo_afiliado_persona_telefono
							ON catalogo_afiliado_persona_telefono.IDAFILIADO = catalogo_afiliado_persona.IDAFILIADO
						  LEFT JOIN $con->catalogo.catalogo_afiliado_persona_ubigeo
							ON catalogo_afiliado_persona_ubigeo.IDAFILIADO = catalogo_afiliado.IDAFILIADO
						  LEFT JOIN $con->catalogo.catalogo_grupo
							ON catalogo_grupo.IDGRUPO = retencion.IDGRUPO					
						WHERE catalogo_afiliado.AFILIADOVISIBLE=0 AND $ver_cuentas /* LEFT(RIGHT(FECHARETENCION,8),2)  BETWEEN '".$_POST["cmbhoraini"]."' AND '".$_POST["cmbhorafin"]."'*/ AND
							retencion.IDUSUARIO like '".$_POST["cmbusuario"]."%' and retencion.IDGRUPO like '".$_POST["cmbarearesp"]."%'  AND
							retencion.STATUS_SEGUIMIENTO like '".$_POST["radio"]."%'  ";
				
				for( $i = 1 ; $i <= 4 ; $i ++){

					$busqueda=($i!=3 )?filtradoStr($_REQUEST['txtnombres'],true):filtradoStr($_REQUEST['txtnombres'],false);
 
					if($i ==1 and $_REQUEST["txtnombres"])
					 {
						if(!ctype_digit($_REQUEST["txtnombres"]))		$busqueda="";		else 	$busqueda=$_REQUEST["txtnombres"];
						$Sqlcondicion=" and retencion.IDRETENCION ='".$busqueda."'";					
					 } 
					else if($i==2 and $_REQUEST["txtnombres"]) 	
					 {			
						$Sqlcondicion=" and catalogo_afiliado.CVEAFILIADO like '".$_REQUEST["txtnombres"]."%'";						 
					 }	
					else if($i ==3)	
					 {
						$Sqlcondicion=" and MATCH( catalogo_afiliado_persona.NOMBRE, catalogo_afiliado_persona.APPATERNO, catalogo_afiliado_persona.APMATERNO ) AGAINST('".$busqueda."' IN BOOLEAN MODE) ";	
						 
					 }	
					else if($i ==4) 	
					 {
						$Sqlcondicion=" and catalogo_afiliado_persona_telefono.NUMEROTELEFONO like '$busqueda%' ";	
					 }
 
					$resp_cantidad=$Sql_cantidad.$Sqlcondicion." GROUP BY catalogo_afiliado.IDCUENTA LIMIT 1";

					$rs_busqueda=$con->query($resp_cantidad);
					if($rs_busqueda->num_rows*1 > 0)	
					 {
						$Sql_condicion= $Sqlcondicion;
						
						break;
					 }
 
					 $Sqlcondicion="";					
				}
				
				if($Sql_condicion=="" and $_REQUEST["txtnombres"])	$Sql_condicion=" and catalogo_afiliado.IDAFILIADO ='null' ";                
		  }  
		  
			if($_POST["chkbuscarvicio"]) $Sql=$Sql." and catalogo_afiliado.PERSONAVICIO=1 ";  
			
			foreach($_POST['chkprueba'] as $indice => $nombre){
		
				$nombrev=$nombrev."'".$nombre."',";
				$varls[$nombre]=$nombre;				
			}

			$nombrev=substr($nombrev,0,strlen($nombrev)-1);
			if($nombrev)	$Sql=$Sql."and retencion.MOTIVOLLAMADA in($nombrev)";

		if(trim($_POST["fechaini"]) and trim($_POST["fechafin"]))	$Sql=$Sql." and LEFT(retencion.FECHARETENCION,10) BETWEEN '".$_POST["fechaini"]."' AND '".$_POST["fechafin"]."' ";
		
		$Sql=$Sql.$Sql_condicion." GROUP  BY retencion.IDRETENCION ORDER BY retencion.IDRETENCION DESC ";	

	    if($_POST["btnbuscar"])	 $result=$con->query($Sql." LIMIT 100 ");  
		// echo $Sql;
	    $result=$con->query($Sql);  
		$numreg=$result->num_rows*1;
 
		if($_POST["btnexportar"])	include_once("exportar_ret.php");
		
		$quitar= array(',','  ','%','\'','/','\\');
		$txtnombre=trim(str_replace($quitar, "",$_REQUEST['txtnombres']));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=_("SAC - Reporte");?></title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	<link rel="stylesheet" href="../../../../estilos/tablas/pagination.css" media="all">
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>	
	<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/soaa.ico">	
	<style type="text/css"> 
	<!--
	.style5 {color: #000000}
	-->
	</style> 
		
	<script type="text/javascript">
		
		function validarIngreso(variable){
		
/*            if(document.form1.fechaini.value > document.form1.fechafin.value){
                  //alert("LA FECHA FINAL DEBE SER MAYOR.");
                 // document.form1.fechafin.focus();
                  //return (false);
           }		                
           else if(document.form1.cmbhoraini.value > document.form1.cmbhorafin.value){
                  //alert("LA HORA FINAL DEBE SER MAYOR.");
                 // document.form1.cmbhorafin.focus();
                  //return (false);
           }	   
		
			return (true);     */
		}
		
		function limpiar_busquedaCheck(marca){		
			if(marca ==1){
				$('chkbajaservicio').checked=false;
				$('chkcambiop').checked=false;
				$('chkgeneralidad').checked=false;
				$('chkreactivacion').checked=false;
				$('chkreintegro').checked=false;
				$('chkreclamos').checked=false;			
				$('chkdesafiliacion').checked=false;			
			} else{
				$('chkreclamoAsistencia').checked=false;				
			}
		}
	</script>
 
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
	<style type="text/css">
	@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");
	.style6 {color: #003366}
    </style> 	
</head>
<body>
<?
//visualizar el logo de pruebas
    if($con->logoMensaje){
?>
	<div  id='en_espera'><? include("../../avisosystem.php");?> </div><br> 
<? } ?>
<div class="pagination"><a href="buscarafiliado.php"><?=_("Nueva Busqueda") ;?></a><a href="newafiliado.php"><?=_("Contacto No V&aacute;lido") ;?></a><span class="current"><?=_("Reporte") ;?></span><a href="estadisticas.php"><?=_("Estad&iacute;stica") ;?></a></div>
<h2 class="Box"><?=_("REPORTE GENERAL") ;?></h2>

<form id="form1" name="form1" method="post" action="" onSubmit = "return validarIngreso(this)">
	<input type="hidden" name="txtcodprograma" value="<?=$reg->IDPROGRAMA;?>"/>
	<table width="983" border="0" cellpadding="1" cellspacing="1" style="border:1px dashed #999999">
		<tr>
			<td colspan="4"><strong><?=_("Busqueda historica") ;?></strong></td>
		</tr>
		<tr>
			<td> <?=_("USUARIO") ;?></td>
			<td colspan="2"><? $con->cmbselectdata("SELECT IDUSUARIO,CONCAT(APELLIDOS,' ',NOMBRES) FROM catalogo_usuario WHERE ACTIVO=1 ORDER BY APELLIDOS","cmbusuario",(isset($_POST["cmbusuario"]))?$_POST["cmbusuario"]:$_SESSION["user"]," onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","","TODOS >>>"); ?></td>
			<td>
				<input type="checkbox" name="chkbuscarvicio" id="chkbuscarvicio" <?=($_POST["chkbuscarvicio"])?"checked":"";?> />
				<strong><span class="style6"><?=_("BUSCAR COMO VICIO") ;?></span></strong>
			</td>
		</tr>		
		<tr>
			<td width="111"><?=_("CUENTA") ;?></td>
			<td width="411">
        <?
			if($allcuentas==1)	$sql="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta ORDER BY NOMBRE"; else $sql=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";			
			$con->cmbselectdata($sql,"cmbcuenta",$_REQUEST["cmbcuenta"],"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this)' ","","TODOS >>>");			
		?>		
			</td>
			<td width="169"><?=_("RESP.TRATAMIENTO") ;?></td>
			<td width="274">
				<?								
					$sql="select IDGRUPO,NOMBRE from catalogo_grupo order by NOMBRE";
					$con->cmbselectdata("select IDGRUPO,NOMBRE from catalogo_grupo order by NOMBRE","cmbarearesp",$_POST["cmbarearesp"],"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","","TODOS >>>");
				?>
			</td>
		</tr>
		<tr>
			<td><?=_("FECHA DE") ;?></td>
			<td colspan="3">
				<input name="fechaini" id="fechaini" type="text" size="14" class='classtexto' readonly onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' value="<?=($_POST['fechaini'])?$_POST['fechaini']:date("Y-m-d");?>" ><button type="reset" id="cal-button-1">...</button>
				<script type="text/javascript">
					Calendar.setup({
						inputField     :    "fechaini",      // id of the input field
						ifFormat       :    "%Y-%m-%d",       // format of the input field
						showsTime      :    false,            // will display a time selector
						button         :    "cal-button-1",   // trigger for the calendar (button ID)
						singleClick    :    true,           // double-click mode
						step           :    1                // show all years in drop-down boxes (instead of every other year as default)
					});
				</script>
				<?=_("AL") ;?>
				<input name="fechafin" id="fechafin" type="text" size="14" class='classtexto' readonly onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' value="<?=($_POST['fechafin'])?$_POST['fechafin']:date("Y-m-d");?>" ><button type="reset" id="cal-button-2">...</button>
				<script type="text/javascript">
					Calendar.setup({
						inputField     :    "fechafin",      // id of the input field
						ifFormat       :    "%Y-%m-%d",       // format of the input field
						showsTime      :    false,            // will display a time selector
						button         :    "cal-button-2",   // trigger for the calendar (button ID)
						singleClick    :    true,           // double-click mode
						step           :    1                // show all years in drop-down boxes (instead of every other year as default)
					});
				</script>		
			</td>
			<!--td><?=_("HORAS ENTRE") ;?></td>
			<td>            
				<?							
					//$con->cmb_array("cmbhoraini",$combo_horas,($_POST["cmbhoraini"])?$_POST["cmbhoraini"]:"00"," class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1");
				?>
				y
				<?							
					//$con->cmb_array("cmbhorafin",$combo_horas,($_POST["cmbhorafin"])?$_POST["cmbhorafin"]:"23"," class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1");
				?>   
			</td-->
		</tr>
		<tr>
			<td><?=_("BUSQUEDA POR") ;?>:</td>
			<td colspan="2">
				<label>
				<select name="cmbbusqueda" id="cmbbusqueda" class="classtexto" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);">
					<option value="null" <? if ($_POST["cmbbusqueda"]==0)	echo "selected";?>><?=_("TODOS >>>") ;?></option>
					<option value="1" <? if ($_POST["cmbbusqueda"]==1)	echo "selected";?>><?=_("# CASO") ;?></option>
					<option value="2" <? if ($_POST["cmbbusqueda"]==2)	echo "selected";?>><?=_("IDENTIFICADOR") ;?></option>
					<option value="3" <? if ($_POST["cmbbusqueda"]==3)	echo "selected";?>><?=_("NOMBRE/APELLIDO") ;?></option>
					<option value="4" <? if ($_POST["cmbbusqueda"]==4)	echo "selected";?>><?=_("TELEFONO") ;?></option>
				</select>
				</label>
				<label><input name="txtnombres" type="text" class="classtexto" id="txtnombres" value="<?=$txtnombre;?>" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="60" /></label>
			</td>
			<td>
				<input type="submit" name="btnbuscar" id="btnbuscar" value="<?=_("BUSCAR") ;?>"  style="font-size:11px;width:100px"/>
				<input type="submit" name="btnexportar" id="btnexportar" value="<?=_("EXPORTAR") ;?>" style="font-weight:bold;font-size:11px;width:90px;"/>
			</td>
		</tr>
	</table>
	<table width="85%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFEC">
		<tr>
			<td width="140" bgcolor="#bbbbbb"><input name="chkprueba[]" type="checkbox" id="chkbajaservicio" value="BAJASERVICIO" onclick="limpiar_busquedaCheck(2)"  <? if($varls["BAJASERVICIO"])	echo "checked"; ?>/><strong><?=_("Baja Servicio") ;?></strong></td>
			<td width="140" bgcolor="#bbbbbb"><input name="chkprueba[]" type="checkbox" id="chkcambiop" value="CAMBIOPROGRAMA" onclick="limpiar_busquedaCheck(2)" <? if($varls["CAMBIOPROGRAMA"])	echo "checked"; ?>/><strong><?=_("Cambio de Plan") ;?></strong></td>
			<td width="185" bgcolor="#bbbbbb" colspan="2"><input name="chkprueba[]" type="checkbox" id="chkgeneralidad" value="GENERALIDAD" onclick="limpiar_busquedaCheck(2)" <? if($varls["GENERALIDAD"])	echo "checked"; ?>/><strong><?=_("Generalidad") ;?></strong></td>
			<td width="244" rowspan="2">	  
				<table width="410" border="0" cellpadding="1" cellspacing="1" bgcolor="#666666">
					<tr>
						<td width="285" bgcolor="#EEEEEE"><input type="radio" name="radio" id="radio" value="PRO"  <? if($_POST["radio"]=="PRO")	echo "checked"; ?> /><?=_("En Proceso") ;?></td>
						<td width="295" bgcolor="#EEEEEE"><input name="radio" type="radio" id="radio3" value=""  <? if($_POST["radio"]=="")	echo "checked"; ?> /><?=_("Todos") ;?></td>
					    <td width="295" rowspan="2" bgcolor="#470707" style="color:#FFFFFF"><input name="chkreclamoAsistencia" type="checkbox" id="chkreclamoAsistencia" value="RECLAMO_ASIS" title="Solo Reclamos realizados por las Asistencias" onclick="limpiar_busquedaCheck(1)" <? if($_POST["chkreclamoAsistencia"])	echo "checked"; ?>/><?=_("Reclamos de Asist.") ;?></td>
					</tr>
					<tr>
						<td bgcolor="#EEEEEE"><input type="radio" name="radio" id="radio2" value="CON"  <? if($_POST["radio"]=="CON")	echo "checked"; ?>/><?=_("Concluido") ;?></td>
						<td bgcolor="#EEEEEE"><input type="radio" name="radio" id="radio4" value="REC"  <? if($_POST["radio"]=="REC")	echo "checked"; ?>/><?=_("Recibido") ;?></td>
				    </tr>
				</table>
			</td>
		</tr>
		<tr bgcolor="#aaaaaa">
			<td><input name="chkprueba[]" type="checkbox" id="chkreactivacion" value="REACTIVACION" onclick="limpiar_busquedaCheck(2)" <? if($varls["REACTIVACION"])	echo "checked"; ?>/><strong><?=_("Reactivacion") ;?></strong></td>
			<td><input name="chkprueba[]" type="checkbox" id="chkreintegro" value="REINTEGRO" onclick="limpiar_busquedaCheck(2)" <? if($varls["REINTEGRO"])	echo "checked"; ?>/><strong><?=_("Reitegro") ;?></strong></td>
			<td><input name="chkprueba[]" type="checkbox" id="chkreclamos" <? if($varls["QUEJASRECLAMO"]) echo "checked"; ?> onclick="limpiar_busquedaCheck(2)" value="QUEJASRECLAMO" /><strong><?=_("Reclamos") ;?></strong></td>
			<td style="color:#FFFFFF;font-weight:bold" bgcolor="#1a2225"><input name="chkprueba[]" type="checkbox" id="chkdesafiliacion" <? if($varls["DESAFILIACION"]) echo "checked"; ?> onclick="limpiar_busquedaCheck(2)" value="DESAFILIACION" /><strong><?=_("Desafiliacion AON") ;?></strong></td>
		</tr>
	</table>
</form>  
	<table width="100%" border="0" cellpadding="1" cellspacing="1" >
		<tr>
			<td style="border:1px solid #82C0FF;" bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("#") ;?></strong></div></td>
			<td style="border:1px solid #82C0FF;" bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("CASO") ;?></strong></div></td>
			<td bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("CVE") ;?></strong></div></td>			
			<td style="border:1px solid #82C0FF;" bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("NOMBRES") ;?></strong></div></td>
			<td style="border:1px solid #82C0FF;" bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("FECHAHORA") ;?></strong></div></td>
			<td style="border:1px solid #82C0FF;" bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("HORA") ;?></strong></div></td>
			<td style="border:1px solid #82C0FF;" bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("USUARIO") ;?></strong></div></td>
			<td style="border:1px solid #82C0FF;" bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("TELEFONO1") ;?></strong></div></td>
			<td style="border:1px solid #82C0FF;" bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("TELEFONO2") ;?></strong></div></td>
			<td bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("CUENTA") ;?></strong></div></td>
			<td bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("PLAN") ;?></strong></div></td>
			<td bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("STATUS") ;?></strong></div></td>
			<td bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("GESTION") ;?></strong></div></td>
			<td bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("MOTIVO") ;?></strong></div></td>
			<td bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("AREARESP.") ;?></strong></div></td>
			<td bgcolor="#A8D3FF"><div align="center" class="style5"><strong><?=_("EXP./ASIS.") ;?></strong></div></td>
			<td bgcolor="#A8D3FF"><div align="center" class="style5"></div></td>
		</tr>	
		 <?
			
			if($_POST["btnbuscar"]){
				
				while($reg = $result->fetch_object()){
					if($c%2==0) $fondo='er'; 
					if($c%2==0) $clase='trbuc3'; else $clase='trdir3';

					$id=$id+1;
					
					if($reg->MOTIVOLLAMADA =="DESAFILIACION"){					
						$resp_motivoLlamada=$con->consultation("SELECT DESCRIPCION from $con->catalogo.catalogo_motivoscancelacionAON WHERE IDMOTIVOCANCELACION=$reg->IDDETMOTIVOLLAMADA");
						$reg->DETMOTIVOLLADA=$resp_motivoLlamada[0][0];
						$desafiliacionOk="&desafiliacionOk=1";
					}
		?>		
		<tr bgcolor=<?=$fondo;?> class='<?=$clase;?>' style="cursor:pointer" title="<?=$reg->IDRETENCION." - ".$reg->nombres;?>" ondblclick="window.open('detalle.php?idcaso=<?=$reg->IDRETENCION.$desafiliacionOk;?>','detalle','resizable=no,location=1,status=1,scrollbars=1,width=1200,height=500');"  >
			<td style="border:1px solid #82C0FF;"><div align="center"><?=$id;?></div></td>
			<td style="text-align:center"><?=$reg->IDRETENCION;?></td>
			<td style="text-align:center"><?=$reg->CVEAFILIADO;?></td>
			<td><?=utf8_encode($reg->nombres);?></td>
			<td style="text-align:center"><?=$reg->FECHA;?></td>			
			<td style="text-align:center"><?=$reg->HORAMIN;?></td>			
			<td style="text-align:center"><?=$reg->IDUSUARIO;?></td>			
			 <?
				$Sql_tel="SELECT
						  catalogo_afiliado_persona_telefono.NUMEROTELEFONO
						FROM catalogo_afiliado_persona_telefono
						  INNER JOIN catalogo_afiliado_persona
							ON catalogo_afiliado_persona.IDAFILIADO = catalogo_afiliado_persona_telefono.IDAFILIADO
						WHERE catalogo_afiliado_persona_telefono.IDAFILIADO = '".$reg->IDAFILIADO."'
						ORDER BY catalogo_afiliado_persona_telefono.PRIORIDAD LIMIT 2";				
				
				$resultel=$con->query($Sql_tel);
				while($row = $resultel->fetch_object()){
					$ii=$ii+1;
					$telefono[$ii]=$row->NUMEROTELEFONO;
				}
					$ii=0;
				
				for ($i=1;$i<=2;$i++){
			?>
			<td><?=$telefono[$i];?></td>
			<?		
					$telefono[$i]="";
				}
				 
				if($reg->STATUS_SEGUIMIENTO=="")	$reg->STATUS_SEGUIMIENTO="S/D";
			?>
			<td><?=$reg->CUENTA;?></td>
			<td><?=$reg->PLAN;?></td>
			<td style="text-align:center"  bgcolor='<?=$color;?>'><div align="center"><strong><?=$statusproceso_sac[$reg->STATUS_SEGUIMIENTO];?></strong></div></td>
			<td style="text-align:center"><div align="center"><?=$reg->MOTIVOLLAMADA;?></div></td>
			<td><?=utf8_encode($reg->DETMOTIVOLLADA);?></td>
			<td style="text-align:center"><div align="center"><?=($reg->NOMSAC)?$reg->NOMSAC:"S/D";?></div></td>
			<td><?=utf8_encode($reg->RECLA_ASIS);?></td>
			<td><div align="center"><input type="button" name="button" id="button" value="<?=_("DETALLE") ;?>" style="font-weight:bold;font-size:9px;" onclick="window.open('detalle.php?idcaso=<?=$reg->IDRETENCION.$desafiliacionOk;?>','detalle','resizable=no,location=1,status=1,scrollbars=1,width=1200,height=500');" /></div></td>
 		</tr>	
		<?
				$c=$c+1;
				$stylo="";
				$desafiliacionOk="";
			 }
		  }
		?>
	</table>	
</body>
</html>