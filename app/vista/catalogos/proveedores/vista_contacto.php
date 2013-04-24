<?
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../includes/arreglos.php');
include_once('../../includes/head_prot_win.php');

$idusuariomod = $_SESSION[user];
$idextension = $_SESSION[extension];

if (isset($_POST[idproveedor])) $idproveedor = $_POST[idproveedor];
if (isset($_POST[idcontacto])){
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_persona.inc.php');
	include_once('../../../modelo/clase_contacto.inc.php');
	include_once('../../../modelo/clase_proveedor.inc.php');

	$contacto = new contacto();
	$contacto->carga_datos($_POST[idcontacto]);
	list($anio,$mes,$dia)= explode('-',$contacto->fechanac);
	$idproveedor= $contacto->idproveedor;
	$edicion = (isset($_POST[edicion]))?$_POST[edicion]:0;
}

$con = new DB_mysqli();

$lista_ddn = $con->uparray("SELECT DDN,DESCRIPCION FROM catalogo_ddn");
$lista_t_telf = $con->uparray("SELECT IDTIPOTELEFONO, DESCRIPCION FROM catalogo_tipotelefono");
$lista_tsp = $con->uparray("SELECT IDTSP,DESCRIPCION FROM catalogo_tsp");
?>
<fieldset>
<legend><?=_('CONTACTO')?></legend>
	<form id='form_contacto'>
	<input type="hidden" name="IDPROVEEDOR" id='idproveedor' value="<?=$idproveedor?>">
	<input type="hidden" name="IDUSUARIOMOD" id='idusuariomod' value="<?=$idusuariomod?>">
	<input type="hidden" name="IDCONTACTO" id='idcontacto' value="<?=$contacto->idcontacto?>">
	<input type="hidden" name="edicion"  value="<?=$edicion?>">
		<table>
		<tr>
			<td><?=_('NOMBRES') ?></td>
			<td><input type='text' name='NOMBRE' id='nombre' value="<?=$contacto->nombre?>" size='30' ></td>
		</tr>
		<tr>
			<td><?=_('A.PATERNO') ?></td>
			<td><input type='text' name='APPATERNO' id='appaterno' value="<?=$contacto->appaterno?>" size='40'></td>
		</tr>
		<tr>
			<td><?=_('A.MATERNO') ?></td>
			<td><input type='text' name='APMATERNO' id='apmaterno' value="<?=$contacto->apmaterno?>" size='40'></td>
		</tr>
		<tr class='modo1'>
			<td ><?=_('EMAILS')?></td>
			<td>
				 1.-<input type="text" name="EMAIL1" id='email1' size='45' value="<?=$contacto->email1; ?>" />
				<img src='/imagenes/32x32/Down.png' id='email_add_img' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' onclick="abrir('email_add')"/>		
				<div id='email_add' style='display:none;'>
				 2.-<input type="text" name="EMAIL2" id='email2' size='45' value="<?=$contacto->email2; ?>"  /><br>
				 3.-<input type="text" name="EMAIL3" id='email3' size='45' value="<?=$contacto->email3; ?>" />
				</div>
			</td>
		</tr>
		<tr class='modo1'>
			<td ><?=_('TELEFONOS')?></td>
			<td>
				<table>
					<thead>
						<tr>
							<th></th>
							<th><?=_('DDN')?></th>
							<th><?=_('TELEFONO')?></th>
							<th><?=_('EXT')?></th>
							<th><?=_('TIPO')?></th>
							<th><?=_('COMPANIA')?></th>
							<th></th>
							<th><img src='/imagenes/32x32/Down.png' id='telf_add_img' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' onclick="abrir('telf_add')"/></td>
						</tr>
					</thead>
					<tbody>
					<tr>
						<td><img src='/imagenes/iconos/search.gif' alt="15" width="15" align='absbottom' border='0' style='cursor: pointer;' onclick="ver_ddn('codigoarea[0]');"></td>
						<td><input type='text' name='CODIGOAREA[0]' id='codigoarea[0]' value="<?=($contacto->telefonos[0][CODIGOAREA]==''?$con->lee_parametro('PREFIJO_DDN'):$contacto->telefonos[0][CODIGOAREA])?>" size='3' onKeyPress="return validarnumero(event)" ></td>
						<td><input type='text' name='NUMEROTELEFONO[0]'  value ='<?=$contacto->telefonos[0][NUMEROTELEFONO]?>' size="13" id='numerotelefono[0]'   onKeyPress="return validarnumtelefono(event)"></td>
						<td><input type='text' name='EXTENSION[0]' value ='<?=$contacto->telefonos[0][EXTENSION] ?>' size="4" onKeyPress="return validarnumero(event)"></td>
						<td><? $con->cmbselect_ar('IDTIPOTELEFONO[0]',$lista_t_telf,($contacto->telefonos[0][IDTIPOTELEFONO]==''?'Blank':$contacto->telefonos[0][IDTIPOTELEFONO]),'id="cvetipotelefono"' ,'','Seleccione'); ?> </td>
						<td><? $con->cmbselect_ar('IDTSP[0]',$lista_tsp,($contacto->telefonos[0][IDTSP]==''?'Blank':$contacto->telefonos[0][IDTSP]),'','','Seleccione') ?></td>
						<textarea name="TELF_COMENTARIO[0]" id='telf_0' style="display:none"><?=$contacto->telefonos[0][TELF_COMENTARIO]?></textarea>
						<td><img src='/imagenes/32x32/Paste.png' id='' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' title="<?=_('COMENTARIO')?>" onclick="comentario('telf_0');"/></td>
						<td><img  width="15px" height="16px" src="/imagenes/iconos/telefono.jpg"  align='absbottom' border='0' style='cursor: pointer;' onClick="llamada($F('codigoarea[0]')+$F('numerotelefono[0]'))"></img></td>
					</tr>
					</tbody>
				</table>
	
				<div id='telf_add' style='display:none;'>
				<table>
					<? for($i=1;$i<=$con->lee_parametro('NUMERO_TELF_PROVEEDOR');$i++) { ?>
					<tr>
					<td><img src='/imagenes/iconos/search.gif' alt="15" width="15" align='absbottom' border='0' style='cursor: pointer;' onclick="ver_ddn('codigoarea[<?=$i?>]');"></td>
					<td><input type='text' name='CODIGOAREA[<?=$i?>]' id='codigoarea[<?=$i?>]' value="<?=($contacto->telefonos[$i][CODIGOAREA]==''?$con->lee_parametro('PREFIJO_DDN'):$contacto->telefonos[$i][CODIGOAREA])?>" size='3' ></td>
					<td><input type='text' name='NUMEROTELEFONO[<?=$i?>]'  value ='<?=$contacto->telefonos[$i][NUMEROTELEFONO]?>' size="13"  id='numerotelefono[<?=$i?>]' onKeyPress="return validarnumtelefono(event)"></td>
					<td><input type='text' name='EXTENSION[<?=$i?>]' value ='<?=$contacto->telefonos[$i][EXTENSION] ?>' size="4"  onKeyPress="return validarnum(event)"></td>
					<td><? $con->cmbselect_ar("IDTIPOTELEFONO[$i]",$lista_t_telf,($contacto->telefonos[$i][IDTIPOTELEFONO]==''?'Blank':$contacto->telefonos[$i][IDTIPOTELEFONO]),'id="cvetipotelefono" ','','Seleccione'); ?> </td>
					<td><? $con->cmbselect_ar("IDTSP[$i]",$lista_tsp,($contacto->telefonos[$i][IDTSP]==''?'Blank':$contacto->telefonos[$i][IDTSP]),'','','Seleccione') ?></td>
					<textarea name="TELF_COMENTARIO[<?=$i?>]" id='telf_<?=$i?>' style="display:none"><?=$contacto->telefonos[$i][TELF_COMENTARIO]?></textarea>
					<td><img src='/imagenes/32x32/Paste.png' id='' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' title="<?=_('COMENTARIO')?>" onclick="comentario('telf_<?=$i?>');"/></td>
					<td><img  width="15px" height="16px" src="/imagenes/iconos/telefono.jpg" align='absbottom' border='0' style='cursor: pointer;' onClick="llamada($F('codigoarea[<?=$i?>]')+$F('numerotelefono[<?=$i?>]'))"></img></td>
					</tr>
					<?}?>
				</table>
				</div>
			</td>
		</tr>
		<tr>
			<td><?=_('RESPONSABLE')?></td>
			<td>
			<?
			if ($contacto->responsable)$checked='checked';
			else $checked='';
				?>
			<input type='checkbox' name='RESPONSABLE' id="responsable" <?=$checked?>></td>
		</tr>
		<tr>
			<td><?=_('FECHANAC')?></td>
			<td>
			<select name='DIA' id='dia'>
			<option value='' ><?=_('DIA')?></option>
			<? for($i=1;$i<=31;$i++){
				if (($i<=9) &&($i>=1)) $valor='0'.$i;
				else $valor="$i";

				if ($i== $dia) echo "<option value='$valor' selected >$valor</option>";
				else echo "<option value='$valor' >$valor</option>";
			}
			?>
			</select>
			<?
			$con->cmbselect_ar('MES',$mes_del_anio,$mes,'id="mes" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;"','','seleccione');
			?>
			-
			<?
			$fecha = getdate();
			$con->cmbselect_anio('ANIO',$fecha[year]-18,40,"$anio","id='anio'",'','Año');
			?>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		
		<tr>
		<td colspan="4" align="center">
		<input type="button" value="<?=_('NUEVO CONTACTO')?>" class="normal" onclick="reDirigir('contacto.php?idproveedor='+<?=$idproveedor?>+'&edicion='+<?=$edicion?>)" <?=($edicion==1)?'':'disabled';?>>
		<input type='button' value="<?=_('GRABAR')?>" class="guardar" onclick="grabar();"  <?=($edicion==1)?'':'disabled';?>>
		<input type='button' value="<?=_('SALIR')?>" class="cancelar" onclick="salir();">
		</td>
		</tr>
		</table>
	
	</form>
