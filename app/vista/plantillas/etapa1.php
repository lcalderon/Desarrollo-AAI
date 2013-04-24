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
include_once('../../modelo/clase_expediente.inc.php');
include_once('../../modelo/clase_asistencia.inc.php');
include_once("../../vista/login/Auth.class.php");

//VALIDAR USUARIO LOGUEADO
Auth::required();

/* DATOS QUE VIENEN DE LA SESSION */
$idusuario = $_SESSION[user];
$idextension= $_SESSION[extension];


/* DATOS QUE VIENEN DEL EXTERIOR*/
$idexpediente=  $_GET[idexpediente];
$idfamilia= $_GET[idfamilia];

if ($_GET[cobertura]) $arrcondicionservicio = 'COB';
else $arrcondicionservicio='CON';

/* CREANDO OBJETOS PARA EXTRACCION DE DATOS */
$fam= new familia();
$fam->carga_datos($idfamilia);
$nombrefamilia= $fam->descripcion;

$exp= new expediente();
$exp->carga_datos($idexpediente);

/* VARIABLES */
$afiliado = $exp->personas[TITULAR][NOMBRE].' '.$exp->personas[TITULAR][APPATERNO].' '.$exp->personas[TITULAR][APMATERNO];
$contactante = $exp->personas[CONTACTO][NOMBRE].' '.$exp->personas[CONTACTO][APPATERNO].' '.$exp->personas[CONTACTO][APMATERNO];
$cuenta = $exp->cuenta->nombre;
$plan = $exp->programa->nombre;


$idetapa=1;  // inicia en la etapa 1
$etapa = new etapa();
$etapa->carga_datos($idetapa);

$idetapa_asis=1;
$idprograma = $exp->programa->idprograma;
$titulo = 'Nueva Asistencia -> Etapa '. $idetapa;

include_once('../includes/arreglos.php');
include_once('../../modelo/functions.php');
include_once('../includes/head_prot_win.php');
?>

<title><?=_('ASISTENCIA').' # '.$idasistencia?></title>
<body onload="carga_inicial();" style="overflow:scroll;overflow-x:hidden;">

<!-- DATOS DEL EXPEDIENTE-->
<div>
	<fieldset style="background: #ECE9D8">
	<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_expediente').toggle();"><?=_('EXPEDIENTE')." $exp->idexpediente --> <font color='#B70E0E'>". $etapa->descripcion ?></font></legend>
		<? include_once('vista_datos_expediente.php');?>
	</fieldset>	
</div>  

<!-- DATOS DE POLIZA PARA GRANATIA EXTENDIDA -->
<? if ($idfamilia==12):?>
<div>
		<? include_once('vista_datospoliza.php');?>
</div>
<?endif;?>

<!-- DATOS DE LA ASISTENCIA-->
<div>
<fieldset style="background: <?=$fam->color?> ">
<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_asistencia').toggle();"><?=$fam->descripcion?></legend>
	<div id='datos_asistencia'>
		<? include_once(strtolower($fam->descripcion).'/vista_general.php'); ?>
	</div>
</fieldset>
</div>
<div id='datos_servicio' >
<fieldset style="background:<?=$fam->color?>" id='form_servicio'>

</div> 


<div id='barra' class='barraestado'>


<table align="right">
	<tbody>
		<tr>
			<td><input type='button' id='btn_guardar' value="<?=_('GUARDAR ASISTENCIA').' >>'?>" class='guardar' onClick="validar('total')" disabled></td>
			<td>&nbsp;</td>			
			<td><input type='button' id='btn_cancelar' value="<?=_('CANCELAR AL MOMENTO')?>" class="cancelar" onclick="cancelar($F('idasistencia'));" disabled ></td>
		</tr>
	</tbody>
</table>
</div> <!--FIN DEL DIV DE LA BARRA-->

</body>

<script type="text/javascript" src="<?=strtolower($fam->descripcion)?>/validaciones/validaciones.js"></script>
<script type="text/javascript" >
var validar_func = '';
var win = null;
var winMAP = false;
/*
function mod_ubigeo(idubigeo,campo_ubigeo,campo_display,tabla){
	if (!winMAP || winMAP.closed){
		 winMAP = window.open("/app/vista/ubigeo/mapas/geolocalizacion.php?idubigeo="+idubigeo+"&campo_ubigeo="+campo_ubigeo+"&campo_display="+campo_display+"&tabla="+tabla,
					'_blank',
					'width=1024,height=768,	top=0,	left=0,	toolbar=no,	scrollbars=no, resizable=no, menubar=no, status=no,	directories=no,	location=no');
	}
	else
		winMAP.focus();
	
}
*/



