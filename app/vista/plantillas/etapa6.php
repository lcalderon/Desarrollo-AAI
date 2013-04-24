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
include_once('../../modelo/clase_proveedor.inc.php');
include_once('../../modelo/clase_expediente.inc.php');
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


/* VARIABLES */
$afiliado = $exp->personas[TITULAR][NOMBRE].' '.$exp->personas[TITULAR][APPATERNO].' '.$exp->personas[TITULAR][APMATERNO];
$contactante = $exp->personas[CONTACTO][NOMBRE].' '.$exp->personas[CONTACTO][APPATERNO].' '.$exp->personas[CONTACTO][APMATERNO];
$idcontacto = $exp->contacto->idcontacto;
$cuenta = $exp->cuenta->nombre;
$plan = $exp->programa->nombre;
$etapa = new etapa();

$idetapa=6;
$etapa->carga_datos($idetapa); // inicia en la etapa 1

/* SI LA ASISTENCIA DE CONCLUCION TEMPRANA PASA DE FRENTE A LA ETAPA 8*/
$idetapa_asis= ($asis->servicio->concluciontemprana)?8:$asis->etapa->idetapa;
/*
if ($asis->servicio->concluciontemprana) {
	$idetapa_asis=8; // AL FINAL
	if ($asis->servicio->conclucionconproveedor && $asis->etapa->idetapa<=7) $idetapa_asis=7;  // AL COSTEO
}
else $idetapa_asis=$asis->etapa->idetapa;  // A LA ETAPA QUE LE CORRESPONDE
*/
$ubigeo = $exp->ubigeo;
$idprograma = $exp->programa->idprograma;
if($idetapa_asis>6){
	$disabled2='disabled';
}

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


$arribo = $con->consultation("SELECT
							  COUNT(*)
							FROM $con->temporal.asistencia_bitacora_etapa6
							  INNER JOIN $con->temporal.asistencia_asig_proveedor
								ON asistencia_asig_proveedor.IDASIGPROV = asistencia_bitacora_etapa6.IDASIGPROV
							WHERE asistencia_asig_proveedor.IDASISTENCIA ='$idasistencia'
								AND asistencia_asig_proveedor.STATUSPROVEEDOR = 'AC' 
								AND asistencia_bitacora_etapa6.STATUSARRCON IN('ARRENT','ARRSAL') ");

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
	$btn_ultimo_monitoreo='disabled';
	$dis_arribo='disabled';
	$dis_contacto='disabled';
}
else
{
	/* VERIFICA SI EXISTEN TAREAS DE ULT_MON  (ultimo monitoreo) PENDIENTES */
	$sql="
	SELECT
 		IDTAREA
	FROM
 		$asis->temporal.monitor_tarea
	WHERE
 		IDASISTENCIA ='$idasistencia'
 		AND IDTAREA='ULT_MON'
 		AND STATUSTAREA IN ('PENDIENTE','INVISIBLE')
	";
	$result=$asis->query($sql);
	if (!$result->num_rows) $btn_ultimo_monitoreo='disabled';

	/* SI YA TIENE  ARRIBO DESHABILITA EL BOTON*/
	if ($fechaarribo!=0 ) $dis_arribo='disabled';

	/*COMPRUEBA SI NO TIENE TAREAS DE CONTACTO AL AFILIADO PENDIENTES*/
	$sql="
	SELECT
 		IDTAREA
	FROM
 		$asis->temporal.monitor_tarea
	WHERE
 		IDASISTENCIA ='$idasistencia'
 		AND IDTAREA='CONT_AFIL'
 		AND STATUSTAREA IN ('PENDIENTE','INVISIBLE')
	";
	$result=$asis->query($sql);
	if (!$result->num_rows) $dis_contacto='disabled';
}


?>
<input type='hidden' id='hid_proveedor' value='<?=$proveedor_act?>'>
<input type='hidden' id='contactosarribo' value='<?=$arribo[0][0]*1;?>'>
<input type='hidden' id='hid_idasigprov' value='<?=$idasigprov?>'>

<div id='barra' class="barraestado" style="float:right"> 
	<?
	if($gestion)
	{
	?>	 
	<table align="right">
		<tbody>
			<tr>
		
				<td><input type='button' id='btn_agregar' value="<?=_('AGREGAR DEFICIENCIA').''?>" class='guardar' ></td>
	 
			</tr>
		</tbody>
	</table>		
	
<?
	}
	else
	{
?>	

<table align="right">
	<tbody>
		<tr>
			<td><input type='button' id='btn_ultimo_monitoreo' value='<?=_('ULTIMO MONITOREO PROV')?>' class='normal' onClick="grabar_ultimo_monitoreo('ARRENT')" <?=$btn_ultimo_monitoreo?> ></td>	
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><input type='button' id='btn_arribo_prov' value='<?=_('ARRIBO PROV')?>' class='normal' onClick="grabar_arribo()" <?=$disabled?> <?=$dis_arribo?>></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><input type='button' id='btn_contacto' value='<?=_('CONTACTO N/A').' >>'?>' class='normal' onClick="grabar_contacto()" <?=$disabled?> <?=$dis_contacto?> ></td>
		</tr>
	</tbody>
</table>

<?
	}
