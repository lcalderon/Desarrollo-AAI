<?
session_start();

include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_ubigeo.inc.php');
include_once('../../modelo/clase_moneda.inc.php');
include_once('../../modelo/clase_plantilla.inc.php');
include_once('../../modelo/clase_persona.inc.php');
include_once('../../modelo/clase_telefono.inc.php');
include_once('../../modelo/clase_cuenta.inc.php');
include_once('../../modelo/clase_familia.inc.php');
include_once('../../modelo/clase_servicio.inc.php');
include_once('../../modelo/clase_programa_servicio.inc.php');
include_once('../../modelo/clase_programa.inc.php');
include_once('../../modelo/clase_afiliado.inc.php');
include_once('../../modelo/clase_etapa.inc.php');
include_once('../../modelo/clase_contacto.inc.php');
include_once('../../modelo/clase_poligono.inc.php');
include_once('../../modelo/clase_circulo.inc.php');
include_once('../../modelo/clase_proveedor.inc.php');
include_once('../../modelo/clase_expediente.inc.php');
include_once('../../modelo/clase_asistencia.inc.php');
include_once('../../modelo/functions.php');
include_once('../asistencia/asignacion_proveedor/AlgoritmoProveedores.php');
include_once("../../vista/login/Auth.class.php");

//VALIDAR USUARIO LOGUEADO
Auth::required();

/* DATOS QUE VIENEN DE LA SESSION */
$idusuario = $_SESSION[user];
$idextension= $_SESSION[extension];

/* DATOS QUE VIENEN DEL EXTERIOR*/

$idasistencia= $_GET[idasistencia];
$asis = new asistencia();
$asis->carga_datos($idasistencia);


/* ubigeo */
$ubigeo = $asis->lugardelevento;
//var_dump($ubigeo);

/* NOMBRE COMERCIAL  DEL SERVICIO */
$prog = new programa_servicio();
$prog->carga_datos($asis->idprogramaservicio);
$nombreservicio =$prog->etiqueta;
$nombre_servicio = ($nombreservicio!='')?$nombreservicio:$asis->servicio->descripcion;


$idexpediente= $asis->expediente->idexpediente;
$exp= new expediente();
$exp->carga_datos($idexpediente);



/* VARIABLES */
$afiliado = $exp->personas[TITULAR][NOMBRE].' '.$exp->personas[TITULAR][APPATERNO].' '.$exp->personas[TITULAR][APMATERNO];
$contactante = $exp->personas[CONTACTO][NOMBRE].' '.$exp->personas[CONTACTO][APPATERNO].' '.$exp->personas[CONTACTO][APMATERNO];
$cuenta = $asis->expediente->cuenta->nombre;
$plan = $asis->expediente->programa->nombre;
$ubigeo_exp = $asis->expediente->ubigeo;
$atencion=$asis->arrprioridadatencion;


$idetapa=2; //
$etapa = new etapa();
$etapa->carga_datos($idetapa);
//echo $asis->servicio->concluciontemprana;


/* SI LA ASISTENCIA DE CONCLUCION TEMPRANA PASA DE FRENTE A LA ETAPA 8*/
$idetapa_asis= ($asis->servicio->concluciontemprana)?8:$asis->etapa->idetapa;


$idprograma = $asis->expediente->programa->idprograma;
$idfamilia= $asis->familia->idfamilia;


$fechaarribo = '';
$fechacontacto = '';
$idasigprov='';


foreach ($asis->proveedores as $prov)
{
	if ($prov[statusproveedor]=='AC'){
		$proveedor_act= $prov[idproveedor];
		$fechaarribo = $prov[fechaarribo];
		$fechacontacto = $prov[fechacontacto];
		$idasigprov=$prov[idasigprov];


	}
}





if($proveedor_act=='0')	$disabledbtn='disabled';


include_once('../includes/arreglos.php');
include_once('../includes/head_prot_win.php');

?>

<link href="../../../estilos/plantillas/menu.css" rel="stylesheet" type="text/css" />	
<script src="../asistencia/principal/mmenu.js" type="text/javascript"></script>
<script type="text/javascript" src="/librerias/tinytablev3.0/script.js"></script>
<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/tooltips.js"></script>

<title><?=_('ASISTENCIA').' # '.$idasistencia?></title>

<body onload='<?=($asis->arrstatusasistencia=="PRO")?"despliega($idetapa)":"";?>'  style="overflow:scroll;overflow-x:hidden;">
<? 
//   INCLUSION DEL MENU FLOTANTE //
$gestion=$_GET["gestion"];

if($gestion)
{
	include_once("../asistencia/principal/menuItems_calidad.js.php");

}
else
{
	include_once("../asistencia/principal/menuItems.js.php");
}


?>
<!-- DATOS DEL EXPEDIENTE -->
<div>
	<fieldset style="background: #ECE9D8">
	<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_expediente').toggle();"><?=_('EXPEDIENTE')." $exp->idexpediente --> <font color='#B70E0E'>". $etapa->descripcion ?></font></legend>
		<? include_once('vista_datos_expediente.php');?>
	</fieldset>	
</div>  

<!-- DATOS DE LA ASISTENCIA -->

<div>
	<fieldset style="background: #ECE9D8">
	<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_asistencia').toggle();"><?=_('ASISTENCIA')." $idasistencia --><font color='#B70E0E'>".$nombre_servicio.'-->'.$desc_status_asistencia[$asis->arrstatusasistencia]?></font></legend>
		<div id='datos_asistencia'>
		<? include_once('vista_datos_asistencia.php')?>
		</div>	
	</fieldset>	
</div> 

<!-- DATOS DE PROVEEDORES -->
<div style="display:<?=($asis->proveedores)?'block':'none';?>">
	<fieldset style="background: #ECE9D8">
	<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_proveedor').toggle();"><?=_('PROVEEDORES')?></legend>
		<div id='datos_proveedor'>
		<? include_once('vista_datos_proveedor.php')?>
		</div>
	</fieldset>		
	
</div>

<?  
$zonatiempo1='none';
if ($asis->servicio->validaciontiempo)
{
	if (!$asis->proveedor_ac ) $zonatiempo1='block'; //OR !$asis->conclucionconproveedor
}
?>


