<?
include_once('lib_funciones.php');

$error='';

$array_estado_certificado= array("ACT"=>"ACTIVO",
"ANU"=>"ANULADO",
"MOD"=>"MODIFICADO"
);

switch ($_POST[BUSQUEDA]){
	case 1 : //  BUSQUEDA POR TIPO DE DOCUMENTO
	$var_tipo_documento=$_POST[TIPODOCUMENTO];
	$var_nro_documento=trim($_POST[NDOCUMENTO]);
	$var_id_max=1;
	$obj_datos=FConsultarDatos_Axcelx_Cliente($var_id_max, $var_tipo_documento, $var_nro_documento, $var_id_user, $var_ip_usuario);
	if(!isset($obj_datos->DATA->ASEGURADO->NUMID))
	$error = _("ERROR: NO SE ENCONTRO DATOS DEL CLIENTE CON NRO DE DOCUMENTO ").$var_nro_documento;
	else
	{
		$asegurado[0] = $obj_datos->DATA->ASEGURADO;
		$var_num_id_asegurado=$obj_datos->DATA->ASEGURADO->NUMID;
		$obj_datos_poliza=FConsultarDatos_Axcelx_NumidAsegurado($var_id_max, $var_num_id_asegurado, $var_id_user, $var_ip_usuario);

		if (isset($obj_datos_poliza->DATA->POLIZA))  //SI TIENE POLIZAS
		{
			if (is_array($obj_datos_poliza->DATA->POLIZA)) // SI TIENE VARIAS POLIZAS
			{
				foreach ($obj_datos_poliza->DATA->POLIZA as $objeto)
				{
					$polizas[]=$objeto;
				}
			}
			else  // SI TIENE SOLO UNA POLIZA
			{

				$polizas[0]=$obj_datos_poliza->DATA->POLIZA;
			}
		}
	}


	break;

	case 2:  // BUSQUEDA POR CERTIFICADO
	{
		$var_nro_certificado=trim($_POST[NCERTIFICADO]);
		$var_id_max=1;
		$obj_datos_certificado=FConsultarDatos_Axcelx($var_id_max, $var_nro_certificado, $var_id_user, $var_ip_usuario);
		if(!isset($obj_datos_certificado->DATA->POLIZA->NUMPOL)) $error=_("ERROR: NO SE ENCONTRO DATOS DEL CERTIFICADO ").$var_nro_certificado;
		else
		$polizas[0] = $obj_datos_certificado->DATA->POLIZA;

		/* busca los datos del asegurado */
		$var_tipo_documento=$obj_datos_certificado->DATA->POLIZA->TIPOIDDOCASEG;
		$var_nro_documento=$obj_datos_certificado->DATA->POLIZA->NUMIDDOCASEG;
		$var_id_max=1;
		$obj_datos_cliente=FConsultarDatos_Axcelx_Cliente($var_id_max, $var_tipo_documento, $var_nro_documento, $var_id_user, $var_ip_usuario);
		if(isset($obj_datos_cliente->DATA->ASEGURADO->NUMID))	$asegurado[0]=$obj_datos_cliente->DATA->ASEGURADO;
		


		break;
	}
	case 3: //BUSQUEDA POR NOMBRE
	$var_asegurado_nom=strtoupper(trim($_POST[NOMBRES]));
	$var_asegurado_app=strtoupper(trim($_POST[APPATERNO]));
	$var_asegurado_apm=strtoupper(trim($_POST[APMATERNO]));

	$var_nombres_completos=$var_asegurado_nom." ".$var_asegurado_app." ".$var_asegurado_apm;

	if($var_asegurado_nom=="" || $var_asegurado_app=="" || $var_asegurado_apm=="") $error= _("ERROR: SE REQUIEREN NOMBRES, AP PATERNO Y AP MATERNO PARA HACER LA CONSULTA");
	else{
		$var_id_max=1;
		$obj_datos_asegurado=FConsultarDatos_Axcelx_ClienteNombres($var_id_max, $var_asegurado_nom, $var_asegurado_app, $var_asegurado_apm, $var_id_user, $var_ip_usuario);
		if(!isset($obj_datos_asegurado->DATA->ASEGURADO->NUMID)) echo _("ERROR: NO SE ENCONTRO DATOS DEL CLIENTE ").$var_nombres_completos;
		else
		{
			$var_id_max=1;
			$asegurado[0] = $obj_datos_asegurado->DATA->ASEGURADO;
			$var_num_id_asegurado=$obj_datos_asegurado->DATA->ASEGURADO->NUMID;
			$obj_datos_poliza=FConsultarDatos_Axcelx_NumidAsegurado($var_id_max, $var_num_id_asegurado, $var_id_user, $var_ip_usuario);

			if (isset($obj_datos_poliza->DATA->POLIZA))  //SI TIENE POLIZAS
			{
				if (is_array($obj_datos_poliza->DATA->POLIZA)) // SI TIENE VARIAS POLIZAS
				{
					foreach ($obj_datos_poliza->DATA->POLIZA as $objeto)
					{
						$polizas[]=$objeto;
					}
				}
				else  // SI TIENE SOLO UNA POLIZA
				{
					$polizas[0]=$obj_datos_poliza->DATA->POLIZA;
				}
			}
		}
	}
	break;
}


if ($error!=''):?>
<?=$error?>
<?else :?>

<fieldset>
<legend><?=_('DATOS ENCONTRADOS')?></legend>

<!--DATOS DEL ASEGURADO-->
<? if (isset($asegurado)):    // ?>
<? foreach ($asegurado as $ase):?>
<form id='asegurado'>
<div> 
 	<fieldset>
 	<legend><?=_('ASEGURADO')?></legend>
 		<?include_once('vista_datos_asegurados.php');?>
	</fieldset>
</div>
</form> 
<? endforeach;?>
<? endif; ?>
<!--FIN DE DATOS DEL ASEGURADO-->


<!-- DATOS DE LA POLIZA-->
<? if (isset($polizas)):?>
<? foreach ($polizas as $poli):?>
<form id='<?=$poli->NUMCERT?>'>
<div> 
	<fieldset>
 	<legend><?=_('CERTIFICADO').'->'.$poli->NUMCERT?> <input type="button" value="<?=_('ASIGNAR')?>" onclick="selecccionar('<?=$poli->NUMCERT?>');"class="normal"></legend>
		<?include('vista_datos_polizas.php');?>
	</fieldset>
</div>
</form>
<? endforeach; ?>
<? endif;?>
<!-- FIN DE DATOS DE LA POLIZA-->




</fieldset>
<?endif;?>

