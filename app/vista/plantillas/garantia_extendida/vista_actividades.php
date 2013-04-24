<?
// devuelve la posicion (indice) del elemento en el array
function posicion($valor,$arr){
	$sw=0; $i=0;
	foreach ($arr as $indice=>$value)
	{
		$i++;
		if ($valor==$indice) { $sw=1; break;}

	}
	return ($sw)?$i+1:1;
}

// proximo indice del array
function proximo($act_ant,$arr){
	$ar = array_keys($arr);
	for($i=0;$i<=count($ar);$i++)	if ($act_ant==$ar[$i]) break;
	return ($i<6)?$ar[$i+1]:$ar[$i];
}


// variables de fecha

$fecha_act = date("Y-m-d");
$hora = date("H");
$minuto = date("i");


// array de actividades
$array_actividades = array(
"AS1"=>'AS1 - ENVIO DE CASO A AEQUITAS',
"AS2"=>'AS2 - RESPUESTAS DE ASIGNACION DE ST POR AEQUITAS',
"AS3"=>'AS3 - CONFIRMACION DE ASIGNACION DE ST',
"AS4"=>'AS4 - CAMBIO DE STATUS EN BPM POR PARTE DEL ST (cambio de estado)',
"AS5"=>'AS5 - ENVIO DE HOJA DE RUTA POR PARTE DE'
);
//$lista_act = implode(",",array_keys($array_actividades));
$lista_act ="'AS1','AS2','AS3','AS4','AS5'";

// query para saber que conocer la ultima actividad realizada
$sql="
select
 * 
from 
	$con->temporal.asistencia_bitacora_etapa1 
where 
	ARRCLASIFICACION IN ($lista_act) 
 	AND IDASISTENCIA='$idasistencia'
 	ORDER BY ARRCLASIFICACION DESC
	LIMIT 1
 	";

$result =$con->query($sql);
while($reg=$result->fetch_object()){
	$actividad_ant =$reg->ARRCLASIFICACION;
}

$actividad_prox = (isset($actividad_ant))?proximo($actividad_ant,$array_actividades):'';

?>


<table style="width:100%;">
	<tbody>
		<tr>
			<td><?=_('ACTIVIDAD')?>
			<select name='ACTIVIDAD' id='actividad' onchange="opcion_seleccionada(this.value);">
				<?foreach($array_actividades as $indice=>$value):?>
				   <? if ($actividad_prox>='AS3' && (strcmp($indice,'AS3')<0)) $disabled='disabled';  else $disabled='';?>
					<option value="<?=$indice?>" <?=($actividad_prox==$indice)?'selected':'';?> <?=$disabled?> ><?=$value?></option>
				<?endforeach;?>
			</select>
			</td>
				<td><?=_('FECHA')?>
			<input type="text" name='FECHAMANUAL' id='date2' value="<?=$fecha_act?>" readonly>
 			<input type="button" id="calendarButton2" value="..." onmouseover="setupCalendars_asist();" class='normal'>
			</td>
			<td><?=_('HH')?><? $con->cmbselect_hora('HORA',$hora,'',"id='hora'",'')?></td>
			<td><?=_('MM')?><? $con->cmbselect_minuto('MINUTO',$minuto,1,"id='minuto'",'','')?></td>
			</td>
			<td>
				<input type="button" name="" id="btn_grabaractividad" value="<?=_('GRABAR ACTIVIDAD')?>" class="normal" onclick="grabar_actividades();"/>
			</td>
		</tr>
		<tr id='zona_nombrest' style="display:none;">
			<td colspan="5"><?=_('NOMBRE DEL ST ASIGNADO')?>
			<input type="text" name='NOMBREST' id='nombrest' value='' size='70'></input>
			</td>
		</tr>
	</tbody>
</table>



<script type="text/javascript">
function desactivar_opciones(){
	var posicion = '<?= posicion($actividad_ant,$array_actividades)?>';
	$$('select#actividad option').each(function(el,indice)
	{

		if (posicion <= indice)	el.disabled = true;
		if (posicion == 2) $('zona_nombrest').style.display='block';
	}
	);
	return;
}