<? if ($asis->arrstatusasistencia=='PRO'):?>
<!--PROCESO DE ASIGNACION DE PROVEEDOR-->
<div id='proceso_asignacion' style="height:220px;overflow:scroll;overflow-x:hidden;display:<?=$zonatiempo1;?>">
	<fieldset style="background: #ECE9D8">
	<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('asignacion_proveedor').toggle();"><?=_('ASIGNACION DE PROVEEDORES')?></legend>  
	<div id='asignacion_proveedor'>
	<span id='zonatiempo1' style="display:<?=$zonatiempo1?>;">
		<font size=-2>
		<span>
		<?=_('TIPO DE ATENCION')?>
		<? $con->cmbselect_ar('ARRPRIORIDADATENCION',$desc_prioridadAtencion,$atencion,'id=arrprioridadatencion',"onchange=ver_disponibilidad(this,'datos_disponibilidad','zona_asignacion_expediente','teme','$atencion')",'')?></td>
		</span>&nbsp;&nbsp;&nbsp;&nbsp;
		<span>
		<?=_('TIPO DE COSTOS')?>
		  <select id='tipocostos' >
		 	<option value='L' selected><?=_('LOCAL')?></option>
		 	<option value='I'><?=_('INTERMEDIO')?></option>
		 	<option value='F'><?=_('FORANEO')?></option> 
		  </select>
		</span>&nbsp;&nbsp;&nbsp;&nbsp;
		<span style='align:center'>
   		  <input type="button" id="btnManual" name="btnManual" <?=$disabledgral?> value="<?=_('MANUAL')?>" class='normal'  onclick="return confirmar(this); grabar();" />
 		  <input type="button" id="btnAsigMapa" name="btnAsigMapa" <?=$disabledgral?> value="<?=_('ASIGNACION X MAPA')?>" class='normal'  onclick="asig_mapa()" />
 	 	  <!--<input type="button" id="btnRecAlg" name="btnRecAlg" <?=$disabledgral?> value="<?=_('ACTUALIZAR RANKING')?>"  class='normal' onClick="RecargarRanking(<?=$idasistencia?>,<?=$idservicio?>)" />  -->
   	 	  <input type="button" id="btnTodos" name="btnTodos" <?=$disabledgral?> value="<?=_('VER TODOS')?>"  class='normal'  />
   		</span>
   		</font>
   	</span>	
	   <div id="cargando" class="spinner" style="display:none;" >
			<img src="/imagenes/iconos/spinner.gif" align="center" />Cargando…
	  </div> 
	<? $idservicio=$asis->servicio->idservicio;?>
	
	<form name='frmasignacion' id='frmasignacion' method='POST'> 
		<div id='zonatiempo2' style="display:<?=$zonatiempo1?>">
				<div id="datos_disponibilidad" style="display:<?=($atencion=='EME')?'none':'block';?>">
					<? include_once('../asistencia/disponibilidad/vista_servicio_programado.php');?>
				</div>
				<div id='teme'  style="display:<?=($atencion=='PRO')?'none':'block';?>" >  
					<? include_once('../asistencia/disponibilidad/vista_servicio_emergencia.php');?>
				</div>
		</div>
		
		<div id='datos_busqueda' style="display:none">  
			<?	$servicio = $con->uparray("SELECT IDSERVICIO,DESCRIPCION from $con->catalogo.catalogo_servicio");
			include_once('../asistencia/asignacion/vista_form_busqueda.php'); ?>
		</div>  
		
		<div id='zona_asignacion_expediente' style="display:<?=($asis->proveedor_ac or $asis->arrstatusasistencia=='CON')?'none':'block';?>" >
			<?include_once('../asistencia/asignacion/asignacion_automatica.php');?>
		</div>
	</form>	
	</div> <!--fin del div de asignacion del proveedor-->
	</fieldset>
</div> <!--fin del proceso de asignacion-->

<?endif;?>

<!--ZONA DE BITACORA-->
<div>
<fieldset>
 <legend><img  style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('zona_bitacora').toggle();"><?=_('BITACORA')?></legend>
  <div id='zona_bitacora'>
	<div>
		<? include_once('../asistencia/asignacion/vista_form_bitacora.php');?>
	</div>
	<div id='listado_bitacora'>
		<? include_once('listar_bitacora_etapa.php'); ?>
	</div>
  </div>	
</fieldset>
</div>


<?
if ($asis->arrstatusasistencia=='CM' OR $asis->arrstatusasistencia=='CP' or $asis->arrstatusasistencia=='CON') {
	$disabled='disabled';
}

$justificacion_repro=justificacion('REP');
$justificacion_reasig=justificacion('RASI');
$justificacion_cp=justificacion('CP');
$justificacion_cm=justificacion('CM');
$justificacion_manual =justificacion('AMAN');
?>

<div id='listado_de_servicios' align="center" style="weight:48%">
<? 

//if($gestion) include("../calidad/frmdeficiencias.php");

?> 
</div>
 

<br>
<br>
<br>
<br>

<div id='barra' class="barraestado" style="float:right">
<?if($gestion):	?>	 
	<table align="right">
		<tbody>
			<tr>
				<td><input type='button' id='btn_agregar' value="<?=_('AGREGAR DEFICIENCIA').''?>" class='guardar' ></td>
			</tr>
		</tbody>
	</table>		
<?else:?>	
<table align="right">
<tbody>
	<tr>
		<td>
		<input type='hidden' id='btnGrabarTiempo' name='btnGrabarTiempo' value='<?=_('Grabar Tiempos sin asignar Proveedor')?>' class='guardar' onClick="grabar('GT')" <?= $disabledbtn?> >
		</td>	
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<input type='hidden' id='hid_prov0' value='<?=$proveedor_act?>'>
	<? if(isset($asis->proveedores)):?>		
		<td><input type='button' id='btn_' value='<?=_('CANCELADO POSTERIOR')?>' class='cancelar' onClick="cancelado_posterior();" <?=$disabled?>></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<? if ($asis->servicio->validaciontiempo):?>
			<td><input type='button' id='btn_reprograma' value='<?=_('REPROGRAMAR TIEMPOS')?>' class='normal' onClick="reprogramar_tiempos();" <?=$disabled?>></td>	
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		<?endif;?>
		<td><input type='button' id='btn_reasignar_proveedor' value='<?=_('REASIGNAR PROVEEDOR')?>' class='normal' onClick="reasignar_proveedor()" <?=$disabled?>></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	<?else:?>
	<td><input type='button' id='btn_' value='<?=_('CANCELADO AL MOMENTO')?>' class='cancelar' onClick="cancelado_momento()" <?=$disabled?>></td>
	<?endif;?>
	</tr>