</fieldset>
<script type="text/javascript">
var win = null;

/* VALIDA FECHA EN FORMATO DD-MM-AAAA  INCLUYE BISIESTOS*/

function validar_fecha(fecha){
	var sw = false;

	if (fecha)
	{
		sw= true;
		if ((fecha.substr(2,1) == "-") && (fecha.substr(5,1) == "-"))
		{
			for (i=0; i<10; i++)
			{
				if (((fecha.substr(i,1)<"0") || (fecha.substr(i,1)>"9")) && (i != 2) && (i != 5))
				{
					sw = false;
					break;
				}
			}
			if (sw)
			{
				a = fecha.substr(6,4);
				m = fecha.substr(3,2);
				d = fecha.substr(0,2);
				if((a < 1900) || (a > 2050) || (m < 1) || (m > 12) || (d < 1) || (d > 31))
				sw = false;
				else
				{
					if((a%4 != 0) && (m == 2) && (d > 28))
					sw = false; // Año no viciesto y es febrero y el dia es mayor a 28
					else
					{
						if ((((m == 4) || (m == 6) || (m == 9) || (m==11)) && (d>30)) || ((m==2) && (d>29)))
						sw = false;
					}
				}
			}
		}
		else
		sw = false;
	}
	return sw;
}

function grabar(){
	var result;

	fecha = $F('dia')+'-'+$F('mes')+'-'+$F('anio');

	if (($F('dia')!='' || $F('mes')!='' || $F('anio')!='' ))  result = validar_fecha(fecha);
	else result = true;

	if (trim($F('nombre'))=='') alert("<?=_('Ingrese el nombre del contacto')?>");
	else if (trim($F('appaterno'))=='') alert("<?=_('Ingrese el apellido paterno')?>");
	else if (trim($F('numerotelefono[0]'))=='') alert("<?=_('El contacto debe tener al menos un telefono')?>");
	else if (!result)  alert("<?=_('Ingrese una fecha correcta')?>");
	else{
		new Ajax.Request('../../../controlador/ajax/ajax_proveedor_contacto.php?opcion=grabar',
		{
			method: 'post',
			parameters:  $('form_contacto').serialize(true),
			onSuccess: function(t){
				reDirigir('contacto.php?idproveedor='+<?=$idproveedor?>+'&edicion='+<?=$edicion?>);
			}
		});
	}
	return;
}

function ver_ddn(campo){

	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("Codigos de Area")?>',
			width: 300,
			height: 300,
			showEffect: Element.show,
			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "listar_codigoarea.php?campo="+campo

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

function abrir(campo)
{
	if ( $(campo).style.display=='none' )
	{
		$(campo).show();
		$(campo+'_img').src='/imagenes/32x32/Up.png';
	}
	else
	{
		$(campo).hide();
		$(campo+'_img').src='/imagenes/32x32/Down.png';
	}
	return;
}


function llamada(numero){
	new Ajax.Request('/app/controlador/ajax/ajax_llamada.php',
	{	method : 'get',
	parameters: {
		prefijo: "",
		num: numero,
		ext: '<?=$idextension?>'
	}
	}
	);
}


function comentario(campo){
	valor = $F(campo);
	Dialog.alert("<textarea cols='30' id='comentario_telefono'>"+valor+"</textarea>",
		{
			top: 200,
			width:300,
			showEffect: Element.show,
			hideEffect: Element.hide,
			className: "alphacube",
			okLabel: "SALIR",
			
			buttonClass: "normal",
			onOk: function(dlg)
			{
				$(campo).value = $F('comentario_telefono');
				
				return true;
			}
		});
	
	return;
}
</script>