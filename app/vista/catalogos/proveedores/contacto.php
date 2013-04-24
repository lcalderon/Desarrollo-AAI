<?
session_start();

include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_persona.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');
include_once('../../includes/arreglos.php');
include_once('../../includes/head_prot_win.php');

$idproveedor = $_GET[idproveedor];
$edicion = (isset($_GET[edicion]))?$_GET[edicion]:0;

?>

<body>

<div id='zona_form_contacto' >
<? include_once('vista_contacto.php');?>
</div>

<div id='zona_listado_contactos' style=" overflow:auto">
<? include_once('listado_proveedores_contacto.php')?>

</div>
</body>
</html>
<script type="text/javascript">

function listado(){
	new Ajax.Updater('zona_listado_contactos',"listado_proveedores_contacto.php",
	{
		method : 'post',
		evalScripts : true,
		parameters : { 
			idproveedor : '<?=$idproveedor?>',
			edicion: "<?=$edicion?>"
		}
	}
	);
	return;
}

function borrar_contacto(idcontacto){
	new Ajax.Request('../../../controlador/ajax/ajax_proveedor_contacto.php?opcion=borrar',
	{
		method: 'post',
		parameters:  { IDCONTACTO: idcontacto },
		onSuccess: function()
		{
			listado();
		}
	});
	return;
}

function editar_contacto(idcontacto){
	$('zona_form_contacto').show();
	new Ajax.Updater('zona_form_contacto',"vista_contacto.php",
	{
		method : 'post',
		parameters: { 
			idcontacto : idcontacto,
			edicion: "<?=$edicion?>"

		}
	}
	);
	return;
}

function salir(){
	parent.win.close();
	
}

</script>