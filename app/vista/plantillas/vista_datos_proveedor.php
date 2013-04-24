
<table width="100%">
	<thead>
		<tr>
			<th width="3%"></th>
			<th width="15%" ><?=_('PROVEEDOR')?></th>
			<th class="nosort" width="20%" ><?=_('CONTACTO')?></th>
			<th class="nosort" width="15%"><?=_('TELEFONOS')?></th>
			<th class="nosort" width="10%"><?=_('TIPO ATENCION')?></th>
			<th class="nosort" width="10%"><?=_('TIEMPO MINIMO')?></th>
			<th class="nosort" width="10%"><?=_('TIEMPO MAXIMO')?></th>
			<th class="nosort" width="10%"><?=_('FECHA ASIGNACION')?></th>
			<th class="nosort" width="5%" ><?=_('STATUS')?></th>
			<th class="nosort" width="5%"><?=_('COSTOS')?></th>
		</tr>
	</thead>
	<tbody>
		<?	
		$linea=0;
		foreach ($asis->proveedores as $prov)
		{
			$prov_act[]=$prov[statusproveedor];
			$colorlinea = ($linea%2)? 'par':'impar';
			$proveedor = new proveedor();
			$proveedor->carga_datos($prov[idproveedor]);

			$contacto = new contacto();
			$contacto->carga_datos(key($proveedor->contactos));
			
			unset($telef_cont);
			foreach ($contacto->telefonos as $telefonos)
			$telef_cont[trim($telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO])]=$telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO];
			?>
				<tr class="<?=$colorlinea?>">
				<td><img src='/imagenes/32x32/Info.png' align='absbottom' border='0' alt='20px' height="20px"style='cursor: pointer;' onclick="edit_proveedor(<?=$prov[idproveedor]?>)"  /></td>
				<td><?=$proveedor->nombrecomercial?></td>
				<td><?=$asis->cmbselect_ar('CONTACTO',$proveedor->contactos,"id='contacto'","onchange=act_telefono(this.value,'$prov[idproveedor]')",'','')?></td>	
				<td id="<?=$prov[idproveedor]?>_telf_contacto" align="center"> 
					<? include('ajax_telefono_contacto.php')?>
				</td>
				<td align="center"><?=$prov[arrprioridadatencion]?></td>
				<td style="border:groove;border-color:#D9E8FF" align="center"><?=$prov[teat]?></td>
				<td style="border:groove;border-color:#D9E8FF" align="center"><?=$prov[team]?></td>
				<td style="border:groove;border-color:#D9E8FF"   align="center"><?=$prov[fechahora]?></td>
				<td align="center"><?=$status_asig_prov[$prov[statusproveedor]] ?></td>
				<td ><img src='/imagenes/iconos/calculadora.gif' title='Costos' align='absbottom' border='0' style='cursor: pointer;' onclick=carga_calculadora("<?=$asis->proveedores[$linea][idasigprov]?>")> </td>
				</tr>
			<?
			$linea++;
		}
			?>
	<tbody>
</table>

<script type="text/javascript">

function carga_calculadora(idasigprov)
{

	if ('<?=$modo?>'=='AUTORIZA') {
		ancho='750';
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
			resizable: false,
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

function act_telefono(idcontacto,idproveedor){
	new Ajax.Updater(idproveedor+'_telf_contacto','ajax_telefono_contacto.php',
	{
		evalScripts: true,
		method : 'get',
		parameters : {
			IDPROVEEDOR : idproveedor,
			IDCONTACTO : idcontacto,
			IDETAPA: '<?=$idetapa?>',
			IDASISTENCIA: '<?=$idasistencia?>',
			IDEXTENSION: '<?=$idextension?>'
		},
		onSuccess: function(t){
			//alert(t.responseText);

		}
	});
	return;
}


function edit_proveedor(idproveedor){

	if (idproveedor=='') parametro='';
	else
	parametro ='?idproveedor='+idproveedor;

	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			width: 930,
			height: 500,
			showEffect: Element.show,
			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "/app/vista/catalogos/proveedores/form_catprov.php"+parametro
		});

		win.keepMultiModalWindow = true;
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