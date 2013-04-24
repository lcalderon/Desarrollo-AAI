<?php
include_once('../../../../modelo/clase_lang.inc.php');
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once '../../../includes/jquery/head_prot_win.php';
$con= new DB_mysqli();
$idcuenta=  $_GET['IDCUENTA'];
$idprograma= $_GET['IDPROGRAMA'];
$idtipodocumento = $_GET['IDTIPODOCUMENTO'];
$pcNumDocumento =$_GET['IDDOCUMENTO'];
$idafiliado = $_GET['IDAFILIADO'];


/* Obteniendo el codigo del producto Dato del sponsor*/
$sql="select IDCODIGOSPONSOR from $catalogo.catalogo_programa where IDPROGRAMA='$idprograma' and IDCUENTA='$idcuenta' ";
$result = $con->query($sql);
while($reg = $result->fetch_object()){
	$pnIdProducto = $reg->IDCODIGOSPONSOR;
}

/*  Determinando el tipo de documento para el sponsor */
switch($idtipodocumento){
	case 'DNI': $pcIdTipDocumento='L'; break;
	case 'RUC': $pcIdTipDocumento='R'; break;
}


 
?>
<script type="text/javascript">
$(document).ready(function(){
	$('#enviar').click(function(){
		
		$('#resultado').val('');

		if ($('#pnIdMotivo').val()=='') alert('Seleccione un motivo');
	else {
	
		$('#spin').html("<img src='/imagenes/iconos/spin.gif'>");
		
		var ajaxReq = $.post('ajax_ws_aon.php',{
			PNIDPRODUCTO : $('#pnIdProducto').val(),
			PCIDTIPDOCUMENTO : $('#pcIdTipDocumento').val(),
			PCNUMDOCUMENTO: $('#pcNumDocumento').val(),
			PNIDMOTIVO: $('#pnIdMotivo').val(),
			PCDESCRIPCIONMOTIVO: $('#pcDescripcionMotivo').val(),
			IDAFILIADO: $('#idafiliado').val(),
			IDCUENTA: "<?=$_GET['IDCUENTA'] ?>",
			IDPROGRAMA : "<?=$_GET['IDPROGRAMA']?>",
			IDPROCESO : "<?=$_GET['IDPROCESO']?>"
			}
		);
		ajaxReq.success( function( msg ){
			$('#spin').html('');
			$('#resultado').html(msg);
			})
		
		} /* fin del else*/

		
	});
})
</script>
<body>
<h2>Sistema de desafiliaci&oacute;n AON</h2>
<form>
<fieldset>
<input type='hidden' name='pnIdProducto' id='pnIdProducto' value='<?=$pnIdProducto?>'> 
<input type='hidden' name='pcIdTipDocumento' id='pcIdTipDocumento' value='<?=$pcIdTipDocumento?>'> 
<input type='hidden' name='pcNumDocumento' id='pcNumDocumento' value='<?=$pcNumDocumento?>'>
<input type='hidden' name='idafiliado' id='idafiliado' value='<?=$idafiliado?>'>
<p>Motivo <br>
<?php
$sql ="select IDMOTIVOCANCELACION,DESCRIPCION from $catalogo.catalogo_motivoscancelacionAON";
$con->cmbselect_db('pnIdMotivo',$sql,'',"id='pnIdMotivo'",'','');
?></p>
<p><?=_('Observacion')?><br>
<textarea name='pcDescripcionMotivo' rows="4" cols="50"></textarea></p>

<p><?=_('Respuesta AON')?> <br>
<div id='resultado'></div>
<!-- <input type='text' name='resultado' id='resultado'  size='70' value=''
	readonly> --> <span id='spin'></span> 
<p><br>
<input type='button' name='Enviar' id='enviar' value='Enviar'
	class='guardar'> <input type='button' name='Cerrar' value='Cerrar' class='cancelar' onclick="window.close();"></p>

</fieldset>
</form>
</body>
</html>