</tbody>
</table>		
	  
<?endif;?>		
		 
</div>
</body>

<script type="text/javascript" >
var validar_func = '';
var win = null;
var winx = null;


var globalCallbacks = {
	onCreate: function(){
		$('cargando').show();
	},
	onComplete: function() {
		if(Ajax.activeRequestCount == 0){
			$('cargando').hide();
		}
	}
};

/* Se registran los callbacks en Ajax.Responders */

Ajax.Responders.register( globalCallbacks );


function asig_mapa(){
	window.open("/app/vista/ubigeo/mapas/mapa_vista.php?IDASISTENCIA=<?=$idasistencia?>",'_blank','width=1024, height=768,top=0,left=0,toolbar=no,scrollbars=no, resizable=no,menubar=no,status=no, directories=no,location=no');

	
}


function actualizar_busqueda()
{
	new Ajax.Updater('zona_asignacion_expediente','/app/vista/asistencia/asignacion/vista_resultado.php',
	{
		method : 'post',
		evalScripts : true,
		parameters : $('frm_buscar').serialize(true),
	});
	return;
}

function CargaProveedor(prov){

	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("INFORMACION DEL PROVEEDOR")?>',
			width: 960,
			height: 400,
			//			showEffect: Element.show,
			//			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "/app/vista/catalogos/proveedores/form_catprov.php?idproveedor="+prov+"&og=1"

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
}


function grabarjustificacion(xprov,xstatus){

	observacion = $('OBSERVACION').value;
	motivo = $('JUSTIFICACION').value;

	new Ajax.Request('/app/vista/justificacion/grabar_justificacion.php',
	{
		method : 'post',
		parameters : {
			IDASISTENCIA : '<?=$idasistencia?>',
			IDUSUARIOMOD : '<?=$idusuario?>',
			OBSERVACION  : observacion,
			JUSTIFICACION : motivo,
			IDPROVEEDOR  : xprov,
			STATUS       : xstatus,
			ARRCLASIFICACION: 'JUST'
		},
		onComplete: function(t){
			actualizar_bitacora();

		}
	});
}


function confirmar(elemento,prov){

	if(elemento.value=='SGTE PROVEEDOR')
	{
		Dialog.confirm("<? echo $justificacion ?>",
		{
			top: 200,
			width:520,
			//			showEffect: Element.show,
			//			hideEffect: Element.hide,
			className: "alphacube",
			okLabel: "Guardar",
			cancelLabel:"Cancelar",
			buttonClass: "normal",
			onCancel: function(dlg){
				$('btnManual').disabled=false;
				$('btnAgregar').disabled=false;
			},
			onOk: function(dlg)
			{
				observacion = $('OBSERVACION').value;
				motivo = $('JUSTIFICACION').value;
				if(motivo=='OTROS' && observacion==''){
					alert('<?=_('DEBE INGRESAR UNA OBSERVACION!!')?>');

				}else if(motivo=='SEL'){
					alert('<?=_('DEBE SELECIONAR UNA JUSTIFICACION!!')?>');
				}else{

					grabarjustificacion(prov,'SGTE');
					return true;

				}
			},
		});   // FIN DEL DIALOG

	}
	else if(elemento.value=='MANUAL')
	{
		$('btnManual').disabled=true;
		$('datos_busqueda').style.display='block';
		$('proceso_asignacion').style.height='300px';

				Dialog.confirm("<? echo $justificacion_manual ?>",
				{
					top: 200,
					width:520,
					//showEffect: Element.show,
					//hideEffect: Element.hide,
					className: "alphacube",
					okLabel: "Guardar",
					cancelLabel:"Cancelar",
					buttonClass: "normal",
					onCancel: function(dlg){
						$('btnManual').disabled=false;
						$('btnAgregar').disabled=false;
					},
					onOk: function(dlg)
					{
						observacion = $('OBSERVACION').value;
						motivo = $('JUSTIFICACION').value;
						if(motivo=='OTROS' && observacion==''){
							alert('<?=_('DEBE INGRESAR UNA OBSERVACION!!')?>');
		
						}else if(motivo=='SEL'){
							alert('<?=_('DEBE SELECIONAR UNA JUSTIFICACION!!')?>');
						}else{
							grabarjustificacion(0,'MANUAL');
						$('btnManual').disabled=true;
						$('datos_busqueda').style.display='block';
						$('proceso_asignacion').style.height='300px';
						return true;
						}
					},
				});   // FIN DEL DIALOG
	}
	return;
}

new Event.observe('btnTodos','click',function()
{
	if (winx != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		winx = new Window({
			className: "alphacube",
			title: '<?=_("RANKING DE PROVEEDORES")?>',
			width: 960,
			height: 400,
			//			showEffect: Element.show,
			//			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "/app/vista/asistencia/asignacion/proveedor_ranking_listado.php?idasistencia=<?=$idasistencia?>"

		});
		winx.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			if (win1 == winx) {
				winx = null;
				Windows.removeObserver(this);
			}
		}
		}
		Windows.addObserver(myObserver);
	}
});

function ver_disponibilidad(elemento,valor,valor2,valor3,atencion)
{

	if (elemento.value=='PRO')
	{
		$('proceso_asignacion').style.height='260px';
		$(valor).style.display='block';
		$(valor2).style.display='block';
		$(valor3).style.display='none';
		$('btnGrabarTiempo').style.display='block';
		$('grabartiempopro').style.display='block';
		actualizar_disponibilidad(<?=$idasistencia?>,<?=$idetapa?>,'E');
	}
	else if(elemento.value=='EME')
	{
		$('proceso_asignacion').style.height='220px';
		$(valor).style.display='none';
		$(valor2).style.display='block';
		$(valor3).style.display='block';
		$('btnGrabarTiempo').style.display='none';
	}
	else if(elemento.value=='ASIGNAR')
	{
		$(valor).style.display='block';
		$(valor2).style.display='none';
	}


}

