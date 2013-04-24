<?php
 
	session_start(); 
 
	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once('../../../modelo/functions.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once('../../includes/arreglos.php');
	
	$con= new DB_mysqli();
	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

//verificar login.
	Auth::required($_SERVER['REQUEST_URI']);

//verficar acceso al formulario.
 	validar_permisos("DELEGAR_ASISTENCIA",1);
	
//verificar permisos de accesos a las cuentas.	
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);

//buscar los usuarios perntenecientes al grupo.

	$sql="SELECT IDGRUPO FROM $con->temporal.grupo_usuario where IDUSUARIO='".$_SESSION["user"]."'";
	$rsusuaario=$con->query($sql);
	while($regusu = $rsusuaario->fetch_object())	$accesos[$regusu->IDGRUPO]=$regusu->IDGRUPO;
	  
	if($_POST["cmbusuariobus"] and  $accesos["SUCA"]) $userbusqueda=$_POST["cmbusuariobus"]; else $userbusqueda=$_SESSION["user"]; 
	
//Sql de asistencias actuales segun el responsable actual.
	$Sql_expedientes="SELECT
					/* CONSULTA DELEGAR RESPONSABLE */
					  a.IDASISTENCIA,
					  a.IDEXPEDIENTE,
					  e.ARRSTATUSEXPEDIENTE,
					  a.IDCUENTA,
					  catalogo_cuenta.NOMBRE       NOM_CUENTA,
					  a.IDPROGRAMASERVICIO,
					  a.IDSERVICIO,
					  a.USUARIODELEGAHACIA,
					  a.DELEGAR,
					  cs.DESCRIPCION                   AA_SERVICIO,
					  cps.ETIQUETA                     NOM_SERVICIO,
					  a.IDPROGRAMA,
					  cp.NOMBRE                        NOM_PROGRAMA,
					  a.ARRSTATUSASISTENCIA,
					  a.ARRPRIORIDADATENCION,
					  a.ARRCONDICIONSERVICIO,
					  a.IDETAPA,
					  ce.DESCRIPCION                   NOM_ETAPA,
					  a.IDUSUARIORESPONSABLE,
					  a.DESVIACION,
					  a.STATUSAUTORIZACIONDESVIO,
					  a.IDUSUARIOAUTORIZACIONDESVIO,
					  CONCAT(ep.APPATERNO,' ',ep.APMATERNO,' ',ep.NOMBRE)    NOM_AFILIADO,
					  MIN(au.FECHAHORA)                FECHAHORA
					FROM ($con->temporal.asistencia a,
					   $con->temporal.expediente e,
					   $con->catalogo.catalogo_usuario cu)
					  LEFT JOIN $con->catalogo.catalogo_cuenta 
						ON catalogo_cuenta.IDCUENTA = a.IDCUENTA
					  LEFT JOIN $con->catalogo.catalogo_programa_servicio cps
						ON cps.IDPROGRAMASERVICIO = a.IDPROGRAMASERVICIO
					  LEFT JOIN $con->catalogo.catalogo_servicio cs
						ON cs.IDSERVICIO = a.IDSERVICIO
					  LEFT JOIN $con->catalogo.catalogo_programa cp
						ON cp.IDPROGRAMA = a.IDPROGRAMA
					  LEFT JOIN $con->catalogo.catalogo_etapa ce
						ON ce.IDETAPA = a.IDETAPA
					  LEFT JOIN $con->temporal.expediente_persona ep
						ON ep.IDEXPEDIENTE = a.IDEXPEDIENTE
						  AND ep.ARRTIPOPERSONA = 'TITULAR'
					  LEFT JOIN $con->temporal.asistencia_usuario au
						ON au.IDASISTENCIA = a.IDASISTENCIA
						  AND au.IDETAPA = 1
					WHERE $ver_cuentas
						a.IDEXPEDIENTE = e.IDEXPEDIENTE
						AND e.ARRSTATUSEXPEDIENTE = 'PRO'
						AND a.ARRSTATUSASISTENCIA ='PRO'
						AND a.IDUSUARIORESPONSABLE LIKE '".$userbusqueda."%'
						AND a.STATUSAUTORIZACIONDESVIO = 0
						AND IDUSUARIOAUTORIZACIONDESVIO = ''						  
					GROUP BY e.IDEXPEDIENTE,a.IDASISTENCIA
					ORDER BY 1 DESC";

