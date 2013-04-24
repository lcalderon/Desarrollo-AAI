<?
 	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/functions.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	include_once('../../modelo/functions.php');
	
	$con= new DB_mysqli();
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	$con->select_db($con->catalogo);
		
 	session_start(); 
	
	$anio_vehiculo=mostrarAnio_Vehiculo();
 
	if($_POST["idvehiculo"])
	 {	
			if($_POST["opcion"]=="SAC")	
			 {
				$Sql_vehi="SELECT
					  catalogo_afiliado.IDAFILIADO,
					  catalogo_afiliado_persona_vehiculo.ARRPESO,
					  catalogo_afiliado_persona_vehiculo.IDFAMILIAVEH,
					  catalogo_afiliado_persona_vehiculo.ANIO,
					  catalogo_afiliado_persona_vehiculo.ARRCOMBUSTIBLE,
					  catalogo_afiliado_persona_vehiculo.ARRTRANSMISION,
					  catalogo_afiliado_persona_vehiculo.COLOR,
					  catalogo_afiliado_persona_vehiculo.FECHAMOD,
					  catalogo_afiliado_persona_vehiculo.ID,
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
					WHERE catalogo_afiliado_persona_vehiculo.ID = '".$_POST["idvehiculo"]."' ";
			 }
			else
			 {
				$Sql_vehi="SELECT
					  asistencia_vehicular_datosvehiculo.ARRPESO,
					  asistencia_vehicular_datosvehiculo.IDFAMILIAVEH,
					  asistencia_vehicular_datosvehiculo.ANIO,
					  asistencia_vehicular_datosvehiculo.ARRCOMBUSTIBLE,
					  asistencia_vehicular_datosvehiculo.ARRTRANSMISION,
					  asistencia_vehicular_datosvehiculo.COLOR,
					  asistencia_vehicular_datosvehiculo.FECHAMOD,
					  asistencia_vehicular_datosvehiculo.ID,
					  asistencia_vehicular_datosvehiculo.MARCA,
					  asistencia_vehicular_datosvehiculo.NUMSERIECHASIS,
					  asistencia_vehicular_datosvehiculo.NUMSERIEMOTOR,
					  asistencia_vehicular_datosvehiculo.NUMVIN,
					  asistencia_vehicular_datosvehiculo.PLACA,
					  asistencia_vehicular_datosvehiculo.SUBMARCA,
					  asistencia_vehicular_datosvehiculo.USO
					FROM $con->temporal.asistencia_vehicular_datosvehiculo
					  INNER JOIN $con->temporal.asistencia
						ON asistencia.IDASISTENCIA = asistencia_vehicular_datosvehiculo.IDASISTENCIA
					WHERE asistencia_vehicular_datosvehiculo.ID = '".$_POST["idvehiculo"]."' ";
			 }
			
	 }
	else
	 {
					$Sql_vehi="SELECT
					  asistencia_vehicular_datosvehiculo.ARRPESO,
					  asistencia_vehicular_datosvehiculo.IDFAMILIAVEH,
					  asistencia_vehicular_datosvehiculo.ANIO,
					  asistencia_vehicular_datosvehiculo.ARRCOMBUSTIBLE,
					  asistencia_vehicular_datosvehiculo.ARRTRANSMISION,
					  asistencia_vehicular_datosvehiculo.COLOR,
					  asistencia_vehicular_datosvehiculo.FECHAMOD,
					  asistencia_vehicular_datosvehiculo.ID,
					  asistencia_vehicular_datosvehiculo.MARCA,
					  asistencia_vehicular_datosvehiculo.NUMSERIECHASIS,
					  asistencia_vehicular_datosvehiculo.NUMSERIEMOTOR,
					  asistencia_vehicular_datosvehiculo.NUMVIN,
					  asistencia_vehicular_datosvehiculo.PLACA,
					  asistencia_vehicular_datosvehiculo.SUBMARCA,
					  asistencia_vehicular_datosvehiculo.USO
					FROM $con->temporal.asistencia_vehicular_datosvehiculo
					  INNER JOIN $con->temporal.asistencia_vehicular
						ON asistencia_vehicular.IDASISTENCIA = asistencia_vehicular_datosvehiculo.IDASISTENCIA
					WHERE asistencia_vehicular_datosvehiculo.ID = '".$asis->asistencia_familia->IDVEHICULO."' ";	
	 }
		 //echo $Sql_vehi;
	$result=$con->query($Sql_vehi);
	$row_veh = $result->fetch_object();
	 
	 	
