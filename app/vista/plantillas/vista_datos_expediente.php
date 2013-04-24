<table id='datos_expediente' >
		<tbody>
			<tr>
				<td width="25%">
				<?=_('TITULAR')?><br>
				<input type='text' size='40' value="<?=$afiliado?>" disabled>
				</td>
				
				<td width="25%">
				<?=_('CONTACTANTE')?><br>
				<input type='text'  size='40' value="<?=$contactante?>" disabled>
				</td>
				
				<td width="10%"><?=_('CUENTA')?><br>
				<input type='text'value="<?=$cuenta?>" size="30" disabled>
				</td>
				<td width="5%">
				<? if ($exp->cuenta->cuentavip) echo "VIP"?>
				</td>
				</td>
				<td width="10%"><?=_('PLAN')?><br>
				<input type='text' value="<?=$plan?>" size="30" disabled>
				</td>
				<td width="5%">
				<?
					if ((!$exp->cuenta->cuentavip) && ($exp->programa->programavip)) echo "VIP";
					if(isset($asis)){
				?><img src='../../../imagenes/iconos/pdf.gif' alt='<?=_('VER DETALLE')?>' width='15' style='cursor: pointer;' title='<?=_('VER CONTRATO');?>' onclick=ver_detalle('<?=$idprograma;?>')></img><?}?>
				</td>
				<td width="20%">
				<? if(isset($asis)) {?><input type="button" value="bitacora" id='bitacora_general' class="normal" onclick="bitacora('<?=$asis->idasistencia?>','<?=$gestion;?>')"  > <?}?>
				<? if(isset($asis)) {?><input type="button" value="Tareas" id='agregar_tarea' class="normal" onclick="agregarTarea('<?=$asis->idasistencia?>','<?=$asis->expediente->idexpediente?>')"  > <?}?>
				<? if(isset($asis)) {?><input type="button" value="Imagenes" id='agregar_imagen' class="normal" onclick="agregarImagen('<?=$asis->idasistencia?>')"  > <?}?>
				
				</td>
			
				
			</tr>
			<tr>
				<td>
				<?=_('TELEFONOS')?><br>
				<?			
				$exp->leer_telf_persona($exp->personas['TITULAR'][IDPERSONA]);
				foreach ($exp->telefonos as $telefonos) $telf[$telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO]] = $telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO];				
				$exp->cmbselect_ar('TELEF_TIT',$telf,'',"id='telf_tit'",'','');
				?>
				<img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onclick="llamada_asistencia($('telf_tit').value,'<?=$idextension?>','<?=$idetapa?>','<?=$idasistencia?>')">
				</td>
				<td>
				<?=_('TELEFONOS')?><br>
				<?
				$exp->leer_telf_persona($exp->personas['CONTACTO'][IDPERSONA]);
				foreach ($exp->telefonos as $indice=>$telefonos) $telf[$telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO]] = $telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO];
				$exp->cmbselect_ar('TELEF_CON',$telf,'',"id='telf_con'",'','');
				?>
				<img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onclick="llamada_asistencia($('telf_con').value,'<?=$idextension?>','<?=$idetapa?>','<?=$idasistencia?>')">
				</td>
			
			</tr>
			
		</tbody>
	</table>


<script type="text/javascript" >

function bitacora(idasistencia,gestion){
	if (win != null) alert("<?=_('CIERRE LA VENTANA ANTERIOR')?>");
	else
	{
		if ('<?=$modo?>'=='AUTORIZA') ruta="/app/vista/bitacora/bitacora.php?idasistencia="+idasistencia+"&etapaactiva=7";
		else
		 ruta="/app/vista/bitacora/bitacora.php?idasistencia="+idasistencia+"&gestion="+gestion;
		 
		win = new Window({
			className: "alphacube",
			title:"<?=_('BITACORA')?>",
			width: 800,
			height: 300,
			showEffect: Element.show,
			hideEffect: Element.hide,
			resizable: true,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: ruta
		});

//		win.keepMultiModalWindow = true;
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


function agregarTarea(idasistencia,idexpediente){
	
	if (win != null) alert("<?=_('CIERRE LA VENTANA ANTERIOR')?>");
	else
	{
		if ('<?=$modo?>'=='AUTORIZA') ruta="/app/vista//form_tarea.php?idasistencia="+idasistencia+"&etapaactiva=7";
		else
		 ruta="/app/vista/monitor_tareas/form_tarea.php?idasistencia="+idasistencia+"&idexpediente="+idexpediente;
		 
		win = new Window({
			className: "alphacube",
			title:"<?=_('AGREGAR TAREAS')?>",
			width: 400,
			height: 300,
			showEffect: Element.show,
			hideEffect: Element.hide,
			resizable: true,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: ruta
		});

//		win.keepMultiModalWindow = true;
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

function agregarImagen(idasistencia){
	
	if (win != null) alert("<?=_('CIERRE LA VENTANA ANTERIOR')?>");
	else
	{
		
		 ruta="/app/vista/archivos/index.php?idasistencia="+idasistencia;
		 
		win = new Window({
			className: "alphacube",
			title:"<?=_('AGREGAR IMAGENES')?>",
			width: 400,
			height: 300,
			showEffect: Element.show,
			hideEffect: Element.hide,
			resizable: true,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: ruta
		});

//		win.keepMultiModalWindow = true;
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

<script type="text/javascript">
function ver_detalle(idprograma){
		
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("DETALLE DEL CONTRATO")?>',
			width: 800,
			height: 500,
			showEffect: Element.show,
			hideEffect: Element.hide,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			resizable: true,
			opacity: 0.95,
			url: "contrato.php?idprograma="+idprograma
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