/* calculo de la fecha correspondiente */
function calculo_fecha(fecha,act_ejecutandose){
	var rfecha;
	var	aa= fecha.getFullYear();
	var	mm= fecha.getMonth();
	var	dd= fecha.getDate(); // dia del mes
	var	d=fecha.getDay(); // dia de la semana
	var	h = fecha.getHours();
	var m = fecha.getMinutes();

	switch (act_ejecutandose)
	{
		case 'AS1':
		{
			var h_ini=8; // hora de inicio del siguiente dia
			var m_ini=0; // minuto de inicio del siguiente dia

			if (d==0) rfecha = new Date(aa,mm,dd+1,h_ini,m_ini,0); //domingo , prox lunes
			else
			if (d==6)// sabado
			if (h<8) rfecha = new Date(aa,mm,dd,h_ini,m_ini,0); //mismo dia
			else if ((h>19) ||( h==19 && m>25 )) 	rfecha = new Date(aa,mm,dd+2,h_ini,m_ini,0); //lunes
			else rfecha= new Date(aa,mm,dd,h,m,0); //en el momento
			else if (h<8) rfecha = new Date(aa,mm,dd,h_ini,m_ini,0);
			else if ((h>19) ||( h==19 && m>25 )) 	rfecha = new Date(aa,mm,dd+1,h_ini,m_ini,0); //lunes
			else rfecha= new Date(aa,mm,dd,h,m,0); //en el momento

			/* borra las tareas de la asistencia */
			new Ajax.Request('/app/controlador/ajax/ajax_borrar_tarea.php',
			{
				method : 'post',
				parameters: { IDASISTENCIA : $F('idasistencia') }
			});

			break;
		}

		case 'AS1_4':  //PROGRAMACION DE LA TAREA AS4
		{
			var h_ini=9; // hora de inicio del siguiente dia
			var m_ini=0; // minuto de inicio del siguiente dia

			if (d==0) rfecha = new Date(aa,mm,dd+1,h_ini,m_ini,0); //domingo , prox lunes
			else
			if (d==6)// sabado
			if (h<9) rfecha = new Date(aa,mm,dd,h_ini,m_ini,0); //mismo dia
			else if (h>11) rfecha = new Date(aa,mm,dd+2,h_ini,m_ini,0); //lunes
			else rfecha= new Date(aa,mm,dd,h,m,0); //en el momento
			else if (h<9) rfecha = new Date(aa,mm,dd,h_ini,m_ini,0); //mismo dia
			else if (h>16) 	rfecha = new Date(aa,mm,dd+1,h_ini,m_ini,0); // siguiente dia
			else rfecha= new Date(aa,mm,dd,h,m,0); //en el momento

			break;
		}

		case 'AS1_5':  //PROGRAMACION DE LA TAREA AS5
		{
			var h_ini=9; // hora de inicio del siguiente dia
			var m_ini=0; // minuto de inicio del siguiente dia

			if (d==0) rfecha = new Date(aa,mm,dd+2,h_ini,m_ini,0); //domingo , prox martes
			else if (d==6)// sabado
			if (h<7) rfecha = new Date(aa,mm,dd+2,h_ini,m_ini,0); // mismo dia
			else if (h>=13) rfecha = new Date(aa,mm,dd+3,h_ini,m_ini,0); //prox martes
			else rfecha= new Date(aa,mm,dd+2,h,m,0); //prox lunes
			else if (d==5 && h>16 )  rfecha = new Date(aa,mm,dd+3,h_ini,m_ini,0); //prox lunes
			else if (h<8) rfecha = new Date(aa,mm,dd+1,h_ini,m_ini,0);  // al siguiente dia
			else if (h>16) 	rfecha = new Date(aa,mm,dd+2,h_ini,m_ini,0); // a 2 dias despues
			else rfecha= new Date(aa,mm,dd+1,h_ini,m_ini,0); // al siguiente dia
			break;
		}
		case 'AS2':
		{
			var h_ini=9; // hora de inicio del siguiente dia
			var m_ini=0; // minuto de inicio del siguiente dia

			if (d==0) rfecha = new Date(aa,mm,dd+1,h_ini,m_ini,0); //domingo , prox lunes
			else if (d==6)// sabado
			if (h<9) rfecha = new Date(aa,mm,dd,h_ini,m_ini,0); //mismo dia
			else if (h>=11) rfecha = new Date(aa,mm,dd+2,h_ini,m_ini,0); //lunes
			else rfecha= new Date(aa,mm,dd,h,m,0); //en el momento
			else if (h<9) rfecha = new Date(aa,mm,dd,h_ini,m_ini,0);
			else if ((h>19) ||( h==19 && m>25 )) 	rfecha = new Date(aa,mm,dd+1,h_ini,m_ini,0); //lunes
			else rfecha= new Date(aa,mm,dd,h,m,0); //en el momento

			break;
		}

	}

	/* borra las tareas de la asistencia */
	new Ajax.Request('/app/controlador/ajax/ajax_borrar_tarea.php',
	{
		method : 'post',
		parameters: { IDASISTENCIA : $F('idasistencia'),
		IDTAREA: act_ejecutandose }
	});


	aa= rfecha.getFullYear();
	mm= rfecha.getMonth()+1;
	dd= rfecha.getDate(); // dia del mes
	h = rfecha.getHours();
	m =rfecha.getMinutes();

	var strfecha= aa+'-'+mm+'-'+dd+' '+h+':'+m+':'+'00';    //fecha en formato string "aa-mm-dd h:m:s"
	return strfecha;
}

