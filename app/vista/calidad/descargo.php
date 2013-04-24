<?
	session_start();  
	
	include_once("../../modelo/clase_lang.inc.php");	
	include_once("../../modelo/clase_mysqli.inc.php");
	include_once("../../vista/login/Auth.class.php");
	include_once("../includes/arreglos.php");
	include_once("../../modelo/functions.php");
 
    $con= new DB_mysqli();	 
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	Auth::required($_SERVER['REQUEST_URI']);
	$idcodigo=$_GET["plan"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>


	<script type="text/javascript" src="../../../estilos/functionjs/func_global.js"></script>	
	<link rel="stylesheet" href="../../../librerias/tinytablev3.0/style_sac.css" />
	<link href="../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	 
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	
	<script type="text/javascript">

	function validarIngreso(valors){

		$('txtasegimiento').value=$('txtasegimiento').value.replace(/^\s*|\s*$/g,"");
		
		if($('txtasegimiento').value==""){
					  alert("INGRESE EL DESCARGO.");
					  $('txtasegimiento').focus();
					  return (false);
			}
				
		if(confirm('<?=_("DESEA INGRESAR EL DESCARGO?.") ;?>\n')){
					
					$('brndescargo').value='PROCESANDO...';
					$('brndescargo').disabled=true;
					
					new Ajax.Request('gdescargo.php',{
						method: 'post',
						parameters : $('frmDescargo').serialize(true),
						onSuccess: function(resp){				
							
							new Ajax.Updater('div-contenido', 'vista_descargo.php',{
								parameters: { idcodigo:$('txtcodigo').value },
								method: 'post'
							});
							
							$('txtasegimiento').value='';

							$('brndescargo').value='AGREGAR DESCARGO';
							$('brndescargo').disabled=false;
							 					 
						},
						onFailure: function(){
							alert('ERROR, NO SE HA REALIZADO LA OPERACION.');
							$('brndescargo').value='AGREGAR DESCARGO';
							$('brndescargo').disabled=false;
						}
					});		
		}		
						
		   
		return (false);
	}
	
	</script>
	 
</head>

<body>




 

	<form name="frmDescargo" id="frmDescargo" method="post" action="">
 
		<input type="hidden" id="txtcodigo" name="txtcodigo" value="<?=$idcodigo?>"/>
		<table width="100%" border="0" cellpadding="5" cellspacing="5" bgcolor="#DFDFFF" style="border:1px solid #8080c0">
			<tr>
				<td>DEFICIENCIA: </td>
				<td><strong><u><?=$_REQUEST["opcion"] ;?></u></strong></td> 
			</tr>	
			<tr>
				<td><?=_("DESCARGO") ;?>:</td>
				<td><textarea name="txtasegimiento" id="txtasegimiento" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" class='classtexto' cols="100" rows="4"  style="text-transform:uppercase;"></textarea></td>			 
			</tr>
			<tr>
				<td align="center" colspan="2"><input type="button" name="brndescargo" id="brndescargo" value="AGREGAR DESCARGO" onclick="validarIngreso()" style="text-align:center;font-weight:bold;height:35px;"></td> 
			</tr>
		</table> 
	

	</form>
	 	<div id="div-contenido"><? include("vista_descargo.php"); ?></div>	

</body>
</html>
	 