<?
session_start();
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_moneda.inc.php');
include_once('../../modelo/clase_ubigeo.inc.php');
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
include_once('../../modelo/clase_proveedor.inc.php');
include_once('../../modelo/clase_expediente.inc.php');
include_once('../../modelo/clase_asistencia.inc.php');
include_once('../../modelo/functions.php');
include_once("../../vista/login/Auth.class.php");

//VALIDAR USUARIO LOGUEADO
Auth::required();

/* DATOS QUE VIENEN DE LA SESSION */
$idusuario = $_SESSION[user];
$idextension= $_SESSION[extension];
$idasistencia = $_GET[idasistencia];
if (isset($_GET[idasistencia]))
{
	$asis = new asistencia();
	$asis->carga_datos($_GET[idasistencia]);
	$idexpediente= $asis->expediente->idexpediente;
	$idfamilia =$asis->familia->idfamilia;
	$arrcondicionservicio=$asis->arrcondicionservicio;
}
else
{
	/* DATOS QUE VIENEN DEL EXTERIOR*/
	$idexpediente=  $_POST[idexpediente];
	$idfamilia= $_POST[idfamilia];
	$_POST[cobertura]=1;
	if ($_POST[cobertura]) $arrcondicionservicio = 'COB';

}

/* CREANDO OBJETOS PARA EXTRACCION DE DATOS */
$fam= new familia();
$fam->carga_datos($idfamilia);

$exp= new expediente();
$exp->carga_datos($idexpediente);


/* NOMBRE COMERCIAL  DEL SERVICIO */
$prog = new programa_servicio();
$prog->carga_datos($asis->idprogramaservicio);
$nombreservicio =$prog->etiqueta;
$nombre_servicio = ($nombreservicio!='')?$nombreservicio:$asis->servicio->descripcion;


/* NOMBRE COMERCIAL  DEL SERVICIO */
$prog = new programa_servicio();
$prog->carga_datos($asis->idprogramaservicio);
$nombreservicio =$prog->etiqueta;
$nombre_servicio = ($nombreservicio!='')?$nombreservicio:$asis->servicio->descripcion;


/* VARIABLES */
$afiliado = $exp->personas[TITULAR][NOMBRE].' '.$exp->personas[TITULAR][APPATERNO].' '.$exp->personas[TITULAR][APMATERNO];
$contactante = $exp->personas[CONTACTO][NOMBRE].' '.$exp->personas[CONTACTO][APPATERNO].' '.$exp->personas[CONTACTO][APMATERNO];
$idcontacto = $exp->contacto->idcontacto;
$cuenta = $exp->cuenta->nombre;
$plan = $exp->programa->nombre;
$etapa = new etapa();
$idetapa=5;
$etapa->carga_datos($idetapa); // inicia en la etapa 1


/* SI LA ASISTENCIA DE CONCLUCION TEMPRANA PASA DE FRENTE A LA ETAPA 8*/
$idetapa_asis= ($asis->servicio->concluciontemprana)?8:$asis->etapa->idetapa;
/*
if ($asis->servicio->concluciontemprana) {
	$idetapa_asis=8; // AL FINAL
	if ($asis->servicio->conclucionconproveedor && $asis->etapa->idetapa<=7 ) $idetapa_asis=7;  // AL COSTEO
}
else $idetapa_asis=$asis->etapa->idetapa;  // A LA ETAPA QUE LE CORRESPONDE
*/

//$idetapa_asis= ($asis->servicio->concluciontemprana)?8:$asis->etapa->idetapa;

//$asis->carga_bitacora_etapa($idasistencia,$idetapa);
$ubigeo = $exp->ubigeo;
$idprograma = $exp->programa->idprograma;

if($idetapa_asis>5){
	$disabled2='disabled';
}



$fechaarribo = '';
$fechacontacto = '';
$idasigprov='';

foreach ($asis->proveedores as $prov)
{
	if ($prov[statusproveedor]=='AC')
	{
		$proveedor_act= $prov[idproveedor];
		$fechaarribo = $prov[fechaarribo];
		$fechacontacto = $prov[fechacontacto];
		$idasigprov=$prov[idasigprov];
	}
}