?> 
<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../../librerias/scriptaculous/scriptaculous.js"></script>
<link href="../../../estilos/suggest/ubigeo.css" rel="stylesheet" type="text/css" />
	<style type="text/css">
	.CamposObligatorio{
		color: #FF4242;
		font-weight: bold;
		font-size: 17px;
	}
	</style>
  <table border="0" id="main" width="300" cellpadding="2" cellspacing="1" style="border:1px solid #999999">
		<tr>
			<td class="style1" width="104">&nbsp;<?=_('MARCA')?></td>
			<td class="style1" ><input name='txtmarca' type='text' id='txtmarca' onblur="cambiarestilo('txtmarca');" value='<?=$row_veh->MARCA; ?>' autocomplete="off"  size="15" maxlength="15"><span class="CamposObligatorio">*</span>
		  <? if(!$asis->idasistencia) {?><img src="../../../imagenes/iconos/copy_data.gif" onClick="ventana_vehiculo()" style="cursor:pointer" border="0" width="18" height="18" title="<?=_('BUSCAR VEHICULO AFILIADO')?>" alt="Vehiculo"><? } ?></td>
			<div id="mostrarmarca" class="autocomplete" style="display:none"></div>
		</tr>
		
		<tr>
			<td class="style1">&nbsp;<?=_('MODELO')?></td>
			<td class="style1"><input name='txtsubmarca' type='text' id='txtsubmarca' onblur="cambiarestilo('txtsubmarca');" value='<?=$row_veh->SUBMARCA; ?>' autocomplete="off"  size="30" maxlength="30"><span class="CamposObligatorio">*</span></td>
			 <div id="mostrarsmarca" class="autocomplete" style="display:none"></div>
		</tr>
		<tr>
			<td class="style1">&nbsp;<?=_('PLACA')?></td>
			<td class="style1"><input name='txtplaca' type='text' id='txtplaca' onblur="cambiarestilo('txtplaca');" value='<?=$row_veh->PLACA; ?>' size="20" maxlength="18"><span class="CamposObligatorio">*</span></td>
		</tr>
		<tr>
			<td class="style1">&nbsp;<?=_('# VIM')?></td>
			<td class="style1"><input name='txtvim' type='text' id='txtvim' value='<?=$row_veh->NUMVIN; ?>' size="20" maxlength="18"></td>
		</tr>
		<tr>
			<td class="style1">&nbsp;<?=_('TRANSMISION')?></td>
		    <td class="style1"><?
			$con->cmb_array("cmbtrasmision",$desc_transmision,$row_veh->ARRTRANSMISION,"  class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'");
		?>&nbsp;</td>
		</tr>
		<tr>
		  <td class="style1">&nbsp;<?=_('COMBUSTIBLE')?></td>
		  <td class="style1"><?
			$con->cmb_array("cmbcombustible",$desc_combustible,$row_veh->ARRCOMBUSTIBLE,"  class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'");
		?>&nbsp;</td>
		</tr>
		<tr>
		  <td class="style1">&nbsp;<?=_('USO')?></td>
		      <td class="style1"><?
			$con->cmb_array("cmbuso",$desc_uso,$row_veh->USO," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'")
		?></td>
		</tr>
		<tr>
		  <td class="style1">&nbsp;<?=_('CLASE VEHICULO')?></td>
		   <td class="style1"><?
			$con->cmbselectdata("select IDFAMILIAVEH,DESCRIPCION from catalogo_familiavehiculo WHERE IDFAMILIAVEH IN('1','5') order by DESCRIPCION","cmbfamilia",$row_veh->IDFAMILIAVEH," onchange=\"comportamientoDiv(this.value,'comportamientoDiv')\" class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","","S/D");
		?><span class="CamposObligatorio">*</span></td>
		</tr>
		
	<tr>
		  <td class="style1"><div id='peso1' style='display:<?=($row_veh->IDFAMILIAVEH==1)?"block":"none"	?>' >&nbsp;<?=_('PESO')?></div></td>
    <td class="style1"><div id='peso2' style='display:<?=($row_veh->IDFAMILIAVEH==1)?"block":"none"	?>' ><?
			$con->cmb_array("cmbpeso",$desc_peso,$row_veh->ARRPESO," class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'")
		?></div></td>
  </tr>		
	<tr>
		  <td class="style1">&nbsp;# <?=_('MOTOR')?></td>
		  <td class="style1"><input name='txtmotor' type='text' id='txtmotor' value='<?=$row_veh->NUMSERIEMOTOR; ?>' size="20" maxlength="18"></td>
	</tr>
	<tr>
		  <td class="style1">&nbsp;# <?=_('SERIE')?></td>
		  <td class="style1"><input name='txtserie' type='text' id='txtserie' value='<?=$row_veh->NUMSERIECHASIS; ?>' size="20" maxlength="18"></td>
	</tr>	
	<tr>
		  <td class="style1">&nbsp;<?=_('COLOR')?></td>
		  <td class="style1"><input name='txtcolor' type='text' id='txtcolor' onblur="cambiarestilo('txtcolor');" value='<?=$row_veh->COLOR; ?>' size="20" maxlength="18"><span class="CamposObligatorio">*</span></td>
	</tr>

	<tr>
		<td class="style1">&nbsp;<?=_('ANIO')?></td>
		  <td class="style1"><?
			$con->cmb_array("cmbanio",$anio_vehiculo,$row_veh->ANIO," onblur=\"cambiarestilo('cmbanio')\" class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'")
	?><span class="CamposObligatorio">*</span></td>
	</tr>
	<tr>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
	</tr>
