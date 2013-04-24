<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_asistencia.inc.php');
include_once('../includes/head_prot_win.php');


$idusuario = $_SESSION[user];
$idasistencia=$_GET[idasistencia];
$idetapa = $_GET[idetapa];

$asis = new asistencia();
$asis->carga_bitacora($idasistencia,$idetapa);


?>
<body>

<div id='zona_formulario'>
<form id='form_datos_bitacora'>
<input type="hidden" name='IDASISTENCIA' id='idasistencia' value="<?=$idasistencia?>">
<input type="hidden" name='IDETAPA' id='idetapa' value="<?=$idetapa?>">
<input type="hidden" name='IDUSUARIOMOD' id='idusuariomod' value="<?=$idusuario?>">
<input type="hidden" name="ARRCLASIFICACION" id='arrclasificacion' value="">
<table>
<tr>
	<td>
		<?=_('COMENTARIO:')?><br>
		<textarea cols="40" name="COMENTARIO" id="comentario"></textarea>
	</td>
	<td>
		<input type="button" VALUE="<?=_('AGREGAR')?>" onclick="grabar()" class="normal">
	</td>
</tr>
</table>
</form>
</div>
<div id='listado_bitacora'>
<? include_once('listar_bitacora.php');?>
</div>


</body>
</html>
<script type="text/javascript" >

function grabar(){
	new Ajax.Request('/app/controlador/ajax/ajax_grabar_asistencia_bitacora.php',
	{
		method: 'post',
		parameters:  $('form_datos_bitacora').serialize(true),
		onSuccess: function(t){
			$('comentario').clear();
			new Ajax.Updater('listado_bitacora','listar_bitacora',{
				method: 'post',
				evalScripts : true,
				parameters: {
					idasistencia : "<?=$idasistencia?>",
					idetapa : "<?=$idetapa?>"
					}
			});
		}
	});

}
</script>