function despliega(idetapa){
	window.frames.name='ASISTENCIA_'+'<?=$idasistencia?>';

	prioridad=document.getElementById('arrprioridadatencion').value;
	proveedor0=document.getElementById('hid_prov0').value;
	if(prioridad =='EME' && idetapa==2){
		document.getElementById('datos_disponibilidad').style.display='none';
		document.getElementById('btnGrabarTiempo').style.display='none';

	}else if(prioridad=='EME' && idetapa>2){
		document.getElementById('barrasuperior').style.display='none';
		document.getElementById('zona_asignacion_expediente').style.display='none';
		document.getElementById('datos_disponibilidad').style.display='none';
		document.getElementById('teme').style.display='none';
		document.getElementById('btnGrabarTiempo').style.display='none';
		document.getElementById('arrprioridadatencion').disabled='true';
	}else if(prioridad=='PRO' && idetapa==2){

		document.getElementById('datos_disponibilidad').style.display='none';
		document.getElementById('teme').style.display='none';


	}else if(prioridad=='PRO' && idetapa>2){
		if(proveedor0=='0'){
			document.getElementById('datos_disponibilidad').style.display='none';
			document.getElementById('teme').style.display='none';
			document.getElementById('arrprioridadatencion').disabled='true';
		}else{
			document.getElementById('barrasuperior').style.display='none';
			document.getElementById('zona_asignacion_expediente').style.display='none';
			document.getElementById('datos_disponibilidad').style.display='none';
			document.getElementById('teme').style.display='none';
			document.getElementById('arrprioridadatencion').disabled='true';
			document.getElementById('btnGrabarTiempo').style.display='none';

		}
	}

	$('lin_1').style.display='table-row';
	return;
}

function actualizar_bitacora(){
	new Ajax.Updater('listado_bitacora','listar_bitacora_etapa.php',
	{
		method : 'post',
		evalScripts : true,
		parameters : {
			idasistencia : '<?=$idasistencia?>',
			idetapa : '<?=$idetapa?>'
		},

	});
	return;
}
function llamada(numero){
	numero = numero;
	new Ajax.Request('/app/controlador/ajax/ajax_llamada.php',
	{
		method : 'get',
		parameters: {
			prefijo: "",
			num: numero,
			ext: '<?=$idextension?>'
		},
		onComplete: function(t){

		}
	}
	);
	return;
}
function actualizar_disponibilidad(idasistencia,idetapa,opcion){
	new Ajax.Updater('zona_disponibilidad','vista_disponibilidad_afiliado.php?idasistencia='+idasistencia+'&idetapa='+idetapa+'&opcion='+opcion,
	{
		method : 'post',
		parameters : {idasistencia : '<?=$asis->idasistencia?>',
		},

	});
	return;
}

function grabar_dispo(){
	FECHAD=document.getElementById('date').value;
	FECHAD2=document.getElementById('date4').value;
	HORAINI= document.getElementById('dcbhora1').value+':'+document.getElementById('dcbminuto1').value+':00';
	HORAFIN= document.getElementById('dcbhora2').value+':'+document.getElementById('dcbminuto2').value+':00';

	FECHAINI= FECHAD+' '+HORAINI;
	FECHAFIN= FECHAD2+' '+HORAFIN;
	idasist=$('hid_idasistencia').value;
	;

	if(FECHAINI>= FECHAFIN)	alert('<?=_('LA FECHA FINAL NO PUEDE SER MENOR O IGUAL A LA FECHA INICIAL!!')?>');
	else{
		new Ajax.Request('/app/controlador/ajax/ajax_grabar_disponibilidad.php',
		{
			method : 'post',
			evalScripts : true,
			parameters : {
				IDASISTENCIA : idasist,
				IDUSUARIOMOD : '<?=$idusuario?>',
				FECHA : FECHAINI,
				FECHA2 : FECHAFIN,
				OPCION : $('btnagregardispo').value,
				IDDISPO : $('hid_iddispo').value
			},
			onComplete: function(t){
				if($('btnagregardispo').value=='Guardar Edicion'){
					$('btnagregardispo').value='Agregar';
				}
				actualizar_disponibilidad(idasist,<?=$idetapa?>,'E');

			}
		});
	}
}

function accion_disponibilidad(modo,fechaini,fechafin,iddispo){
	idasist=$('hid_idasistencia').value;

	switch (modo){
		case 'editar': // GUARDA LA BITACORA
		{
			$('btnagregardispo').value='Guardar Edicion';
			$('date').value=fechaini.substring(0,10);
			$('date4').value=fechafin.substring(0,10);
			hora1 = fechaini.substring(11,13);
			hora2 = fechafin.substring(11,13);

			minuto1 = fechaini.substring(14,16);
			minuto2 = fechafin.substring(14,16);

			$('dcbhora1').value=hora1;
			$('dcbhora2').value=hora2;
			$('dcbminuto1').value=minuto1;
			$('dcbminuto2').value=minuto2;
			$('hid_iddispo').value=iddispo;

			break;
		}

		case 'eliminar':  // CANCELADO POSTERIOR DE LA ASISTENCIA
		{
			if(confirm('<?=_('ESTA SEGURO QUE DESEA ELIMINAR LA DISPONIBILIDAD SELECCIONADA?')?>')){
				new Ajax.Request('/app/controlador/ajax/ajax_eliminar_disponibilidad.php',
				{
					method : 'post',
					parameters : {
						IDDISPO : iddispo

					},
					onComplete: function(t){
						$('btnagregardispo').value='Agregar';
						actualizar_disponibilidad(idasist,<?=$idetapa?>,'E');
					}
				});
			}



			break;
		}



	}
	return;
}

function Guardar()
{
	document.getElementById('frm_disponibilidad').action='../asistencia/disponibilidad/grabardisponibilidad.php';
	document.getElementById('frm_disponibilidad').submit()
}

