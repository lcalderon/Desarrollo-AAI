<?
if ($_POST[cobertura]) $arrcondicionservicio = 'COB';


/* CREANDO OBJETOS PARA EXTRACCION DE DATOS */
$fam= new familia();
$fam->carga_datos($idfamilia);

$exp= new expediente();
$exp->carga_datos($idexpediente);

/* VARIABLES */
$afiliado = $exp->titular_persona->nombre.' '.$exp->titular_persona->appaterno.' '.$exp->titular_persona->apmaterno;
$contactante = $exp->contacto->nombre.' '.$exp->contacto->appaterno.' '.$exp->contacto->apmaterno;
$cuenta = $exp->cuenta->nombre;
$plan = $exp->programa->nombre;
$etapa = new etapa();
$etapa->carga_datos(1); // inicia en la etapa 1

//$etapa = $asis->etapa->descripcion;
//$objetivo = $asis->etapa->objetivo;
$ubigeo = $exp->ubigeo;
$idprograma = $exp->programa->idprograma;
/*
if ($arrcondicionservicio=='COB') $servicios =$exp->programa->servicios;
else
{
	$fam->carga_servicios($idfamilia);
	$servicios = $fam->servicios;
}*/
?>

<body>
<div id='datos_expediente'>
	<fieldset style="background: #ECE9D8">
	<legend><?=_('DATOS DEL EXPEDIENTE')?></legend>
	<table align="left">
		<tbody>
			<tr>
				<td width='7%'><?=_('ETAPA')?></td>
				<td width='20%'><?=$etapa->descripcion?></td>
				<td width='7%'><?=_('Titular')?></td>
				<td width='30%'><input type='text' size='40' value="<?=$afiliado?>" readonly></td>
				<td width='7%'><?=_('Cuenta')?></td>
				<td width='20%'><input type='text'value="<?=$cuenta?>" readonly>&nbsp;
				<? if ($exp->cuenta->cuentavip) echo "VIP"?>
				</td>
			</tr>
			<tr>
				<td><?=_('OBJETIVO')?></td>
				<td><?=$etapa->objetivo?></td>
				<td><?=_('Contactante')?></td>
				<td><input type='text'  size='40' value="<?=$contactante?>" readonly></td>
				<td><?=_('Plan')?></td>
				<td><input type='text' value="<?=$plan?>" readonly>
				<? if ((!$exp->cuenta->cuentavip) && ($exp->programa->programavip)) echo "VIP"?>
				</td>
			</tr>
		</tbody>
	</table>
	</fieldset>
</div> 