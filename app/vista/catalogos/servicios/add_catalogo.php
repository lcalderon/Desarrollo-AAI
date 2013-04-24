<?php

include_once('../../../modelo/clase_mysqli.inc.php');
include_once("../../../vista/login/Auth.class.php");

$con = new DB_mysqli();

if ($con->Errno)
{
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
}

$con->select_db($con->catalogo);

	session_start();
 	Auth::required($_SERVER['REQUEST_URI']);

$Sql_moneda="SELECT
				  catalogo_parametro_moneda.IDMONEDA,
				  catalogo_moneda.DESCRIPCION
				FROM catalogo_parametro_moneda
				  INNER JOIN catalogo_moneda
					ON catalogo_moneda.IDMONEDA = catalogo_parametro_moneda.IDMONEDA
				ORDER BY catalogo_parametro_moneda.PRIORIDAD ASC";

$lista_medida = $con->uparray("select IDMEDIDA,DESCRIPCION from catalogo_medida where DESCRIPCION!='' order by DESCRIPCION ");

$rsinfra=$con->query("select IDINFRAESTRUCTURA,DESCRIPCION from catalogo_infraestructura where ACTIVO=1 order by DESCRIPCION ");
$rsinfraes=$con->query("select IDINFRAESTRUCTURA from catalogo_infraestructura order by DESCRIPCION ");

$rs_servinfra=$con->query("select IDINFRAESTRUCTURA from catalogo_servicio_infraestructura where IDSERVICIO='$idservicio' order by PRIORIDAD ");
while($regi = $rs_servinfra->fetch_object())
{
	$rowi[]=$regi->IDINFRAESTRUCTURA;
}

if(count($rowi)== 0)	$rowi=$rsinfraes->num_rows;