function grabar(modo){

	switch (modo){
		case 'parcial': // GUARDA LA BITACORA
		{
			if (trim($F('comentario'))=='') alert("<?=_('INGRESE UN COMENTARIO')?>");
			else
			{
				new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_asignacion.php',
				{
					method : 'post',
					parameters : $('form_bitacora').serialize(true),
					onComplete: function()
					{
						$('comentario').value='';
						actualizar_bitacora();
					}
				});
			}
			break;
		}


		case 'RTEME':  // REPROGRAMAR TIEMPOS EMERGENCIA
		{
			minutos = $('cbminutomax_re').value;
			prioridad=$('arrprioridadatencion').value;
			if(confirm('<?=_('ESTA SEGURO QUE DESEA REPROGRAMAR TIEMPOS?')?>'))
			{
				new Ajax.Request('/app/vista/asistencia/asignacion_proveedor/ajax_reprogramar_tiempo_emergencia.php',
				{
					method : 'post',
					parameters :
					{
						IDASISTENCIA : '<?=$idasistencia?>',
						IDUSUARIOMOD : '<?=$idusuario?>',
						MINUTO : minutos,  // asistencia cancelado posterior
						PRIORIDAD  : prioridad,
						ARRCLASIFICACION:'RTEME'
					},
					onComplete: function(t)
					{
						url= 'etapa5.php?&idasistencia=<?=$idasistencia?>';
						reDirigir(url); // pasa a la siguiente etapa
					}
				});
			}
			break;
		}

		case 'RTPRO':  // REPROGRAMAR TIEMPOS PROGRAMADO
		{
			prioridad=document.getElementById('arrprioridadatencion').value;
			teat = $('date5').value+' '+$('cbhora1').value+':'+$('cbminuto1').value+':00';
			team = $('date6').value+' '+$('cbhora2').value+':'+$('cbminuto2').value+':00';
			if(team <= teat){
				alert('<?=_('La hora maxima no puede ser mayor a la hora minima!!!')?>');
			}else{
				if(confirm('<?=_('ESTA SEGURO QUE DESEA REPROGRAMAR TIEMPOS?')?>')){
					new Ajax.Request('/app/vista/asistencia/asignacion_proveedor/ajax_reprogramar_tiempo_programado.php',
					{
						method : 'post',
						parameters : {
							IDASISTENCIA : '<?=$idasistencia?>',
							IDUSUARIOMOD : '<?=$idusuario?>',
							TEAT	     :	teat,
							TEAM	     :	team,
							PRIORIDAD  : prioridad,
							ARRCLASIFICACION:'RTPRO'
						},
						onComplete: function(){
							url= 'etapa5.php?&idasistencia=<?=$idasistencia?>';
							reDirigir(url); // pasa a la siguiente etapa
						}
					});
				}

			}
			break;
		}
		case 'GT':  // GUARDAR TIEMPOS SIN REGISTRAR UN PROVEEDOR
		{
			prioridad=document.getElementById('arrprioridadatencion').value;
			proveedor0=document.getElementById('hid_prov0').value;

			document.getElementById('arrprioridadatencion').disabled=true;
			teat = document.getElementById('date5').value+' '+document.getElementById('cbhora1').value+':'+document.getElementById('cbminuto1').value+':00';
			team = document.getElementById('date6').value+' '+document.getElementById('cbhora2').value+':'+document.getElementById('cbminuto2').value+':00';
			coment=document.getElementById('comentario').value;
			if(team <= teat){
				alert('<?=_('LA HORA MINIMA DEBE SER MENOR QUE LA MAXIMA!!')?>');
			}else{
				if(confirm('<?=_('ESTA SEGURO QUE DESEA REGISTRAR LA PROGRAMACION SIN ASIGNAR UN PROVEEDOR ?')?>')){
					new Ajax.Request('/app/vista/asistencia/asignacion_proveedor/ajax_grabar_tiempos.php',
					{
						method : 'post',
						parameters : {
							IDASISTENCIA : '<?=$idasistencia?>',
							IDUSUARIOMOD : '<?=$idusuario?>',
							IDSERVICIO   : '<?=$idservicio?>',
							IDPROVEEDOR  : proveedor0,
							PRIORIDAD    : prioridad,

							COMENTARIOBITACORA : coment,
							TEAT	     :	teat,
							TEAM	     :	team
						},
						onComplete: function(t){

							url= 'etapa2.php?&idasistencia=<?=$idasistencia?>';
							reDirigir(url); // pasa a la siguiente etapa

						}
					});
				}

			}
			break;
		}

	}
	return;
}

function Sugerir(){
	fecha1=document.getElementById('date5').value;
	hora1=document.getElementById('cbhora1').value;
	minuto1=document.getElementById('cbminuto1').value;
	hora = parseFloat(hora1)+2;
	if(hora<=9){
		horax='0'+hora;
	}else{
		horax=hora;
	}
	if(horax==24){
		horax='00';
	}

	document.getElementById('date6').value=fecha1;
	document.getElementById('cbhora2').value=horax;
	document.getElementById('cbminuto2').value=minuto1;
}

function Sugerir_Eme(){

	minutomax = document.getElementById('cbminutomax').value;
	minutomin = document.getElementById('cbminutomin').value;

	nuevomin = parseFloat(minutomin)+10;

	document.getElementById('cbminutomax').value=nuevomin;

}


