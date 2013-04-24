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
include_once('../../modelo/clase_expediente.inc.php');
include_once('../../modelo/clase_asistencia.inc.php');
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


$idexpediente= $asis->expediente->idexpediente;
$idfamilia= $asis->familia->idfamilia;
//echo $asis->asistencia_familia->IDVEHICULO;

/* CREANDO OBJETOS PARA EXTRACCION DE DATOS */
$fam= new familia();
$fam->carga_datos($idfamilia);

$exp= new expediente();
$exp->carga_datos($idexpediente);

/* VARIABLES */
$afiliado = $exp->personas[TITULAR][NOMBRE].' '.$exp->personas[TITULAR][APPATERNO].' '.$exp->personas[TITULAR][APMATERNO];
$contactante = $exp->personas[CONTACTO][NOMBRE].' '.$exp->personas[CONTACTO][APPATERNO].' '.$exp->personas[CONTACTO][APMATERNO];
$cuenta = $exp->cuenta->nombre;
$plan = $exp->programa->nombre;

$idetapa=1;
$etapa = new etapa();
$etapa->carga_datos($idetapa); // inicia en la etapa 1

/* SI LA ASISTENCIA DE CONCLUCION TEMPRANA PASA DE FRENTE A LA ETAPA 8*/
$idetapa_asis= ($asis->servicio->concluciontemprana)?8:$asis->etapa->idetapa;


/*  CARGA LOS DATOS */
$asis->carga_bitacora($idasistencia,$idetapa);

if($idetapa_asis>1){
	$disabled2='disabled';
}

$ubigeo = $exp->ubigeo;
$idprograma = $exp->programa->idprograma;

$titulo = 'Asistencia # '.$idasistencia .'-> Etapa '. $idetapa_asis;

include_once('../includes/arreglos.php');
include_once('../../modelo/functions.php');
include_once('../includes/head_prot_win.php');

// DESACTIVA LOS BOTONES SI LA ASISTENCIA ESTA CONCLUIDA/CANCELADO POSTERIOR/CANCELADO AL MOMENTO
if (isset($asis)) {
	$desactivado='';
	if (in_array($asis->arrstatusasistencia,array('CM','CON','CP'))) $desactivado='disabled';
}

if($asis->arrstatusasistencia=='PRO'){
	$desactivado_cal='disabled';
}
?>
<head>
<link href="../../../estilos/plantillas/menu.css" rel="stylesheet" type="text/css" />	
<script src="../asistencia/principal/mmenu.js" type="text/javascript"></script>
<title><?=_('ASISTENCIA').' # '.$idasistencia?></title>

</head>

<body onload="inicio();" style="overflow:scroll;overflow-x:hidden;">
<? 
//   INCLUSION DEL MENU FLOTANTE // desactivar_opciones();
//si es calidad cambia la ubicacion de los menus desplegables
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
<!--fin del DIV   DATOS DEL EXPEDIENTE-->
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


<div>
<fieldset id='form_familia' style="background: <?=$fam->color?> ">
	<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_asistencia').toggle();"><?=$fam->descripcion?></legend>		
		<div id='datos_asistencia'>
			<? include_once(strtolower($fam->descripcion).'/vista_general.php'); ?>
		</div>			
	</fieldset>
</div>  <!--fin del DIV   DATOS DE LA FAMILIA -->

<div>
<fieldset id='form_servicio' style="background: <?=$fam->color?>" >
	<div id='datos_servicio'>	
		<?	$url_servicio= strtolower($asis->familia->descripcion.'/'.$asis->servicio->plantilla->vista);
			if (isset($asis)) include_once($url_servicio);
		?>
	</div>		
</fieldset>
</div> 

<!--fin del DIV   DATOS DEL SERVICIO-->
<? if ($idfamilia==12):?> 

<div >
<fieldset style="background:#ECE9D8;">
<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_actividades').toggle();"><?=_('ACTIVIDADES')?></font></legend>
<div id='datos_actividades'>
	<? include_once(strtolower($fam->descripcion).'/vista_actividades.php');?>
</div>
</fieldset>
</div>
<?endif;?>

  <!--ZONA DE BITACORA-->
<div >
<fieldset>
 <legend><img  style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('zona_bitacora').toggle();"><?=_('BITACORA')?></legend>
  <div id='zona_bitacora' >
	<div>
		<? include_once('../asistencia/asignacion/vista_form_bitacora.php');?>
	</div>
	<div id='listado_bitacora' style="height:180px;overflow:auto; overflow-x:hidden;">
		<? include_once('listar_bitacora_etapa.php'); ?>
	</div>
  </div>	
</fieldset>
</div>


<div id='barra' class='barraestado' >
<table align="right" width="100%">
	<tbody>
		<tr>
		<!--td style="font-size:16px;width:167px;border:solid;background-color:#0000a0;color:#ffff00" title="Hora del Sistema"><strong><div id="horaSistema">&nbsp;<?=date("Y-m-d H:i:s");?></div></strong></td-->
		<?
		if($gestion)
		{
		?>
		
			<td align="right"><input type='button' id='btn_agregar' value="<?=_('AGREGAR DEFICIENCIA').''?>" class='guardar' ></td>
		<?
		}
		else
		{
		?>	
			<td align="right"><input type='button' id='btn_guardar_parcial' value="<?=_('GUARDAR ASISTENCIA').''?>" class='guardar' onClick="validar('parcial')" <?=$desactivado?> ></td>
		<?
		}
		?>
		</tr>
	</tbody>
