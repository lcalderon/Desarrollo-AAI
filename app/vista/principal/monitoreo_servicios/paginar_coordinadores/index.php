<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Paginar Resultados</title>
	<script type="text/javascript" src="../../../../../estilos/functionjs/validator.js"></script>
	<script type="text/javascript" src="../../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<link href="../../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	<style>
	td{
		width:200px;
	}
	a{
		text-decoration:underline;
		cursor:pointer;
	}
	</style>
	
	<script type="text/javascript">		
		function paginar(nropagina){
		
			new Ajax.Updater('contenido', 'paginador.php', {
				parameters: { pag: nropagina, nombrecoord: $F('txtnombre') },
				method: 'get',
				onCreate: function(objeto){
					document.getElementById('contenido').innerHTML= '<img src="../../../../../imagenes/iconos/loader.gif">';
				}
			});			
			
		}		
		
		function buscarProveedor(){
		
			new Ajax.Updater('contenido', 'paginador.php', {
				parameters: { nombrecoord: $F('txtnombre') },
				method: 'get',
				onCreate: function(objeto){
					document.getElementById('btnbuscar').value= 'Procesando...';
					document.getElementById('btnbuscar').disabled= true;
				},
				onSuccess: function(resp) {
					document.getElementById('btnbuscar').value= 'Buscar';
					document.getElementById('btnbuscar').disabled= false;
					ddocument.getElementById('txtnombre').focus();					
				}
			});			
		}
		
	function grabar_proveedor(nombrecoord,idcheck){
 
		new Ajax.Request('grabar_proveedor.php', {
			method: 'post',
			parameters: { nombrecoord: nombrecoord, idcheck: $(idcheck).checked },
			onSuccess: function(t) {

			}
		});
	}	
 			
	</script>

</head>
<body onload="document.getElementById('txtnombre').focus()"> 
		<div id="contenido" style="margin:auto;width:500px;text-align:center;"><?php include('paginador.php')?></div>
</body>
</html>