function Asignar(prov,nombre,localforaneo)
{
	prioridad=$F('arrprioridadatencion');
	if (($('cbminutomax').value=='') && (prioridad =='EME') ) alert ("<?=_('NO SELECCIONO LOS MINUTOS MAXIMOS')?>");
	else
	{
		if ('<?=$asis->servicio->validaciontiempo?>'=='0'){
			minutomax = 10;
			minutomin = 0;
		}
		else
		{
			minutomax = eval($('cbminutomax').value);
			minutomin = eval($('cbminutomin').value);
		}


		teat = $('date5').value+' '+$('cbhora1').value+':'+$('cbminuto1').value+':00';
		team = $('date6').value+' '+$('cbhora2').value+':'+$('cbminuto2').value+':00';

		try{
			coment=$('comentario').value;
		}catch(e){
			coment='';
		}

		mensaje = "ASIGNACION DE PROVEEDOR\n";
		mensaje += "PROVEEDOR : "+nombre+"\n";
		mensaje += "PRIORIDAD DE ATENCION : "+prioridad+"\n";

		if (prioridad=='EME')
		{
			minutomin = minutomax-5;
			if(confirm('<?=_('ESTA SEGURO QUE DESEA ASIGNAR AL PROVEEDOR?')?>')){

				new Ajax.Request('/app/vista/asistencia/asignacion_proveedor/ajax_grabar_asignacion.php',
				{
					method : 'post',
					parameters : {
						IDASISTENCIA : '<?=$idasistencia?>',
						IDEXPEDIENTE : '<?=$idexpediente?>',
						IDUSUARIOMOD : '<?=$idusuario?>',
						IDSERVICIO   : '<?=$idservicio?>',
						IDPROVEEDOR  : prov,
						PRIORIDAD    : prioridad,
						MINUTOMIN      : minutomin, // modificado segun nueva definicion - EMERGENCIA
						MINUTOMAX      : minutomax, // modificado segun nueva definicion -EMERGENCIA
						LOCALFORANEO : localforaneo,
						TEAT	     :	teat,
						TEAM	     :	team,
						NOMBRE       :	nombre,
						COMENTARIO   :  mensaje,
						COMENTARIOBITACORA : coment,
						ARRCLASIFICACION: 'ASIG'
					},
					onComplete: function(t)
					{
						new Ajax.Request('/app/controlador/ajax/ajax_grabar_deficienciasE2.php',
						{
							method : 'post',
							parameters :
							{
								IDASISTENCIA : '<?=$idasistencia?>',
								IDEXPEDIENTE : '<?=$idexpediente?>',
								ARRPRIORIDADATENCION : prioridad
							},
							onComplete: function()
							{
								if (<?=$asis->servicio->conclucionconproveedor?>)
								url= 'etapa7.php?&idasistencia=<?=$idasistencia?>';
								else
								url= 'etapa3.php?&idasistencia=<?=$idasistencia?>';

								reDirigir(url); // pasa a la siguiente etapa
							}
						});
					}
				});
			}
		}
		else if(prioridad=='PRO')
		{
			if(teat >= team)alert('<?=_('LA HORA MINIMA NO PUEDE SER MAYOR QUE LA MAXIMA!!')?>');
			else{
				if(confirm('<?=_('ESTA SEGURO QUE DESEA ASIGNAR AL PROVEEDOR?')?>')){

					new Ajax.Request('/app/vista/asistencia/asignacion_proveedor/ajax_grabar_asignacion.php',
					{
						method : 'post',
						parameters : {
							IDASISTENCIA : '<?=$idasistencia?>',
							IDUSUARIOMOD : '<?=$idusuario?>',
							IDSERVICIO   : '<?=$idservicio?>',
							IDPROVEEDOR  : prov,
							PRIORIDAD    : prioridad,
							MINUTOMIN      : minutomin, // modificado segun nueva definicion - EMERGENCIA
							MINUTOMAX      : minutomax, // modificado segun nueva definicion -EMERGENCIA
							LOCALFORANEO : localforaneo,
							TEAT	     :	teat,
							TEAM	     :	team,
							NOMBRE       :	nombre,
							COMENTARIO   :  mensaje,
							COMENTARIOBITACORA : coment,
							ARRCLASIFICACION: 'ASIG'

						},
						onComplete: function(t){
							//						alert(t.responseText);
							actualizar_bitacora();
							if (<?=$asis->servicio->conclucionconproveedor?>) 	url= 'etapa7.php?&idasistencia=<?=$idasistencia?>';
							else
							new Ajax.Request('/app/controlador/ajax/ajax_grabar_deficienciasE2.php',
							{
								method : 'post',
								parameters :
								{
									IDASISTENCIA : '<?=$idasistencia?>',
									IDEXPEDIENTE : '<?=$idexpediente?>'
								},
								onComplete: function(t)
								{
									url= 'etapa3.php?&idasistencia=<?=$idasistencia?>';
									reDirigir(url); // pasa a la siguiente etapa
								}
							});


						}
					});
				} /* fin del else conirm */
			} /*  fin del else del team */
		} /* fin del else PRO*/
	}
}

function ventana_deficiencia()
{
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("AGREGAR DEFICIENCIA")?>',
			width: 700,
			height: 260,
			//			showEffect: Element.show,
			//			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "/app/vista/calidad/agregardeficiencia.php?idetapa=<?=$idetapa?>&idasistencia=<?=$idasistencia?>"

		});
		win.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			lastSpy();
			if (win1 == win) {
				win = null;
				Windows.removeObserver(this);
			}
		}
		}
		Windows.addObserver(myObserver);
	}
}



function det_deficiencia(idprincipal,def,nom,origen,num,idetapa,prov,cord,param,titulo){
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: titulo,
			width: 450,
			height: 200,
			//			showEffect: Element.show,
			//			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "/app/vista/calidad/validardeficiencia.php?idasistencia=<?=$idasistencia?>&cvedeficiencia="+def+"&nom="+nom+"&origen="+origen+"&idetapa="+idetapa+"&numero="+num+"&prov="+prov+"&cord="+cord+"&param="+param+"&idprincipal="+idprincipal


		});
		win.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			lastSpy();
			if (win1 == win) {
				win = null;
				Windows.removeObserver(this);
			}
		}
		}
		Windows.addObserver(myObserver);
	}


}

var win3 = null
function bitacora_deficiencia(idprincipal,def,nom,num,origen,idetapa,prov,cord){
	//alert(idetapa);
	if (win3 != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win3 = new Window({
			className: "alphacube",
			title: '<?=_("BITACORA DEFICIENCIA")?>',
			width: 650,
			height: 250,
			//			showEffect: Element.show,
			//			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "/app/vista/calidad/vista_deficiencia_bitacora.php?cvedeficiencia="+def+"&nombre="+nom+"&idasistencia=<?=$idasistencia?>&idetapa="+idetapa+"&numero="+num+"&origen="+origen+"&prov="+prov+"&cord="+cord+"&idprincipal="+idprincipal


		});
		win3.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			if (win1 == win3) {
				win3 = null;
				Windows.removeObserver(this);
			}
		}
		}
		Windows.addObserver(myObserver);
	}


}




function just(idproveedor,modo,prov_lin)
{
	Dialog.confirm("<? echo $justificacion ?>",
	{
		top: 200,
		width:520,
		//		showEffect: Element.show,
		//		hideEffect: Element.hide,
		className: "alphacube",
		okLabel: "GUARDAR",
		cancelLabel:"CANCELAR",
		buttonClass: "normal",
		onOk: function(dlg)
		{
			grabarjustificacion(idproveedor,modo);
			if (modo=='AUTO') sgt_prov(prov_lin);
			return true;
		},
	});   // FIN DEL DIALOG
	return;
}

