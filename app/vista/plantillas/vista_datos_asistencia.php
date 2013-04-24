<table align="left" width="100%">
		<tbody>
			<tr>
				<td><?=_('DIRECCION DEL AFILIADO')?><br>
				<input type="text" value="<?=$exp->direccion?>" size="40" disabled>
				<img src='/imagenes/32x32/Tag.png'align='absbottom' border='0' style='cursor: pointer;' alt="16px" width="16px" onclick='ver_ubigeo("<?=$exp->idexpediente?>","expediente_ubigeo","IDEXPEDIENTE");'></img>
				</td>
				
				<td><?=_('DIRECCION DEL EVENTO')?><br>
				<input type="text" value="<?=$asis->lugardelevento->direccion?>" size="40" disabled>
				<img src='/imagenes/32x32/Tag.png' align='absbottom' border='0' style='cursor: pointer;' alt="16px" width="16px" onclick='ver_ubigeo("<?=$asis->lugardelevento->ID?>","asistencia_lugardelevento","ID");'></img>
				</td>
				<td rowspan="2">
				<?=$asis->servicio->plantilla->etiquetaapresentar?><br>
				<textarea cols="30" disabled ><?=$asis->asistencia_servicio->{$asis->servicio->plantilla->campoapresentar};?></textarea>
				</td>
				<!-- <td rowspan="2"><?=_('DISPONIBILIDAD DEL AFILIADO')?><br><? include_once('vista_disponibilidad_afiliado.php');?>
				</td> -->	
			</tr>
			<tr>
				<td><?=_('FAMILIA')?><br>
				<input type="text" value="<?=$asis->familia->descripcion?>" size='20' disabled></td>
				<td><?=_('SERVICIO')?><br>
				<input type="text" value="<?=$asis->servicio->descripcion?>" size='30' disabled></td>
			</tr>
		</tbody>
</table>
<script type="text/javascript">
var win = null;

function ver_ubigeo(idubigeo,tabla,campo){
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("UBICACION")?>',
			width: 500,
			height: 450,
			showEffect: Element.show,
			hideEffect: Element.hide,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			resizable: true,
			opacity: 0.95,
			url: "../ubigeo/ver_localizacion.php?idubigeo="+idubigeo+'&tabla='+tabla+'&campo='+campo
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
</script>


