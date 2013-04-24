<?
include('../../includes/head_prot.php');
include('../../../modelo/clase_mysqli.inc.php');


$array_tipo_busqueda = array(
'0'=>_('SELECCIONE'),
'1'=>_('DOCUMENTO IDENTIDAD'),
'2'=>_('NUMERO CERTIFICADO'),
'3'=>_('NOMBRE DEL ASEGURADO')
);

$array_tipo_documento = array(
'1'=>_('RUC'),
'2'=>_('DNI'),
'3'=>_('NIT'),
'4'=>_('CARNET DE EXTRANJERIA'),
'5'=>_('CARNET DE IDENTIDAD'),
'6'=>_('PASAPORTE'),
'7'=>_('LIBRETA MILITAR'),
'8'=>_('DPI'),
'9'=>_('CODIGO INTERNO'),
'10'=>_('IDEN. MENORES DE EDAD'),
'11'=>_('IDENT.EMP.EXT')
);

$con= new DB_mysqli();
?>

<body onload="select_opcion($F('busqueda'));" >
<form name='FORM_BUSQUEDA' id='form_busqueda' >
<fieldset>
<legend><?=_('DATOS DE BUSQUEDA')?></legend>
<div id='zona_tipo_busqueda' style='float:left;width:30%'>
<table>
	<tr>
		<td><?=_('BUSQUEDA')?></td>
		<td><? $con->cmbselect_ar('BUSQUEDA',$array_tipo_busqueda,0,"id ='busqueda'","onChange='select_opcion(this.value)';",'');?></td>
	</tr>
</table>
</div>

<div id='zona_formulario' style="float:right;width:70%">
	
		<table style="width:80%">
			<tr id='zona_certificado'>
				<td><?=_('CERTIFICADO')?><br>
					<input class='dato' name='NCERTIFICADO' id='ncertificado' value='' size='15'  /></td>
			</tr>
			<tr id='zona_documento'>
				<td><?=_('TIPO DOCUMENTO')?><br>
				<? $con->cmbselect_ar('TIPODOCUMENTO',$array_tipo_documento,'','id=tipo_documento',"class='dato'",'Seleccione');?> </td>
				<td><?=_('N. DOCUMENTO')?><br>
				<input class='dato' name='NDOCUMENTO' id='ndocumento' value='' size='15' />			</td>
			</tr>	
			<tr id='zona_nombres'>
				<td><?=_('NOMBRES')?><br>
				<input class='dato' name='NOMBRES' id='nombres' value='' size='20' /></td>
				<td><?=_('AP. PATERNO')?><br>
				<input class='dato' name='APPATERNO' id='appaterno' value='' size='20' /></td>
				<td><?=_('AP. MATERNO')?><br>
				<input class='dato' name='APMATERNO' id='apmaterno' value='' size='20' /></td>
			</tr>
		</table>
	
<br>
<div>
	<table width="30%">
		<tr>
			<td><input type="button" id='' name=''  value="<?=_('CERRAR')?>" onclick="window.close();" class="normal"> </td>
			<td><input type="button" id='btn_buscar' name='' value="<?=_('BUSCAR')?>" onclick="buscar();" class="normal"> </td>
		</tr>
	</table>
</div>
</fieldset>	
</form>
</div>
<div id="cargando" class="spinner" style="display:none"><img src="/imagenes/iconos/spinner.gif" align="center" />Cargando…</div> 

<div id='resultado'>


</div>




</body>
<script type="text/javascript">

var globalCallbacks = {
	onCreate: function(){
		$('cargando').show();
	},
	onComplete: function() {
		if(Ajax.activeRequestCount == 0){
			$('cargando').hide();
		}
	}
};

/* Se registran los callbacks en Ajax.Responders */

Ajax.Responders.register( globalCallbacks );



function bloquear_campos(){
	$$('form#form_busqueda .dato').each(function (el){
		el.disabled=true;
	});
	return;
}

function desbloquear_campos(id){
	$$('tr#'+id+' .dato').each(function(el){
		el.disabled= false;
	});
	return;
}

function select_opcion(opcion_busqueda){
	$('ncertificado').value='<?=$_GET[ncertificado];?>';
	$('ndocumento').value='<?=$_GET[ndocumento];?>';
	$('appaterno').value='<?=$_GET[appaterno];?>';
	$('apmaterno').value='<?=$_GET[apmaterno];?>';
	$('nombres').value='<?=$_GET[nombres];?>';

	bloquear_campos();
	switch (opcion_busqueda)
	{
		case '1': {desbloquear_campos('zona_documento');break;}
		case '2': {desbloquear_campos('zona_certificado');break;}
		case '3': {desbloquear_campos('zona_nombres');break;}
	}
	return;
}

function buscar(){
	$('btn_buscar').disabled=true;
	$('resultado').innerHTML='';

	//	alert($F('busqueda'));
	new Ajax.Updater('resultado','datos_encontrados.php',
	{
		method : 'post',
		parameters: $('form_busqueda').serialize(),
		onSuccess: function (t){

			$('btn_buscar').disabled=false;
		}
	});
	return;
}



function selecccionar(certificado){

	new Ajax.Request('/app/controlador/ajax/ajax_grabar_garantia_extendida.php',{
		method: 'post',
		parameters:  $(certificado).serialize(),
		onSuccess: function (t){

			/* actualiza el formulario del expediente*/
			opener.document.getElementById('txtclavetitular').value=certificado;
			opener.document.getElementById('txtpaternotitular').value=$F('idappaterno');
			
			opener.document.getElementById('txtpaternotitular').value=$F('idappaterno');
			opener.document.getElementById('txtmaternotitular').value=$F('idapmaterno');
			opener.document.getElementById('txtnombretitular').value=$F('idnombres');

			opener.document.getElementById('txtnumdoctitular').value=$F('idnumdoc');
			
			opener.document.myForm.txttelefonotitular[0].value=$F('telefono1');
			opener.document.myForm.txttelefonotitular[1].value=$F('telefono2');
			opener.document.myForm.txttelefonotitular[2].value=$F('telefono3');
			
			opener.document.getElementById('cveentidad1').value=$F('cveentidad1')*1;
			opener.document.getElementById('cveentidad2').value=$F('cveentidad2')*1;
			opener.document.getElementById('cveentidad3').value=$F('cveentidad3')*1;			
			
			
			window.close();

			return;
		}
	});

	return;
}

</script>

