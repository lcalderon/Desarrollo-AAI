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
include_once('../../modelo/clase_contacto.inc.php');
include_once('../../modelo/clase_proveedor.inc.php');
include_once('../../modelo/clase_afiliado.inc.php');
include_once('../../modelo/clase_etapa.inc.php');
include_once('../../modelo/clase_expediente.inc.php');
include_once('../../modelo/clase_asistencia.inc.php');
include_once("../../vista/login/Auth.class.php");

//VALIDAR USUARIO LOGUEADO
Auth::required();

/* DATOS QUE VIENEN DE LA SESSION */
$idusuario = $_SESSION[user];
$idasistencia = $_GET[idasistencia];

$idextension =$_SESSION[extension];
if (isset($_GET[idasistencia]))
{
	$asis = new asistencia();
	$asis->carga_datos($_GET[idasistencia]);
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

$cuenta = $exp->cuenta->nombre;
$plan = $exp->programa->nombre;

$idetapa=7;
$etapa = new etapa();
$etapa->carga_datos($idetapa);


/* SI LA ASISTENCIA DE CONCLUCION TEMPRANA PASA DE FRENTE A LA ETAPA 8*/
$idetapa_asis= ($asis->servicio->concluciontemprana)?8:$asis->etapa->idetapa;
/*if ($asis->servicio->concluciontemprana) {
	$idetapa_asis=8; // AL FINAL
	if ($asis->servicio->conclucionconproveedor && $asis->etapa->idetapa<=7) $idetapa_asis=7;  // AL COSTEO
}
else $idetapa_asis=$asis->etapa->idetapa;  // A LA ETAPA QUE LE CORRESPONDE
*/
//$idetapa_asis= ($asis->servicio->concluciontemprana)?8:$asis->etapa->idetapa;
$asis->carga_bitacora($idasistencia,$idetapa);


$ubigeo = $exp->ubigeo;
$idprograma = $exp->programa->idprograma;

// determinar el proveedor activo

foreach ($asis->proveedores as $proveedor)
if ($proveedor[statusproveedor]=='AC')
{
	$proveedor_act= $proveedor[idasigprov];
	$idproveedor_act= $proveedor[idproveedor];
}





$titulo = 'Asistencia # '.$idasistencia .'-> Etapa '. $idetapa_asis;
include_once('../includes/arreglos.php');
include_once('../includes/head_prot_win.php');
?>

<link href="../../../estilos/plantillas/menu.css" rel="stylesheet" type="text/css" />	
<script src="../asistencia/principal/mmenu.js" type="text/javascript"></script>
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

<!-- DATOS DE PROVEEDORES -->
<div>
	<fieldset style="background: #ECE9D8">
	<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_proveedor').toggle();"><?=_('PROVEEDORES')?></legend>
		<div id='datos_proveedor'>
		<? include_once('vista_datos_proveedor.php')?>
		</div>
	</fieldset>		
</div>

<div>
	<fieldset style="background: #ECE9D8">
	<legend><img style='cursor: pointer;' src='/imagenes/iconos/collapse-expand2.GIF' border="0" align="right" onclick="$('datos_costeo').toggle();"><?=_('COSTEO')?></legend>
	<div id='datos_costeo' >
		<?include_once('../catalogos/costos/form_costeo.php')?>
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
<div id='barra' class='barraestado'>
<table align="right">
	<tbody>
		<tr>
	<? if ($gestion):?>	 
		<td><input type='button' id='btn_agregar' value="<?=_('AGREGAR DEFICIENCIA').''?>" class='guardar' ></td>
	<? else:?>	
		<td><input type='button' id='btn_guardar' value='<?=_("TERMINO PROV").' >>'?>' class='guardar' onClick="grabar('PC')"  <?=(in_array("AC",$prov_act)?'':'disabled')?> ></td>
	<? endif;?>
		</tr>
	</tbody>
</table>

</div> <!--FIN DEL DIV DE LA BARRA-->
</body> 
<script type="text/javascript" >
var validar_func = '';
var win = null;


function grabar(modo){
	var sw;
	switch (modo){
		case 'parcial': // GUARDA LA BITACORA
		{
			if (trim($F('comentario'))=='') alert("<?=_('INGRESE UN COMENTARIO ')?>");
			else
			{
				new Ajax.Request('/app/controlador/ajax/ajax_grabar_asistencia_bitacora.php',
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

		case 'PC': // PROVEEDOR CONCLUIDO
		{
			new Ajax.Request('/app/controlador/ajax/ajax_validar_campos.php',
			{
				method :'post',
				parameters : { IDASISTENCIA : '<?=$idasistencia?>'},
				onComplete: function(t)
				{
					if (t.responseText=='TRUE')// SI TODOS LOS CAMPOS ESTAN COMPLETOS
					{

						if(confirm('<?=_('ESTA SEGURO QUE DESEA REGISTRAR EL TERMINO DEL PROVEEDOR?')?>'))
						{
							$('arrclasificacion').value='PROV_CONC';
							$('comentario').value="Proveedor concluido \n";

							new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_costeo.php?idproveedor='+<?=($idproveedor_act)*1?>,
							{
								method : 'post',
								parameters : $('form_bitacora').serialize(true),
								onComplete: function(t)
								{
									$('comentario').clear();
									new Ajax.Request('/app/controlador/ajax/ajax_grabar_etapa.php',
									{
										method : 'post',
										parameters : {
											IDETAPA : '8',
											IDASISTENCIA : '<?=$idasistencia?>',
											IDUSUARIOMOD : '<?=$idusuario?>',
											IDASIGPROV : '<?=$proveedor_act?>',
											STATUSPROVEEDOR : modo   // proveedor concluido
										},
										onComplete: function()
										{
											new Ajax.Request('/app/controlador/ajax/ajax_graba_monitor.php',
											{
												method : 'post',
												parameters: {
													IDTAREA: 'LLAM_CON',
													IDUSUARIO:'<?=$idusuario?>',
													FECHATAREA: '<?=date("Y-m-d H:i:s",time()+(2*60))?>',
													IDEXPEDIENTE:'<?=$idexpediente?>',
													IDASISTENCIA:'<?=$idasistencia?>',
													STATUSTAREA:'PENDIENTE'
												},
												onComplete: function()
												{
													url= 'etapa8.php?&idasistencia='+$F('idasistencia');
													reDirigir(url); // pasa a la siguiente etapa
												}
											});
										}
									});
								}
							});
						}
					}
					else
					alert("<?=_('LOS DATOS EN LA APERTURA DE LA ASISTENCIA NO ESTAN COMPLETOS')?>");
				}
			});
			break;
		}
	} // fin del switch
	return;
}

function actualizar_bitacora(){
	new Ajax.Updater('zona_bitacora','listar_bitacora.php',
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
		onComplete: function(t){
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
var win= null
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