$titulo = 'Asistencia # '.$idasistencia .'-> Etapa '. $idetapa_asis;

include_once('../includes/arreglos.php');
include_once('../includes/head_prot_win.php');
?>
<link href="../../../estilos/plantillas/menu.css" rel="stylesheet" type="text/css" />	
<script src="../asistencia/principal/mmenu.js" type="text/javascript"></script>
<title><?=_('ASISTENCIA').' # '.$idasistencia?></title>
<body  style="overflow:scroll;overflow-x:hidden;">
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
<div>
	<fieldset style="background: #ECE9D8">
	<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_proveedor').toggle();"><?=_('PROVEEDORES')?></legend>
		<div id='datos_proveedor'>
		<? include_once('vista_datos_proveedor.php')?>
		</div>
	</fieldset>		
</div>


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


<div id='listado_de_servicios' align="center" style="weight:48%"></div>
<br>
<br>
 
<div id='barra' style="float:right">
<? 

if (in_array($asis->arrstatusasistencia,array('CM','CP','CON')) )
{
	$btn_reasignar='disabled';
	$btn_can_pos='disabled';
	$btn_mon_afil ='disabled';
}
else
{
	/* VERIFICA SI EXISTEN TAREAS DE CONF_SERV PENDIENTES */
	$sql="
	SELECT
 		IDTAREA
	FROM
 		$asis->temporal.monitor_tarea
	WHERE
 		IDASISTENCIA ='$idasistencia'
 		AND IDTAREA='MON_AFIL'
 		AND STATUSTAREA IN ('PENDIENTE','INVISIBLE')
	";

	$result=$asis->query($sql);
	if (!$result->num_rows) $btn_mon_afil='disabled';

}

$justificacion_reasig=justificacion('RASI');
$justificacion_cp=justificacion('CP');
$justificacion_cm=justificacion('CM');

?>
<div id='barra' class="barraestado" style="float:right">

<? if($gestion):?>	 
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
			<td><input type='button' id='btn_reasignar_proveedor' value="<?=_('REASIGNAR PROVEEDOR')?>"  class='normal' onClick="reasignar_proveedor();" <?=$btn_reasignar?>></td>	
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><input type='button' id='btn_' value="<?=_('CANCELADO POSTERIOR')?>" class='cancelar' onClick="cancelado_posterior();" <?=$btn_can_pos?>></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><input type='button' id='siguiente' value="<?=_('CONFIRMACION REPROGRAMA TIEMPOS A N/A').' >>'?>" class='guardar' onClick="siguiente_etapa()" <?=$btn_mon_afil?> ></td>
		</tr>
	</tbody>
</table>
<?endif;?>		
</div>

</div>


</body>

<script type="text/javascript" src="<?=strtolower($fam->descripcion)?>/validaciones/validaciones.js"></script>

<script type="text/javascript" >

var win = null;
function mod_ubigeo(idubigeo,campo_ubigeo,campo_display){
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("Ubicacion")?>',
			width: 500,
			height: 450,
			//			showEffect: Element.show,
			//			hideEffect: Element.hide,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			resizable: true,
			opacity: 0.95,
			url: "../ubigeo/localizador.php?idubigeo="+idubigeo+"&campo_ubigeo="+campo_ubigeo+"&campo_display="+campo_display
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


function plantilla(vista,plant,idservicio)
{
	validar_func = plant;
	$('idservicio').value = idservicio;
	new Ajax.Updater('form_servicio',vista,
	{
		method : 'post',
		onComplete: function(){
			$('btn_guardar_parcial').disabled = false;
			$('btn_guardar').disabled = false;
		}
	});
	return;

}



function validar(modo){
	var sw1;
	var sw2;
	validar_func = "<?=substr($asis->servicio->plantilla->vista,0,-4)?>";
	$('observacion').innerHTML = $F('justificacion');
	sw1 = validar_datos_generales();
	if (sw1){
		sw2 = new window[validar_func]();
		if (sw2){
			if ($F('arrcondicionservicio')!="<?=$arrcondicionservicio?>")  func_observacion(modo);
			else
			grabar("<?=$fam->descripcion ?>",modo);
		}
	}
	return;
}



function actualizar_bitacora(){
	new Ajax.Updater('zona_bitacora','listar_bitacora_etapa.php',
	{
		method : 'post',
		parameters : {idasistencia : '<?=$idasistencia?>',
		idetapa : '<?=$idetapa?>'},

	});
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
									url= 'etapa2.php?&idasistencia=<?=$idasistencia?>';
									reDirigir(url); // pasa a la siguiente etapa
								}
							});

						}
					});
					return true;
				}
			},
		}); // FIN DEL DIALOG
	}
	return;
}





