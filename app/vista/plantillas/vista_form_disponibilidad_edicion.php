<?
session_start();
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_ubigeo.inc.php');
include_once('../../modelo/clase_moneda.inc.php');
include_once('../../modelo/clase_plantilla.inc.php');
include_once('../../modelo/clase_persona.inc.php');
include_once('../../modelo/clase_telefono.inc.php');
include_once('../../modelo/clase_cuenta.inc.php');
include_once('../../modelo/clase_familia.inc.php');
include_once('../../modelo/clase_servicio.inc.php');
include_once('../../modelo/clase_programa_servicio.inc.php');
include_once('../../modelo/clase_programa.inc.php');
include_once('../../modelo/clase_afiliado.inc.php');
include_once('../../modelo/clase_etapa.inc.php');
include_once('../../modelo/clase_expediente.inc.php');
include_once('../../modelo/clase_asistencia.inc.php');



/* DATOS QUE VIENEN DE LA SESSION */
$idusuario = $_SESSION[user];
$idextension= $_SESSION[extension];


/* DATOS QUE VIENEN DEL EXTERIOR*/
$idexpediente=  $_GET[idexpediente];
$idfamilia= $_GET[idfamilia];

if ($_GET[cobertura]) $arrcondicionservicio = 'COB';



/* CREANDO OBJETOS PARA EXTRACCION DE DATOS */
$fam= new familia();
$fam->carga_datos($idfamilia);
$nombrefamilia= $fam->descripcion;

$exp= new expediente();
$exp->carga_datos($idexpediente);

$ubigeo[IDEXPEDIENTE]= $exp->idexpediente;
$ubi[CVEPAIS] = $exp->cvepais;
$ubi[CVEENTIDAD1] = ($exp->cveentidad1=='')?'0':$exp->cveentidad1;
$ubi[CVEENTIDAD2] = ($exp->cveentidad2=='')?'0':$exp->cveentidad2;
$ubi[CVEENTIDAD3] = ($exp->cveentidad3=='')?'0':$exp->cveentidad3;
$ubi[CVEENTIDAD4] = ($exp->cveentidad4=='')?'0':$exp->cveentidad4;
$ubi[CVEENTIDAD5] = ($exp->cveentidad5=='')?'0':$exp->cveentidad5;
$ubi[CVEENTIDAD6] = ($exp->cveentidad6=='')?'0':$exp->cveentidad6;
$ubi[CVEENTIDAD7] = ($exp->cveentidad7=='')?'0':$exp->cveentidad7;
$ubi[DIRECCION] = $exp->direccion;
$ubi[NUMERO] = $exp->numero;
$ubi[LATITUD] = $exp->latitud;
$ubi[LONGITUD] = $exp->longitud;
$ubi[REFERENCIA1] = $exp->referencia1;
$ubi[REFERENCIA2] = $exp->referencia2;

$exp->insert_reg("$exp->temporal.asistencia_lugardelevento",$ubi);
$id_lugardelevento = $exp->reg_id();
//$id_asis_lugardelevento;


/* VARIABLES */
$afiliado = $exp->personas[TITULAR][NOMBRE].' '.$exp->personas[TITULAR][APPATERNO].' '.$exp->personas[TITULAR][APMATERNO];
$contactante = $exp->personas[CONTACTO][NOMBRE].' '.$exp->personas[CONTACTO][APPATERNO].' '.$exp->personas[CONTACTO][APMATERNO];
$cuenta = $exp->cuenta->nombre;
$plan = $exp->programa->nombre;


$idetapa=1;  // inicia en la etapa 1
$etapa = new etapa();
$etapa->carga_datos($idetapa);

$idetapa_asis=1;



$idprograma = $exp->programa->idprograma;


$idasistencia=$_GET[idasistencia];


include_once('../includes/arreglos.php');

include_once('../../modelo/functions.php');
include_once('../includes/head_prot_win.php');


?>


<body>


<div id='zona_disponibilidad' >
<?
include_once('vista_form_disponibilidad.php');
?>
</div>  <!--fin del DIV   DATOS DEL SERVICIO-->



</body>



<script type="text/javascript" >

function actualizar_disponibilidad(idasistencia){
	new Ajax.Updater('zona_disponibilidad','vista_disponibilidad_afiliado.php?idasistencia='+idasistencia,
	{
		method : 'post',
		parameters : {idasistencia : '<?=$asis->idasistencia?>',
		
		},

	});
	return;
}

function grabar_dispo(){
 FECHAD=document.getElementById('date').value;
 HORAINI= document.getElementById('cbhora1').value+':'+document.getElementById('cbminuto1').value+':00';
  HORAFIN= document.getElementById('cbhora2').value+':'+document.getElementById('cbminuto2').value+':00';
  idasist=$('idasistencia').value;
 //alert(FECHAD + HORAINI + HORAFIN + idasist);

				new Ajax.Request('/app/controlador/ajax/ajax_grabar_disponibilidad.php',
				{
				method : 'post',
				evalScripts : true,
				parameters : {
					IDASISTENCIA : idasist,
					IDUSUARIOMOD : '<?=$idusuario?>',
					FECHA : FECHAD, 
					HORA1 : HORAINI,
					HORA2 : HORAFIN,
					OPCION : $('btnagregardispo').value,
					IDDISPO : $('hid_iddispo').value
				},
				onSuccess: function(t){
				//alert(t.responseText);
					alert($('btnagregardispo').value);
					if($('btnagregardispo').value=='Guardar Edicion'){
						  $('btnagregardispo').value='Agregar';
					  }
					actualizar_disponibilidad(idasist);
					
				}
			});
}

function accion_disponibilidad(modo,fecha,horaini,horafin,iddispo){
      idasist=$('idasistencia').value;
  
	switch (modo){
		case 'editar': // GUARDA LA BITACORA
		{
			$('btnagregardispo').value='Guardar Edicion';
			$('date').value=fecha;
			hora1 = horaini.substring(0,2);  
			hora2 = horafin.substring(0,2);  
			
			minuto1 = horaini.substring(3,5);
			minuto2 = horafin.substring(3,5);  

			$('cbhora1').value=hora1;
			$('cbhora2').value=hora2;
			$('cbminuto1').value=minuto1;
			$('cbminuto2').value=minuto2;
			$('hid_iddispo').value=iddispo;
			
				/*new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_monitoreo_proveedor.php?proveedor_act='+prov,
				{
					method : 'post',
					parameters : $('form_bitacora').serialize(true),
					onSuccess: function(t)
					{
						//alert(t.responseText);
						$('comentario').value='';
						actualizar_bitacora();
					}
				});
			*/
			break;
		}

	case 'eliminar':  // CANCELADO POSTERIOR DE LA ASISTENCIA
		{
	    alert(iddispo);
		     if(confirm('<?=_('ESTA SEGURO QUE DESEA ELIMINAR LA DISPONIBILIDAD SELECCIONADA?')?>')){
			new Ajax.Request('/app/controlador/ajax/ajax_eliminar_disponibilidad.php',
			{
				method : 'post',
				parameters : {
					IDDISPO : iddispo
					
				},
				onSuccess: function(t){
					//					alert(t.responseText);
				  $('btnagregardispo').value='Agregar';	
				  actualizar_disponibilidad(idasist);	
				}
			});
		    }


			break;
		}
	
		
		
	}
	return;
}




















</script>
