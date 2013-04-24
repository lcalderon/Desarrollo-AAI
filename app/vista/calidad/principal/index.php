<?
    session_start();
		
	include_once("../../../modelo/clase_lang.inc.php");
	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../modelo/functions.php");
	include_once("../../includes/arreglos.php");
	include_once("../../../vista/login/Auth.class.php");
    
	Auth::required($_SERVER['REQUEST_URI']);
    
	$con = new DB_mysqli();	
	if ($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}	

	list($allcuentas,$sql_cuentaresp,$idcuentas)=accesos_cuentas($_SESSION["user"]);
	
	$arrstatusasistencia = ($_POST["cmbstatusasistencia"])?$_POST["cmbstatusasistencia"]:"CON','CP','CM";

	$marcadoCuenta="checked";
	$marcadoUsuario="checked";

	//variable array cuenta
		if(isset($_POST["todascuentas"]) ==0){
			
			foreach($_POST["cmbcuenta"] as $nombrecuenta){
			
				$seleccionadoCue[]=$nombrecuenta;
				$arrcuentas[]="'".$nombrecuenta."'";
				$marcadoCuenta="";
			
				$idcuentas =(isset($arrcuentas))?implode(",",$arrcuentas):'';
			}
						
		} else{
			$marcadoCuenta="checked";
		}

	//variable array usuario

		if($_POST["todousuarios"] ==0){
			
			foreach($_POST["cmbusuario"] as $nombreusuario){
		
				$arrSeleccionadoUsu[]=$nombreusuario;
				$arrusuarios[]="'".$nombreusuario."'";
				$marcadoUsuario="";
			
				$idusuarios =(isset($arrusuarios))?implode(',',$arrusuarios):'';
			}

		} else{
			$marcadoUsuario="checked";
		}

	//lista de asistencias  a presentar en el grid 
		//if($_POST["cmbBusquedapor"] =="nombreafi" and $_POST["txtbuscador"]!="") $havingAfiliado=" HAVING NOM_AFILIADO LIKE '%".$_POST["txtbuscador"]."%' ";
		if($_POST["cmbBusquedapor"] =="codigoexp" and $_POST["txtbuscador"]!=""){
			$condicion=" AND expediente.IDEXPEDIENTE='".$_POST["txtbuscador"]."'";
		} else if($_POST["cmbBusquedapor"] =="codigoasi" and $_POST["txtbuscador"]!=""){
			$condicion=" AND asistencia.IDASISTENCIA='".$_POST["txtbuscador"]."'";		
		} else{
			$condicion="
				AND expediente_usuario.ARRTIPOMOVEXP='REG'
				AND expediente.ARRSTATUSEXPEDIENTE ='CER' 
				AND asistencia.REEMBOLSO LIKE '".$_POST["chkreembolso"]."%'
				AND DATE(expediente_usuario.FECHAHORA) BETWEEN '".$_POST["fechaini"]."' AND '".$_POST["fechafin"]."'
				AND asistencia.ARRCONDICIONSERVICIO LIKE '".$_POST["cmbcondicionser"]."%'
				AND asistencia.EVALAUDITORIA LIKE '".$_POST["cmbauditoria"]."%'
				AND asistencia.ARRSTATUSENCUESTA LIKE '".$_POST["cmbencuesta"]."%'
				AND asistencia.STATUSCALIDAD LIKE '".$_POST["cmbevalExpediente"]."%'";
		}		

		if($arrstatusasistencia) $condicion.= " AND asistencia.ARRSTATUSASISTENCIA IN('$arrstatusasistencia')";

		if((count($seleccionadoCue) >0 and !$marcadoCuenta) || $allcuentas ==0) $condicion.= " AND catalogo_cuenta.IDCUENTA in ($idcuentas)";
		//echo $condicion;
		if(count($arrSeleccionadoUsu) >0 and !$marcadoUsuario) $condicion.= " AND asistencia.IDUSUARIORESPONSABLE in ( $idusuarios)";

		$Sql_calidad="SELECT
			  /* BUSCAR EXPEDIENTE CALIDAD */ 					  
			  asistencia.IDASISTENCIA,
			  asistencia.IDEXPEDIENTE,
			  expediente.ARRSTATUSEXPEDIENTE,
			  asistencia.IDCUENTA,
			  asistencia.IDPROGRAMASERVICIO,
			  asistencia.IDSERVICIO,
			  catalogo_servicio.DESCRIPCION  AS AA_SERVICIO,
			  catalogo_programa_servicio.ETIQUETA NOM_SERVICIO,
			  asistencia.IDPROGRAMA,
			  asistencia.ARRSTATUSASISTENCIA,
			  asistencia.ARRPRIORIDADATENCION,
			  asistencia.ARRCONDICIONSERVICIO,
			  asistencia.IDETAPA,
			  catalogo_etapa.DESCRIPCION AS NOM_ETAPA,
			  asistencia.IDUSUARIORESPONSABLE,
			  asistencia.DESVIACION,
			  asistencia.STATUSAUTORIZACIONDESVIO,
			  asistencia.IDUSUARIOAUTORIZACIONDESVIO,
			  (SELECT  CONCAT(expediente_persona.APPATERNO,' ',expediente_persona.APMATERNO,', ',expediente_persona.NOMBRE) FROM $con->temporal.expediente_persona WHERE expediente_persona.IDEXPEDIENTE=asistencia.IDEXPEDIENTE AND expediente_persona.ARRTIPOPERSONA='TITULAR') AS NOM_AFILIADO,
			  /*MIN(asistencia_usuario.FECHAHORA) AS FECHAHORA,*/
			  expediente_usuario.FECHAHORA as FECHAHORA,
			  asistencia_encuesta_calidad.EVALENCUESTA,
			  asistencia.EVALAUDITORIA,
			  asistencia.ARRSTATUSENCUESTA,
			  asistencia.STATUSCALIDAD
			FROM $con->temporal.asistencia
			  INNER JOIN $con->temporal.expediente
				ON expediente.IDEXPEDIENTE = asistencia.IDEXPEDIENTE
			  INNER JOIN $con->temporal.expediente_usuario 
				ON expediente_usuario.IDEXPEDIENTE = asistencia.IDEXPEDIENTE				
			  INNER JOIN $con->catalogo.catalogo_cuenta
				ON catalogo_cuenta.IDCUENTA = asistencia.IDCUENTA
			  INNER JOIN $con->catalogo.catalogo_servicio
				ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
			  INNER JOIN $con->catalogo.catalogo_programa
				ON catalogo_programa.IDPROGRAMA = asistencia.IDPROGRAMA
			  LEFT JOIN $con->catalogo.catalogo_programa_servicio
				ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO
			  INNER JOIN $con->temporal.expediente_persona
				ON expediente_persona.IDEXPEDIENTE = asistencia.IDEXPEDIENTE
				  AND expediente_persona.ARRTIPOPERSONA = 'TITULAR'
			  INNER JOIN $con->catalogo.catalogo_etapa
				ON catalogo_etapa.IDETAPA = asistencia.IDETAPA
			  LEFT JOIN $con->temporal.asistencia_encuesta_calidad
				ON asistencia_encuesta_calidad.IDASISTENCIA = asistencia.IDASISTENCIA
			  LEFT JOIN $con->temporal.asistencia_usuario
				ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
				  AND asistencia_usuario.IDETAPA = 1
			  WHERE asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE
					$condicion
				GROUP BY asistencia.IDASISTENCIA
				ORDER BY asistencia.IDASISTENCIA DESC  LIMIT 100 ";
				
		//echo $Sql_calidad;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<title>:: <?=_("Gestion Calidad");?></title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/func_global.js"></script>	
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	<link rel="stylesheet" href="../../../../librerias/tinytablev3.0/style.css" />
	
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" ></link> 
	<link href="../../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" ></link>	
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_effects.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_ext.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/favicon.ico">
	<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/soaa.ico">
	
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
	<style type="text/css">
	@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");
	.style6 {color: #003366}
	<!--
	.style3 {color: #FFFFFF; font-weight: bold; }
	html body {
		margin:1px;
		padding:1px;
	}
	-->
	</style>			 
</head>
<body>

	<form name='datos_busqueda'  id='datos_busqueda' method="post" action="<?=$_SEVER['PHP_SELF'];?>">
		<table width="100%" border="0" bgcolor="#484848">
			<tr>
				<td width="90%" style="color:#FFFFFF" colspan="3"><font size="4px"><strong><?=_("GESTION CALIDAD: EVALUACION DE EXPEDIENTES")?></strong></font></td>
			</tr>
		</table>

		<div id="tablewrapper">   
			<div id="tableheader">
				<div class="search" style="width:100%">						 
					<table width="100%" border="0" cellpadding="1" cellspacing="1" background="../../../../imagenes/logos/fondotabla_1.png">
						<tr>
							<td colspan="2" class="busqueda"><?=_("BUSQUEDA POR")?></td>
							<td width="21%" class="busqueda"><?=_("STATUS ASISTENCIA")?></td>
							<td width="15%" class="busqueda"><?=_("CONDISERV")?></td>
							<td width="15%" class="busqueda"><?=_("AUDITORIA")?></td>
							<td width="16%" class="busqueda"><?=_("ENCUESTA")?></td>
						</tr>    
						<tr>
							<td width="07%" class="busqueda">
									<select id="columns"  onchange="sorter.search('query')"    style='display:none'></select>
									<input type="hidden" id="query" size='40' onKeyUp="sorter.search('query')" />								
									
									<select name="cmbBusquedapor" id="cmbBusquedapor" class="classtexto" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);">
										<option value="codigoexp" <? if($_POST["cmbBusquedapor"]=="codigoexp")	echo "selected";?> ><?=_("NRO EXPEDIENTE")?></option>
										<option value="codigoasi" <? if($_POST["cmbBusquedapor"]=="codigoasi")	echo "selected";?> ><?=_("NRO ASISTENCIA")?></option>
										<!--option value="nombreafi" <? if($_POST["cmbBusquedapor"]=="nombreafi")	echo "selected";?> ><?=_("AFILIADO")?></option-->
									</select>                           
							</td>                            
							<td width="18%" class="busqueda"><input type="text" name="txtbuscador" id="txtbuscador" class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' size="30" value="<?=$_POST["txtbuscador"]?>" ></td>
							<td>
							<?
								$con->cmb_array("cmbstatusasistencia",$desc_status_asistencia,$_POST["cmbstatusasistencia"],"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","",_("TODOS"),"","PRO");
							?>              
							</td>
							<td>
							<?
								$con->cmb_array("cmbcondicionser",$desc_cobertura_servicio,$_POST["cmbcondicionser"],"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","",_("TODOS"));
							?>
							</td>
							<td><? $con->cmb_array("cmbauditoria",$evalauditoria,$_POST["cmbauditoria"],"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","",_("TODOS")); ?></td>
							<td><? $con->cmb_array("cmbencuesta",$evalencuesta_new,$_POST["cmbencuesta"],"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","",_("TODOS"));?></td>
						</tr>
						<tr>
							<td colspan="2"><?=_('FECHA EXPDIENTE')?></td>
							<td><?=_('ASIS EVAL')?></td>
							<td colspan="3"><?=_('REEMBOLSO')?></td>
						</tr>
						<tr>
							<td><input name="fechaini" id="fechaini" type="text" size="14" class='classtexto' readonly onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' value="<?=($_POST['fechaini'])?$_POST['fechaini']:date("Y-m-d");?>" ><button type="reset" id="cal-button-1">...</button></td>
							<td><?=_('AL')?>&nbsp;&nbsp;<input name="fechafin" id="fechafin" type="text" size="14" class='classtexto' readonly onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' value="<?=($_POST['fechafin'])?$_POST['fechafin']:date("Y-m-d");?>" ><button type="reset" id="cal-button-2">...</button></td>
							<td>
								<? $con->cmb_array("cmbevalExpediente",$evalexped,$_POST["cmbevalExpediente"],"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","",_("TODOS"));?>
							</td>
							<td colspan="3"><input type="checkbox" name="chkreembolso" id="" value="1" <? if($_POST["chkreembolso"]==1)	echo "checked";?> ></td>
						</tr>
						<tr>
							<td colspan="2">
								<?=_('CUENTAS') ?><img src="../../../../imagenes/32x32/Forward.png" width="19" height="12" alt="flecha">
								<?=_('TODOS')?><input type="checkbox"  name='todascuentas' id='todascuentas' value="1" <?=$marcadoCuenta?> onclick="marcar_todasCks('','cmbcuenta','todascuentas')"></input> 
							</td>
							<td colspan="2">
								<?=_('COORDINADORES')?><img src="../../../../imagenes/32x32/Forward.png" width="19" height="12" alt="flecha">
								<?=_('TODOS')?><input type="checkbox"  name='todousuarios' id='todousuarios' value=1 <?=$marcadoUsuario?> onclick="marcar_todasCks('','cmbusuario','todousuarios')"/>
							</td>
							<td colspan="2" rowspan="2" class="busqueda">
								<button type="submit" title="Buscar Registros..." name="btnbuscar"  style="text-align:center;font-weight:bold;font-size:12px;width:130px;height:78px">
									<b><?=_('CONSULTAR ').' >>>'?></b><img src="../../../../imagenes/iconos/buscar.png" alt="BuscarCalidad" border="0">
								</button>
							</td>
						</tr>
						<tr>
							<td colspan="2">										
								<div id="div-cuenta">
									<select name="cmbcuenta[]" id="cmbcuenta" size='7' onclick="desactiva_check('todascuentas')" multiple onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'>							
									<?	
								 
									if($allcuentas==1)	$sqlCuenta="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta ORDER BY NOMBRE"; else $sqlCuenta=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";

									$result=$con->query($sqlCuenta);
										while($reg=$result->fetch_object()){
											if (in_array($reg->IDCUENTA,$seleccionadoCue)) $selectedcue="selected";	else $selectedcue="";
											if(count($seleccionadoCue) <1) $selectedcue="selected"; 
									?>
											<option value="<?=$reg->IDCUENTA;?>" <?=$selectedcue?> ><?=$reg->NOMBRE;?></option>
									<? 
										}
									?>
									</select>
								</div>									 
							</td>                                              
							<td colspan="2">                                             
							<?						 									
									$Sql_usuario="SELECT
										catalogo_usuario.IDUSUARIO,
										CONCAT(
											catalogo_usuario.APELLIDOS,
											', ',
											catalogo_usuario.NOMBRES
										) AS NOMBRES
									FROM
										$con->catalogo.catalogo_usuario									
									INNER JOIN $con->temporal.grupo_usuario ON grupo_usuario.IDUSUARIO = catalogo_usuario.IDUSUARIO
									WHERE grupo_usuario.IDGRUPO='CORD' ";

							?>
								<div id="div-usuario">
									<select name="cmbusuario[]" id="cmbusuario" size='7' onclick="desactiva_check('todousuarios')" multiple onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'>							
									<?	
								 
									$result=$con->query($Sql_usuario);
										while($reg=$result->fetch_object()){
											if (in_array($reg->IDUSUARIO,$arrSeleccionadoUsu)) $selectedusu='selected';	else $selectedusu='';
											if(count($arrSeleccionadoUsu) <1) $selectedusu="selected"; 
									?>
											<option value="<?=$reg->IDUSUARIO;?>" <?=$selectedusu?> ><?=$reg->NOMBRES;?></option>
									<? 
										}
									?>
									</select>
								</div>						 
							</td>
						</tr>									
					</table>
					<script type="text/javascript">
						Calendar.setup({
							inputField     :    "fechaini",       // id of the input field
							ifFormat       :    "%Y-%m-%d",       // format of the input field
							showsTime      :    false,            // will display a time selector
							button         :    "cal-button-1",   // trigger for the calendar (button ID)
							singleClick    :    true,             // double-click mode
							step           :    1                 // show all years in drop-down boxes (instead of every other year as default)
						});
					</script>
					<script type="text/javascript">
						Calendar.setup({
							inputField     :    "fechafin",       // id of the input field
							ifFormat       :    "%Y-%m-%d",       // format of the input field
							showsTime      :    false,            // will display a time selector
							button         :    "cal-button-2",   // trigger for the calendar (button ID)
							singleClick    :    true,             // double-click mode
							step           :    1                 // show all years in drop-down boxes (instead of every other year as default)
						});
					</script>					
				</div>
				<div class="details" style="display:none">
					<div><?=_('Registros')?> <span id="startrecord"></span>-<span id="endrecord"></span> <?=_('de')?> <span id="totalrecords"></span></div>
					<div><a href="javascript:sorter.reset()"> <?=_('reset')?></a></div>
				</div>
			</div>
		</div>
    </form>
	
    <table cellpadding="0" cellspacing="0" border="0" id="table" class="tinytable" width="100%">
        <thead>
            <tr>
				<th width="2%"><h3><?=_("EXP")?></h3></th>
                <th class="desc" width="2%"><h3><?=_("ASIS")?></h3></th>
                <th width="2%"><h3><?=_("CTA")?></h3></th>
				<th width="2%"><h3><?=_("PLAN")?></h3></th>
				<th width="10%"><h3><?=_("AFILIADO")?></h3></th>
				<th width="15%"><h3><?=_("SERVICIO")?></h3></th>
				<th width="2%" title='<?=_('CONDICION DEL SERVICIO')?>'><h3><?=_("CON")?></h3></th>
				<th width="2%" ><h3><?=_("PRI")?></h3></th>
				<th width="2%"><h3><?=_("ESTADO")?></h3></th>
				<th width="5%" title='<?=_('EVALUACION DEL EXPEDIENTE')?>'><h3><?=_("EVAL EXPED")?></h3></th>				
				<th width="5%" title='<?=_('EVALUACION DE LA AUDITORIA')?>'><h3><?=_("EVAL AUDIT")?></h3></th>
				<th width="5%" title='<?=_('EVALUACION DE LA ENCUESTA')?>'><h3><?=_("EVAL ENC")?></h3></th>				
				<th width="2%" title='<?=_('RESPONSABLE DE LA ASISTENCIA')?>'><h3><?=_("RESP.")?></h3></th>
				<th width="2%"><h3><?=_("FECHA")?></h3></th>
				<th class="nosort" width="2%"><h3></h3></th>
				<th class="nosort" width="2%"><h3></h3></th>
                <th class="nosort" width="2%"><h3></h3></th>                  
            </tr>
        </thead>
        <tbody>
        <?    
			$lista_asis=$con->query($Sql_calidad);
            while ($reg=$lista_asis->fetch_object()){
 
                $varexis=crypt($reg->IDEXPEDIENTE,"666"); 
	
                if($reg->ARRSTATUSENCUESTA !="SEVA") $colorenc="style='background-color:#D6F29F'";   else $colorenc="";
                if($reg->EVALAUDITORIA !="SAUDITAR") $coloraud="style='background-color:#D6F29F'";   else $coloraud="";
                if($reg->STATUSCALIDAD !="SEVALUAR") $colordef="style='background-color:#D6F29F'";   else $colordef="";
		?>
			<tr onDblClick="window.open('../../expediente/entrada/expediente_frmexpediente.php?varexis=<?=$varexis?>&idexpediente=<?=$reg->IDEXPEDIENTE?>&gestion=CALIDAD')">
				<td><?=$reg->IDEXPEDIENTE?></td>
                <td><?=$reg->IDASISTENCIA?></td>
                <td><?=$reg->IDCUENTA?></td>
                <td><?=$reg->IDPROGRAMA?></td>
                <td><?=$reg->NOM_AFILIADO?></td>
                <td><?=($reg->NOM_SERVICIO=='')?$reg->AA_SERVICIO:$reg->NOM_SERVICIO?></td>
                <td><?=$reg->ARRCONDICIONSERVICIO?></td>
                <td><?=$reg->ARRPRIORIDADATENCION?></td>
                <td><?=$desc_status_asistencia[$reg->ARRSTATUSASISTENCIA]?></td>
                <td align='center' <?=$colordef?> ><?=$evalexped[$reg->STATUSCALIDAD]?></td>
                <td align='center' <?=$coloraud?> ><?=$evalauditoria[$reg->EVALAUDITORIA]?></td>
                <td align='center' <?=$colorenc?> ><?=$evalencuesta_new[$reg->ARRSTATUSENCUESTA]?></td>				
                <td><?=$reg->IDUSUARIORESPONSABLE?></td>
                <td align='center'><?=$reg->FECHAHORA?></td>
                <td align='center' <?=$colordef?> ><img src='/imagenes/iconos/evaluar.gif' title='<?=_("EVALUAR DEFICIENCIA")?>' align='absbottom' border='0' style='cursor: pointer;' onclick="window.open('../../expediente/entrada/expediente_frmexpediente.php?varexis=<?=$varexis?>&idexpediente=<?=$reg->IDEXPEDIENTE?>&gestion=CALIDAD')" ></td>
                <td align='center' <?=$coloraud?> ><img align='absbottom' width='22px' height='22px' title='<?=_("EVALUAR AUDITORIA")?>' src='/imagenes/iconos/icon_audio.jpg'  border='0' style='cursor: pointer;' id='btn_audito' onClick="presentar_formulario('','gestionarAuditoria.php','<?=_("CIERRE LA VENTANA ANTERIOR")?>','<?=_("GESTION AUDITORIA")?>','868','435','','','<?=$reg->IDASISTENCIA?>')"></td>
                <td align='center' <?=$colorenc?> ><img align='absbottom' title='<?=_("EVALUAR ENCUESTAR")?>' src='/imagenes/iconos/encuestar.gif'  border='0' style='cursor: pointer;' id='btn_audito' onClick="presentar_formulario('','gestionarEncuesta.php','<?=_("CIERRE LA VENTANA ANTERIOR")?>','<?=_("GESTION ENCUESTA")?>','890','473','','<?=$reg->IDEXPEDIENTE?>','<?=$reg->IDASISTENCIA?>')"></td>
			</tr>
        <? 
            }
        ?>
        </tbody>    
    </table>
        
		<div id="tablefooter">
			<div id="tablenav">
				<div>
					<img src="/librerias/tinytablev3.0/images/first.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1,true)"/>
					<img src="/librerias/tinytablev3.0/images/previous.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1)"/>
					<img src="/librerias/tinytablev3.0/images/next.gif" width="16" height="16" alt="First Page" onclick="sorter.move(1)" />
					<img src="/librerias/tinytablev3.0/images/last.gif" width="16" height="16" alt="Last Page" onclick="sorter.move(1,true)"/>
				</div>
				<div style="display:none"><select id="pagedropdown"></select></div>
				<div style="display:none"><a href="javascript:sorter.showall()"><?=_('Ver Todos')?></a></div>
			</div>
			<div id="tablelocation">
				<div style="display:none">
					<select onchange="sorter.size(this.value)">
						<option value="5">5</option>
						<option value="10" selected="selected">10</option>
						<option value="20">20</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select>
					<span><?=_('Entradas por pagina')?></span>
				</div>
				<div class="page"><?=_('Pagina')?><span id="currentpage"></span> <?=_('de') ?><span id="totalpages"></span></div>
			</div>
		</div>
		
	<script type="text/javascript" src="../../../../librerias/tinytablev3.0/script.js"></script>	
	<script type="text/javascript">
		var sorter = new TINY.table.sorter('sorter','table',{
			headclass:'head',
			ascclass:'asc',
			descclass:'desc',
			evenclass:'evenrow',
			oddclass:'oddrow',
			evenselclass:'evenselected',
			oddselclass:'oddselected',
			paginate:true,
			size:10,
			colddid:'columns',
			currentid:'currentpage',
			totalid:'totalpages',
			startingrecid:'startrecord',
			endingrecid:'endrecord',
			totalrecid:'totalrecords',
			hoverid:'selectedrow',
			pageddid:'pagedropdown',
			navid:'tablenav',
			sortcolumn:1,
			sortdir:1,
			//sum:[8],
			//avg:[6,7,8,9],
			//columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],
			init:true
		});
	</script>  
</body>
</html> 

