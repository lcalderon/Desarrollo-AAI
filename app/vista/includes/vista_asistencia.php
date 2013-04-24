<?
if ($_POST[cobertura]) $arrcondicionservicio = 'COB';

//$idasistencia=21;
/* CREANDO OBJETOS PARA EXTRACCION DE DATOS */
$fam= new familia();
$fam->carga_datos($idfamilia);

$exp= new expediente();
$exp->carga_datos($idexpediente);

$asis = new asistencia();
$asis->carga_datos($idasistencia);
$idservicio=$asis->idservicio;

$serv = new servicio();
$serv->carga_datos($idservicio);

$servicio = $serv->descripcion;


$asis->disponibilidad($idasistencia);
$disponibilidad=$asis->fecha;


?>

<body>
<div id='datos_asistencia'>
	<fieldset style="background: #CBD4DF">
	<legend><?=_('DATOS DE LA ASISTENCIA')?></legend>
	<table align="left">
		<tbody>
			<tr>
				<td width='12%'><?=_('ASISTENCIA')?></td>
				<td width='20%'><input type=text name='txtservicio' value='<?=$servicio?>' readonly></td>
				<td width='15%'><?=_('DISPONIBILIDAD DE AFILIADO')?></td>
				<td width='40%'><table border=1 bgcolor='#e9e8e8' cellpadding="0" cellspacing="0"><tr><td><? for($i=0;$i<count($disponibilidad);$i++){
						  echo substr($disponibilidad[$i][0],0,10).' DESDE  '.substr($disponibilidad[$i][0],11,2).' HASTA '.substr($disponibilidad[$i][1],11,2).' HORAS '.'<br>';
						 }?></td></tr></table></td>
			</tr>
			<tr>
				<td COLSPAN=2><?=_('DETALLE DE FALLA')?></td>
				<td></td>
				
			</tr>
		</tbody>
	</table>
	</fieldset>
</div> 