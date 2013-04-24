<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_poligono.inc.php');
include_once('../includes/head_prot_win_zap.php');


$idproveedor =$_GET[idproveedor];
$idservicio = $_GET[idservicio];
$idusuariomod = $_SESSION[user];
$array_poligonos = new poligono();
$edicion = (isset($_GET[edicion]))?$_GET[edicion]:0;

?>
<link rel="stylesheet" href="/librerias/tinytablev3.0/style.css" />
<body>

<div id="tabBarExped" style="width: 100%">&nbsp;</div>
 <div id="tabsExped"  style="display: block; height:100%"; >
 <div id="<?=_('ENTIDADES')?>" style='height:500px;overflow:auto' >
	<label title='<?=_('ENTIDADES')?>'><?=_('ENTIDADES') ?></label>
		<form id='form_entidad' name='form_entidad'>
			<table>
			<? include_once('vista_entidades.php')?>
			<input type="hidden" name='IDPROVEEDOR' value='<?=$idproveedor?>'>
			<input type="hidden" name='IDSERVICIO' value='<?=$idservicio?>'>
			<input type="hidden" name='IDUSUARIOMOD' value='<?=$idusuariomod?>'>
			<tr>
			<td><?=_('AMBITO')?></td>
			<td>
			<input type='radio' id='local' name='ARRAMBITO' value='LOC' <?= ($checked=='LOC')?'checked':'' ?> > <?=_('LOCAL')?>
			<input type='radio' id='foraneo' name='ARRAMBITO' value='FOR' <?= ($checked=='FOR')?'checked':'' ?> > <?=_('FORANEO')?>
			</td>
			</tr>
			<tr>
			<td colspan="2" align="center"><input type='button' value="<?=_('AGREGAR')?>" onclick="grabar();" class="normal" <?=($edicion==1)?'':'disabled';?>></input></td>
			</tr>
			</table>
		</form>
		
	<span id='listado_unidades' style="overflow:auto">
		<? include_once('listado_unidades.php')?>
	</span>
 </div>

 <div id="<?=_('POLIGONOS')?>" style="height:500px;overflow:auto" >
	<label title='<?=_('POLIGONOS')?>'><?=_('POLIGONOS') ?></label>
	<fieldset>
	<?=_('REGISTRAR POLIGONOS')?>
	<form id='form_poligonos' name='form_poligonos' >
	<table>
	<tr>
		<td colspan="2" align="center">
		<input type='button' value="<?=_('MAPA')?>" onclick="mapa('poligonos')" class="normal" <?=($edicion==1)?'':'disabled';?>></input>
		</td>
	</tr>
	</table>
	</form>
	</fieldset>
	<span id='listado_poligonos' style="overflow:auto">
	<? include_once('listado_poligonos.php')?>
	</span>
 </div>

 <div id="<?=_('CIRCULOS')?>" style="height:420px"  >
	<label title='<?=_('CIRCULOS')?>'><?=_('CIRCULOS') ?></label>
	<fieldset>
	<form id='form_circulos' >
	<table>
	<tr>
		<td colspan="2" align="center">
		<input type='button' class="normal" value="<?=_('MAPA')?>" onclick="mapa('circulos')" <?=($edicion==1)?'':'disabled';?>></input>
		</td>
	</tr>
	</table>
	</form>
	</fieldset>
	<span id='listado_circulos' style="width: 100%; height:320px; overflow: auto">
	<? include_once('listado_circulos.php')?>
	</span>
</div>	
	
	
<div id="<?=_('ZONAS')?> " style="height:420px" >
	<label title='<?=_('ZONAS')?>'><?=_('ZONAS') ?></label>
	<form id='form_zonas' >
	<table>
	<tr>
	<td>
	<? $con->cmbselect_ar('IDPOLIGONO',$array_poligonos->poligonos_predefinidos(), 'BLANK',''," id='idpoligono'  ");?>
	 <input type= 'button' class="normal" value='VER MAPA' onclick="ver_area($('idpoligono').value);" <?=($edicion==1)?'':'disabled';?>></input>
	</td>
	
	
	</tr>
	</table>
	</form>
	<span id='listado_areas'  style="width: 100%; height:320px; overflow: auto">
	<? include_once('listado_areas.php')?>
	</span>
	
	
 </div>
 </body>
 
<script type="text/javascript">

var objTabs = new Zapatec.Tabs({
	// ID of Top bar to show the Tabs: Game, Photo, Music, Chat
	tabBar: 'tabBarExped',
	/*
	ID to get the LABEL contents to create the tabBar tabs
	Also, each DIV in this ID will contain the contents for each tab
	*/
	tabs: 'tabsExped',
	// Theme to use for the tabs
	theme: 'rounded',
	themePath: '../../../librerias/zapatec/zptabs/themes/',
	closeAction: 'hide'
});


var win = null;

function mapa(tipo_mapa)
{
	if (win != null) alert('<?=_('CIERRE EL MAPA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: 	tipo_mapa,
			width: 500,
			height: 400,
			resizable: false,
			destroyOnClose: true,
			showEffect: Element.show,
			minimizable: false,
			maximizable: false,
			url: "mapa_"+tipo_mapa+".php?idproveedor="+<?= $idproveedor?>+
			"&idservicio="+<?= $idservicio?>
		});
		win.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			if (win1 == win) {
				win = null;
				Windows.removeObserver(this);
				actualizar_listado('<?= $idproveedor?>','<?= $idservicio?>',tipo_mapa);
			}
		}
		}
		Windows.addObserver(myObserver);

	}
	return;
}


