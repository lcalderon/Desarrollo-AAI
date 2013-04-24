<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>American Assist International</title>
<style type="text/css">
                            /*Menu Links*/

/*NOTE: anything not specified for the #(menu id's) a selector and its pseudo classes
may be inherited in some browsers from other 'a' element styles (if any) on the page*/

#menu1 a {color:black;background-color:white;text-decoration:none;text-indent:1ex;}
#menu1 a:active {color:black;text-decoration:none;}
#menu1 a:hover {color:black;background-color:#FFFF99}
#menu1 a:visited {color:black;text-decoration:none;}

#menu2 a {color:navy;background-color:white;text-decoration:none;text-indent:1ex;}
#menu2 a:active	{color:blue;text-decoration:none;}
#menu2 a:visited {color:blue;text-decoration:none;}
#menu2 a:hover {color:navy;background-color:#f0fea8}

#menu3 a { /*Menu3 Links*/
color:black;
background-color:white;
text-decoration:none;
text-indent:1ex;
}
#menu3 a:hover {
color:black;background-color:#FFFF99;
}
#menu3 a:active	{color:black;text-decoration:none;}
#menu3 a:visited	{color:black;text-decoration:none;}


                           /*End Menu Links*/
</style>
<?php
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/functions.php');
$con = new DB_mysqli();
$idasistencia=21;

$con->select_db($con->temporal);
$sql_asistencia = "SELECT IDETAPA FROM asistencia WHERE IDASISTENCIA = $idasistencia";

$exec_asistencia = $con->query($sql_asistencia);
while($rset_asistencia=$exec_asistencia->fetch_object()){
	$idetapa = $rset_asistencia->IDETAPA;
}
$idetapa=5;
	    switch($idetapa)
		{
		      case 1: 
			     $url = '../../expediente/entrada/expediente_frmexpediente_new.php';
			     $target = 'ifr_dispo';break;			
		      
		      case 4: 
			      $url = "../disponibilidad/disponibilidad_afiliado.php?idasistencia=$idasistencia";
			      $target = 'ifr_dispo';break;
		      case 5: 
			      $url = "../asignacion/asignacion_proveedor2.php?idasistencia=$idasistencia";
			      $target = 'ifr_detalle';break;
		     
			     
		}

//echo $url.' '.$target;

?>
<script src="mmenu.js" type="text/javascript"></script>
<script src="menuItems.js.php?idasistencia=<?=$idasistencia?>&idetapa=<?=$idetapa?>" type="text/javascript">

/***********************************************
* Omni Slide Menu script - � John Davenport Scheuer: http://home.comcast.net/~jscheuer1/
* very freely adapted from Dynamic-FX Slide-In Menu (v 6.5) script- by maXimus
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full original source code
***********************************************/

</script>

<script language="javascript" type="text/javascript">

		function mostrar_dispo(elemento,valor,valor2) {

			if(elemento.value==">") {
				document.getElementById(valor).style.display='block';
				document.getElementById(valor2).style.display='none';
				elemento.value="<";
			}	
			else if(elemento.value=="<") {
				document.getElementById(valor).style.display='none';
				document.getElementById(valor2).style.display='block';
				elemento.value=">";
			}		
		}



</script>
</head>
<body><table border=0 width="100%">
		<tr>
			<td><table border="0" width="100%"><tr><td><div id='disponibilidad' align='center' <? if($target=='ifr_dispo'){ ?>style='display: block;width:99%'<? } else {?>  style='display: none;width:99%' <? } ?>>
			<iframe name="ifr_dispo" id="ifr_dispo" height="768px" style="width:100%" frameborder="0" <? if($target=='ifr_dispo'){ ?> src="<?=$url?>" <? } ?>></iframe></div></td></tr></table></td>
			<td><div id='asistencia' <? if($target=='ifr_detalle'){ ?>style='display: block;width:99%'<? } else {?>  style='display: none;width:99%' <? } ?>>
			<table width='99%'>
	    <tr><td><div id='apertura' ></div></td></tr>
	    <tr><td><div id='servicio' ></div></td></tr>
	    <tr><td>
	    <iframe id='ifr_detalle' width=100%  height="768px" frameborder="0" <? if($target=='ifr_detalle'){ ?> src="<?=$url?>" <? } ?>></iframe></td>
	    </tr>
	 </table></div></td>
		</tr>
	</table>
</body>
</html>
