<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');		
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once("../../../vista/login/Auth.class.php");	
	include_once('../../includes/head_prot_win.php');
	include_once('../../../modelo/clase_unidadfederativa.inc.php');
	
	$con= new DB_mysqli();
	 
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	
 	session_start(); 
 	Auth::required();
	
	validar_permisos("MENU_SAC",1);
	
	if ($_GET[iddomicilio]!=''){
		
			$ubigeo= new ubigeo();
			$ubigeo->leer('ID',$ubigeo->catalogo,"catalogo_afiliado_persona_domicilio_ubigeo",$_GET[iddomicilio]);
			
	}

	if($_GET["verinfo"])
	 {
		$_GET["iddomicilio"]=$_GET["verinfo"];	
		$readonly="readonly";
		$disabled="disabled";
	 }	

		$Sql_domicilio="SELECT
					  catalogo_afiliado_persona_domicilio_ubigeo.ID,
					  catalogo_afiliado_persona_domicilio_ubigeo.IDAFILIADO,
					  catalogo_afiliado_persona_domicilio_ubigeo.DESCRIPCION,
					  catalogo_afiliado_persona_domicilio_ubigeo.DIRECCION,
					  catalogo_afiliado_persona_domicilio_ubigeo.NUMERO,
					  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD1,
					  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD2,
					  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD3,
					  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD4,
					  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD5,
					  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD6,
					  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD7,
					  catalogo_afiliado_persona_domicilio_ubigeo.ACTIVO,
					  catalogo_afiliado_persona_domicilio_ubigeo.DESCRIPCION
					  FROM $con->catalogo.catalogo_afiliado_persona_domicilio_ubigeo
					  INNER JOIN $con->catalogo.catalogo_afiliado
						ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_domicilio_ubigeo.IDAFILIADO
					  WHERE catalogo_afiliado_persona_domicilio_ubigeo.IDAFILIADO= ".$_GET["idafiliado"]."
					  ORDER BY catalogo_afiliado_persona_domicilio_ubigeo.ID DESC";
 
		$rs_domicilio=$con->query($Sql_domicilio);

		if($_GET["iddomicilio"])
		 {
			$Sql_dom="SELECT
					  catalogo_afiliado_persona_vehiculo.ID,
					  catalogo_afiliado_persona_vehiculo.IDAFILIADO,
					  catalogo_afiliado_persona_vehiculo.ARRPESO,
					  catalogo_afiliado_persona_vehiculo.IDFAMILIAVEH,
					  catalogo_afiliado_persona_vehiculo.ANIO,
					  catalogo_afiliado_persona_vehiculo.ARRCOMBUSTIBLE,
					  catalogo_afiliado_persona_vehiculo.ARRTRANSMISION,
					  catalogo_afiliado_persona_vehiculo.COLOR,
					  catalogo_afiliado_persona_vehiculo.FECHAMOD,
					  catalogo_afiliado_persona_vehiculo.MARCA,
					  catalogo_afiliado_persona_vehiculo.NUMSERIECHASIS,
					  catalogo_afiliado_persona_vehiculo.NUMSERIEMOTOR,
					  catalogo_afiliado_persona_vehiculo.NUMVIN,
					  catalogo_afiliado_persona_vehiculo.PLACA,
					  catalogo_afiliado_persona_vehiculo.SUBMARCA,
					  catalogo_afiliado_persona_vehiculo.USO
					FROM catalogo_afiliado_persona_vehiculo
					  INNER JOIN catalogo_afiliado
						ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_vehiculo.IDAFILIADO
					WHERE catalogo_afiliado_persona_vehiculo.ID=".$_GET["iddomicilio"];

			$rsdomicilio=$con->query($Sql_dom);
			$rowdom= $rsdomicilio->fetch_object();	
		 }	 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>American Assist</title>
	<script type="text/javascript">
		
		function validarIngreso(variable){	 
		
			if(document.form_localizador.DIRECCION.value ==""){
                  alert('<?=_('INGRESE LA DIRECCION')?>.');
                  document.form_localizador.DIRECCION.focus();
                  return (false);
			}
		
			if(confirm('<?=(!$_GET["iddomicilio"])?_("DESEA AGREGAR UN NUEVO REGISTRO?."):_("ESTA SEGURO QUE DESEA ACTUALIZAR LOS CAMBIOS?.") ;?>'))		  
			{
				//document.form1.action="gdomicilio.php" ;
				document.form1.submit();
			}			
				return (false);
		}
	</script>
	