?>	

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

function actualizar_bitacora(){
	new Ajax.Updater('listado_bitacora','listar_bitacora_etapa.php',
	{
		method : 'post',
		parameters : {idasistencia : '<?=$idasistencia?>',
		idetapa : '<?=$idetapa?>'},

	});
	return;
}


function grabar_contacto()
{

	$('btn_contacto').disabled = true;
	modo = 'CONT_AFIL';
	coment = $('comentario').value;
	prov = $('hid_proveedor').value;
	idasig = $('hid_idasigprov').value;
	$('arrclasificacion').value='CONT_AFIL';

	/* GRABA EL CONTACTO EN LA BITACORA */
	new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_contacto.php?modo='+modo+'&proveedor_act='+prov+'&idasigprov='+idasig,
	{
		method:'post',
		parameters: $('form_bitacora').serialize(true),
		onComplete : function ()
		{
//			alert('a');
			new Ajax.Request('/app/controlador/ajax/ajax_grabar_deficienciasE6_contacto.php',
			{
				method:'post',
				parameters:
				{
					IDASIGPROV  : idasig,
					IDEXPEDIENTE:'<?=$idexpediente?>',
					IDASISTENCIA: '<?=$idasistencia?>'
					
				},
				onComplete: function (t)
				{
//					alert(t.responseText);
					url= 'etapa7.php?idasistencia=<?=$idasistencia?>';
					reDirigir(url); // pasa a la siguiente etapa
				}
			});
		}
	});

}

function confirmacion_grabar(modo){

	if ($F('contactosarribo') ==0){
		if(confirm('<?=_('NO TIENE REGISTRO DE ARRIBO,ESTA SEGURO DE CONTINUAR A LA SIGUIENTE ETAPA?')?>')){
			grabar(modo);
		}
		else
		{
			exit;
		}
	}
	else
	{
		grabar(modo);
	}
}

function grabar(modo)
{
	coment=document.getElementById('comentario').value;
	prov=document.getElementById('hid_proveedor').value;
	idasig=document.getElementById('hid_idasigprov').value;

	switch(modo){
		case 'ARRENT':
		case 'ARRSAL':
		{
			$('arrclasificacion').value='MON_PROV';
			break;
		}
		case 'CONSAL':
		{
			$('arrclasificacion').value='CONT_AFIL';
			break;
		}
	}


	new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_ult_monitoreo.php?modo='+modo+'&proveedor_act='+prov+'&idasigprov='+idasig,
	{
		method : 'post',
		parameters : $('form_bitacora').serialize(true),
		onComplete: function(t)
		{
			$('comentario').value='';
			actualizar_bitacora();

			if (modo =='ARRENT' || modo =='ARRSAL')
			{
				/* AGREGA TAREA DE CONTACTO DEL AFILIADO*/
				//				monitor('CONT_AFIL',0,'',0,'','<?=date("Y-m-d H:i:s",time())?>');
				url= 'etapa6.php?idasistencia=<?=$idasistencia?>';
				reDirigir(url);
			}
			else if (modo!='parcial')
			{
				new Ajax.Request('/app/controlador/ajax/ajax_grabar_etapa_monitoreo.php',
				{
					method : 'post',
					parameters : {
						IDETAPA : 7,
						IDASISTENCIA : '<?=$idasistencia?>',
						IDUSUARIOMOD : '<?=$idusuario?>',

					},
					onComplete: function(t){

						switch (modo)
						{
							case 'CONSAL':
							{
								/* DEFICIENCIAS AUTOMATICAS */
								new Ajax.Request('/app/controlador/ajax/ajax_grabar_deficienciasE6.php',
								{
									method:'post',
									parameters: {
										IDEXPEDIENTE:'<?=$idexpediente?>',
										IDASISTENCIA: '<?=$idasistencia?>',

									},
									onComplete: function (t){

										new Ajax.Request('/app/controlador/ajax/ajax_tarea_atendida.php',
										{
											method: 'post',
											parameters: {
												IDASISTENCIA :'<?=$idasistencia?>',
												IDTAREA: 'CONT_AFIL',
											},
											onComplete: function(t){

												/*PASA A LA SIGUIENTE ETAPA */
												url= 'etapa7.php?idasistencia=<?=$idasistencia?>';
												reDirigir(url); // pasa a la siguiente etapa
											}
										}); /*fin del ajax de borrar tarea */
										return;
									}
								});
								break;
							}
						}
						return;
					}
				});
			}
			return;
		}
	});
	return;
}