?>
<html>
	<head>
		<title>American Assist</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/ajax_catalogo.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />		
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
		
		<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/prototype.js"></script>
			<script type="text/javascript" src="/librerias/scriptaculous/scriptaculous.js"></script>
		<style type="text/css">@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");</style>
		<style type="text/css">		
			.style1 {color: #FFFFFF};
			.style2 {color: #FF4848}
			
        </style>
		<script language="JavaScript">

		function validarCampo(variable){

			document.frmaddregistro.nombre.value=document.frmaddregistro.nombre.value.replace(/^\s*|\s*$/g,"");
			if(document.frmaddregistro.nombre.value =='' ){
				alert('<?=_("INGRESE LA DESCRIPCION DEL SERVICIO.") ;?>');
				//alert('INGRESE LA DESCRIPCION DEL SERVICIO.');
				document.frmaddregistro.nombre.focus();
				return (false);
			}
			else if(document.frmaddregistro.cmbplantilla.value =='' ){
				alert('<?=_("SELECCIONE ALGUNA PLANTILLA.") ;?>');
				document.frmaddregistro.cmbplantilla.focus();
				return (false);
			}
			else if(document.frmaddregistro.cmbfamilia.value =='' ){
				alert('<?=_("SELECCIONE ALGUNA FAMILIA.") ;?>');
				document.frmaddregistro.cmbfamilia.focus();
				return (false);
			}

			return (true);
		}


		function adicionarFila(){

			var tabla = document.getElementById("infra").tBodies[0];
			var fila = document.createElement("TR");
			fila.setAttribute("align","left");

			var celda1 = document.createElement("TD");

			var celda2 = document.createElement("TD");
			var sel = document.createElement("SELECT");
			sel.setAttribute("size","1");
			sel.setAttribute("onFocus","coloronFocus(this)");
			sel.setAttribute("onBlur","colorOffFocus(this)");
			sel.setAttribute("class","classtexto");
			sel.setAttribute("name","cmbinfra[]" );


			opcion0 = document.createElement('OPTION');
			opcion0.innerHTML = 'SELECCIONE';
			opcion0.value = '';
			//if($reg->IDINFRAESTRUCTURA == 1)	echo "opcion$i.selected = '".$reg->IDINFRAESTRUCTURA."';\n";
			sel.appendChild(opcion0);

			<?php

			$i=1;
			while($reg= $rsinfra->fetch_object())
			{
				echo "opcion$i = document.createElement('OPTION');\n";
				echo "opcion$i.innerHTML = '".utf8_decode($reg->DESCRIPCION)."';\n";
				echo "opcion$i.value = '".$reg->IDINFRAESTRUCTURA."';\n";
				//if($reg->IDINFRAESTRUCTURA == 1)	echo "opcion$i.selected = '".$reg->IDINFRAESTRUCTURA."';\n";
				echo "sel.appendChild(opcion$i);\n";
				$i=$i+1;

			}
			?>

			celda2.appendChild(sel);

			//var celda3 = document.createElement('TD');
			var boton = document.createElement('IMG');

			boton.setAttribute('src','../../../../imagenes/iconos/deletep.gif');
			boton.setAttribute('title','ELIMINAR');
			boton.setAttribute('border','0');
			boton.setAttribute('height','14');
			boton.setAttribute('style','cursor:pointer');
			boton.onclick=function(){borrarFila(this);}
			celda2.appendChild(boton);

			fila.appendChild(celda1);
			fila.appendChild(celda2);
			//fila.appendChild(celda3);

			tabla.appendChild(fila);

		}

		function borrarFila(button){
			var fila = button.parentNode.parentNode;
			var tabla = document.getElementById('infra').getElementsByTagName('tbody')[0];
			tabla.removeChild(fila);
		}
		</script>
	</head>
	<body onLoad="document.frmaddregistro.nombre.focus();">
	<form name="frmaddregistro" action="grabar_servicio.php" method="POST" onSubmit = "return validarCampo(this)" >
		<input name="idservicio" type="hidden" value="" />
		<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
		<table border="0" cellpadding="1" cellspacing="1" width="70%" class="catalogos">
			<tr bgcolor="#333333">
				<th style="text-align:left"><?=_("AGREGAR SERVICIO");?></th>
			</tr>
			<tr class='modo1'>
				<td><?=_("DESCRIPCION");?></td>
				<td><input name="nombre" type="text" size="50" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" style="text-transform:uppercase;"  ></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("PLANTILLA");?></td>
				<td><? $con->cmbselect_db('cmbplantilla','select IDPLANTILLA,DESCRIPCION from catalogo_plantilla order by DESCRIPCION', 'Interbank',' onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" '); ?></td>
			</tr>
			<!--tr class='modo1'>
				  <td><? //=_("COBERTURA");?> </td>
				  <td><? //$con->cmbselect_db('cmbcobertura','select IDCOBERTURA,NOMBRE from catalogo_cobertura ', 'Blank',' onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" '); ?></td>
				</tr-->
			<tr class='modo1'>
			  <td><?=_("FAMILIA");?> </td>
			  <td><? $con->cmbselect_db('cmbfamilia','select IDFAMILIA,DESCRIPCION from catalogo_familia order by DESCRIPCION', 'Blank',' onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" ' ); ?></td> 
			</tr>		  
			<tr class='modo1'>
				<td><?=_("FECHA INI. VIGENCIA");?></td>
				<td><input type="text" readonly class="classtexto"  id="fechainiserv" name="fechainiserv" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" />
				  <button   id="cal-button-1">...</button>
			    <script type="text/javascript">
			    Calendar.setup({
			    	inputField    : "fechainiserv",
			    	button        : "cal-button-1",
			    	align         : "Tr"
			    });
				  </script>&nbsp;<img src="../../../../imagenes/iconos/limpiar.jpg" title="<?=_('LIMPIAR FECHA');?>" onClick="document.frmaddregistro.fechainiserv.value='0000-00-00' " style="cursor:pointer" /></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("FECHA FIN VIGENCIA");?></td>
				<td><input type="text" readonly id="fechafinserv" name="fechafinserv" class="classtexto" maxlength="10" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" />
				  <button   id="cal-button-2">...</button>
	             <script type="text/javascript">
	             Calendar.setup({
	             	inputField    : "fechafinserv",
	             	button        : "cal-button-2",
	             	align         : "Tr"
	             });
					 </script>&nbsp;<img src="../../../../imagenes/iconos/limpiar.jpg" title="<?=_('LIMPIAR FECHA');?>" onClick="document.frmaddregistro.fechafinserv.value='0000-00-00' " style="cursor:pointer" /></td>
			</tr>
			<tr class='modo1'>
			  <td><?=_("DURACION ESTIMADA");?> </td>
			  <td><input name="txtduracionestimada" type="text" size="10" class="classtexto" maxlength="4" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" onKeyPress="return validarnumero(event)" > <?=_('MINUTOS');?></td>
			</tr>			 			
			<tr class='modo1'>
			   <td><?=_("VALIDACION DE TIEMPOS ");?></td>
			   <td><input type="checkbox" name='validaciontiempo' checked></td>
			</tr>
			<tr class='modo1'>
			   <td><?=_("CONCLUIR TEMPRANO");?></td>
			   <td>
			     <span><input type="checkbox" name='concluciontemprana' onchange="act_zona_con();" ></span>
			   	 <span id='zona_con' style="float:center;display:none">
			   		 <input type="radio" name='conclucionconproveedor' value='1' checked ><?=_('CON PROVEEDOR')?>
  			     	 <input type="radio" name='conclucionconproveedor' value='0' ><?=_('SIN PROVEEDOR')?>
			   	 </span>
			    </td>
			</tr>
			<tr class='modo1'>
			   <td><?=_("SAP PAGER_SERV");?></td>
			   <td><? $con->cmbselect_db('PAGER_SERV',"SELECT PAGER_SERV,SERVICIOS FROM $con->catalogo.catalogo_sap_pager_serv",'','class="classtexto" id="pager_serv"','','SELECCIONE')?></td>
			</tr>
			<tr class='modo1'>
			   <td><?=_("SAP MATNR");?></td>
			   <td><? $con->cmbselect_db('MATNR',"SELECT MATNR,SERVICIO FROM $con->catalogo.catalogo_sap_matnr;",'','class="classtexto" id="matnr"','','SELECCIONE')?></td>
			</tr>
			
			
					
			<tr class='modo1'>
				<td colspan="2" align="right"><div align="center">
				  <input type="button" class="botonstandar" value="CANCELAR" onClick="reDirigir('general.php')" title="CANCELAR">				  
				  <input name="Submit"  class="botonstandar" type="submit" value="<?=_('GRABAR');?>" title="<?=_('GRABAR SERVICIO');?>" >
			    </div></td>
			</tr>
        </table>
	</form> 
	</body>
 </html>
 <script type="text/javascript">


 // ACTIVA / DESACTIVA LA ZONA DE CONCLUCION
 function act_zona_con(){
 	$('zona_con').toggle();
 	return;
 }

 </script>
 