function grabar_actividades(){
	$('btn_grabaractividad').disabled=true;
	if ($F('actividad')=='AS2' && $F('nombrest')=='')
	{
		alert('<?=_("INGRESE EL NOMBRE DE ST ASIGNADO")?>');
		$('btn_grabaractividad').disabled=false;
	}
	else
	{
		new Ajax.Request('/app/controlador/ajax/ajax_grabar_asistencia_bitacora.php',
		{
			method: 'post',
			parameters: {
				IDASISTENCIA : $F('idasistencia'),
				IDUSUARIOMOD : '<?=$idusuario?>' ,
				ARRCLASIFICACION : $F('actividad'),
				COMENTARIO : $F('nombrest'),
				FECHAMANUAL : $F('date2')+' '+$F('hora')+':'+$F('minuto')+':00',
				IDETAPA: 1
			},
			onSuccess: function ()
			{
				/* Fecha que ingreso el coordinador*/
				var xfecha = $F('date2');
				var arrfecha= xfecha.split('-');
				var h= $F('hora');
				var m= $F('minuto');
				var s= 0;
				var fecha = new Date(arrfecha[0],arrfecha[1]-1,arrfecha[2],h,m,s);

				//				alert($F('actividad'));
				switch ($F('actividad')){
					case 'AS1': idtarea='AS2'; break;
					case 'AS2': idtarea='AS3'; break;
					default :
					location.reload();
					break;

				}
				var fechatarea = calculo_fecha(fecha,$F('actividad')); //calcula la fecha de la tarea

				new Ajax.Request('/app/controlador/ajax/ajax_graba_monitor.php',
				{
					method : 'post',
					parameters: {
						IDTAREA: idtarea,
						IDUSUARIO:'<?=$idusuario?>',
						FECHATAREA: fechatarea,
						IDEXPEDIENTE:'<?=$idexpediente?>',
						IDASISTENCIA:$F('idasistencia'),
						STATUSTAREA:'PENDIENTE'
					},
					onSuccess: function (){
						if ($F('actividad')=='AS1')
						{
							idtarea='AS4';
							fechatarea = calculo_fecha(fecha,'AS1_4'); //calcula la fecha de la tarea

							new Ajax.Request('/app/controlador/ajax/ajax_graba_monitor.php',
							{
								method : 'post',
								parameters: {
									IDTAREA: idtarea,
									IDUSUARIO:'<?=$idusuario?>',
									FECHATAREA: fechatarea,
									IDEXPEDIENTE:'<?=$idexpediente?>',
									IDASISTENCIA:$F('idasistencia'),
									STATUSTAREA:'PENDIENTE'
								},
								onSuccess: function (){
									idtarea='AS5';
									fechatarea = calculo_fecha(fecha,'AS1_5'); //calcula la fecha de la tarea
									
									new Ajax.Request('/app/controlador/ajax/ajax_graba_monitor.php',
									{
										method : 'post',
										parameters: {
											IDTAREA: idtarea,
											IDUSUARIO:'<?=$idusuario?>',
											FECHATAREA: fechatarea,
											IDEXPEDIENTE:'<?=$idexpediente?>',
											IDASISTENCIA:$F('idasistencia'),
											STATUSTAREA:'PENDIENTE'
										},
										onSuccess: function (){
											location.reload();
										}
									});
								}
							});
						}
						else
						location.reload();
					}
				});
			}
		});
	}
	return;
}

function opcion_seleccionada(valor){

	if (valor=='AS2') $('zona_nombrest').style.display='block';
	else
	$('zona_nombrest').style.display='none';

	return;
}
</script>