</table>

</div> <!--FIN DEL DIV DE LA BARRA-->
</body>

<script type="text/javascript" src="<?=strtolower($fam->descripcion)?>/validaciones/validaciones.js"></script>

<script type="text/javascript" >


var validar_func = '';
var win = null;
function mod_ubigeo(idubigeo,campo_ubigeo,campo_display,tabla){
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("Ubicacion")?>',
			width: 500,
			height: 450,
			showEffect: Element.show,
			hideEffect: Element.hide,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			resizable: true,
			opacity: 0.95,
			url: "../ubigeo/localizador_asistencia.php?idubigeo="+idubigeo+"&campo_ubigeo="+campo_ubigeo+"&campo_display="+campo_display+"&tabla="+tabla
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
			showEffect: Element.show,
			hideEffect: Element.hide,
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
			showEffect: Element.show,
			hideEffect: Element.hide,
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


function validar(modo){ // modo = parcial /final
	validar_func = "<?=substr($asis->servicio->plantilla->vista,0,-4)?>";
	grabar("<?=$fam->descripcion ?>",modo);
	return;
}


function grabar(familia,modo){
	var sw1;
	var sw2;

	var arch_ajax1= familia.toLowerCase()+'/ajax/ajax_'+familia.toLowerCase()+'.php';
	var arch_ajax2= familia.toLowerCase()+'/ajax/ajax_'+validar_func+'.php';

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
			}else{
				$('idasistencia').value = t.responseText;
//				$('bitacora').disabled = false;

			}
			// GRABA LOS DATOS ESPECIFICOS DEL SERVICIO //
			new Ajax.Request(arch_ajax2+'?idasistencia='+$('idasistencia').value,{
				method: 'post',
				parameters:  $('form_'+validar_func).serialize(true),
				onComplete: function(t){
					sw1 = validar_datos_generales();
					if (sw1)
					{
						sw2 = window[validar_func]();
						if (sw2 )
						{
							switch(modo){
								case 'parcial':
								{
									if (('<?=$asis->arrcondicionservicio?>' != $F('arrcondicionservicio')) && ($F('justificacion')==''))
									{
										Dialog.confirm($('zona_observacion').innerHTML,
										{
											className:"alphacube",
											width:400,
											showEffect: Element.show,
											hideEffect: Element.hide,
											okLabel: "<?=_('Aceptar')?>",
											cancelLabel: "<?=_('Cancelar')?>",
											buttonClass: 'normal',
											onOk: function(win)
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
														IDETAPA:"1", // etapa de costeo
														IDUSUARIOMOD:"<?=$idusuario?>",
														COMENTARIO: comentario
													},
													onComplete: function()
													{
														alert("<?=_('SE GRABO LA ASISTENCIA ') . $idasistencia ?>");
														location.reload();
													}
												});
												return true;
											}
										}
										);
									}
									else{
										alert("<?=_('SE GRABO LA ASISTENCIA ') . $idasistencia ?>");
										location.reload();
									}
									break;
								}

							} //fin del switch
						} //fin del if sw1
					} // fin del if sw2
				}
			});
		}
	});
	return;
}



function actualizar_lista(arrcondicion){
	if ($('idasistencia').value ==''){
		new Ajax.Updater('listado_de_servicios','vista_lista_servicios.php',
		{
			method : 'get',
			parameters : {
				arrcondicionservicio : arrcondicion,
				idfamilia : '<?=$idfamilia?>',
				idprograma : '<?=$idprograma?>',
			}
		});
	}
	return;
}


function act_reembolso(){
	$('zona_reembolso').toggle();
	return;
}



function act_garantia(){
	if ($F('arrcondicionservicio')=='GAR') $('zona_garantia').show();
	else $('zona_garantia').hide();
	return;
}

function inicio(){
	if ('<?=$asis->arrcondicionservicio?>'=='GAR') $('zona_garantia').show();
	if ('<?=$asis->reembolso?>'=='1') $('zona_reembolso').show();

	return;
}


function cambioseleccion()
{

	if (<?=count($asis->costos)?>) alert("<?=_('HAY COSTOS DEFINIDOS QUE DEBE MODIFICAR AL CAMBIAR LA CONDICION DEL SERVICIO')?>");
	$('justificacion').clear();
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
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: titulo,
			width: 450,
			height: 200,
			showEffect: Element.show,
			hideEffect: Element.hide,
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
			showEffect: Element.show,
			hideEffect: Element.hide,
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

//actualizar Hora del sistema
/* 	function recargar_horaSistema(){

		new Ajax.PeriodicalUpdater('horaSistema', 'mostrarHora_sistema.php', {
			method: 'post', frequency: 1, decay: 0
		});
		
	} */
</script>