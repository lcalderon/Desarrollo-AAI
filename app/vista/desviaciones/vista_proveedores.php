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
include_once('../../modelo/clase_contacto.inc.php');
include_once('../../modelo/clase_proveedor.inc.php');
include_once('../../modelo/clase_asigprov.inc.php');
include_once('../../modelo/clase_asistencia.inc.php');

/* DATOS QUE VIENEN DE LA SESSION */
$idusuario = $_SESSION[user];
$idextension= $_SESSION[extension];
$idasistencia = $_GET[idasistencia];
if (isset($_GET[idasistencia]))
{
	$asis = new asistencia();
	$asis->carga_datos($_GET[idasistencia]);

	$asigprov = new asigprov();
	$asigprov->carga_datos($asis->proveedores[0][idasigprov]);
	$idexpediente= $asis->expediente->idexpediente;
	$idfamilia =$asis->familia->idfamilia;
	$arrcondicionservicio=$asis->arrcondicionservicio;
}

/* CREANDO OBJETOS PARA EXTRACCION DE DATOS */
$fam= new familia();
$fam->carga_datos($idfamilia);

$exp= new expediente();
$exp->carga_datos($idexpediente);

/* VARIABLES */
$afiliado = $exp->personas[TITULAR][NOMBRE].' '.$exp->personas[TITULAR][APPATERNO].' '.$exp->personas[TITULAR][APMATERNO];
$contactante = $exp->personas[CONTACTO][NOMBRE].' '.$exp->personas[CONTACTO][APPATERNO].' '.$exp->personas[CONTACTO][APMATERNO];
$idcontacto = $exp->contacto->idcontacto;
$cuenta = $exp->cuenta->nombre;
$plan = $exp->programa->nombre;
$etapa = new etapa();
$idetapa=7;
$etapa->carga_datos($idetapa); // inicia en la etapa 1

$idetapa_asis=$asis->etapa->idetapa;
$asis->carga_bitacora($idasistencia,$idetapa);
$ubigeo = $exp->ubigeo;
$idprograma = $exp->programa->idprograma;

$modo='AUTORIZA';   // modo autorizacion

include_once('../includes/arreglos.php');
include_once('../includes/head_prot_win.php');
?>
<link href="../../../estilos/plantillas/menu.css" rel="stylesheet" type="text/css" />	

<title><?=_('ASISTENCIA').' # '.$idasistencia?></title>
<body style="overflow:scroll;overflow-x:hidden;">

<!-- DATOS DEL EXPEDIENTE -->
<div>
	<fieldset style="background: #ECE9D8">
	<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_expediente').toggle();"><?=_('EXPEDIENTE')." $exp->idexpediente --> <font color='#FFB2AC'>". $etapa->descripcion ?></font></legend>
		<? include_once('../plantillas/vista_datos_expediente.php');?>
	</fieldset>	
</div>  

<!-- DATOS DE LA ASISTENCIA -->
<div>
	<fieldset style="background: #ECE9D8">
	<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_asistencia').toggle();"><?=_('ASISTENCIA')." $idasistencia --><font color='#FFB2A9'>".$asis->servicio->descripcion.'-->'.$desc_status_asistencia[$asis->arrstatusasistencia]?></font></legend>
		<div id='datos_asistencia'>
		<? include_once('../plantillas/vista_datos_asistencia.php')?>
		</div>	
	</fieldset>	
</div> 

<!-- DATOS DE COSTEO -->
<div>
	<fieldset style="background: #ECE9D8">
	<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_costeo').toggle();"><?=_('COSTEO')?></legend>
		<div id='datos_costeo' >
		<?include_once('../catalogos/costos/form_costeo.php')?>
	</div>
	</fieldset>	
</div> 	
	
	
	
<div id='zona_bitacora' style="height:120px;overflow:auto;" >
		<? include_once('../plantillas/listar_bitacora.php')?>
</div>  
<div id='barra' style="float:right">

<table align="right">
	<tbody>
		<tr>
			<td><input type='button' id='btn_cerrar_enviar' value='<?=_('CERRAR Y ENVIAR A SAP')?>' class='normal' onClick="confirmar('<?=$idasistencia?>')"  <?=(($asis->statusautorizaciondesvio =='1')?'disabled':'')?> ></td>
			<td><input type='button' id='btn_guardar_parcial' value='<?=_('GUARDAR BITACORA')?>' class='normal' onClick="guardarbitacora()" <?=$disabled?>></td>
			
		</tr>
	</tbody>
</table>
</div>


</body>