function sgt_prov(prov_lin){
	var ant = eval(prov_lin);
	var prox = eval(prov_lin)+1;

	$('lin_'+ant).style.display='none';
	try{
		$('lin_'+prox).style.display='table-row';}
		catch(e){
			return;

		};

		return;
}




function reasignar_proveedor()
{
	if(confirm('<?=_('ESTA SEGURO QUE DESEA REASIGNAR AL PROVEEDOR?')?>')){
		Dialog.confirm("<? echo $justificacion_reasig ?>",
		{
			top: 200,
			width:480,
			//			showEffect: Element.show,
			//			hideEffect: Element.hide,
			className: "alphacube",
			okLabel: "Guardar",
			cancelLabel:"Cancelar",
			buttonClass: "normal",
			onOk: function(dlg)
			{
				observacion = document.getElementById('OBSERVACION').value;
				motivo = document.getElementById('JUSTIFICACION').value;

				if(motivo=='OTROS' && observacion=='')	alert('<?=_('DEBE INGRESAR UNA OBSERVACION!!')?>');
				else if(motivo=='SEL') 	alert('<?=_('DEBE SELECIONAR UNA JUSTIFICACION!!')?>');
				else
				{

					observacion = document.getElementById('OBSERVACION').value;
					motivo = document.getElementById('JUSTIFICACION').value;
					new Ajax.Request('/app/vista/justificacion/grabar_justificacion.php',
					{
						method : 'post',
						parameters : {
							IDASISTENCIA : '<?=$idasistencia?>',
							IDUSUARIOMOD : '<?=$idusuario?>',
							OBSERVACION  : observacion,
							JUSTIFICACION : motivo,
							IDPROVEEDOR  : '<?=$proveedor_act?>',
							STATUS       : 'REASIG',
							ARRCLASIFICACION: 'REASIG'
						},
						onComplete: function(t){
							new Ajax.Request('/app/controlador/ajax/ajax_reasignar_proveedor.php',
							{
								method : 'post',
								parameters : {
									IDASISTENCIA : '<?=$idasistencia?>',
									IDUSUARIOMOD : '<?=$idusuario?>',
									JUSTIFICACION : motivo,
									IDPROVEEDOR  : '<?=$proveedor_act?>'

								},
								onComplete: function(t){
									//									alert(t.responseText);
									url= 'etapa2.php?&idasistencia=<?=$idasistencia?>';
									reDirigir(url); // pasa a la siguiente etapa
								}
							});

						}
					});
					//grabarjustificacion('<?=$proveedor_act?>','REASIG');
					//sorter.move(1);
					return true;
				}
			},
		}); // FIN DEL DIALOG
	}
	return;
}


function reprogramar_tiempos()
{
	Dialog.confirm("<? echo $justificacion_repro ?>",
	{
		top: 200,
		width:480,
		//		showEffect: Element.show,
		//		hideEffect: Element.hide,
		className: "alphacube",
		okLabel: "Guardar",
		buttonClass: "normal",
		cancelLabel:"Cancelar",
		onOk: function(dlg)
		{
			observacion = $('OBSERVACION').value;
			motivo = $('JUSTIFICACION').value;

			if(motivo=='OTROS' && observacion=='') alert('<?=_('DEBE INGRESAR UNA OBSERVACION!!')?>');
			else
			if(motivo=='SEL') alert('<?=_('DEBE SELECIONAR UNA JUSTIFICACION!!')?>');
			else
			{
				new Ajax.Request('/app/vista/justificacion/grabar_justificacion.php',
				{
					method : 'post',
					parameters :
					{
						IDASISTENCIA : '<?=$idasistencia?>',
						IDUSUARIOMOD : '<?=$idusuario?>',
						OBSERVACION  : observacion,
						JUSTIFICACION : motivo,
						IDPROVEEDOR  : '<?=$proveedor_act?>',
						STATUS       : 'REP',
						ARRCLASIFICACION: 'REP'
					},
					onComplete: function(t)
					{
						/* cancela las tareas pendientes */



						prioridad= document.getElementById('arrprioridadatencion').value;
						if (prioridad=='EME')
						{
							document.getElementById('arrprioridadatencion').disabled=false;
							$('proceso_asignacion').style.display='block';
							$('zonatiempo1').style.display='none';
							$('zonatiempo2').style.display='block';
							document.getElementById('grabartiempoeme').style.display='block';
							document.getElementById('tableeme').style.display='none';
							document.getElementById('tableeme_re').style.display='block';
						}
						else if (prioridad=='PRO')
						{
							$('proceso_asignacion').style.display='block';
							$('zonatiempo1').style.display='none';
							$('zonatiempo2').style.display='block';
							document.getElementById('arrprioridadatencion').disabled=false;
							document.getElementById('datos_disponibilidad').style.display='block';
							document.getElementById('grabartiempopro').style.display='block';
							document.getElementById('zona_asignacion_expediente').style.display='none';
							document.getElementById('barrasuperior').style.display='none';
						}
						//							}
						//						});
					}
				});
			} //FIN DEL ELSE
			return true;
		}
	});
	return;
}


function cancelado_posterior()
{

	if (confirm('<?=_('ESTA SEGURO QUE DESEA CANCELAR LA ASISTENCIA?')?>'))
	{
		Dialog.confirm("<? echo $justificacion_cp ?>",
		{
			top: 200,
			width:500,
			//			showEffect: Element.show,
			//			hideEffect: Element.hide,
			className: "alphacube",
			okLabel: "Guardar",
			cancelLabel:"Cancelar",
			buttonClass: "normal",
			onOk: function(dlg)
			{
				observacion = $('OBSERVACION').value;
				motivo = $('JUSTIFICACION').value;

				if(motivo=='OTROS' && observacion=='') alert('<?=_('DEBE INGRESAR UNA OBSERVACION!!')?>');
				else if(motivo=='SEL') alert('<?=_('DEBE SELECIONAR UNA JUSTIFICACION!!')?>');
				else
				{
					new Ajax.Request('/app/vista/justificacion/grabar_justificacion.php',
					{
						method : 'post',
						parameters :
						{
							IDASISTENCIA : '<?=$idasistencia?>',
							IDUSUARIOMOD : '<?=$idusuario?>',
							OBSERVACION  : observacion,
							JUSTIFICACION : motivo,
							IDPROVEEDOR  : '<?=$proveedor_act?>',
							STATUS       : 'CP',
							ARRCLASIFICACION: 'CP'
						},
						onComplete: function(t)
						{
							new Ajax.Request('/app/controlador/ajax/ajax_cancelado_posterior.php',
							{
								method : 'post',
								parameters : {
									IDASIGPROV : '<?=$idasigprov?>',
									IDASISTENCIA : '<?=$idasistencia?>',
									IDUSUARIOMOD : '<?=$idusuario?>',
									JUSTIFICACION : motivo,
									IDPROVEEDOR  : '<?=$proveedor_act?>'
								},
								onComplete: function(t)
								{
									parent.close();

								}
							});
						}
					});
				} // fin del else

			}
		});
	}
}