function grabarjustificacion(xprov,xstatus){
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
			IDPROVEEDOR  : xprov,
			STATUS       : xstatus


		},
		onComplete: function(t){
			//					alert(t.responseText);
			actualizar_bitacora();
			//parent.close();
			//url= 'etapa3.php?&idasistencia=<?=$idasistencia?>';
			//reDirigir(url); // pasa a la siguiente etapa
		}
	});
}


function siguiente_etapa(){
	/* GRABA LA BITACORA  */
	$('arrclasificacion').value='MON_AFIL';
	new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_monitoreo_afiliado.php',
	{
		method : 'post',
		parameters : $('form_bitacora').serialize(true),
		onComplete: function()
		{
			$('comentario').value='';
			new Ajax.Request('/app/controlador/ajax/ajax_grabar_deficienciasE5.php',
			{
				method:'post',
				parameters:{
					IDEXPEDIENTE:'<?=$idexpediente?>',
					IDASISTENCIA: '<?=$idasistencia?>'
				},
				onComplete: function()
				{
					actualizar_bitacora();
					url= 'etapa6.php?idasistencia=<?=$idasistencia?>';
					reDirigir(url); // pasa a la siguiente etapa
				}

			});
		}
	});


	return;
}




//Calidad
<?
if($gestion)
{
	?>
	function lastSpy() {

			 new Ajax.Updater('listado_de_servicios','../calidad/form_consolidado.php',
			{
				method : 'get',
				parameters : {idasistencia : '<?=$idasistencia?>',idetapa : '<?=$idetapa?>'},

			});	
	}
	
	Event.observe(window, 'load', lastSpy, false);
<?
}
?>

new Event.observe('btn_agregar','click',function()
{
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
			url: "../calidad/form_agregardeficiencia.php?idetapa=<?=$idetapa?>&expediente=<?=$idexpediente?>&asistencia=<?=$idasistencia?>&nombreEtapa=<?=_("APERTURA ASISTENCIA")?>"

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



function det_deficiencia(idprincipal,def,nom,origen,num,idetapa,prov,cord,param,titulo){
	//alert(idetapa);
	//alert(def);
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
			okLabel: "GUARDAR",
			cancelLabel:"CANCELAR",
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
							IDASIGPROV: '<?=$idasigprov?>',
							IDASISTENCIA : '<?=$idasistencia?>',
							IDUSUARIOMOD : '<?=$idusuario?>',
							OBSERVACION  : observacion,
							JUSTIFICACION : motivo,
							IDPROVEEDOR  : '<?=$proveedor_act?>',
							STATUS       : 'CP'
						},
						onComplete: function(t)
						{
							new Ajax.Request('/app/controlador/ajax/ajax_cancelado_posterior.php',
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
									parent.close();
									/*	new Ajax.Request('/app/controlador/ajax/ajax_grabar_status.php',
									{
									method : 'post',
									parameters : {
									IDASISTENCIA : '<?=$idasistencia?>',
									IDUSUARIOMOD : '<?=$idusuario?>',
									ARRSTATUSASISTENCIA :'CP'
									},
									onComplete: function(t)
									{
									new Ajax.Request('/app/controlador/ajax/ajax_borrar_tarea.php',
									{
									method: 'post',
									parameters: {
									IDASISTENCIA :'<?=$idasistencia?>'
									},
									onComplete: function()
									{
									parent.close();
									}
									});
									}
									});*/
								}
							});
						}
					});
				} // fin del else

			}
		});
	}
}

</script>