//Sql de asistencias actuales pendientes por aceptar.				
	$Sql_exp_respo="SELECT
					/* CONSULTA DELEGAR PENDIENTES */
					  a.IDASISTENCIA,
					  a.IDEXPEDIENTE,
					  e.ARRSTATUSEXPEDIENTE,
					  a.IDCUENTA,
					  catalogo_cuenta.NOMBRE                        NOM_CUENTA,
					  a.IDPROGRAMASERVICIO,
					  a.IDSERVICIO,
					  a.USUARIODELAGADOPOR,
					  a.DELEGAR,
					  a.USUARIODELEGAHACIA,
					  cs.DESCRIPCION                   AA_SERVICIO,
					  cps.ETIQUETA                     NOM_SERVICIO,
					  a.IDPROGRAMA,
					  cp.NOMBRE                        NOM_PROGRAMA,
					  a.ARRSTATUSASISTENCIA,
					  a.ARRPRIORIDADATENCION,
					  a.ARRCONDICIONSERVICIO,
					  a.IDETAPA,
					  ce.DESCRIPCION                   NOM_ETAPA,
					  a.IDUSUARIORESPONSABLE,
					  a.DESVIACION,
					  a.STATUSAUTORIZACIONDESVIO,
					  a.IDUSUARIOAUTORIZACIONDESVIO,
					  CONCAT(ep.APPATERNO,' ',ep.APMATERNO,' ',ep.NOMBRE)    NOM_AFILIADO,
					  MIN(au.FECHAHORA)                FECHAHORA
					FROM ($con->temporal.asistencia a,
					   $con->temporal.expediente e,
					   $con->catalogo.catalogo_usuario cu)
					  LEFT JOIN $con->catalogo.catalogo_cuenta 
						ON catalogo_cuenta.IDCUENTA = a.IDCUENTA
					  LEFT JOIN $con->catalogo.catalogo_programa_servicio cps
						ON cps.IDPROGRAMASERVICIO = a.IDPROGRAMASERVICIO
					  LEFT JOIN $con->catalogo.catalogo_servicio cs
						ON cs.IDSERVICIO = a.IDSERVICIO
					  LEFT JOIN $con->catalogo.catalogo_programa cp
						ON cp.IDPROGRAMA = a.IDPROGRAMA
					  LEFT JOIN $con->catalogo.catalogo_etapa ce
						ON ce.IDETAPA = a.IDETAPA
					  LEFT JOIN $con->temporal.expediente_persona ep
						ON ep.IDEXPEDIENTE = a.IDEXPEDIENTE
						  AND ep.ARRTIPOPERSONA = 'TITULAR'
					  LEFT JOIN $con->temporal.asistencia_usuario au
						ON au.IDASISTENCIA = a.IDASISTENCIA
						  AND au.IDETAPA = 1
					WHERE $ver_cuentas
						a.IDEXPEDIENTE = e.IDEXPEDIENTE
						AND e.ARRSTATUSEXPEDIENTE = 'PRO'
						AND a.ARRSTATUSASISTENCIA ='PRO'
						AND a.USUARIODELEGAHACIA ='".$userbusqueda."'
						AND a.DELEGAR = 1
						AND a.STATUSAUTORIZACIONDESVIO = 0
						AND IDUSUARIOAUTORIZACIONDESVIO = ''						 
					GROUP BY e.IDEXPEDIENTE,a.IDASISTENCIA
					ORDER BY 1 DESC";
 
	$result=$con->query($Sql_expedientes);
	$result_resp=$con->query($Sql_exp_respo); 	

	$numreg=$result->num_rows*1;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Delegaciones de Asistencias</title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	<link rel="stylesheet" href="../../../../estilos/tablas/pagination.css" media="all">	
	<link rel="stylesheet" href="../../../../librerias/tinytablev3.0/style_sac.css" />	
	
	<!-- se usa para el autocompletar y ventanas emergentes(windows_js) -->
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../../estilos/suggest/ubigeo.css" rel="stylesheet" type="text/css" />	
	<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" >	 </link> 
	<link href="../../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" >	 </link>

	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_effects.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"> </script>
		
	<style type="text/css">
	<!--
		.style3 {color: #FFFFFF; font-weight: bold; }
		html body {
			margin:2px;
			padding:1px;
			background-image: url(tira09.jpg);
		}
	-->
	</style>
	<script>

		function seleccionar_todo(marcado){

			if(marcado){
				for (i=0;i<document.frmdelgar.elements.length;i++)
					if(document.frmdelgar.elements[i].type == "checkbox")	
						document.frmdelgar.elements[i].checked=1
			} else{
				for (i=0;i<document.frmdelgar.elements.length;i++)
					if(document.frmdelgar.elements[i].type == "checkbox")	
						document.frmdelgar.elements[i].checked=0
			}
		}

		function gestion_respuesta(id,resp,opc){

			if(opc ==1){
				if(confirm('<?=_("DESEA ELIMINAR LA DEGELACION DEL REGISTRO?.")?>')){

					 reDirigir('g_delegar.php?idasistencia='+id+'&opcion='+opc+'&responsable='+resp)
				}
			}
			else if(opc ==2){
				if(confirm('<?=_("DESEA RECHAZAR EL REGISTRO?.")?>')){

					 reDirigir('g_delegar.php?idasistencia='+id+'&opcion='+opc+'&responsable='+resp)
				}
			}	
			else if(opc ==3){
				if(confirm('<?=_("DESEA ACEPTAR EL REGISTRO?.")?>')){

					 reDirigir('g_delegar.php?idasistencia='+id+'&opcion='+opc)
				}
			}
		}
	</script>

	<script language="JavaScript">
		function validarCampo(variable){
		
			if(document.frmdelgar.cmbusuariobus.value ==''){
				alert('<?=_("SELECCIONE AL USUARIO RESPONSABLE.")?>');
				document.frmdelgar.cmbusuariobus.focus();
				return (false);
			}		
			else if(document.frmdelgar.cmbusuariobus.value==document.frmdelgar.cmbusuario.value){
				alert('<?=_("NO SE PUEDE DELEGAR AL MISMO USUARIO.")?>');	 
				return (false);
			}
			else{
				if(confirm('<?=_("DESEA PROSEGUIR CON LA OPERACION?.")?>')){
					document.frmdelgar.action ='g_delegar.php';
				}
				else
				{
					return (false);
				}
			}
			return (true);
		}		
	</script>

</head>
<body>
	<form id="frmdelgar" name="frmdelgar" method="post" action="" onSubmit = "return validarCampo(this)">	

	<table width="100%" border="0" cellpadding="1" cellspacing="1">
		<tr>
			<td width="325" style="font-size:12px"><strong><?=_("RESPONSABLE ACTUAL ->") ;?></strong></td>
			<td width="325">
				<?
					$Sql_coordinador="SELECT DISTINCT
									  catalogo_usuario.IDUSUARIO,
									  CONCAT(catalogo_usuario.APELLIDOS,', ',catalogo_usuario.NOMBRES) AS USUARIOS
									FROM $con->temporal.grupo_usuario
									  INNER JOIN $con->catalogo.catalogo_usuario
										ON catalogo_usuario.IDUSUARIO = grupo_usuario.IDUSUARIO
									WHERE grupo_usuario.IDGRUPO IN('CORD','SUCA') AND catalogo_usuario.ACTIVO=1 order by catalogo_usuario.APELLIDOS";

					$con->cmbselectdata($Sql_coordinador,"cmbusuariobus",$userbusqueda,"onChange='submit()' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto'",($accesos["SUCA"])?"2":"1");
				?>	</td>
			<td width="325">&nbsp;</td>
			<td width="325" style="font-size:12px"><strong><?=_("DELEGAR RESPONSBLE ->") ;?></strong></td>
			<td width="325"><? $con->cmbselectdata($Sql_coordinador,"cmbusuario","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto'","2"); ?></td>
	  </tr>
	</table> 

	<div id="tablewrapper">	
		<input type="hidden" id="columns" />
        <input type="hidden" id="query" />
        <span class="details"><div style="display:none" ><span id="startrecord"></span><span id="endrecord"></span><span id="totalrecords"></span></div></span>
        
		<table cellpadding="0" cellspacing="0" border="0" id="table" style="width:147%" class="tinytable">
            <thead>
                <tr>
                    <th class="nosort"><h3></h3></th>
					<th class="nosort"><h3><input type="checkbox" name="ckbmarcar[]" id="checkbox" title="<?=_("SELECCIONAR/DESMARCAR") ;?>" onclick="seleccionar_todo(this.checked)" /></h3></th>
                    <th><h3><?=_("EXPED.") ;?></h3></th>                    
                    <th><h3><?=_("ASIS.") ;?></h3></th>
                    <th class="nosort"><h3><?=_("CUENTA") ;?></h3></th>
                    <th class="nosort"><h3><?=_("PLAN") ;?></h3></th>
                    <th class="nosort"><h3><?=_("AFILIADO") ;?></h3></th>
                    <th class="nosort"><h3><?=_("SERVICIO") ;?></h3></th>
                    <th class="nosort"><h3><?=_("ESTADO") ;?></h3></th>
                    <th class="nosort"><h3><?=_("ETAPA") ;?></h3></th>
                    <th class="nosort"><h3><?=_("RESPONSABLE") ;?></h3></th>
                    <th><h3><?=_("FECHACREACION") ;?></h3></th>
                    <th><h3><?=_("DELEGADO HACIA") ;?></h3></th>
                    <th class="nosort"><h3></h3></th>
                </tr>	
            </thead>
            <tbody>			
		 <?
			//registros de asistencias del responsable actual.
			$c=0;
			while($reg = $result->fetch_object()){				
				if($c%2==0) $fondo='#FFFFFF'; else $fondo='#F9F9F9';	
				if($c%2==0) $clase='trbuc0'; else $clase='trbuc1';			
		?>		
		 <tr bgcolor=<?=$fondo;?> class='<?=$clase;?>' title="<?=$reg->IDASISTENCIA." - ".$reg->NOM_AFILIADO;?>" >
			<td><img src="../../../../imagenes/iconos/adds.gif" onclick="ventana_historico('<?=$reg->IDASISTENCIA;?>')"  style="cursor:pointer" alt="<?=_("HISTORIAL")?>" title="<?=_("HISTORIAL");?>" /></td>
			<td><div align="center"><input type="checkbox" name="ckbmarcar[]" value="<?=$reg->IDASISTENCIA?>" id="checkbox" /></div></td>
			<td><div align="center"><?=$reg->IDEXPEDIENTE;?></div></td>			
            <td><div align="center"><?=$reg->IDASISTENCIA;?></div></td>
			<td><?=$reg->IDCUENTA;?></td> 
			<td><?=$reg->IDPROGRAMA;?></td>
			<td><?=utf8_encode($reg->NOM_AFILIADO);?></td> 
			<td><?=$reg->AA_SERVICIO;?></td>
			<td><?=$desc_status_asistencia[$reg->ARRSTATUSASISTENCIA];?></td>					
			<td><?=$reg->IDETAPA;?></td> 		
			<td><?=$reg->IDUSUARIORESPONSABLE;?></td>			
			<td><?=$reg->FECHAHORA;?></td>			
			<td><?=$reg->USUARIODELEGAHACIA;?></td>			
			<td><? if($reg->DELEGAR ==1){?><img src="../../../../imagenes/iconos/eliminar.gif" onclick="gestion_respuesta('<?=$reg->IDASISTENCIA;?>','<?=$reg->IDUSUARIORESPONSABLE;?>','1')" style="cursor:pointer" width="15" alt="<?=_("ELIMINAR")?>" title="<?=_("CANCELAR ASISTENCIA NRO:").$reg->IDASISTENCIA?>" /><?}?></td>			
		</tr>
		<?
				$c=$c+1;
				$stylo="";
			}
	 
		?>                
            </tbody>
        </table> 
		
		<table width="534" border="0" cellpadding="1" cellspacing="1">
			<tr>
				<td width="193"><input type="submit" name="button" id="button" value="DELEGAR SELECCIONADOS"  style="text-align: center; font-weight: bold; font-size: 10px; height: 25px;" /></td>
			</tr>
		</table>
		
		<div id="tablefooter" style="display:<?=($numreg >10)?"":"none";?>;border:1px dashed #003366;height:17px">
			<div id="tablenav">
				<div >
					<img src="../../../../librerias/tinytablev3.0/images/first.gif" width="16" height="16" alt="First Page" title="Primera Pagina" onclick="sorter.move(-1,true)" />
					<img src="../../../../librerias/tinytablev3.0/images/previous.gif" width="16" height="16" alt="First Page" title="Pagina Siguiente" onclick="sorter.move(-1)" />
					<img src="../../../../librerias/tinytablev3.0/images/next.gif" width="16" height="16" alt="First Page" title="Pagina Anterior" onclick="sorter.move(1)" />
					<img src="../../../../librerias/tinytablev3.0/images/last.gif" width="16" height="16" alt="Last Page" title="Ultima Pagina" onclick="sorter.move(1,true)" />
				</div>
			</div>
			<div id="tablelocation">
				<div>
					<select onchange="sorter.size(this.value)" class="classtexto" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);">
					<option value="5">5</option>
						<option value="10" selected="selected">10</option>
						<option value="20">20</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select>
					<span><?=_("Entrada de Pagina") ;?></span>
				</div>
				<div class="page">Pag. <span id="currentpage"></span> de <span id="totalpages"></span></div>
			</div>
		</div>	 
    </div><br/>	
	
	<p style="font-size:12px"><strong><?=_("ACEPTAR RESPONSABILIDAD") ;?></strong></p>
	<div id="tablewrapper2">	 
   
        <input type="hidden" id="columns"/>
        <input type="hidden" id="query"/>
        <span class="details"><div style="display:none"><span id="startrecord"></span><span id="endrecord"></span><span id="totalrecords"></span></div></span>
		
        <table cellpadding="0" cellspacing="0" border="0" id="table-2" style="width:100%" class="tinytable">
            <thead>
                <tr>
                    <th class="nosort"><h3></h3></th>
                    <th class="nosort"><h3><?=_("EXPED.") ;?></h3></th>                    
					<th class="nosort"><h3><?=_("ASIS.") ;?></h3></th>
                    <th class="nosort"><h3><?=_("CUENTA") ;?></h3></th>
                    <th class="nosort"><h3><?=_("PLAN") ;?></h3></th>
                    <th class="nosort"><h3><?=_("AFILIADO") ;?></h3></th>
                    <th class="nosort"><h3><?=_("SERVICIO") ;?></h3></th>
                    <th class="nosort"><h3><?=_("ESTADO") ;?></h3></th>
                    <th class="nosort"><h3><?=_("ETAPA") ;?></h3></th>
                    <th class="nosort"><h3><?=_("RESPONSABLE") ;?></h3></th>
                    <th class="nosort"><h3><?=_("FECHACREACION") ;?></h3></th>
                    <th class="nosort"><h3><?=_("DELEGADOPOR") ;?></h3></th>
                    <th class="nosort"><h3></h3></th>
                    <th class="nosort"><h3></h3></th>
                </tr>				 
            </thead>
            <tbody>			
		 <?
			//registros de asistencia por aceptar.
			while($reg = $result_resp->fetch_object())
			 {				
				if($c%2==0) $fondo='#FFFFFF'; else $fondo='#F9F9F9';	
				if($c%2==0) $clase='trbuc0'; else $clase='trbuc1';	
	
				$rechazar=delegar_aprobacion($accesos["SUCA"],$reg->USUARIODELAGADOPOR,$reg->USUARIODELEGAHACIA);			 
		?>		
		 <tr bgcolor=<?=$fondo;?> class='<?=$clase;?>' title="<?=$reg->IDASISTENCIA." - ".$reg->NOM_AFILIADO;?>" >
			<td><img src="../../../../imagenes/iconos/adds.gif" onclick="ventana_historico('<?=$reg->IDASISTENCIA;?>')"  style="cursor:pointer" alt="<?=_("HISTORIAL")?>" title="<?=_("HISTORIAL");?>" /></td>			
			<td><div align="center"><?=$reg->IDEXPEDIENTE;?></div></td>
			<td><div align="center"><?=$reg->IDASISTENCIA;?></div></td>            
			<td><?=$reg->IDCUENTA;?></td> 
			<td><?=$reg->IDPROGRAMA;?></td>
			<td><?=utf8_encode($reg->NOM_AFILIADO);?></td> 
			<td><?=$reg->AA_SERVICIO;?></td>
			<td><?=$desc_status_asistencia[$reg->ARRSTATUSASISTENCIA];?></td>					
			<td><?=$reg->IDETAPA;?></td> 		
			<td><?=$reg->IDUSUARIORESPONSABLE;?></td>			
			<td><?=$reg->FECHAHORA;?></td>			
			<td><?=$reg->USUARIODELAGADOPOR;?></td>			
			<td><? if($reg->DELEGAR ==1 and $rechazar){?><img src="../../../../imagenes/iconos/eliminar.gif" onclick="gestion_respuesta('<?=$reg->IDASISTENCIA;?>','<?=$reg->IDUSUARIORESPONSABLE;?>','2')" style="cursor:pointer" width="15" alt="<?=_("ELIMINAR")?>" title="<?=_("CANCELAR ASISTENCIA NRO: ").$reg->IDASISTENCIA?>" /><?} else {?> <img src="../../../../imagenes/iconos/eliminar_inactivo.gif"  width="15" alt="<?=_("ELIMINAR")?>" /><?}?></td>
			<td><? if($reg->DELEGAR ==1 and $reg->USUARIODELEGAHACIA==$_SESSION["user"]){?><img src="../../../../imagenes/iconos/ok.gif" onclick="gestion_respuesta('<?=$reg->IDASISTENCIA;?>','','3')" style="cursor:pointer" width="15" alt="<?=_("ACEPTAR")?>" title="<?=_("ACEPTAR REGISTRO: ").$reg->IDASISTENCIA?>" /><?} else {?><img src="../../../../imagenes/iconos/ok_inactivo.gif"  width="15" alt="<?=_("ACEPTAR")?>"/><?}?></td>		 
		 </tr>
		<?
			 $c=$c+1;
			 
			}
		?>                
            </tbody>
        </table>

			<div id="tablefooter" style="display:none;border:1px dashed #003366;height:17px">
				<div id="tablenav">
					<div>
						<img src="../../../../librerias/tinytablev3.0/images/first.gif" width="16" height="16" alt="First Page" title="Primera Pagina" onclick="sorter.move(-1,true)" />
						<img src="../../../../librerias/tinytablev3.0/images/previous.gif" width="16" height="16" alt="First Page" title="Pagina Siguiente" onclick="sorter.move(-1)" />
						<img src="../../../../librerias/tinytablev3.0/images/next.gif" width="16" height="16" alt="First Page" title="Pagina Anterior" onclick="sorter.move(1)" />
						<img src="../../../../librerias/tinytablev3.0/images/last.gif" width="16" height="16" alt="Last Page" title="Ultima Pagina" onclick="sorter.move(1,true)" />
					</div>	 
				</div>
				<div id="tablelocation2">
					<div></div>
					<div class="pag2e"><span id="currentpage2"></span><span id="totalpages"></span></div>
				</div>
			</div>  
	</div>	
	</form>	
	