</table> 

<script type="text/javascript" >  

 	new Ajax.Autocompleter('txtmarca',	'mostrarmarca',"vehicular/consulta_marca.php",
	{
		method: "post",
		paramName: "marca",

		minChars: 2,
		selectFirst: true
	}
	);
	
	new Ajax.Autocompleter('txtsubmarca','mostrarsmarca',"vehicular/consulta_marca.php",
	{
		method: "post",
		paramName: "modelo",
		callback: function (element, entry) {
			parametros = "&marca2="+ $F('txtmarca')
			return entry +parametros;
		 },

		minChars: 2,
		selectFirst: true
	}
	);
 
	function comportamientoDiv(opc,nombrediv){
		if(opc=="1"){
			document.getElementById('peso1').style.display="block";
			document.getElementById('peso2').style.display="block";
		}else{
			document.getElementById('peso1').style.display="none";
			document.getElementById('peso2').style.display="none";
		}
	}
</script>


 <script type="text/javascript" >  
 
	function seleccion_vehiculo(){

	 var win = null;
	 
		if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
		else
		{
			win = new Window({
				className: "alphacube",
				title: '<?=_("SELECCION DEL VEHICULO (AFILIADO)")?>',
				width: 870,
				height: 350,
				showEffect: Element.show,
				hideEffect: Element.hide,
				resizable: false,
				destroyOnClose: true,
				minimizable: false,
				maximizable: false,
				url: 'vehicular/frm_vehiculosiac.php?idafiliado=<?=$exp->idafiliado;?>'
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


