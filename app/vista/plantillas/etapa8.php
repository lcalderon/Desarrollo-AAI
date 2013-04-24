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
$idetapa=8;
$etapa->carga_datos($idetapa); // inicia en la etapa 1


/* SI LA ASISTENCIA DE CONCLUCION TEMPRANA PASA DE FRENTE A LA ETAPA 8*/
$idetapa_asis= ($asis->servicio->concluciontemprana)?8:$asis->etapa->idetapa;


//asis->carga_bitacora_etapa($idasistencia,$idetapa);
$ubigeo = $exp->ubigeo;
$idprograma = $exp->programa->idprograma;

include_once('../includes/arreglos.php');
include_once('../includes/head_prot_win.php');




?>
<link href="../../../estilos/plantillas/menu.css" rel="stylesheet" type="text/css" />	

<script src="../asistencia/principal/mmenu.js" type="text/javascript"></script>
<!--<link href="../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />		-->
<title><?=_('ASISTENCIA').' # '.$idasistencia?></title>
<body style="overflow:scroll;overflow-x:hidden;">
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


<!--ZONA DE BITACORA-->
<div>
<fieldset>
 <legend><img  style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('zona_bit').toggle();"><?=_('BITACORA')?></legend>
  <div id='zona_bit'>
	<div>
		<? include_once('../asistencia/asignacion/vista_form_bitacora.php');?>
	</div>
	<div  id='listado_bitacora'>
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
if ($asis->arrstatusasistencia=='CM' OR $asis->arrstatusasistencia=='CP' or $asis->arrstatusasistencia=='CON') {
	$disabled='disabled';
}
?>

<div id='barra' class='barraestado'>

<?	if($gestion):	?>	 
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
			<td><input type='hidden' id='btninconformidad' value='<?=_('INCONFORMIDAD')?>' class='normal' <?=$disabled?>></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><input type='button' id='btnReclamo' value='<?=_('RECLAMO')?>' class='normal' onclick="ventana_reclamo()" <?=$disabled?>></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><input type='button' id='btn_guardar' value='<?=_('GUARDAR Y CONCLUIR')?>' class='guardar' onClick="grabar('CON')" <?=$disabled?> <?=$disabled2?>></td>
		</tr>
	</tbody>
</table>
<?endif;?>
</div>

</div>
</body>
<script type="text/javascript" >
var validar_func = '';
var win = null;
var winz = null;

function actualizar_bitacora(){
	new Ajax.Updater('listado_bitacora','listar_bitacora_etapa.php',
	{
		method : 'post',
		parameters : {idasistencia : '<?=$idasistencia?>',
		idetapa : '<?=$idetapa?>'},
	});
	return;
}

function grabar(modo){
	var sw;
	switch (modo){
		case 'parcial': // GUARDA LA BITACORA
		{
			if (trim($F('comentario'))=='') alert("<?=_('INGRESE UN COMENTARIO')?>");
			else
			{
				new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_satisfaccion.php',
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
		case 'CON':  // CONCLUIR ASISTENCIA
		{

			if (('<?=($asis->servicio->concluciontemprana)?0:1;?>'))  //ojo aca puse los valores al reves
			{

				if (!($('btn_llamada').disabled))
				{
					alert('NO HAY LLAMADA DE SATISFACCION');
					break;
				}
			}

			<? if (!isset($asis->costos)) {?>
			Dialog.confirm("<?=_('ESTA ASISTENCIA NO TIENE COSTOS, DESEA GRABAR Y CONCLUIR LA ASISTENCIA?')?>",
			{
				top: 10,
				width:250,
//				showEffect: Element.show,
//				hideEffect: Element.hide,
				className: "alphacube",
				okLabel: "Si",
				cancelLabel:"No",
				buttonClass: "normal",
				onOk: function(dlg){
					grabar_status(modo);
					return true;
				}
			});
			<?}else {?>

			Dialog.confirm("<?=_('DESEA GRABAR Y CONCLUIR LA ASISTENCIA?')?>",
			{
				top: 10,
				width:250,
//				showEffect: Element.show,
//				hideEffect: Element.hide,
				className: "alphacube",
				okLabel: "Si",
				cancelLabel:"No",
				buttonClass: "normal",
				onOk: function(dlg)
				{
					grabar_status(modo);
					return true;
				}
			});
			<?}?>
		}
		break;
		//		}
	} // fin del switch
	return;
}

function grabar_status(modo)
{
	$('arrclasificacion').value='CON_ASIS';
	$('comentario').value='ASISTENCIA CONCLUIDA';
	new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_satisfaccion.php',
	{
		method : 'post',
		parameters : $('form_bitacora').serialize(true),
		onComplete: function()
		{
			$('comentario').value='';
			new Ajax.Request('/app/controlador/ajax/ajax_grabar_deficienciasE8.php',
			{
				method:'post',
				parameters: 
				{
					IDEXPEDIENTE:'<?=$idexpediente?>',
					IDASISTENCIA: '<?=$idasistencia?>',
				},
				onComplete: function (t)
				{
					new Ajax.Request('/app/controlador/ajax/ajax_grabar_status.php',
					{
						method : 'post',
						parameters : {
							IDASISTENCIA : '<?=$idasistencia?>',
							IDUSUARIOMOD : '<?=$idusuario?>',
							ARRSTATUSASISTENCIA : modo  // asistencia cancelado posterior
						},
						onComplete: function(t)
						{
							parent.close();
						}
					});
				}
			});
		}
	});


}

function ventana_reclamo()
{
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("GESTION DE LLAMADA(RECLAMOS Y QUEJAS)")?>',
			width: 700,
			height: 200,
//			showEffect: Element.show,
//			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "/app/vista/asistencia/principal/monitoreo/reclamo.php?idexpediente=<?=$idexpediente?>&idasistencia=<?=$idasistencia?>"

		});
		win.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			window.location.reload();
			if (win1 == win) {
				win = null;
				Windows.removeObserver(this);
			}
		}
		}
		Windows.addObserver(myObserver);
	}
}


function grabar_llamadasatisfaccion()
{
	$('arrclasificacion').value = 'LLCNF';
	$('comentario').value ='LLAMADA DE CONFORMIDAD '+ $F('comentario');
	new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_satisfaccion.php',
	{
		method : 'post',
		parameters : $('form_bitacora').serialize(true),
		onSuccess: function()
		{
			$('comentario').value = '';
			$('btn_llamada').disabled = true;
					actualizar_bitacora();

//			new Ajax.Request('/app/controlador/ajax/ajax_borrar_tarea.php',
//			{
//				method: 'post',
//				parameters: {
//					IDASISTENCIA :'<?=$idasistencia?>'
//				},
//				onSuccess: function(){
//			
//				}
//			}); /*fin del ajax de borrar tarea */

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


</script>