function actualizar_listado(idproveedor,idservicio,tipo_mapa){

	new Ajax.Updater('listado_'+tipo_mapa,'listado_'+tipo_mapa+'.php',
	{
		method : 'post',
		evalScripts: true,
		parameters : {
			idproveedor: idproveedor,
			idservicio: idservicio,
			edicion : "<?=$edicion?>"
		},
	});
	return;
}



/******************** FUNCIONES DE POLIGONOS ***************************************/
function ver_poligono(idpoligono)
{
	if (win != null) alert('<?=_('CIERRE EL MAPA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("Poligonos")?>',
			width: 500,
			height: 400,
			showEffect: Element.show,
			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "mapa_poligonos.php?idpoligono="+idpoligono
		});

		win.keepMultiModalWindow = true;
		win.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			if (win1 == win) {
				win = null;
				Windows.removeObserver(this);
				actualizar_listado('<?= $idproveedor?>','<?= $idservicio?>','poligonos');
			}
		}
		}
		Windows.addObserver(myObserver);
	}
}

function eliminar_poligono(idpoligono){

	new Ajax.Request('../../controlador/ajax/ajax_poligono.php?opcion=borrar',
	{
		method: 'post',
		parameters: { IDPOLIGONO: idpoligono },
		onComplete: function(){
			actualizar_listado('<?= $idproveedor?>','<?= $idservicio?>','poligonos');
		}
	});
}


/******************** FUNCIONES DE CIRCULOS ***************************************/
function ver_circulo(idcirculo)
{

	if (win != null) alert('<?=_('CIERRE EL MAPA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("Circulos")?>',
			width: 500,
			height: 400,
			showEffect: Element.show,
			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "mapa_circulos.php?idcirculo="+idcirculo
		});

		win.keepMultiModalWindow = true;
		win.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			if (win1 == win) {
				win = null;
				Windows.removeObserver(this);
				actualizar_listado('<?= $idproveedor?>','<?= $idservicio?>','circulos');
			}
		}
		}
		Windows.addObserver(myObserver);

	}

}

function eliminar_circulo(idcirculo){

	new Ajax.Request('../../controlador/ajax/ajax_circulo.php?opcion=borrar',
	{
		method: 'post',
		parameters: {IDCIRCULO: idcirculo },
		onComplete: function(){
			actualizar_listado('<?= $idproveedor?>','<?= $idservicio?>','circulos');
		}
	});
}


//  ***********************GRABA EL FORMULARIO *****************************//
function grabar(){
	if (($F('local')== null) && ($F('foraneo') == null)) alert('SELECCIONE EL AMBITO');
	else
	new Ajax.Request('../../controlador/ajax/ajax_unidadfederativa.php?opcion=grabar',
	{
		method: 'post',
		evalScripts: true,
		parameters:  $('form_entidad').serialize(true),
		onComplete: function(){
			actualizar_listado('<?= $idproveedor?>','<?= $idservicio?>','unidades');
		}
	});
	return;
}

// *********************** ELIMINA UNIDAD FEDERATIVA ***********************//

function eliminar_unidad(idunidadfederativa){
	
	new Ajax.Request('../../controlador/ajax/ajax_unidadfederativa.php?opcion=borrar',
	{
		method: 'post',
		parameters: {IDUNIDADFEDERATIVA: idunidadfederativa },
		onComplete: function(){
			actualizar_listado('<?= $idproveedor?>','<?= $idservicio?>','unidades');
		}
	});
}

/******************** FUNCIONES DE AREAS PREDEFINIDAS ***************************************/
function ver_area(idpoligono)
{
	if (win != null) alert('<?=_('CIERRE EL MAPA ANTERIOR')?>');
	else
	{
		if (idpoligono=='') alert("<?=_('SELECCIONE UN AREA')?>");
		else
		{
			win = new Window({
				className: "alphacube",
				title: '<?=_("Poligonos")?>',
				width: 500,
				height: 400,
				showEffect: Element.show,
				hideEffect: Element.hide,
				resizable: false,
				destroyOnClose: true,
				minimizable: false,
				maximizable: false,
				url: "mapa_areas.php?idpoligono="+idpoligono+'&idproveedor='+<?=$idproveedor?>+'&idservicio='+<?=$idservicio?>
			});

			win.keepMultiModalWindow = true;
			win.showCenter();
			myObserver = {onDestroy: function(eventName, win1)
			{
				if (win1 == win) {
					win = null;
					Windows.removeObserver(this);
					actualizar_listado('<?= $idproveedor?>','<?= $idservicio?>','areas');
				}
			}
			}
			Windows.addObserver(myObserver);
		}
	}
}

function eliminar_area(idpoligono,idproveedor,idservicio,arrambito){

	new Ajax.Request('../../controlador/ajax/ajax_areas.php?opcion=borrar',
	{
		method: 'post',
		parameters: {
			IDPOLIGONO:	idpoligono,
			IDPROVEEDOR: idproveedor,
			IDSERVICIO: idservicio,
			ARRAMBITO: arrambito
		},
		onComplete: function(){
			actualizar_listado('<?= $idproveedor?>','<?= $idservicio?>','areas');
		}
	});
}


</script>