</head>
<body>
<form name='form_localizador' id='form_localizador' action="grabar_localizacion.php" method="post" onSubmit = "return validarIngreso(this)">
<input type="hidden" name="idafiliado" value="<?=$_GET["idafiliado"];?>" />
<input type="hidden" name="iddomicilio" value="<?=$_GET["iddomicilio"];?>" />
<fieldset>
<legend><?=_('REGISTRAR DOMICILIO') ?></legend>
<table>
	<? include_once('../../../../app/vista/includes/vista_entidades_ubigeo.php')?>
	<tr>
		<td><?=_('TIPO VIA')?></td>
		<td>
		<? $con->cmbselect_db('CVETIPOVIA','select IDTIPOVIA, DESCRIPCION from catalogo_tipo_via',($prov->cvetipovia==''?'Blank':$prov->cvetipovia),'id="cvetipovia"','','TODOS')?>
		</td>
	</tr>
	<tr>
		<td><?=_('DIRECCION *')?></td>
		<td><input type="text" name='DIRECCION' value='<?=$ubigeo->direccion; ?>'   id='direccion' size='65' autocomplete="off"  ></td>
		<div id='sugeridos' class="autocomplete" ></div>
	</tr>
	<tr>
		<td><?=_('Nro')?></td>
		<td>
		<input type="text" name="NUMERO" size="12" maxlength="12" id='numero' autocomplete="off"  onkeypress="return validarnum(event)" value="<?=$ubigeo->numero?>"  >
		<input type ='button' value="<?=_('Ajustes de ubicacion') ?>" id='ver_mapa' class="normal" ></input>
		&nbsp; 
		</td>
	</tr>
	
	<tr>
		<td valign="top"><?=_('REFERENCIA')?></td>
		<td><textarea name='DESCRIPCION' cols='40' rows="1" id='descripcion' ><?=$ubigeo->descripcion?></textarea></td>
	</tr>
</table>
</fieldset>
<input type='hidden'  name='IDUBIGEO' value='<?=$ubigeo->idubigeo ?>' >
<input type='hidden'  name='CVEPAIS' value='<?=$idpais ?>' >
<input type='hidden'  name='LATITUD' id='latitud'  value='<?=$ubigeo->latitud?>' >
<input type='hidden'  name='LONGITUD' id='longitud' value='<?=$ubigeo->longitud?>' >
<input type='hidden'  name='tabla' id='tabla' value="catalogo_afiliado_persona_domicilio_ubigeo" >

<table align="center">
	<tr>
		<td><input type='submit' value=<?=_('GUARDAR') ?> id='btn_grabar' class="guardar"  style="height:22px" ></input></td>
		<td><input type='button' value=<?=_('CERRAR') ?> id='btn_salir' onclick="self.close()" class="cancelar" ></input></td>
	</tr>
</table>
<br>