<!-- incluir js de tinytable-->
	<script type="text/javascript" src="../../../../librerias/tinytablev3.0/script.js"></script>
  
	<script type="text/javascript">
	var sorter2 = new TINY.table.sorter('sorter2','table-2',{
		headclass:'head',
		ascclass:'asc',
		descclass:'desc',
		evenclass:'evenrow',
		oddclass:'oddrow',
		evenselclass:'evenselected',
		oddselclass:'oddselected',
		paginate:true,
		size:20,
		colddid:'columns',
		currentid:'currentpage',
		totalid:'totalpages',
		startingrecid:'startrecord',
		endingrecid:'endrecord',
		totalrecid:'totalrecords',
		hoverid:'selectedrow',
		//pageddid:'pagedropdown',
		navid:'tablenav',
		sortcolumn:0,
		sortdir:1,
		//sum:[8],
		//avg:[6,7,8,9],
		columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],
		init:true
	});
	</script>
	  
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
			//pageddid:'pagedropdown',
			navid:'tablenav',
			sortcolumn:0,
			sortdir:1,
			//sum:[8],
			//avg:[6,7,8,9],
			columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],
			init:true
		});
	  </script>  
  
</body>
</html>

	<script type="text/javascript">	
		
	/*** ventana historial:aceptacion, rechazos de delegacion ***/
		
		var validar_func = '';
		var win = null;
		
		function ventana_historico(id){
		
			if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: '<?=_("HISTORIAL DE DELEGACION")?>',
					width: 710,
					height: 330,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: true,
					minimizable: false,
					maximizable: false,
					resizable: true,
					opacity: 0.95,
					url: 'historial_delegar.php?asistencia='+id
				});

				win.showCenter();
				myObserver = {onDestroy: function(eventName, win1)
				{
					if (win1 == win) {
						win = null;
						Windows.removeObserver(this);
					}
				}
				}
				Windows.addObserver(myObserver);
			}
			return;
		}	 
					
	</script>