function grabar_ultimo_monitoreo(modo)
{
	coment = $F('comentario');
	prov   = $F('hid_proveedor');
	idasig = $F('hid_idasigprov');
	$('arrclasificacion').value='MON_PROV';

	new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_ult_monitoreo.php?modo='+modo+'&proveedor_act='+prov+'&idasigprov='+idasig,
	{
		method : 'post',
		parameters : $('form_bitacora').serialize(true),
		onComplete: function(t)
		{
			$('comentario').value='';
			$('btn_ultimo_monitoreo').disabled= true;

			new Ajax.Request('/app/controlador/ajax/ajax_grabar_deficienciasE6.php',
			{
				method : 'post',
				parameters : {
					IDASIGPROV  : idasig,
					IDPROVEEDOR :  prov,
					IDASISTENCIA : '<?=$idasistencia?>',
					IDEXPEDIENTE : '<?=$idexpediente?>',
					EVENTO : 'MON_PROV'
				},
				onComplete: function (t)
				{
					
					/* ACTUALIZA LA BITACORA*/
					new Ajax.Updater('listado_bitacora','/app/vista/plantillas/listar_bitacora_etapa.php',
					{
						method: 'post',
						evalScripts : true,
						parameters:
						{
							idasistencia:'<?=$idasistencia?>',
							idetapa: '<?=$idetapa?>'
						}
					});
				}
			});
		}
	});


	return;
}





//Calidad
<? if($gestion):?>
	function lastSpy() {

			 new Ajax.Updater('listado_de_servicios','../calidad/form_consolidado.php',
			{
				method : 'get',
				parameters : {idasistencia : '<?=$idasistencia?>',idetapa : '<?=$idetapa?>'},

			});	
	}
	
	Event.observe(window, 'load', lastSpy, false);
<?endif;?>

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

var win3 = null;

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
	});
}


function grabar_arribo(){
	modo = 'ARRENT';
	coment=$F('comentario');
	prov=$F('hid_proveedor');
	idasig=$F('hid_idasigprov');


	/*VALIDACION DE FECHA */
	dia =$F('date2');
	hora = $F('h_arribo');
	minutos =$F('m_arribo');
	$('arrclasificacion').value='ARR_PROV';

	if (minutos=='') alert("<?=_('ERROR NO INGRESO LOS MINUTOS')?>");
	else if (minutos <0 || minutos >59)  alert("<?=_('ERROR AL INGRESAR FECHA')?>");
	else
	{
		$('btn_arribo_prov').disabled= true;
		/* ARMA LA FECHA EN FORMATO AAAA-MM-DD hh:mm:00*/
		fecha_arribo = dia+' '+ hora+':'+minutos+':00';
		$('comentario').value = coment + "\n" + "HORA DE ARRIBO "+fecha_arribo;

		/* GRABA BITACORA DEL ARRIBO DEL PROVEEDOR */
		new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_arribo_prov.php?proveedor_act='+prov+'&idasigprov='+idasig,
		{
			method : 'post',
			parameters :
			{
				FECHAARRIBO: fecha_arribo,
				IDASISTENCIA: '<?=$idasistencia?>',
				IDEXPEDIENTE: '<?=$idexpediente?>',
				IDUSUARIOMOD: '<?=$idusuario?>',
				COMENTARIO : $F('comentario'),
				ARRCLASIFICACION : $F('arrclasificacion'),
			},
			onComplete: function ()
			{
				$('btn_ultimo_monitoreo').disabled= true;
				$('btn_arribo_prov').disabled= true;
				$('btn_contacto').disabled = false;

				$('comentario').clear();
				/* CALCULA DEFICIENCIA PARA EL PROVEEDOR */
				new Ajax.Request('/app/controlador/ajax/ajax_grabar_deficienciasE6.php',
				{
					method : 'post',
					parameters :
					{
						IDASIGPROV  : idasig,
						IDPROVEEDOR :  prov,
						IDASISTENCIA : '<?=$idasistencia?>',
						IDEXPEDIENTE : '<?=$idexpediente?>'
						
					},
					onComplete: function (t)
					{
						new Ajax.Updater('listado_bitacora','/app/vista/plantillas/listar_bitacora_etapa.php',
						{
							method: 'post',
							evalScripts : true,
							parameters:
							{
								idasistencia:'<?=$idasistencia?>',
								idetapa: '<?=$idetapa?>'
							},
							onComplete: function (){

							}

						});
					}
				});
			}
		});




		
	}
	return;
}
</script>