<script type="text/javascript" >
var win = null;
function grabar(modo){

	switch (modo){
		case 'parcial': // GUARDA LA BITACORA
		{
			if (trim($F('comentario'))=='') alert("<?=_('INGRESE UN COMENTARIO ')?>");
			else
			{
				new Ajax.Request('/app/controlador/ajax/ajax_grabar_asistencia_bitacora.php',
				{
					method : 'post',
					parameters : $('form_costeo').serialize(true),
					onSuccess: function()
					{
						$('comentario').value='';
						actualizar_bitacora();
					}
				});
			}
			break;
		}

	}
	return;
}

function actualizar_bitacora(){
	new Ajax.Updater('zona_bitacora','/app/vista/plantillas/listar_bitacora.php',
	{
		method : 'post',
		evalScripts : true,
		parameters : {idasistencia : '<?=$idasistencia?>',
		idetapa : '<?=$idetapa?>'},

	});
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
		onSuccess: function(t){
			//		alert(t.responseText);

		}
	}
	);
	return;
}


function monitor(tarea,alarma,fechaalarma,recordatorio,fecharecordatorio,fechatarea){

	new Ajax.Request('/app/controlador/ajax/ajax_graba_monitor.php',
	{
		method : 'post',
		parameters: {
			IDTAREA: tarea,
			IDUSUARIO:'<?=$idusuario?>',
			ALARMA:'',
			FECHAALARMA: fechaalarma,
			RECORDATORIO: recordatorio,
			FECHARECORDATORIO: fecharecordatorio,
			FECHATAREA: fechatarea,
			IDEXPEDIENTE:'<?=$idexpediente?>',
			IDASISTENCIA:'<?=$idasistencia?>',
			STATUSTAREA:'PENDIENTE'
		}
		//	onSuccess: function(t){
		//		alert(t.responseText);
		//
		//	}

	});


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
		onSuccess: function(t){
			//		alert(t.responseText);

		}
	}
	);
	return;
}


function carga_calculadora(idasigprov)
{

	if ('<?=$modo?>'=='AUTORIZA') {
		ancho=750;
		ruta= "/app/vista/desviaciones/calculadora_autoriza.php?idasigprov="+idasigprov;
	}
	else {
		ancho=600;
		ruta="/app/vista/catalogos/costos/calculadora.php?idasigprov="+idasigprov;
	}

	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("CALCULADORA")?>',
			width: ancho,
			height: 450,
			showEffect: Element.show,
			hideEffect: Element.hide,
			resizable: true,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url:  ruta

		});
		win.showCenter();
		myObserver = {
			onDestroy: function(eventName, win1)
			{
				actualizar_bitacora();
				if (<?=$idetapa?>=='7') window.location.reload();
				if (win1 == win) {
					win = null;
					Windows.removeObserver(this);
				}
				//				window.location.reload();
			}
		}
		Windows.addObserver(myObserver);

	}
	return;
}



function confirmar(idasistencia){

	Dialog.confirm("<?=_('UNA VEZ ENVIADO A SAP, NO PODRA MODIFICAR LOS COSTOS.  	DESEA ENVIAR ?')?>",
	{
		top: 10,
		width:250,
		showEffect: Element.show,
		hideEffect: Element.hide,
		className: "alphacube",
		okLabel: "Si",
		cancelLabel:"No",
		buttonClass: 'normal',
		onOk: function(dlg){
			grabar(idasistencia);
			return true;
		}
	});

	return;
}




function grabar(idasistencia){

	new Ajax.Request('/app/controlador/ajax/ajax_grabar_statusautorizaciondesvio.php',
	{
		method : 'post',
		parameters: {IDASISTENCIA : idasistencia},
		onSuccess: function(t){
//			alert(t.responseText);
			if (t.responseText=='Error') alert("<?=_('EXISTEN COSTOS PENDIENTE DE AUTORIZAR AUN')?>");
			else{
			
			comentario ="SE ENVIO A SAP \n";
			comentario+="USUARIO : <?=$idusuario?> \n";
			comentario+="FECHA :<?= date("Y-m-d H:i:s",time())?> ";
	
			new Ajax.Request('/app/controlador/ajax/ajax_grabar_asistencia_bitacora.php',
			{
				method : 'post',
				parameters :{
					IDASISTENCIA:"<?=$idasistencia?>" ,
					IDETAPA:"7", // etapa de costeo
					IDUSUARIOMOD:"<?=$idusuario?>",
					COMENTARIO: comentario
				},
				onSuccess: function()
				{
					parent.close();
				}
			});
				
			
			}
			

		}
	});
	return
}


function guardarbitacora(){
	if (trim($F('comentario'))=='') alert("<?=_('INGRESE UN COMENTARIO ')?>");
	else
	{
		new Ajax.Request('/app/controlador/ajax/ajax_grabar_asistencia_bitacora.php',
		{
			method : 'post',
			parameters :  $('form_costeo').serialize(true),
			onSuccess: function()
			{
				$('comentario').value='';
				actualizar_bitacora();
			}
		});
	}

	return
}


</script>