<table width="95%" border="0" cellpadding="1" cellspacing="1" bgcolor="#E0EFFC" style="border:1px solid #A6C9E2">
		<tr>
		  <td bgcolor="#E1EFFB"><div align="center"> <span class="style3">#
		  </span> </div></td>
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	     <strong> <?=_("ENTIDAD 1") ;?></strong>
		  </span></div></td>	
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("ENTIDAD 2") ;?>
		  </span></div></td>
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("ENTIDAD 3") ;?>
		  </span></div></td>
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("DIRECCION") ;?>
		  </span></div></td>
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("NUMERO") ;?>
		  </span></div></td>
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("REFERENCIA") ;?>
		  </span></div></td>	
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <?=_("STATUS") ;?>
		  </span></div></td> 
			<td   colspan="2"><div align="center"></div></td>
		</tr>	
		  
		<?
			$unidad = new unidadfederativa();
			
			while($reg = $rs_domicilio->fetch_object())
			 {

				$entidad[1]=$reg->CVEENTIDAD1;
				$entidad[2]=$reg->CVEENTIDAD2;
				$entidad[3]=$reg->CVEENTIDAD3;
				$entidad[4]=$reg->CVEENTIDAD4;
				$entidad[5]=$reg->CVEENTIDAD5;
				$entidad[6]=$reg->CVEENTIDAD6;
				$entidad[7]=$reg->CVEENTIDAD7;
				$arr = $unidad->nombre_entidades_array($entidad);
				
				$c=$c+1;
		
		?>		
		 <tr bgcolor="<?=($_GET["iddomicilio"]==$reg->ID and !$_GET["verinfo"])?"#FFBBBB":"#FFFFFF" ?>" >
			<td bgcolor="#A6C9E2"><div align="center"><strong><?=$c;?></strong></div></td>
			<td align="center"><?=$arr[1];?></td>									
			<td><?=$arr[2];?></td>			
			<td><?=$arr[3];?></td>			
			<td><?=$reg->DIRECCION;?></td>
			<td><?=$reg->NUMERO;?></td>
			<td><?=$reg->DESCRIPCION;?></td>
			<td align="center"><div id="div-status<?=$c;?>" style="text-align:center;width:65px;height:15px;background-color:<?=($reg->ACTIVO==1)?"#CEFFCE":"#FF2F2F";?>"><?=($reg->ACTIVO==1)?_("ACTIVO"):_("INACTIVO");?></div></td>
			<td style="text-align:center"><? if($_GET["iddomicilio"]==$reg->ID and !$_GET["verinfo"]){ ?><a href="domicilio_dafiliado.php?idafiliado=<?=$_GET["idafiliado"];?>"><img src="../../../../imagenes/iconos/cancelar_dato.jpg" title="<?=_("CANCELAR") ;?>" width="46" height="15" border="0" onClick="reDirigir('domicilio_dafiliado.php?iddomicilio=<?=$_GET["iddomicilio"];?>')"></a><? }else{ ?><a href="domicilio_dafiliado.php?iddomicilio=<?=$reg->ID;?>&idafiliado=<?=$_GET["idafiliado"];?>"><img src="../../../../imagenes/iconos/editar_dato.jpg" title="<?=_("EDITAR REGISTRO") ;?>" width="35" height="14" border="0"></a><? } ?></td>
			<td style="text-align:center"><input type="checkbox" name="chbactivar<?=$c;?>" id="chbactivar<?=$c;?>" <?=($reg->ACTIVO==1)?"checked":"";?> onclick="actulizar_status('<?=$reg->ID;?>','<?=$reg->IDAFILIADO;?>',this.checked,this.name,'<?="div-status".$c;?>')" title="<?=_("ACTIVAR/DESACTIVAR") ;?>" <? if($_GET["iddomicilio"]==$reg->ID and !$_GET["verinfo"]){ ?> disabled <? } ?>/></td>
			<td style="text-align:center"><img src="../../../../imagenes/iconos/historia_s.gif" title="<?=_("HISTORIAL") ;?>"  style="cursor:pointer" onClick="ventana_historialdomicilio('<?=$reg->ID;?>')" ></td>
 			
		 </tr>	
		<?			  
			
			}
		?>		  
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

	if (lat==0 && lng==0) alert('<?=_('No hay nada que mostrar en el mapa')?>');
	else
	{
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
				url: "../../ubigeo/mapa_localizacion.php?lat="+lat+"&lng="+lng

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
});



// **********************  Ajax para autocompletar las calles ********************************//
new Ajax.Autocompleter('direccion',	'sugeridos',
"../../../controlador/ajax/ajax_calles.php",
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
		new Ajax.Request('../../../controlador/ajax/ajax_interseccion.php',
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

   
</script>



	<script type="text/javascript">	
		
		var validar_func = '';
		var win = null;
		
		function ventana_historialdomicilio(id){
		
			if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: '<?=_("HISTORIAL -> STATUS DOMICILIO")?>',
					width: 350,
					height: 170,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: true,
					minimizable: false,
					maximizable: false,
					resizable: true,
					opacity: 0.95,
					url: 'historial_domicilio.php?id='+id
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
			
			
		function actulizar_status(id,idcodigo,valor,nombre,nombrediv){
 
			if(valor)	valor=1; else valor=0;			
			if(valor==1)	msg='<?=_("DESEA ACTIVAR EL REGISTRO DEL DOMICILIO?")?>.'; else msg='<?=_("DESEA INACTIVAR EL REGISTRO DEL DOMICILIO?")?>.';
			
			
			
			if(confirm(msg))
			 {
					new Ajax.Updater(nombrediv, 'g_statusdom.php', {
					  parameters: 'id='+id+'&idcodigo='+idcodigo+'&valor='+valor, 
					  method: 'post',
					  onSuccess: function(t){
							if(t.responseText=='INACTIVO')	$(nombrediv).style.background="#FF2F2F"; else $(nombrediv).style.background="#CEFFCE";

						}					  
					});

			 }
			else
			 {
				if(valor==1)	valor=0; else valor=1;	
				$(nombre).checked=valor;	
			 }
				
		}	
					
	</script>