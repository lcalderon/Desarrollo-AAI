<form name='form_bitacora' id='form_bitacora'>
<input type="hidden" name="IDUSUARIOMOD" id='idusuario' value="<?=$idusuario?>">	
<input type="hidden" name="IDASISTENCIA" id='idasistencia' value="<?=$idasistencia?>">	
<input type='hidden' name='ARRCLASIFICACION' id="arrclasificacion" value="">
<input type="hidden" name="IDETAPA" value="<?=$idetapa?>">	
<table width=80%>
	<tr>
		<td ><?=_('COMENTARIO EN BITACORA')?><br>
			<textarea name='COMENTARIO' id='comentario' cols="60" <? if($gestion) echo "disabled";?>></textarea> 
		</td>
		
		<td valign="middle">			
			<? if(!$gestion) :?>
			 <? if ($exp->arrstatusexpediente=='PRO'):?>
					<img src='/imagenes/iconos/grabar_bitacora.gif' onclick="grabar_bitacora();" title="<?=_('GRABAR BITACORA1')?>"  align='absbottom' border='0' style='cursor: pointer;' />
			 <?else:?>
			 		<img src='/imagenes/iconos/grabar_bitacora.gif' title="<?=_('GRABAR BITACORA1')?>"  align='absbottom' border='0' style='cursor: pointer;' />
			 <?endif;?>
			<? endif; ?>
		</td>
		
		
		

		
		<? if($idetapa==2 || $idetapa==4){
			$sql_prov_bit="
				SELECT 
					AP.IDPROVEEDOR,P.NOMBRECOMERCIAL 
				FROM 
					$con->temporal.asistencia_asig_proveedor AP 
					INNER JOIN $con->catalogo.catalogo_proveedor P ON AP.IDPROVEEDOR = P.IDPROVEEDOR 
				WHERE 
					AP.IDASISTENCIA = $idasistencia";
			$proveedor_bit = $asis->uparray($sql_prov_bit);
		?>
		<td>
			<? 
			if($proveedor_act!='')
			$con->cmbselect_ar('PROVEEDOR_BIT',$proveedor_bit,$proveedor_act,'id=proveedor_bit'," ",''); }
		 	?>
		</td>
		
		<? if ($idetapa==2 && !$asis->monitoreo_demora && $proveedor_act=='' && $asis->arrstatusasistencia=='PRO'):?>
		<td>
		   <input type="button"  id='btn_demora' value="<?=_('AVISO DE ASIGNACION TARDIA N/A')?>" onclick="grabar_monitoreo_demora();"  class="normal" >
		</td>
		<?endif;?>
		
		
		<? if (($idetapa==6) && ($fechaarribo==0)):?>
		
		<td colspan="3">&nbsp;</td>
		
		<?
		$fecha= getdate();
		$f_act = date("Y-m-d");
		?>
		
		<td>
		<form id='form_arribo'>
		<b><?=_('HORA DE ARRIBO')?></b>
		<fieldset style="background: #ECE9D8;border: 1px solid #7F9DB9;">
			<table >
				<tr>
					<td colspan="2" ><?=_('DIA')?></td>
					<td ><?=_('HORA')?></td>
					<td ><?=_('MINUTO')?></td>
				</tr>
				<tr>
					<td align="center">
						<input type="text" name='D_ARRIBO' id='date2' size=11 value="<?=$f_act?>"readonly>
					</td>
					<td align="center">
						<input type="button" id="calendarButton2" value=".." onmouseover="setupCalendars_asist();" class='normal'>
					</td>
			  		<td align="center">
						<? $asis->cmbselect_hora('H_ARRIBO',$fecha[hours],"id='h_arribo'",'');?>		
			  		</td>	
					<td align="center">
						<input type="text" size='2' name='M_ARRIBO' id='m_arribo' value='' onKeyPress="return validarnumero(event)"/>
			  		</td>	
				</tr>
			</table>
		</fieldset>
		</form>
		</td>
		
		<?endif;?>
		
		
		<? if ($idetapa==8):?>
		<td>
		   <?
		   //condicion del boton de llamadasatisfaccion//
		   if ($asis->servicio->concluciontemprana)
		   {
		   	if ($asis->servicio->conclucionconproveedor)
		   	{
		   		if ($asis->llamadasatisfaccion)	$btn_llamada='disabled';
		   	}
		   	else $btn_llamada='disabled';
		   }
		   else{
		   	if ($asis->llamadasatisfaccion)	$btn_llamada='disabled';
		   }
		   ?>
			<input type="button"  id='btn_llamada' value="<?=_('LLAMADA SATISFACCION N/A')?>" onclick="grabar_llamadasatisfaccion();"  class="normal" <?=$btn_llamada?>>
		</td>
		<?endif;?>


		
	</tr>
</table>


<script type="text/javascript">

function grabar_bitacora()
{
	try {
		idproveedor = $F('proveedor_bit');
	}catch(e){

		idproveedor='';
	}

	new Ajax.Request('/app/controlador/ajax/ajax_grabar_asistencia_bitacora.php',
	{
		method : 'post',
		parameters :
		{
			IDASISTENCIA: $F('idasistencia'),
			IDETAPA:"<?=$idetapa?>",
			IDUSUARIOMOD:"<?=$idusuario?>",
			ARRCLASIFICACION: "BIT",
			COMENTARIO: $F('comentario'),
			IDPROVEEDOR : idproveedor,
		},
		onSuccess: function(t)
		{
			$('comentario').value='';
			//			alert("<?=_('SE GRABO LA ASISTENCIA ') . $idasistencia ?>");
			//			location.reload();
			new Ajax.Updater('listado_bitacora','/app/vista/plantillas/listar_bitacora_etapa.php',{
				method: 'post',
				evalScripts : true,
				parameters:{
					idasistencia: $F('idasistencia'),
					idetapa: '<?=$idetapa?>'
				}

			});

		}
	});
	return;
}

</script>

