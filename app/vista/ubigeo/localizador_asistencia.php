<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');
include_once('../includes/head_prot_win.php');
$tabla=$_GET[tabla];
if ($_GET[idubigeo]!=''){
		include_once('../../modelo/clase_ubigeo.inc.php');
		$ubigeo=new ubigeo();
		$ubigeo->leer('ID',$ubigeo->temporal,$tabla,$_GET[idubigeo]);
}

$campo_display = $_GET[campo_display];
$campo_ubigeo = $_GET[campo_ubigeo];

?>
<body>
<form name='form_localizador' id='form_localizador'>
<fieldset>
<legend><?=_('ENTIDADES') ?></legend>
<table>
	<? include_once('../includes/vista_entidades_ubigeo.php')?>
	<tr>
		<td><?=_('TIPO VIA')?></td>
		<td>
		<? $con->cmbselect_db('CVETIPOVIA','select IDTIPOVIA, DESCRIPCION from catalogo_tipo_via',($prov->cvetipovia==''?'Blank':$prov->cvetipovia),'id="cvetipovia"','','TODOS')?>
		</td>
	</tr>
	<tr>
		<td><?=_('DIRECCION *')?></td>
		<td><input type="text" name='DIRECCION' value="<?=$ubigeo->direccion; ?>" id='direccion' size='65' autocomplete="off"  ></td>
		<div id='sugeridos' class="autocomplete" ></div>
	</tr>
	<tr>
		<td><?=_('Nro')?></td>
		<td>
		<input type="text" name="NUMERO" value="<?=$ubigeo->numero?>" size="12" maxlength="12" id='numero' autocomplete="off"  onkeypress="return validarnum(event)" >
		<input type ='button' value="<?=_('Ajustes de ubicacion') ?>" id='ver_mapa' class="normal" ></input>
		&nbsp;<?=_('INTERSECCION')?>:<input type="button" value="V" id='add_referencia' style="font-weight:bold;width:27px;font-size:10px;" onclick="abrir('referencia');" class='normal'>
			<div id='referencia_add' style="display:none">
				<?=_('Entre:')?><input type=text' name='REFERENCIA1' value="<?=$ubigeo->referencia1?>" size='50' id='referencia1' autocomplete="off"  ><br>
				<?=_('Y:')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=text' name='REFERENCIA2' value="<?=$ubigeo->referencia2?>" size='50' id='referencia2' autocomplete="off"  >
			</div>
		</td>
	</tr>
	
	<tr>
		<td valign="top"><?=_('REFERENCIA')?></td>
		<td><textarea name='DESCRIPCION' cols='40' rows="1" id='descripcion' ><?=$ubigeo->descripcion?></textarea></td>
	</tr>
</table>
</fieldset>
<input type='hidden'  name='IDUBIGEO' value='<?=$ubigeo->idubigeo ?>' >
<!--<input type='hidden'  name='CVEPAIS' value='<?=$idpais ?>' >-->
<input type='hidden'  name='LATITUD' id='latitud'  value='<?=$ubigeo->latitud?>' >
<input type='hidden'  name='LONGITUD' id='longitud' value='<?=$ubigeo->longitud?>' >
<table align="center">
	<tr>
		<td><input type ='button' value=<?=_('GUARDAR') ?> id='btn_grabar' onclick="grabar();" class="guardar"></input></td>
		<td><input type ='button' value=<?=_('CANCELAR') ?> id='btn_salir' onclick="salir();" class="cancelar"></input></td>
	</tr>
</table>



</form>
</body>
</html>


<script type="text/javascript"> // ******************  Eventos del formulario  ***************************//

var win = null;
// ***********************  Abre la ventana del mapa  *****************************************//

new Event.observe('ver_mapa','click',function()
{
	var lat= $F('latitud');
	var lng= $F('longitud');

//	if (lat==0 && lng==0) alert('<?=_('NO HAY NADA QUE MOSTRAR EN EL MAPA')?>');
//	else
//	{
		if (win != null) alert('<?=_('CIERRE EL MAPA ANTERIOR')?>');
		else
		{
			win = new Window({
				className: "alphacube",
				title: '<?=_("Mapa de localizacion")?>',
				width: 400,
				height: 400,
				showEffect: Element.show,
				resizable: false,
				minimizable: false,
				maximizable: false,
				destroyOnClose: true,
				url: "mapa_localizacion.php?lat="+lat+"&lng="+lng

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
//	}
});



// **********************  Ajax para autocompletar las calles ********************************//
new Ajax.Autocompleter('direccion',	'sugeridos',
"../../controlador/ajax/ajax_calles.php",
{
	method: "get",
	paramName: "calle",
	callback: function(editor, paramText){
		parametros = "&cveentidad1="+ $F('cveentidad1')
		+"&cveentidad2="+$F('cveentidad2')
		+"&cveentidad3="+$F('cveentidad3')
		//+"&cveentidad4="+$F('cveentidad4')
		//+"&cveentidad5="+$F('cveentidad5')
		//+"&cveentidad6="+$F('cveentidad6')
		//+"&cveentidad7="+$F('cveentidad7');
		return  paramText+parametros;
	},
	afterUpdateElement: function(text,li){
		var coordenadas = li.id.split(',');
		$('latitud').value=coordenadas[0];
		$('longitud').value=coordenadas[1];
		//			$('cvetipovia').value=coordenadas[2];


	},
	minChars: 2,
	selectFirst: false
}
);

//**************************Ajax de calculo de intersecciones ***********************************//
new Event.observe('calc_inter','click',function(){
	if ($F('via1')!='' && $F('via2')!='' && $F('direccion')=='')
	{
		new Ajax.Request('../../controlador/ajax/ajax_interseccion.php',
		{
			method : 'get',
			parameters : {via1: $F('via1'), via2: $F('via2') },
			onComplete:function(t){
				campo=t.responseText.split('/');
				$('direccion').value=$F('via1')+' con '+$F('via2');
				$('latitud').value=campo[0];
				$('longitud').value=campo[1];
				$('cveentidad1').value=campo[2];
				$('cveentidad2').value=campo[3];
				$('cveentidad3').value=campo[4];
				$('cveentidad4').value=campo[5];
				$('cveentidad5').value=campo[6];
				$('cveentidad6').value=campo[7];
				$('cveentidad7').value=campo[8];

			}
		}
		);
	}
	return true;
});


function grabar()
{
	var tabla="<?=$tabla?>";
	var error = false;
	if ($('direccion').value=='') error= true;

	if (error == true)	alert("<?=_('Asegurate de rellenar todos los campos requeridos')?>");
	else
	{
		new Ajax.Request('ajax_grabar_localizacion.php?tabla='+tabla,
		{
			method : 'post'	,
			parameters:  $('form_localizador').serialize(true),
			onSuccess: function(t){
					parent.$('<?=$campo_ubigeo ?>').value = t.responseText;
					parent.$('<?=$campo_display ?>').value = $F('direccion')+'  # '+$F('numero');
					parent.win.close();
				
			}

		});
	}
	return;
}

function salir(){
	parent.win.close();
	
}



function abrir(campo)
{
	if ($F('add_'+campo)=='V'){
		$(campo+'_add').style.display='block';
		$('add_'+campo).value='0';
	}
	else
	{
		$(campo+'_add').style.display='none';
		$('add_'+campo).value='V';
	}

	return;
}
</script>