function mod_ubigeo(idubigeo,campo_ubigeo,campo_display,tabla){
	
	
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("UBICACION")?>',
			width: 500,
			height: 450,
//			showEffect: Element.show,
//			hideEffect: Element.hide,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			resizable: true,
			opacity: 0.95,
			url: "../ubigeo/localizador_asistencia.php?idubigeo="+idubigeo+"&campo_ubigeo="+campo_ubigeo+"&campo_display="+campo_display+"&tabla="+tabla+"&idexpediente="+"<?=$exp->idexpediente?>"
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


function ver_asistencias(idafiliado){

	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("HISTORIAL DE ASISTENCIAS")?>',
			width: 500,
			height: 450,
//			showEffect: Element.show,
//			hideEffect: Element.hide,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			resizable: true,
			opacity: 0.95,
			url: "historico.php?idafiliado="+idafiliado
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


function ver_bitacora(){

	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("BITACORA")?>',
			width: 500,
			height: 450,
//			showEffect: Element.show,
//			hideEffect: Element.hide,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			resizable: true,
			opacity: 0.95,
			url: "form_bitacora.php?idasistencia="+$F('idasistencia')+"&idetapa=1"
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


function plantilla(vista,plant,idservicio,idprogramaservicio,concluciontemprana,conclucionconproveedor)
{
	//alert(vista);
	validar_func = plant;

	$('idservicio').value = idservicio;
	$('idprogramaservicio').value = idprogramaservicio;
	$('concluciontemprana').value = concluciontemprana;
	$('conclucionconproveedor').value = conclucionconproveedor;
	new Ajax.Updater('form_servicio',vista,
	{
		method : 'post',
		parameters: {
			IDSERVICIO: idservicio,
			IDPROGRAMASERVICIO: idprogramaservicio
		},
		onComplete: function(){
			$('btn_guardar').disabled = false;
		}
	});
	return;

}



function validar(modo){ // modo = parcial /final
	$('btn_guardar').disabled=true;
	grabar("<?=$nombrefamilia ?>",modo);
	return;
}


function grabar(familia,modo){
	var sw1;
	var sw2;

	var arch_ajax1= familia.toLowerCase()+'/ajax/ajax_'+familia.toLowerCase()+'.php';
	var arch_ajax2= familia.toLowerCase()+'/ajax/ajax_'+validar_func+'.php';

	sw1 = validar_datos_generales();
	
	if (sw1){
		sw2 = window[validar_func]();
		if (sw2 )
		{
			//alert('s');
			// GRABA LOS DATOS DE LA ASISTENCIA
			new Ajax.Request(arch_ajax1,{
				method: 'post',
				parameters:  $('form_datos_generales').serialize(true),
				evalScripts : true,
				onComplete: function(t)
				{
					// VALIDA SERVICIO VEHICULAR
					if($('idfamilia').value=='1' )
					{
						var elemento= t.responseText.split(',');
						$('idasistencia').value = elemento[0];
						$('idvehiculo').value = elemento[1];
					}
					else
					{
						$('idasistencia').value = t.responseText;
					} //fin del else  VALIDAR VEHICULAR

					new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_apertura.php',
					{
						method : 'post',
						parameters : {
							IDASISTENCIA: $F('idasistencia'),
							IDUSUARIOMOD:'<?=$idusuario?>',
							COMENTARIO: "<?=_('REGISTRO DE LA ASISTENCIA')?>",
							ARRCLASIFICACION: 'REG_ASIS'
						},
						onComplete: function()
						{
							// GRABA LOS DATOS ESPECIFICOS DEL SERVICIO //
							new Ajax.Request(arch_ajax2+'?idasistencia='+$('idasistencia').value,{
								method: 'post',
								parameters:  $('form_'+validar_func).serialize(true),
								onComplete: function(t){
									//							alert(t.responseText);
									$('form_listado').disable();
									$('btn_cancelar').disabled=false;

									switch(modo){
										case 'total':
										{
											if (('<?=$arrcondicionservicio?>' != $F('arrcondicionservicio')) && ($F('justificacion')==''))
											{

												Dialog.confirm($('zona_observacion').innerHTML,
												{
													width:520,
//													showEffect: Element.show,
//													hideEffect: Element.hide,
													className:"alphacube",
													okLabel: "Guardar",
													cancelLabel: "Cancelar",
													buttonClass: "normal",
													onOk: function(dlg)
													{
														$('justificacion').value = $F('observacion');

														// GRABA LA BITACORA
														comentario="SE CAMBIO LA CONDICION DEL SERVICIO A: "+$F('arrcondicionservicio') + "\n";
														comentario+="JUSTIFICACION : "+ $F('observacion');

														new Ajax.Request('/app/controlador/ajax/ajax_grabar_asistencia_bitacora.php',
														{
															method : 'post',
															parameters :{
																IDASISTENCIA: $F('idasistencia') ,
																IDETAPA:"1",
																IDUSUARIOMOD:"<?=$idusuario?>",
																COMENTARIO: comentario,
																ARRCLASIFICACION: 'JUST'
															},
															onComplete: function()
															{
																// GRABA EL MONITOR DE TAREAS Y PASA A LA SIGUIENTE ETAPA
																if (($F('concluciontemprana')=='0') || ($F('conclucionconproveedor')=='1' ))
																{
																	new Ajax.Request('/app/controlador/ajax/ajax_graba_tareas_etapa1.php',
																	{
																		method : 'post',
																		parameters: {
																			IDUSUARIO: '<?=$idusuario?>',
																			IDEXPEDIENTE: '<?=$idexpediente?>',
																			IDASISTENCIA: $F('idasistencia'),
																		},
																		onComplete: function(){
																			url= 'etapa2.php?idasistencia='+$F('idasistencia');
																			reDirigir(url);
																		}
																	});
																}
																else
																url= 'etapa8.php?idasistencia='+$F('idasistencia');
															}
														});

													},
												});
											}
											else {

												if	(<?=$idfamilia?>!=2)
												{

													if (($F('concluciontemprana')=='0') || ($F('conclucionconproveedor')=='1'))
													{
														new Ajax.Request('/app/controlador/ajax/ajax_graba_tareas_etapa1.php',
														{
															method : 'post',
															parameters: {

																IDUSUARIO: '<?=$idusuario?>',
																IDEXPEDIENTE: '<?=$idexpediente?>',
																IDASISTENCIA: $F('idasistencia'),

															},
															onComplete: function(){
																url= 'etapa2.php?idasistencia='+$F('idasistencia');
																reDirigir(url);
															}
														});
													}
													else
													url= 'etapa8.php?idasistencia='+$F('idasistencia');

												}
												else
												//AQUI LAS TAREAS DE GARANTIA EXTENDIDA
												url = 'etapa1_1.php?idasistencia='+$F('idasistencia');


												reDirigir(url); // pasa a la siguiente etapa
											} // fin del else
											break;
										} // fin del caso "total"
									} // fin del switch
								}
							}); //fin del ajax arch2
						}
					});

				}}
				);
		}// fin del if sw2
		else $('btn_guardar').disabled=false;
	} 	// fin del if sw1
	else $('btn_guardar').disabled=false;
	return;
}



// ACTIVA / DESACTIVA LOS RADIOS DE REEMBOLSO
function act_reembolso(){
	$('zona_reembolso').toggle();
	return;
}


// ACTIVA INPUT DE GARANTIA //
function act_garantia()
{
	
	if (($F('arrcondicionservicio')=='GAR') || ($F('arrcondicionservicio')=='CNO') )   $('zona_garantia').toggle();
	return;
}


// PRESENTA LISTA DE SERVICIOS //
function actualizar_lista(arrcondicion)
{
	// SI LA CONDICION DE SERVICIO ES GARANTIA PRESENTA EL INPUT
	if ($F('arrcondicionservicio')=='GAR' || $F('arrcondicionservicio')=='CNO' ) 	$('zona_garantia').show();
	else	$('zona_garantia').hide();

	if ($('idasistencia').value ==''){
		new Ajax.Updater('listado_de_servicios','vista_lista_servicios.php',
		{
			method : 'get',
			evalScripts: true,
			parameters : {
				arrcondicionservicio : arrcondicion,
				idfamilia : '<?=$idfamilia?>',
				idprograma : '<?=$idprograma?>',
			}
		});
	}
	return;
}


// CANCELA LA ASISTENCIA  //
function cancelar(idasistencia){
	if(confirm('Esta seguro de Cancelar la Asistencia!!'))
	{
		new Ajax.Request('/app/controlador/ajax/ajax_cancelar_asistencia.php',
		{
			method : 'post',
			parameters : {
				IDASISTENCIA : idasistencia,
				ARRSTATUSASISTENCIA : 'CM'
			},
			onComplete: function()
			{
				window.close();
			}
		});
	}
	return;
}


function llamada(codigoarea,numero){
	numero = codigoarea+numero;
	new Ajax.Request('/app/controlador/ajax/ajax_llamada.php',
	{
		method : 'get',
		parameters: {
			prefijo: "",
			num: numero,
			ext: '<?=$idextension?>'
		},
		onComplete: function(t){
			//		alert(t.responseText);

		}
	}
	);
	return;
}

function cambioseleccion()
{
	$('justificacion').clear();
	return;
}

function carga_inicial(){
	if ('<?=$idfamilia?>'=='12'){  // familia GARANTIA EXTENDIDA
		$('form_poliza').disable();
		$('btn_guardar').disabled=false;
		plantilla();
	}
	return;
}

</script>