function cancelado_momento()
{
	if(confirm('<?=_('ESTA SEGURO QUE DESEA CANCELAR LA ASISTENCIA?')?>'))
	{
		Dialog.confirm("<? echo $justificacion_cm ?>",
		{
			top: 200,
			width:480,
			//			showEffect: Element.show,
			//			hideEffect: Element.hide,
			className: "alphacube",
			okLabel: "Guardar",
			cancelLabel:"Cancelar",
			buttonClass: "normal",
			onOk: function()
			{

				observacion = $('OBSERVACION').value;
				motivo = $('JUSTIFICACION').value;

				if (motivo=='OTROS' && observacion=='') alert('<?=_('DEBE INGRESAR UNA OBSERVACION!!')?>');
				else if(motivo=='SEL')	alert('<?=_('DEBE SELECIONAR UNA JUSTIFICACION!!')?>');
				else{

					new Ajax.Request('/app/controlador/ajax/ajax_cancelado_momento.php',
					{
						method : 'post',
						parameters : {
							IDASISTENCIA : '<?=$idasistencia?>',
							IDUSUARIOMOD : '<?=$idusuario?>',
							JUSTIFICACION : motivo,
							IDPROVEEDOR  : '<?=$proveedor_act?>'
						},
						onComplete: function(t)
						{
							new Ajax.Request('/app/vista/justificacion/grabar_justificacion.php',
							{
								method : 'post',
								parameters :
								{
									IDASISTENCIA : '<?=$idasistencia?>',
									IDUSUARIOMOD : '<?=$idusuario?>',
									OBSERVACION  : observacion,
									JUSTIFICACION : motivo,
									STATUS       : 'CM',
									ARRCLASIFICACION: 'CM'
								},
								onComplete: function(t)
								{
									parent.close();

								}
							});
						}
					});



					return true;
				}
			},
		});
	}
	return;
}


function mostrar_comentario(campo,zona)
{
	var my_tooltip = new Tooltip(campo, zona);
	return;
}


function grabar_monitoreo_demora()
{
	$('arrclasificacion').value = 'DEM_ASIG';
	$('comentario').value ='MONITOREO DEMORA EN ASIGNACION '+ $F('comentario');
	new Ajax.Request('/app/controlador/ajax/ajax_grabar_monitoreo_demora.php',
	{
		method : 'post',
		parameters : $('form_bitacora').serialize(true),
		onComplete: function()
		{
			$('comentario').value = '';
			$('btn_demora').disabled = true;

			actualizar_bitacora();

		}
	});
	return;
}

</script>


<?
if($gestion){
	?>
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="/librerias/scriptaculous/scriptaculous.js"></script>
	<script type="text/javascript" src="/estilos/functionjs/func_global.js"></script>
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/effects.js"></script>
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/window.js"></script>
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/window_ext.js"></script> 
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/window_effects.js"></script>
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/debug.js"></script>

	<link href="/librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" ></link>
	<link href="/librerias/windows_js_1.3/themes/spread.css" rel="stylesheet" type="text/css" ></link>
	<link href="/librerias/windows_js_1.3/themes/alert.css" rel="stylesheet" type="text/css" ></link>
	<link href="/librerias/windows_js_1.3/themes/alert_lite.css" rel="stylesheet" type="text/css" ></link>
	<link href="/librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" ></link>
	<link href="/librerias/windows_js_1.3/themes/debug.css" rel="stylesheet" type="text/css" ></link>
<script type="text/javascript" >


/* ***************** Calidad ***************************/

//Calidad

function lastSpy() {

	new Ajax.Updater('listado_de_servicios','../calidad/form_consolidado.php',
	{
		method : 'get',
		parameters : {idasistencia : '<?=$idasistencia?>',idetapa : '<?=$idetapa?>',pais : '<?=$_GET["pais"]?>'},

	});
}

Event.observe(window, 'load', lastSpy, false);


/*
$('#btn_ver_mapa').click(function(){
buscar(1,'desc',1);
var pais = '<?=$pais?>';
var idasistencia='<?=$idasistencia?>';
var url_get = '?pais='+pais+'&idasistencia='+idasistencia +'&'+$('#form_buscar_manual').serialize();

var ancho ='800px';
var alto  ='500px';

$.newWindow(
{id:"win_mapa_proveedores",
title:"MAPA PROVEEDORES",
width: ancho,
height: alto,
posx: 50,
posy: 50,
modal:true,
resizeable: false,
});
$.updateWindowContent("win_mapa_proveedores", "<iframe src='asignacion/mapa_asignacion.php"+url_get+"' width='"+ancho +"' height='"+alto+"' />");
});
*/
var win = null;

new Event.observe('btn_agregar','click',function(){

	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("AGREGAR DEFICIENCIA")?>',
			width: 700,
			height: 265,
			showEffect: Element.show,
			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "../calidad/form_agregardeficiencia.php?pais=<?=$_GET["pais"]?>&idetapa=<?=$idetapa?>&expediente=<?=$idexpediente?>&asistencia=<?=$idasistencia?>&nombreEtapa=<?=_("APERTURA ASISTENCIA")?>"
		});
		win.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			lastSpy();
			if (win1 == win) {
				win = null;
				Windows.removeObserver(this);
			}
		}
		}
		Windows.addObserver(myObserver);
	}
});

</script>
	<?
}
?>